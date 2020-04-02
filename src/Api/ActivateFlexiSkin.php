<?php

namespace MediaWiki\Extension\FlexiSkin\Api;

use ApiBase;
use MediaWiki\MediaWikiServices;

class ActivateFlexiSkin extends FlexiSkinApiBase {

	protected $idSetActive = null;

	public function execute() {
		$this->setFlexiSkinActive();
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
				ApiBase::PARAM_REQUIRED => true,
				ApiBase::PARAM_HELP_MSG => 'apihelp-flexiskin-activate-param-id',
			]
		];
	}

	/**
		*
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
		$result->addValue( null, 'id', $this->idSetActive );
	}

	/**
	 *
	 * @return bool
	 */
	private function setFlexiSkinActive() {
		$user = $this->getUser();
		$userHasRight = MediaWikiServices::getInstance()->getPermissionManager()->userHasRight(
				$user,
				'flexiskin-api'
			);

		if ( !$userHasRight ) {
			return false;
		}
		$this->idToSetActive = $this->getParameter( 'id' );

		$flexiskin = $this->flexiSkinManager->loadFromId( $this->idToSetActive );
		if ( $this->idToSetActive !== null ) {
			$this->flexiSkinManager->setActive( $flexiskin );
			return true;
		}

		return false;
	}
}
