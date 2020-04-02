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
		$activeId = $flexiSkinManager->getActive();

		if ( $activeId !== false ) {
			$bodyAttrs[ 'class' ] .= ' fs-' . $activeId . ' ';
		}

		return true;
	}

}
