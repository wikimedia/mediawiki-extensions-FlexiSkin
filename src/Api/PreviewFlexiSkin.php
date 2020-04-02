<?php

namespace MediaWiki\Extension\FlexiSkin\Api;

use ApiBase;
use ExtensionRegistry;
use MediaWiki\Extension\FlexiSkin\IPlugin;
use MediaWiki\MediaWikiServices;

class PreviewFlexiSkin extends ApiBase {
	protected $previewCSS = '';

	public function execute() {
		$this->generatePreviewCSS();
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
				ApiBase::PARAM_HELP_MSG => 'apihelp-flexiskin-preview-param-id',
			],
			'config' => [
				ApiBase::PARAM_TYPE => 'string',
				ApiBase::PARAM_REQUIRED => true,
				ApiBase::PARAM_HELP_MSG => 'apihelp-flexiskin-preview-param-config',
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
		$result->addValue( null, 'preview', $this->previewCSS );
	}

	/**
	 *
	 * @return bool
	 */
	private function generatePreviewCSS() {
		$user = $this->getUser();
		$userHasRight = MediaWikiServices::getInstance()->getPermissionManager()->userHasRight(
				$user,
				'flexiskin-api'
			);

		if ( !$userHasRight ) {
			return false;
		}

		$idForPreview = $this->getParameter( 'id' );
		$configForPreview = $this->getParameter( 'config' );

		if ( ( $idForPreview === null ) || ( $configForPreview === null ) ) {
			return false;
		}

		$pluginRegistry = ExtensionRegistry::getInstance()->getAttribute( 'FlexiSkinPluginRegistry' );

		/**
		 * @var IPlugin[]
		 */
		$plugins = [];

		foreach ( $pluginRegistry as $pluginKey => $pluginFactoryCallback ) {
			$plugin = call_user_func_array( $pluginFactoryCallback, [] );
			if ( $plugin instanceof IPlugin ) {
				$plugins[$pluginKey] = $plugin;
			}
		}

		foreach ( $plugins as $pluginKey => $plugin ) {
			$this->previewCSS .= $plugin->generateCSS(
					$idForPreview,
					json_decode( $configForPreview, true )
				);
		}

		return true;
	}
}
