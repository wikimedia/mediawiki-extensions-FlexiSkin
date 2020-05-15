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
		*
		* @return array
		*/
	protected function getAllowedParams() {
		return [
			'id' => [
				ApiBase::PARAM_TYPE => 'string',
				ApiBase::PARAM_REQUIRED => true,
				ApiBase::PARAM_HELP_MSG => 'apihelp-flexiskin-preview-param-id',
			]
		];
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
		$id = $this->getParameter( 'id' );
		$flexiSkin = $this->flexiSkinManager->loadFromId( $id );
		if ( !$flexiSkin ) {
			$this->dieWithError( 'Invalid ID' );
		}
		return $flexiSkin;
	}

	/**
	 * @param IFlexiSkin $flexiSkin
	 * @return bool Operation success
	 */
	abstract protected  function executeOperationOnSkin( IFlexiSkin $flexiSkin );
}
