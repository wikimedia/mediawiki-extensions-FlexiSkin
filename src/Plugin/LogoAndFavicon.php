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
	 * @param array $allDataFromDB
	 * @return string The CSS
	 */
	public function generateCSS( $bodyClass, $allDataFromDB ) {
		if ( array_key_exists( 'LogoAndFavicon', $allDataFromDB ) ) {
			$logo = $allDataFromDB['LogoAndFavicon']['logo'];
			if ( $logo['data'] !== '' ) {
			}
		}


		return '';
	}
}
