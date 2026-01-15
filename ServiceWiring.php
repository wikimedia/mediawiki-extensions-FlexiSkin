<?php

use MediaWiki\Extension\FlexiSkin\FlexiSkinManager;
use MediaWiki\MediaWikiServices;

return [
	'FlexiSkinManager' => static function ( MediaWikiServices $services ) {
		return new FlexiSkinManager(
			$services->getService( 'MWStake.StorageUtilities' )
		);
	}
];
