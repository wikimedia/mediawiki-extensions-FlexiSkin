<?php

namespace MediaWiki\Extension\FlexiSkin;

use JsonSerializable;

interface IFlexiSkin extends JsonSerializable {

	/**
	 * @return int|null
	 */
	public function getId();

	/**
	 * @return string|null
	 */
	public function getName();

	/**
	 * @return array|null
	 */
	public function getConfig();

	/**
	 * @return bool
	 */
	public function isActive();

	/**
	 * @return bool
	 */
	public function isDeleted();
}
