<?php

namespace MediaWiki\Extension\FlexiSkin;

class FlexiSkin implements IFlexiSkin {
	/**
	 * @var string|null
	 */
	protected $id = null;

	/**
	 * @var bool|false
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
	 * @param string $id
	 * @param string $name
	 * @param string $config
	 * @param bool $active
	 */
	public function __construct( $id, $name, $config, $active ) {
		$this->id = $id;
		$this->name = $name;
		$this->config = $config;
		$this->active = $active;
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
}
