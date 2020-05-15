<?php

namespace MediaWiki\Extension\FlexiSkin\Api;

use MediaWiki\Extension\FlexiSkin\IFlexiSkin;

class RestoreFlexiSkin extends FlexiSkinOperation {
	protected function executeOperationOnSkin( IFlexiSkin $flexiSkin ) {
		return $this->flexiSkinManager->restore( $flexiSkin );
	}
}
