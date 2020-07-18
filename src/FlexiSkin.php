<?php

namespace MediaWiki\Extension\FlexiSkin;

use MWException;

class FlexiSkin implements IFlexiSkin {
	/**
	 * @var int|null
	 */
	protected $id = null;

	/**
	 * @var bool
	 */
	protected $active = false;

	/**
	 * @var string|null
	 */
	protected $name = null;

	/**
	 * @var string|null
	 */
	protected $config = [];

	/**
	 * @param int|null $id
	 * @param string $name
	 * @param array $config
	 * @param bool|null $active
	 */
	public function __construct( ?int $id, $name, $config, $active = false ) {
		$this->id = $id;
		$this->name = $name;
		$this->config = $config;
		$this->active = $active;
	}

	/**
	 * @param array $data
	 * @return static|null
	 * @throws MWException
	 */
	public static function newFromData( $data ) {
		if ( !isset( $data['name'] ) || !isset( $data['config'] ) ) {
			throw new MWException( __METHOD__ . ': Invalid data passed' );
		}

		return new static(
			(int) $data['id'],
			$data['name'],
			$data['config'],
			$data['active'] ?? false
		);
	}

	/**
	 * @return int|null
	 */
	public function getId() : ?int {
		return $this->id;
	}

	/**
	 * @return string|null
	 */
	public function getName() : ?string {
		return $this->name;
	}

	/**
	 * @return array|null
	 */
	public function getConfig() : ?array {
		return $this->config;
	}

	/**
	 * @return bool
	 */
	public function isActive() : bool {
		return $this->active;
	}

	/**
	 * @return array
	 */
	public function jsonSerialize() {
		return [
			'id' => $this->getId(),
			'name' => $this->getName(),
			'config' => $this->getConfig(),
			'active' => $this->isActive()
		];
	}
}
