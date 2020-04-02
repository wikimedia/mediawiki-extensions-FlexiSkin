<?php

namespace MediaWiki\Extension\FlexiSkin\Api;

use ApiBase;
use MediaWiki\MediaWikiServices;

class DeleteFlexiSkin extends FlexiSkinApiBase {
	protected $flexiSkinId = null;

	public function execute() {
		$this->delete();
		$this->returnParams();
	}

	/**
	 *
	 * @return array
	 */
	protected function getAllowedParams() {
		return [
			'id' => [
				ApiBase::PARAM_TYPE => 'string',
				ApiBase::PARAM_REQUIRED => false,
				ApiBase::PARAM_HELP_MSG => 'apihelp-flexiskin-delete-param-id',
			]
		];
	}

	/**
	 * @param string $paramName Parameter name
	 * @param array|mixed $paramSettings Default value or an array of settings
	 *  using PARAM_* constants.
	 * @param bool $parseLimit Whether to parse and validate 'limit' parameters
	 * @return mixed Parameter value
	 */
	protected function getParameterFromSettings( $paramName, $paramSettings, $parseLimit ) {
		$value = parent::getParameterFromSettings( $paramName, $paramSettings, $parseLimit );

		return $value;
	}

	protected $result;

	/**
	 *
	 */
	protected function returnParams() {
		$result = $this->getResult();
		$result->addValue( null, 'id', $this->flexiSkinId );
	}

	/**
	 *
	 */
	private function delete() {
		$user = $this->getUser();
		$userHasRight = MediaWikiServices::getInstance()->getPermissionManager()
			->userHasRight( $user, 'flexiskin-api' );

		if ( !$userHasRight ) {
			return false;
		}

		$this->flexiSkinId = $this->getParameter( 'id' );
		$flexiSkin = $this->flexiSkinManager->loadFromId( $this->flexiSkinId );
		$flexiSkinDeleted = $this->flexiSkinManager->delete( $flexiSkin );
	}
}
