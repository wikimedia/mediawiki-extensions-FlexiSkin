<?php

namespace MediaWiki\Extension\FlexiSkin;

interface IFlexiSkin {

	/**
	 * @return int
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
}
