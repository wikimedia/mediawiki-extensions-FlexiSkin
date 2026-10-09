<?php

namespace MediaWiki\Extension\FlexiSkin\Api;

use MediaWiki\Api\ApiUsageException;
use MediaWiki\Extension\FlexiSkin\FlexiSkin;

abstract class FlexiSkinOperation extends FlexiSkinApiBase {

	/**
	 * @throws ApiUsageException
	 */
	public function execute() {
		$this->checkPermissions();
		$result = $this->executeAction();
		$this->returnResult( $result );
	}

	/**
	 * @param bool $value
	 */
	protected function returnResult( $value ) {
		$result = $this->getResult();
		$result->addValue( null, 'success', (int)$value );
	}

	/**
	 * @return bool
	 * @throws ApiUsageException
	 */
	protected function executeAction() {
		$flexiSkin = $this->getLocalSkin();
		return $this->executeOperationOnSkin( $flexiSkin );
	}

	/**
	 * @return FlexiSkin
	 * @throws ApiUsageException
	 */
	protected function getLocalSkin() {
		$skin = $this->flexiSkinManager->getLocalSkin();
		if ( !$skin ) {
			if ( $this->mustExist() ) {
				$this->dieWithError( 'Skin must exist!' );
			}
			return $this->flexiSkinManager->createNew();
		}
		return $skin;
	}

	/**
	 * Get a new skin with different config
	 *
	 * @param FlexiSkin $skin
	 * @param array $config
	 * @return FlexiSkin
	 */
	protected function replaceConfig( FlexiSkin $skin, array $config ) {
		return new FlexiSkin(
			$skin->getId(),
			$config,
			$skin->isActive()
		);
	}

	/**
	 * @param FlexiSkin $flexiSkin
	 * @return bool Operation success
	 */
	abstract protected function executeOperationOnSkin( FlexiSkin $flexiSkin );

	/**
	 * @return bool
	 */
	abstract protected function mustExist(): bool;
}
