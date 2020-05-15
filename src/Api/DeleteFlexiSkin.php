<?php

namespace MediaWiki\Extension\FlexiSkin\Api;

use MediaWiki\Extension\FlexiSkin\IFlexiSkin;

class DeleteFlexiSkin extends FlexiSkinOperation {
	protected function executeOperationOnSkin( IFlexiSkin $flexiSkin ) {
		return $this->flexiSkinManager->delete( $flexiSkin );
	}
}
