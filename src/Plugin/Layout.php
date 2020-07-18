<?php

namespace MediaWiki\Extension\FlexiSkin\Plugin;

use MediaWiki\Extension\FlexiSkin\PluginBase;

class Layout extends PluginBase {
	/**
	 * @return array with js files
	 */
	public function getJSFiles() {
		return [
			'js/ui/plugin/Layout.js'
		];
	}

	/**
	 * @return array with css files
	 */
	public function getCSSFiles() {
		return [
			'all' => []
		];
	}

	/**
	 * @return string Name of the module
	 */
	public function getPluginName() {
		return 'flexiskin.ui.plugin.Layout';
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
	 * @inheritDoc
	 */
	public function getLessVars( $config ): array {
		$vars = [
			'content-width' => $config['layout']['content_width'] ?: null,
		];

		return array_filter( $vars, function ( $item ) {
			return $item !== null;
		} );
	}
}
