<?php

namespace MediaWiki\Extension\FlexiSkin\Plugin;

use BlueSpice\Services;
use BlueSpice\TemplateParser;
use MediaWiki\Extension\FlexiSkin\PluginBase;
use MediaWiki\MediaWikiServices;

class Colors extends PluginBase {
	/**
	 * @return array with js files
	 */
	public function getJSFiles() {
		return [
			__DIR__ . '/../../resources/js/ui/plugin/Colors.js'
		];
	}

	/**
	 * @return array with css files
	 */
	public function getCSSFiles() {
		return [
			'all' => [
				'stylesheets/plugin/Colors.css'
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
	 *
	 * @param sting $bodyClass
	 * @param array $allDataFromDB
	 * @return string The CSS
	 */
	public function generateCSS( $bodyClass, $allDataFromDB ) {
		$config = $allDataFromDB;

		if ( array_key_exists( 'Colors', $config ) ) {
			$args = array_merge( $config['Colors'], [ 'bodyClass' => $bodyClass ] );

			return $this->parseFlexiSkinCss( $args );
		}

		return '';
	}

	/**
	 *
	 * @param \BlueSpice\TemplateParser $templateParser
	 * @param array $params
	 * @return string
	 */
	private function parseFlexiSkinCss( $params = [] ) {
		$config = MediaWikiServices::getInstance()->getMainConfig();
		$templateParser = new TemplateParser( $config->get( 'FlexiSkinThemePath' ) );

		return $templateParser->processTemplate(
			'colors',
			$params
		);
	}
}
