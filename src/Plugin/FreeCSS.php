<?php

namespace MediaWiki\Extension\FlexiSkin\Plugin;

use MediaWiki\Extension\FlexiSkin\PluginBase;

class FreeCSS extends PluginBase {
	/**
	 * @return array with js files
	 */
	public function getJSFiles() {
		return [
			__DIR__ . '/../../resources/js/ui/plugin/FreeCSS.js'
		];
	}

	/**
	 * @return array with css files
	 */
	public function getCSSFiles() {
		return [
			'all' => [
				'stylesheets/plugin/FreeCSS.css'
			]
		];
	}

	/**
	 * @return string Name of the module
	 */
	public function getPluginName() {
		return 'flexiskin.ui.plugin.FreeCSS';
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
	 *
	 * @param sting $bodyClass
	 * @param array $allDataFromDB
	 * @return string The CSS
	 */
	public function generateCSS( $bodyClass, $allDataFromDB ) {
		$config = $allDataFromDB;

		if ( array_key_exists( 'FreeCSS', $config ) ) {
			if ( $config['FreeCSS'] !== null ) {
				return $config['FreeCSS'];
			}
		}

		return '';
	}
}
