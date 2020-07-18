<?php

namespace MediaWiki\Extension\FlexiSkin\ResourceLoader;

use ExtensionRegistry;
use MediaWiki\Extension\FlexiSkin\IPlugin;
use MediaWiki\MediaWikiServices;
use OutputPage;
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
		$flexiSkinManager = MediaWikiServices::getInstance()->get( 'FlexiSkinManager' );
		$pluginStyles = [];
		foreach ( $flexiSkinManager->getPlugins() as $pluginKey => $plugin ) {
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
		return $this->readScriptFiles( $this->getPluginFiles( $context ) ) .
			parent::getScript( $context );
	}

	/**
	 * @param ResourceLoaderContext $context
	 * @return array
	 */
	public function getScriptURLsForDebug( ResourceLoaderContext $context ) {
		$pluginFiles = $this->getPluginFiles( $context );

		foreach ( $pluginFiles as &$file ) {
			$file = OutputPage::transformResourcePath(
				$this->getConfig(),
				$this->getRemotePath( $file )
			);
		}
		return array_merge( $pluginFiles, parent::getScriptURLsForDebug( $context ) );
	}

	/**
	 * @param ResourceLoaderContext $context
	 * @return string[]
	 */
	private function getPluginFiles( $context ) {
		$flexiSkinManager = MediaWikiServices::getInstance()->get( 'FlexiSkinManager' );

		$files = [
			'js/Defines.js',
			'js/ui/Plugin.js',
		];

		foreach ( $flexiSkinManager->getPlugins() as $pluginKey => $plugin ) {
			if ( $this->pluginValidForSkin( $plugin->getValidSkins(), $context ) ) {
				$files = array_merge( $files, $plugin->getJSFiles() );
			}
		}

		return $files;
	}

	/**
	 * Get the contents of a list of JavaScript files. Helper for getScript().
	 *
	 * @param array $scripts List of file paths to scripts to read, remap and concetenate
	 * @return string Concatenated JavaScript data from $scripts
	 * @throws RuntimeException
	 */
	private function readScriptFiles( array $scripts ) {
		if ( empty( $scripts ) ) {
			return '';
		}
		$js = '';
		foreach ( array_unique( $scripts, SORT_REGULAR ) as $fileName ) {
			$localPath = $this->getLocalPath( $fileName );
			$contents = $this->getFileContents( $localPath, 'script' );
			$js .= $contents . "\n";
		}
		return $js;
	}

	/**
	 * @param array $pluginSkins
	 * @param ResourceLoaderContext $context
	 * @return bool
	 */
	private function pluginValidForSkin( $pluginSkins, $context ) {
		$skin = $context->getSkin();

		if ( in_array( '*', $pluginSkins ) || in_array( $skin, $pluginSkins ) ) {
			return true;
		}

		return false;
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

}
