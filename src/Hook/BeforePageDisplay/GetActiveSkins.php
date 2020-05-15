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

		if ( $activeId !== null ) {
			$out->addJsConfigVars( 'flexiskinId', $activeId );
		}

		$flexiSkins = $flexiSkinManager->getList( true );

		$jsVar = [];
		foreach ( $flexiSkins as $flexiSkin ) {
			$id = $flexiSkin->getId();
			if ( $id === null || $id === 0 ) {
				continue;
			}
			$jsVar[$id] = [
				'id' => $id,
				'name' => $flexiSkin->getName(),
				'config' => $flexiSkin->getConfig(),
				'deleted' => $flexiSkin->isDeleted()
			];
		}
		$out->addJsConfigVars( 'flexiskins', $jsVar );

		return true;
	}
}
