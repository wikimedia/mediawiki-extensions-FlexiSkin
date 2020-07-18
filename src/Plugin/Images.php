<?php

namespace MediaWiki\Extension\FlexiSkin\Plugin;

use MediaWiki\Extension\FlexiSkin\PluginBase;

class Images extends PluginBase {

	/**
	 * @return array with js files
	 */
	public function getJSFiles() {
		return [
			'js/ui/plugin/Images.js'
		];
	}

	/**
	 * @return array with css files
	 */
	public function getCSSFiles() {
		return [
			'all' => [
				'stylesheets/plugin/Images.css'
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
		return 'flexiskin.ui.plugin.Images';
	}
}
