<?php

use MediaWiki\Extension\FlexiSkin\DBFlexiSkinManager;
use MediaWiki\MediaWikiServices;

return [
	'FlexiSkinManager' => function () {
		$loadBalancer = MediaWikiServices::getInstance()->getDBLoadBalancer();

		$manager = new DBFlexiSkinManager( $loadBalancer );
		return $manager;
	}
];
