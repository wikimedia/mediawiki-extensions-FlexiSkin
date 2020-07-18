<?php

namespace MediaWiki\Extension\FlexiSkin;

use MediaWiki\MediaWikiServices;
use MWStake\MediaWiki\Component\CommonUserInterface\LessVars;

class Extension {
	public static function overrideLessVars() {
		/** @var IFlexiSkinManager $flexiSkinManager */
		$flexiSkinManager = MediaWikiServices::getInstance()->get( 'FlexiSkinManager' );
		$active = $flexiSkinManager->getActive();
		if ( !$active instanceof IFlexiSkin ) {
			return;
		}

		$vars = [];
		foreach ( $flexiSkinManager->getPlugins() as $pluginKey => $plugin ) {
			$vars = array_merge( $vars, $plugin->getLessVars( $active->getConfig() ) );
		}

		$lessVars = LessVars::getInstance();
		foreach ( $vars as $var => $value ) {
			$lessVars->setVar( $var, $value );
		}
	}
}
