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
	 * @param sting $bodyClass
	 * @param StdClass $allDataFromDB
	 * @return string The CSS
	 */
	public function generateCSS( $bodyClass, $allDataFromDB );
}
