<?php

namespace MediaWiki\Extension\FlexiSkin\ResourceLoader;

use ExtensionRegistry;
use MediaWiki\Extension\FlexiSkin\IPlugin;
use ResourceLoaderContext;
use ResourceLoaderFileModule;
use RuntimeException;

class Configurator extends ResourceLoaderFileModule {
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
		$pluginRegistry = ExtensionRegistry::getInstance()->getAttribute( 'FlexiSkinPluginRegistry' );
		/**
		 * @var IPlugin[]
		 */
		$plugins = [];
		$pluginStyles = [];

		foreach ( $pluginRegistry as $pluginKey => $pluginFactoryCallback ) {
			$plugin = call_user_func_array( $pluginFactoryCallback, [] );
			if ( $plugin instanceof IPlugin ) {
				$plugins[$pluginKey] = $plugin;
			}
		}

		foreach ( $plugins as $pluginKey => $plugin ) {
			$pluginStylesFiles = $plugin->getCSSFiles();

			if ( !$pluginStylesFiles ) {
				return parent::readStyleFiles( $styles, $context );
			}

			foreach ( $pluginStylesFiles as $media => $files ) {
				$styleFiles = [];
				foreach ( $files as $file ) {
					$styleFiles[] = $this->readStyleFile( $file, $context );
				}
				if ( !array_key_exists( $media, $pluginStyles ) ) {
					$pluginStyles += [ $media => implode( "\n", $styleFiles ) ];
				} else {
					$pluginStyles[$media] .= implode( "\n", $styleFiles );
				}
			}
		}

		$parentStyles = parent::readStyleFiles( $styles, $context );

		return array_merge( $parentStyles, $pluginStyles );
	}

	/**
	 * Gets all scripts for a given context concatenated together.
	 *
	 * @param ResourceLoaderContext $context Context in which to generate script
	 * @return string|array JavaScript code for $context, or package files data structure
	 */
	public function getScript( ResourceLoaderContext $context ) {
		$scriptCode = "var flexiskinRegistry = [];\n";

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
			if ( $this->pluginValidForSkin( $plugin->getValidSkins(), $context ) ) {
				$scripts = $plugin->getJSFiles();

				foreach ( $scripts as $script ) {
					$scriptCode .= "\n//Plugin '$pluginKey'\n" . $this->getFileContents( $script, 'javascript' );
				}

				$scriptCode .= "\nflexiskinRegistry['" . $pluginKey . "'] = new {$plugin->getPluginName()};";
			}
		}

		$parentReturn = parent::getScript( $context );

		return $scriptCode . $parentReturn;
	}

	/**
	 * Helper method for getting a file.
	 *
	 * @param string $localPath The path to the resource to load
	 * @param string $type The type of resource being loaded (for error reporting only)
	 * @throws RuntimeException If the supplied path is not found, or not a path
	 * @return string
	 */
	private function getFileContents( $localPath, $type ) {
		if ( !is_file( $localPath ) ) {
			throw new RuntimeException(
				__METHOD__ . ": $type file not found, or is not a file: \"$localPath\""
			);
		}
		return $this->stripBom( file_get_contents( $localPath ) );
	}

	/**
	 * @param array
	 * @return bool
	 */
	private function pluginValidForSkin( $pluginSkins, $context ) {
		$skin = $context->getSkin();

		if ( in_array( '*', $pluginSkins ) || in_array( $skin, $pluginSkins ) ) {
			return true;
		}

		return false;
	}

}
