<?php

namespace MediaWiki\Extension\FlexiSkin\Api;

use ApiBase;
use ApiUsageException;
use FormatJson;
use MediaWiki\Extension\FlexiSkin\FlexiSkin;
use MediaWiki\Extension\FlexiSkin\IFlexiSkin;
use MediaWiki\MediaWikiServices;

class SaveFlexiSkin extends FlexiSkinOperation {
	/**
	 * @inheritDoc
	 */
	protected function getAllowedParams() {
		return parent::getAllowedParams() + [
			'config' => [
				ApiBase::PARAM_TYPE => 'string',
				ApiBase::PARAM_REQUIRED => false,
				ApiBase::PARAM_HELP_MSG => 'apihelp-flexiskin-save-param-config',
			]
		];
	}

	protected function getParameterFromSettings( $name, $settings, $parseLimit ) {
		$value = parent::getParameterFromSettings( $name, $settings, $parseLimit );
		if ( $name === 'config' ) {
			$value = FormatJson::decode( $value, true );
			if ( !is_array( $value ) ) {
				$value = [];
			}
		}

		return $value;
	}

	/**
	 * @return bool
	 * @throws ApiUsageException
	 */
	protected function executeAction() {
		$flexiSkin = $this->getFlexiSkin();
		$newSkin = new FlexiSkin( $flexiSkin->getId(), $flexiSkin->getName(), $this->getParameter( 'config' ) );
		return $this->executeOperationOnSkin( $newSkin );
	}

	/**
	 * @param IFlexiSkin $flexiSkin
	 * @return bool
	 */
	protected function executeOperationOnSkin( IFlexiSkin $flexiSkin ) {
		return $this->flexiSkinManager->save( $flexiSkin ) > 0;
	}
}
