<?php

namespace MediaWiki\Extension\FlexiSkin\Api;

use MediaWiki\Extension\FlexiSkin\IFlexiSkin;

class ActivateFlexiSkin extends FlexiSkinOperation {
	protected function executeOperationOnSkin( IFlexiSkin $flexiSkin ) {
		return $this->flexiSkinManager->setActive( $flexiSkin );
	}
}
