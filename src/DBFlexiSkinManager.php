<?php

namespace MediaWiki\Extension\FlexiSkin;

use Wikimedia\Rdbms\IDatabase;
use Wikimedia\Rdbms\LoadBalancer;

class DBFlexiSkinManager implements IFlexiSkinManager {

	/**
	 * @var IDatabase
	 */
	private $dbw = null;

	/**
	 * @var IDatabase
	 */
	private $dbr = null;

	/**
	 * @param LoadBalancer $loadBalancer
	 */
	public function __construct( LoadBalancer $loadBalancer ) {
		$this->dbw = $loadBalancer->getConnection( DB_MASTER );
		$this->dbr = $loadBalancer->getConnection( DB_REPLICA );
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
		$row = $this->dbr->selectRow(
			'flexiskin',
			'*',
			[
				'fs_id' => $id
			]
		);

		if ( $row === false ) {
			$skin = null;
		} else {
			$skin = new FlexiSkin(
				$row->fs_id,
				$row->fs_name,
				json_decode( $row->fs_config, true ),
				$row->fs_active
			);
		}

		return $skin;
	}

	/**
	 * @return int|null
	 */
	public function getActive() {
		$row = $this->dbr->selectRow(
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
		return $row->fs_id;
	}

	/**
	 * @param IFlexiSkin $flexiSkin
	 * @return bool
	 */
	public function setActive( IFlexiSkin $flexiSkin ) {
		$this->resetActive();

		$res = $this->dbw->update(
			'flexiskin',
			[
				'fs_active' => true
			],
			[
				'fs_id' => $flexiSkin->getId()
			],
			__METHOD__
		);

		return true;
	}

	/**
	 *
	 */
	private function resetActive() {
		$allFlexiSkins = $this->getList();

		foreach ( $allFlexiSkins as $flexiSkin ) {
			if ( $flexiSkin->isActive() == true ) {
				$res = $this->dbw->update(
					'flexiskin',
					[
						'fs_active' => false
					],
					[
						'fs_id' => $flexiSkin->getId()
					],
					__METHOD__
				);
			}
		}
	}

	/**
	 * @param IFlexiSkin $flexiSkin
	 * @return bool
	 */
	public function delete( IFlexiSkin $flexiSkin ) {
		$res = $this->dbw->update(
			'flexiskin',
			[
				'fs_deleted' => true
			],
			[
				'fs_id' => $flexiSkin->getId()
			],
			__METHOD__
		);
		return true;
	}

	/**
	 * @param string $name
	 * @param array $config
	 * @return int $id
	 */
	private function addFlexiSkin( $name, $config = [] ) {
		if ( ( $name === null ) || $name === '' ) {
			return false;
		}

		$res = $this->dbw->insert(
			'flexiskin',
			[
				'fs_name' => $name,
				'fs_config' => empty( $config ) ? '' : json_encode( $config )
			],
			__METHOD__
		);

		$newRes = $this->dbr->selectRow( 'flexiskin', 'fs_id', [ 'fs_name' => $name ] );
		$id = $newRes->fs_id;

	return $id;
	}

	/**
	 * @param int $id
	 * @param string $name
	 * @param array $config
	 * @return int $id
	 */
	private function updateFlexiSkin( $id, $name, $config ) {
		$args = [
				'fs_name' => $name,
				'fs_config' => empty( $config ) ? '' : json_encode( $config )
			];

		$res = $this->dbw->update(
			'flexiskin',
			$args,
			[
				'fs_id' => $id
			],
			__METHOD__
		);

		return $id;
	}

	/**
	 * @return IFlexiSkin[]
	 */
	public function getList(): array {
		$res = $this->dbr->select(
			'flexiskin',
			'*',
			[
				'fs_deleted' => false
			],
			__METHOD__,
			[
				'ORDER BY' => 'fs_name'
			]
		);

		$skins = [];
		foreach ( $res as $row ) {
			$skins[] = new FlexiSkin(
				$row->fs_id,
				$row->fs_name,
				json_decode( $row->fs_config, true ),
				$row->fs_active
			);
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
		$newFlexiSkin = new FlexiSkin( $id, $name, $config, false );

		return $newFlexiSkin;
	}

	/**
	 * @param string $name
	 * @return int
	 */
	private function getFlexiSkinIdOrNull( $name ) {
		$row = $this->dbr->selectRow( 'flexiskin', 'fs_id', [ 'fs_name' => $name ] );
		if ( $row === false ) {
			return null;
		}
		return $row->fs_id;
	}

}
