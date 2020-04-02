<?php

namespace MediaWiki\Extension\FlexiSkin\Hook\BeforePageDisplay;

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
		$out->addModuleStyles( 'ext.flexiskin.styles' );

		return true;
	}

}
