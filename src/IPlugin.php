<?php

namespace MediaWiki\Extension\FlexiSkin;

interface IPlugin {

	/**
	 * return array with js files
	 */
	public function getJSFiles();

	 /**
	  * return array with css files
	  */
	public function getCSSFiles();

	/**
	 * @return string Name of the plugin
	 */
	public function getPluginName();

	/**
	 * @return array valid skinnames
	 */
	public function getValidSkins();

	/**
	 * @param array $config
	 * @return array
	 */
	public function getLessVars( $config ): array;

	/**
	 * Adapt configuration values before loading skin config
	 *
	 * @param array &$config
	 * @return void
	 */
	public function adaptConfiguration( &$config );
}
