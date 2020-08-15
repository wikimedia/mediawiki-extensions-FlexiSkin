<?php

namespace MediaWiki\Extension\FlexiSkin\Hook\BeforePageDisplay;

use MediaWiki\Extension\FlexiSkin\IFlexiSkin;
use MediaWiki\Extension\FlexiSkin\IFlexiSkinManager;
use MediaWiki\MediaWikiServices;
use OutputPage;
use Skin;

class AddActiveSkinStyles {
	/**
	 *
	 * @param OutputPage $out
	 * @param Skin $skin
	 * @return bool
	 */
	public static function callback( OutputPage $out, Skin $skin ) {
		// Unfortunatelly this cannot go in the plugin itself just yet
		/** @var IFlexiSkinManager $manager */
		$manager = MediaWikiServices::getInstance()->getService( 'FlexiSkinManager' );
		$active = $manager->getActive();
		if ( $active ) {
			static::setGlobalImages( $active, 'logo', 'wgLogo' );
			static::setGlobalImages( $active, 'favicon', 'wgFavicon' );
		}

		return true;
	}

	protected static function setGlobalImages( IFlexiSkin $skin, $skinVariable, $globalVariable ) {
		$url = $skin->getConfig()['images'][$skinVariable]['url'] ?? null;
		if ( $url ) {
			$GLOBALS[$globalVariable] = $url;
		}
	}
}
