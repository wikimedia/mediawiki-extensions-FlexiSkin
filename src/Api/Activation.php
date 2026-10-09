<?php

namespace MediaWiki\Extension\FlexiSkin\Api;

use MediaWiki\Api\ApiUsageException;
use MediaWiki\Extension\FlexiSkin\FlexiSkin;
use Wikimedia\ParamValidator\ParamValidator;

class Activation extends FlexiSkinOperation {
	/** @inheritDoc */
	protected function getAllowedParams() {
		return parent::getAllowedParams() + [
			'active' => [
				ParamValidator::PARAM_TYPE => 'integer',
				ParamValidator::PARAM_REQUIRED => false,
				static::PARAM_HELP_MSG => 'apihelp-flexiskin-activation-param-active',
				ParamValidator::PARAM_DEFAULT => 1
			]
		];
	}

	/**
	 * @param FlexiSkin $flexiSkin
	 * @return bool
	 * @throws ApiUsageException
	 */
	protected function executeOperationOnSkin( FlexiSkin $flexiSkin ) {
		$active = (bool)$this->getParameter( 'active' );
		return $this->flexiSkinManager->setActive( $flexiSkin, $active );
	}

	/**
	 * @return bool
	 */
	protected function mustExist(): bool {
		return true;
	}
}
