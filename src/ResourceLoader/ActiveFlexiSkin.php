<?php

namespace MediaWiki\Extension\FlexiSkin\ResourceLoader;

use ExtensionRegistry;
use MediaWiki\Extension\FlexiSkin\IPlugin;
use MediaWiki\MediaWikiServices;
use ResourceLoaderContext;
use ResourceLoaderFileModule;
use RuntimeException;

class ActiveFlexiSkin extends ResourceLoaderFileModule {
	/**
	 * Get the contents of a list of CSS files.
	 *
	 * @internal This is considered a private method. Exposed for internal use by WebInstallerOutput.
	 * @param array $styles Map of media type to file paths to read, remap, and concatenate
	 * @param ResourceLoaderContext $context
	 * @return array List of concatenated and remapped CSS data from $styles,
	 *     keyed by media type
	 * @throws RuntimeException
	 */
	public function readStyleFiles( array $styles, ResourceLoaderContext $context ) {
		$parentStyles = parent::readStyleFiles( $styles, $context );
		$flexiSkinCSS = '';

		$flexiSkinManager = MediaWikiServices::getInstance()->get( 'FlexiSkinManager' );

		$activeId = $flexiSkinManager->getActive();

		if ( !$activeId ) {
			return $parentStyles;
		}

		$activeFlexiSkin = $flexiSkinManager->loadFromId( $activeId );
		$flexiSkinConfig = $activeFlexiSkin->getConfig();

		$flexiSkinPlugins = $this->getFlexiSkinPlugins();
		if ( $flexiSkinConfig !== null ) {
			foreach ( $flexiSkinPlugins as $pluginKey => $plugin ) {
				$flexiSkinCSS .= $plugin->generateCSS( 'fs-' . $activeId, $flexiSkinConfig );
			}
		}

		return array_merge( $parentStyles, [ 'all' => str_replace( "\n", "", $flexiSkinCSS ) ] );
	}

	/**
	 *
	 * @return array
	 */
	private function getFlexiSkinPlugins() {
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

		return $plugins;
	}

}
