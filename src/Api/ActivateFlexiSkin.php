<?php

namespace MediaWiki\Extension\FlexiSkin\Api;

use ApiBase;
use MediaWiki\Extension\FlexiSkin\IFlexiSkin;
use MediaWiki\MediaWikiServices;

class ActivateFlexiSkin extends FlexiSkinOperation {
	protected function executeOperationOnSkin( IFlexiSkin $flexiSkin ) {
		return $this->flexiSkinManager->setActive( $flexiSkin );
	}
}
