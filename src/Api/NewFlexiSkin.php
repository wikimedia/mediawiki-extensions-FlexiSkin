<?php

namespace MediaWiki\Extension\FlexiSkin\Api;

use ApiBase;
use ApiUsageException;
use Exception;
use FormatJson;
use MediaWiki\Extension\FlexiSkin\FlexiSkin;
use MediaWiki\Extension\FlexiSkin\IFlexiSkin;
use Wikimedia\ParamValidator\ParamValidator;

class NewFlexiSkin extends FlexiSkinApiBase {
	public function execute() {
		try {
			$skin = $this->makeSkin( $this->getSource() );
			$this->returnResult( $skin );
		} catch ( Exception $ex ) {
			$this->dieWithError( $ex->getMessage() );
		}
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
				ParamValidator::PARAM_DEFAULT => null
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
	 * @param IFlexiSkin|null $skin
	 */
	protected function returnResult( $skin ) {
		$result = $this->getResult();
		$result->addValue( null, 'skin', FormatJson::encode( $skin ) );
	}

	/**
	 *
	 * @return IFlexiSkin|null
	 */
	private function getSource() {
		$sourceId = $this->getParameter( 'source' );
		if ( $sourceId === null ) {
			return null;
		}

		$source = $this->flexiSkinManager->loadFromId( $sourceId );
		return $source instanceof IFlexiSkin ? $source : null;
	}

	/**
	 * @param IFlexiSkin|null $source
	 * @return IFlexiSkin|null
	 * @throws ApiUsageException
	 */
	private function makeSkin( $source = null ) {
		$this->checkPermissions();

		$name = $this->getParameter( 'name' );
		$config = [];
		if ( $source instanceof IFlexiSkin ) {
			$config = $source->getConfig();
		}

		$flexiSkin = $this->flexiSkinManager->create( $name, $config );
		if ( $flexiSkin->getId() ) {
			throw new Exception( 'Skin with this name already exists!' );
		}
		$id = $this->flexiSkinManager->save( $flexiSkin );
		if ( $id > 0 ) {
			return new FlexiSkin( $id, $flexiSkin->getName(), $flexiSkin->getConfig() );
		}

		return null;
	}
}
