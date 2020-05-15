<?php

namespace MediaWiki\Extension\FlexiSkin\Hook\BeforePageDisplay;

use MediaWiki\Extension\FlexiSkin\IFlexiSkinManager;
use MediaWiki\MediaWikiServices;
use OutputPage;
use Skin;

class GetActiveSkins {
	/**
	 *
	 * @param OutputPage $out
	 * @param Skin $skin
	 * @return bool
	 */
	public static function callback( OutputPage $out, Skin $skin ) {
		/** @var IFlexiSkinManager $flexiSkinManager */
		$flexiSkinManager = MediaWikiServices::getInstance()->get( 'FlexiSkinManager' );
		$activeId = $flexiSkinManager->getActive();

		if ( $activeId === null ) {
			return true;
		}

		$out->addJsConfigVars( 'flexiskinId', $activeId );

		$flexiSkins = $flexiSkinManager->getList();

		$jsVar = [];
		foreach ( $flexiSkins as $flexiSkin ) {
			$id = $flexiSkin->getId();
			$jsVar[] = [
				'id' => $id,
				'name' => $flexiSkin->getName(),
				'config' => $flexiSkin->getConfig()
			];
		}
		$out->addJsConfigVars( 'flexiskins', $jsVar );

		return true;
	}
}
