<?php

namespace MediaWiki\Extension\FlexiSkin\Api;

use ApiBase;
use MediaWiki\MediaWikiServices;

class SaveFlexiSkin extends FlexiSkinApiBase {
	protected $flexiSkinId = null;
	protected $flexiSkinName = null;
	protected $flexiSkinConfig = null;

	public function execute() {
		$this->saveFlexiSkinData();
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
				ApiBase::PARAM_HELP_MSG => 'apihelp-flexiskin-save-param-id',
			],
			'name' => [
				ApiBase::PARAM_TYPE => 'string',
				ApiBase::PARAM_REQUIRED => false,
				ApiBase::PARAM_HELP_MSG => 'apihelp-flexiskin-save-param-name',
			],
			'config' => [
				ApiBase::PARAM_TYPE => 'string',
				ApiBase::PARAM_REQUIRED => false,
				ApiBase::PARAM_HELP_MSG => 'apihelp-flexiskin-save-param-config',
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
		$result->addValue( null, 'id', $this->flexiSkinId );
		$result->addValue( null, 'name', $this->flexiSkinName );
		$result->addValue( null, 'config', $this->flexiSkinConfig );
	}

	/**
	 *
	 */
	private function saveFlexiSkinData() {
		$user = $this->getUser();
		$userHasRight = MediaWikiServices::getInstance()->getPermissionManager()->userHasRight(
				$user,
				'flexiskin-api'
			);

		if ( !$userHasRight ) {
			return false;
		}

		$this->flexiSkinId = $this->getParameter( 'id' );
		$this->flexiSkinName = $this->getParameter( 'name' );
		$this->flexiSkinConfig = json_decode( $this->getParameter( 'config' ), true );

		$flexiSkin = $this->flexiSkinManager->create( $this->flexiSkinName, $this->flexiSkinConfig );

		if ( $this->flexiSkinId !== $flexiSkin->getId() ) {
			// MUST NEVER HAPPEN
		}
		$this->flexiSkinId = $this->flexiSkinManager->save( $flexiSkin );
	}
}
