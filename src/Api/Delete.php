<?php

namespace MediaWiki\Extension\FlexiSkin\Api;

use MediaWiki\Api\ApiUsageException;
use MediaWiki\Extension\FlexiSkin\FlexiSkin;

class Delete extends FlexiSkinOperation {
	/**
	 * @return bool
	 * @throws ApiUsageException
	 */
	protected function executeAction() {
		return $this->executeOperationOnSkin( $this->getLocalSkin() );
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
		return true;
	}
}
