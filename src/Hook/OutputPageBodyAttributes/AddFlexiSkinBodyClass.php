<?php

namespace MediaWiki\Extension\FlexiSkin\Hook\OutputPageBodyAttributes;

use MediaWiki\MediaWikiServices;
use OutputPage;
use Skin;

class AddFlexiSkinBodyClass {
	/**
	 *
	 * @param OutputPage $out
	 * @param Skin $skin
	 * @param array &$bodyAttrs
	 * @return bool
	 */
	public static function callback( OutputPage $out, Skin $skin, &$bodyAttrs ) {
		$flexiSkinManager = MediaWikiServices::getInstance()->get( 'FlexiSkinManager' );
		$active = $flexiSkinManager->getActive();

		if ( $active ) {
			$bodyAttrs[ 'class' ] .= ' fs-' . $active->getId() . ' ';
		}

		return true;
	}

}
