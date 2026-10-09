<?php

namespace MediaWiki\Extension\FlexiSkin\HookHandler;

use MediaWiki\Config\Config;
use MediaWiki\Config\MultiConfig;
use MediaWiki\Extension\FlexiSkin\FlexiSkinConfig;
use MediaWiki\Extension\FlexiSkin\FlexiSkinManager;
use MediaWiki\Hook\MediaWikiServicesHook;
use MediaWiki\MediaWikiServices;
use MediaWiki\Output\Hook\BeforePageDisplayHook;

class Main implements MediaWikiServicesHook, BeforePageDisplayHook {

	/**
	 * @inheritDoc
	 */
	public function onMediaWikiServices( $container ) {
		if ( defined( 'MW_QUIBBLE_CI' ) ) {
			return;
		}

		$container->addServiceManipulator(
			'MainConfig',
			static function ( Config $mainConfig ): Config {
				return new MultiConfig( [
					new FlexiSkinConfig(),
					$mainConfig
				] );
			}
		);
	}

	/**
	 * @inheritDoc
	 */
	public function onBeforePageDisplay( $out, $skin ): void {
		/** @var FlexiSkinManager $manager */
		$manager = MediaWikiServices::getInstance()->getService( 'FlexiSkinManager' );
		$skin = $manager->getActive();
		if ( !$skin ) {
			return;
		}
		$style = $skin->getValueForPath( 'free_css/css' );
		if ( !$style ) {
			return;
		}
		$style = str_replace( "\n", '', $style );
		$style = preg_replace( '/\s/', ' ', $style );
		if ( empty( $style ) ) {
			return;
		}

		$out->addInlineStyle( $style );
	}
}
