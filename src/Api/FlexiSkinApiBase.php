<?php

namespace MediaWiki\Extension\FlexiSkin\Api;

use ApiBase;
use ApiMain;
use MediaWiki\Extension\FlexiSkin\IFlexiSkinManager;
use MediaWiki\MediaWikiServices;

abstract class FlexiSkinApiBase extends ApiBase {
	/**
	 * @var IFlexiSkinManager
	 */
	protected $flexiSkinManager = null;

	/**
	 * @param ApiMain $mainModule
	 * @param string $moduleName Name of this module
	 * @param string $modulePrefix Prefix to use for parameter names
	 * @param IFlexiSkinManager|null $flexiSkinManager Prefix to use for parameter names
	 */
	public function __construct( ApiMain $mainModule,
		$moduleName, $modulePrefix = '', $flexiSkinManager = null ) {
		parent::__construct( $mainModule, $moduleName, $modulePrefix );

		$this->flexiSkinManager = $flexiSkinManager;
		if ( $flexiSkinManager === null ) {
			$this->flexiSkinManager = MediaWikiServices::getInstance()->get( 'FlexiSkinManager' );
		}
	}
}
