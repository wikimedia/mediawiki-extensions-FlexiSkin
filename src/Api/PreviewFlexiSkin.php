<?php

namespace MediaWiki\Extension\FlexiSkin\Api;

use ApiBase;
use ApiUsageException;
use ExtensionRegistry;
use MediaWiki\Extension\FlexiSkin\IPlugin;

class PreviewFlexiSkin extends FlexiSkinApiBase {
	protected $previewCSS = '';

	public function execute() {
		$this->generatePreviewCSS();
		$this->returnResult();
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

	protected function returnResult() {
		$result = $this->getResult();
		$result->addValue( null, 'preview', $this->previewCSS );
	}

	/**
	 * @return bool
	 * @throws ApiUsageException
	 */
	private function generatePreviewCSS() {
		$this->checkPermissions();

		$idForPreview = $this->getParameter( 'id' );
		$configForPreview = $this->getParameter( 'config' );

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
