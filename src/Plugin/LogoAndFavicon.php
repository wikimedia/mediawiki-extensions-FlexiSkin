<?php

namespace MediaWiki\Extension\FlexiSkin\Plugin;

use MediaWiki\Extension\FlexiSkin\PluginBase;

class LogoAndFavicon extends PluginBase {
	/**
	 * @return array with js files
	 */
	public function getJSFiles() {
		return [
			__DIR__ . '/../../resources/js/ui/plugin/LogoAndFavicon.js'
		];
	}

	/**
	 * @return array with css files
	 */
	public function getCSSFiles() {
		return [
			'all' => [
				'stylesheets/plugin/LogoAndFavicon.css'
			]
		];
	}

	/**
	 * @return array valid skinnames
	 */
	public function getValidSkins() {
		return [
			'*'
		];
	}

	/**
	 * @return string Name of the module
	 */
	public function getPluginName() {
		return 'flexiskin.ui.plugin.LogoAndFavicon';
	}

	/**
	 *
	 * @param sting $bodyClass
	 * @param StdClass $allDataFromDB
	 * @return string The CSS
	 */
	public function generateCSS( $bodyClass, $allDataFromDB ) {
		return '';
	}
}
