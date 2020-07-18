<?php

namespace MediaWiki\Extension\FlexiSkin\Api;

use ApiBase;
use ApiUsageException;
use MediaWiki\Extension\FlexiSkin\IFlexiSkin;

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
	 *
	 * @return bool
	 * @throws ApiUsageException
	 */
	protected function executeAction() {
		$flexiSkin = $this->getFlexiSkin();
		return $this->executeOperationOnSkin( $flexiSkin );
	}

	/**
	 * @return IFlexiSkin
	 * @throws ApiUsageException
	 */
	protected function getFlexiSkin() {
		$skin = $this->flexiSkinManager->getFlexiSkin();
		if ( $skin === null ) {
			if ( $this->mustExist() ) {
				$this->dieWithError( 'Skin must exist!' );
			}
			return $this->flexiSkinManager->create( 'default', [] );
		}

		return $skin;
	}

	/**
	 * @param IFlexiSkin $flexiSkin
	 * @return bool Operation success
	 */
	abstract protected function executeOperationOnSkin( IFlexiSkin $flexiSkin );


	/**
	 * @return bool
	 */
	abstract protected function mustExist(): bool;
}
