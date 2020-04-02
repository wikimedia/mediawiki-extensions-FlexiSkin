<?php

namespace MediaWiki\Extension\FlexiSkin\Api;

use ApiBase;
use MediaWiki\MediaWikiServices;

class NewFlexiSkin extends FlexiSkinApiBase {
	protected $sourceId = null;
	protected $flexiSkinId = null;
	protected $flexiSkinName = null;
	protected $flexiSkinConfig = null;

	public function execute() {
		$this->getSourceId();

		if ( $this->sourceId !== null ) {
			$this->makeCopy();
		} else {
			$this->makeNew();
		}

		$this->returnParams();
	}

	/**
		*
		* @return array
		*/
	protected function getAllowedParams() {
		return [
			'source' => [
				ApiBase::PARAM_TYPE => 'string',
				ApiBase::PARAM_REQUIRED => false,
				ApiBase::PARAM_HELP_MSG => 'apihelp-flexiskin-save-param-id',
			],
			'name' => [
				ApiBase::PARAM_TYPE => 'string',
				ApiBase::PARAM_REQUIRED => true,
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
		$result->addValue( null, 'source', $this->sourceId );
	}

	/**
	 *
	 * @return bool
	 */
	private function getSourceId() {
		$this->sourceId = $this->getParameter( 'source' );
	}

	/**
	 *
	 */
	private function makeCopy() {
		$user = $this->getUser();
		$userHasRight = MediaWikiServices::getInstance()->getPermissionManager()->userHasRight(
				$user,
				'flexiskin-api'
			);

		if ( !$userHasRight ) {
			return false;
		}

		$sourceFlexiskin = $this->flexiSkinManager->loadFromId( $this->sourceId );

		$this->flexiSkinName = $this->getParameter( 'name' );
		$this->flexiSkinConfig = $sourceFlexiskin->getConfig();

		$newFlexiSkin = $this->flexiSkinManager->create( $this->flexiSkinName, $this->flexiSkinConfig );

		$this->flexiSkinId = $this->flexiSkinManager->save( $newFlexiSkin );
	}

	/**
	 *
	 */
	private function makeNew() {
		$user = $this->getUser();
		$userHasRight = MediaWikiServices::getInstance()->getPermissionManager()->userHasRight(
				$user,
				'flexiskin-api'
			);

		if ( !$userHasRight ) {
			return false;
		}

		$this->flexiSkinName = $this->getParameter( 'name' );
		$this->flexiSkinConfig = [];

		$flexiskin = $this->flexiSkinManager->create( $this->flexiSkinName, $this->flexiSkinConfig );

		$this->flexiSkinId = $this->flexiSkinManager->save( $flexiskin );
	}
}
