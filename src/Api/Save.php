<?php

namespace MediaWiki\Extension\FlexiSkin\Api;

use ApiUsageException;
use FormatJson;
use MediaWiki\Extension\FlexiSkin\FlexiSkin;
use MediaWiki\Extension\FlexiSkin\IFlexiSkin;

class Save extends FlexiSkinOperation {
	/**
	 * @inheritDoc
	 */
	protected function getAllowedParams() {
		return parent::getAllowedParams() + [
			'config' => [
				static::PARAM_TYPE => 'string',
				static::PARAM_REQUIRED => false,
				static::PARAM_HELP_MSG => 'apihelp-flexiskin-save-param-config',
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
		$newSkin = new FlexiSkin(
			$flexiSkin->getId(),
			$flexiSkin->getName(),
			$this->getParameter( 'config' )
		);
		return $this->executeOperationOnSkin( $newSkin );
	}

	/**
	 * @param IFlexiSkin $flexiSkin
	 * @return bool
	 */
	protected function executeOperationOnSkin( IFlexiSkin $flexiSkin ) {
		return $this->flexiSkinManager->save( $flexiSkin ) &&
			$this->flexiSkinManager->setActive( true );
	}

	/**
	 * @return bool
	 */
	protected function mustExist(): bool {
		return false;
	}
}
