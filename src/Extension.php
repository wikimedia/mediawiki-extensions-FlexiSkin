<?php

namespace MediaWiki\Extension\FlexiSkin;

use MediaWiki\MediaWikiServices;
use RequestContext;

class Extension {

	/**
	 * @param User $user
	 * @return void
	 */
	public static function onUserLoadAfterLoadFromSession( $user ) {
		if ( MW_ENTRY_POINT !== 'load' && MW_ENTRY_POINT !== 'index' ) {
			return;
		}
		$skinname = RequestContext::getMain()->getSkin()->getSkinName();
		static::loadFlexiSkin( $skinname );
	}

	/**
	 *
	 * @param string $skinname
	 * @return void
	 */
	private static function loadFlexiSkin( $skinname ) {
		/** @var IFlexiSkinManager $flexiSkinManager */
		$flexiSkinManager = MediaWikiServices::getInstance()->get( 'FlexiSkinManager' );
		$active = $flexiSkinManager->getActive( $skinname );
		if ( !$active instanceof IFlexiSkin ) {
			return;
		}
		$config = $flexiSkinManager->getActiveConfig( $skinname );

		static::setLogo( $config );
		static::setFavicon( $config );
		static::applyFreeCSS( $config );

		$hookContainer = MediaWikiServices::getInstance()->getHookContainer();
		$hookContainer->run( 'FlexiSkinAfterLoad', [ $active ] );
	}

	/**
	 * @param array $config
	 */
	protected static function setFavicon( array $config ) {
		$url = $config['images']['favicon']['url'] ?? null;
		if ( $url ) {
			$GLOBALS['wgFavicon'] = $url;
		}
	}

	/**
	 * @param array $config
	 *
	 * @return void
	 */
	protected static function setLogo( array $config ) {
		$url = $config['images']['logo']['url'] ?? null;
		if ( $url ) {
			$GLOBALS['wgLogos'] = [ '1x' => $url ];
		}
	}

	/**
	 * Apply the CSS styles
	 *
	 * @param array $config
	 */
	protected static function applyFreeCSS( $config ) {
		if ( !isset( $config['free_css']['css'] ) ) {
			return;
		}

		$GLOBALS['wgHooks']['BeforePageDisplay'][] = static function ( \OutputPage $out, \Skin $skin ) use ( $config ) {
			$style = $config['free_css']['css'];
			$style = str_replace( "\n", '', $style );
			$style = preg_replace( '/\s/', ' ', $style );
			if ( empty( $style ) ) {
				return true;
			}

			$out->addInlineStyle( $style );

			return true;
		};
	}
}
