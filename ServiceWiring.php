<?php

use MediaWiki\Extension\FlexiSkin\FlexiSkinManager;
use MediaWiki\Logger\LoggerFactory;
use MediaWiki\MediaWikiServices;

return [
	'FlexiSkinManager' => static function ( MediaWikiServices $services ) {
		return new FlexiSkinManager(
			$services->getDBLoadBalancer(),
			$services->getHookContainer(),
			LoggerFactory::getInstance( 'FlexiSkin' )
		);
	}
];
