<?php

namespace MediaWiki\Extension\FlexiSkin\Api;

use MediaWiki\Extension\FlexiSkin\FlexiSkin;

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
	 * @param FlexiSkin $flexiSkin
	 * @return bool
	 */
	protected function executeOperationOnSkin( FlexiSkin $flexiSkin ) {
		return $this->flexiSkinManager->delete();
	}

	/**
	 * @return bool
	 */
	protected function mustExist(): bool {
		return false;
	}
}
