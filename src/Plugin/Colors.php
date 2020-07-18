<?php

namespace MediaWiki\Extension\FlexiSkin\Plugin;

use BlueSpice\TemplateParser;
use MediaWiki\Extension\FlexiSkin\PluginBase;
use MediaWiki\MediaWikiServices;

class Colors extends PluginBase {
	/**
	 * @return array with js files
	 */
	public function getJSFiles() {
		return [
			'js/ui/plugin/Colors.js'
		];
	}

	/**
	 * @return array with css files
	 */
	public function getCSSFiles() {
		return [
			'all' => [
				'stylesheets/plugin/Colors.css',
			]
		];
	}

	/**
	 * @return string Name of the module
	 */
	public function getPluginName() {
		return 'flexiskin.ui.plugin.Colors';
	}

	/**
	 * @return array valid skinnames
	 */
	public function getValidSkins() {
		return [
			'bluespicecalumma'
		];
	}

	/**
	 * @inheritDoc
	 */
	public function getLessVars( $config ): array {
		$vars = [
			'primary-bg' => $config['colors']['background']['primary'] ?: null,
			'secondary-bg' => $config['colors']['background']['secondary'] ?: null,
			'neutral-bg' => $config['colors']['background']['neutral'] ?: null,
			'primary-fg' => $config['colors']['foreground']['primary'] ?: null,
			'secondary-fg' => $config['colors']['foreground']['primary'] ?: null,
			'neutral-fg' => $config['colors']['foreground']['primary'] ?: null,
			'content-bg' => $config['colors']['background']['content'] ?: null,
			'content-fg' => $config['colors']['foreground']['content'] ?: null,
		];

		return array_filter( $vars, function ( $item ) {
			return $item !== null;
		} );
	}
}
