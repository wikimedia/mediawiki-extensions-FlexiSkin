<?php

namespace MediaWiki\Extension\FlexiSkin\Api;

use MediaWiki\Api\ApiBase;
use MediaWiki\Api\ApiMain;
use MediaWiki\MediaWikiServices;

abstract class FlexiSkinApiBase extends ApiBase {
	/** @var FlexiSkinManager */
	protected $flexiSkinManager = null;

	/**
	 * @param ApiMain $mainModule
	 * @param string $moduleName Name of this module
	 * @param string $modulePrefix Prefix to use for parameter names
	 */
	public function __construct( ApiMain $mainModule, $moduleName, $modulePrefix = '' ) {
		parent::__construct( $mainModule, $moduleName, $modulePrefix );
		$this->flexiSkinManager = MediaWikiServices::getInstance()->getService( 'FlexiSkinManager' );
	}

	protected function checkPermissions() {
		$user = $this->getUser();
		$userHasRight = MediaWikiServices::getInstance()->getPermissionManager()->userHasRight(
			$user,
			'flexiskin-api'
		);

		if ( !$userHasRight ) {
			$this->dieWithError( 'permissiondenied', 'permissiondenied' );
		}
	}
}
