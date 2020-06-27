<?php

namespace MediaWiki\Extension\FlexiSkin;

use stdClass;

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

	/** @var bool */
	protected $deleted = false;

	/**
	 * Helper function
	 *
	 * @param stdClass $row
	 * @return static
	 */
	public static function newFromRow( $row ) {
		return new static(
			(int)$row->fs_id,
			$row->fs_name,
			json_decode( $row->fs_config, true ),
			(bool)$row->fs_active,
			(bool)$row->fs_deleted
		);
	}

	/**
	 * @param int|null $id
	 * @param string $name
	 * @param array $config
	 * @param bool|null $active
	 * @param bool|null $deleted
	 */
	public function __construct( ?int $id, $name, $config, $active = false, $deleted = false ) {
		$this->id = $id;
		$this->name = $name;
		$this->config = $config;
		$this->active = $active;
		$this->deleted = $deleted;
	}

	/**
	 * @return int|null
	 */
	public function getId() {
		return $this->id;
	}

	/**
	 * @return string|null
	 */
	public function getName() {
		return $this->name;
	}

	/**
	 * @return array|null
	 */
	public function getConfig() {
		return $this->config;
	}

	/**
	 * @return bool
	 */
	public function isActive() {
		return $this->active;
	}

	public function jsonSerialize() {
		return [
			'id' => $this->getId(),
			'name' => $this->getName(),
			'config' => $this->getConfig(),
		];
	}

	public function isDeleted() {
		return $this->deleted;
	}
}
