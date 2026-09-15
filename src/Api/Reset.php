<?php

namespace MediaWiki\Extension\FlexiSkin\Api;

use MediaWiki\Extension\FlexiSkin\IFlexiSkin;

/**
 * Discards the configuration of this wiki, so that its styling falls back to the inherited
 * styling, if it inherits any, or to the BlueSpice default styling otherwise.
 */
class Reset extends FlexiSkinOperation {

	/** @inheritDoc */
	public function mustBePosted() {
		return true;
	}

	/** @inheritDoc */
	public function needsToken() {
		return 'csrf';
	}

	/**
	 * @param IFlexiSkin $flexiSkin
	 * @return bool
	 */
	protected function executeOperationOnSkin( IFlexiSkin $flexiSkin ) {
		return $this->flexiSkinManager->delete( $flexiSkin->getName() );
	}

	/**
	 * @return bool
	 */
	protected function mustExist(): bool {
		return false;
	}
}
