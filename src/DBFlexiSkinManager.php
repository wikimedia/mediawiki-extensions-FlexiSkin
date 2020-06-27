<?php

namespace MediaWiki\Extension\FlexiSkin;

use Exception;
use Wikimedia\Rdbms\LoadBalancer;

class DBFlexiSkinManager implements IFlexiSkinManager {

	/** @var LoadBalancer */
	protected $lb;

	/**
	 * @param LoadBalancer $loadBalancer
	 */
	public function __construct( LoadBalancer $loadBalancer ) {
		$this->lb = $loadBalancer;
	}

	/**
	 * @param IFlexiSkin $flexiSkin
	 * @return int
	 */
	public function save( IFlexiSkin $flexiSkin ) {
		if ( $flexiSkin->getId() === null ) {
			return $this->addFlexiSkin(
				$flexiSkin->getName(),
				$flexiSkin->getConfig()
			);
		}
		return $this->updateFlexiSkin(
			$flexiSkin->getId(),
			$flexiSkin->getName(),
			$flexiSkin->getConfig()
		);
	}

	/**
	 * @param int $id
	 * @return IFlexiSkin|null
	 */
	public function loadFromId( $id ) {
		$db = $this->lb->getConnection( DB_REPLICA );
		$row = $db->selectRow(
			'flexiskin',
			'*',
			[
				'fs_id' => $id
			]
		);

		if ( $row === false ) {
			$skin = null;
		} else {
			$skin = FlexiSkin::newFromRow( $row );
		}

		return $skin;
	}

	/**
	 * @return int|null
	 */
	public function getActive() {
		$db = $this->lb->getConnection( DB_REPLICA );
		$row = $db->selectRow(
			'flexiskin',
			[
				'fs_id'
			],
			[
				'fs_active' => true,
				'fs_deleted' => false
			],
			__METHOD__
		);

		if ( !$row ) {
			return null;
		}
		return (int)$row->fs_id;
	}

	/**
	 * @param IFlexiSkin $flexiSkin
	 * @return bool
	 */
	public function setActive( IFlexiSkin $flexiSkin ) {
		$this->resetActive();
		$db = $this->lb->getConnection( DB_MASTER );
		return $db->update(
			'flexiskin',
			[
				'fs_active' => true
			],
			[
				'fs_id' => $flexiSkin->getId()
			],
			__METHOD__
		);
	}

	/**
	 * @return bool
	 */
	private function resetActive() {
		$db = $this->lb->getConnection( DB_MASTER );
		return $db->update(
			'flexiskin',
			[ 'fs_active' => false ],
			[ 'fs_active' => true ],
			__METHOD__
		);
	}

	/**
	 * @param IFlexiSkin $flexiSkin
	 * @return bool
	 */
	public function delete( IFlexiSkin $flexiSkin ) {
		$db = $this->lb->getConnection( DB_MASTER );
		return $db->update(
			'flexiskin',
			[
				'fs_deleted' => true
			],
			[
				'fs_id' => $flexiSkin->getId()
			],
			__METHOD__
		);
	}

	/**
	 * @param string $name
	 * @param array $config
	 * @return int $id
	 * @throws Exception
	 */
	private function addFlexiSkin( $name, $config = [] ) {
		if ( $name === null || $name === '' ) {
			throw new Exception( 'Invalid skin name' );
		}
		$db = $this->lb->getConnection( DB_MASTER );
		$res = $db->insert(
			'flexiskin',
			[
				'fs_name' => $name,
				'fs_config' => empty( $config ) ? '' : json_encode( $config )
			],
			__METHOD__
		);

		if ( $res ) {
			return $db->insertId();
		}

		throw new Exception( $db->lastError() );
	}

	/**
	 * @param int $id
	 * @param string $name
	 * @param array $config
	 * @return int|false $id
	 */
	private function updateFlexiSkin( $id, $name, $config ) {
		$args = [
			'fs_name' => $name,
			'fs_config' => empty( $config ) ? '' : json_encode( $config )
		];

		$db = $this->lb->getConnection( DB_MASTER );
		$res =
			$db->update(
			'flexiskin',
			$args,
			[
				'fs_id' => $id
			],
			__METHOD__
		);

		if ( $res ) {
			return $id;
		}

		return false;
	}

	/**
	 * @param bool $includeDeleted
	 * @return IFlexiSkin[]
	 */
	public function getList( $includeDeleted = false ): array {
		$conds = [];
		if ( !$includeDeleted ) {
			$conds['fs_deleted'] = false;
		}
		$db = $this->lb->getConnection( DB_REPLICA );
		$res = $db->select(
			'flexiskin',
			'*',
			$conds,
			__METHOD__,
			[ 'ORDER BY' => 'fs_name' ]
		);

		$skins = [];
		foreach ( $res as $row ) {
			$skins[] = FlexiSkin::newFromRow( $row );
		}

		return $skins;
	}

	/**
	 * @param string $name
	 * @param array $config
	 * @return IFlexiSkin
	 */
	public function create( $name, $config ): IFlexiSkin {
		$id = $this->getFlexiSkinIdOrNull( $name );
		return new FlexiSkin( $id, $name, $config, false );
	}

	/**
	 * @param string $name
	 * @return int
	 */
	private function getFlexiSkinIdOrNull( $name ) {
		$db = $this->lb->getConnection( DB_REPLICA );
		$row = $db->selectRow( 'flexiskin', 'fs_id', [ 'fs_name' => $name ] );
		if ( $row === false ) {
			return null;
		}
		return (int)$row->fs_id;
	}

	/**
	 * @inheritDoc
	 */
	public function restore( IFlexiSkin $skin ): bool {
		if ( !$skin->isDeleted() ) {
			return true;
		}

		$db = $this->lb->getConnection( DB_MASTER );
		$res =
			$db->update(
				'flexiskin',
				[ 'fs_deleted' => false ],
				[ 'fs_id' => $skin->getId() ],
				__METHOD__
			);

		return $res;
	}
}
