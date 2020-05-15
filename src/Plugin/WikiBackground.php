<?php

namespace MediaWiki\Extension\FlexiSkin\Plugin;

use BlueSpice\Services;
use BlueSpice\TemplateParser;
use MediaWiki\Extension\FlexiSkin\PluginBase;
use MediaWiki\MediaWikiServices;

class WikiBackground extends PluginBase {
	/**
	 * @return array with js files
	 */
	public function getJSFiles() {
		return [
			__DIR__ . '/../../resources/js/ui/plugin/WikiBackground.js'
		];
	}

	/**
	 * @return array with css files
	 */
	public function getCSSFiles() {
		return [
			'all' => [
				'stylesheets/plugin/WikiBackground.css'
			]
		];
	}

	/**
	 * @return string Name of the module
	 */
	public function getPluginName() {
		return 'flexiskin.ui.plugin.WikiBackground';
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

		if ( array_key_exists( 'WikiBackground', $config ) ) {
			$args = array_merge( $config['WikiBackground'], [ 'bodyClass' => $bodyClass ] );

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
			'wikibackground',
			$params
		);
	}
}
