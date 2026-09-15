<?php

namespace MediaWiki\Extension\FlexiSkin;

interface IFlexiSkinManager {
	/**
	 * @param string $name
	 * @param array $config
	 * @return IFlexiSkin
	 */
	public function create( $name, $config ): IFlexiSkin;

	/**
	 * @param IFlexiSkin $flexiSkin
	 * @return bool
	 */
	public function save( IFlexiSkin $flexiSkin );

	/**
	 * @param string $skinname
	 * @return IFlexiSkin|null
	 */
	public function getFlexiSkin( $skinname = '' ): ?IFlexiSkin;

	/**
	 * Remove the configuration of a skin
	 *
	 * @param string $skinname defaults to the currently loaded skin
	 * @return bool
	 */
	public function delete( $skinname = '' );

	/**
	 * @param IFlexiSkin|null $flexiSkin
	 * @param bool|null $active
	 * @return bool
	 */
	public function setActive( ?IFlexiSkin $flexiSkin = null, $active = true ): bool;

	/**
	 * @param string $skinname
	 * @return IFlexiSkin|null
	 */
	public function getActive( $skinname = '' ): ?IFlexiSkin;

	/**
	 * Get all available plugins
	 *
	 * @return IPlugin[]
	 */
	public function getPlugins(): array;

	/**
	 * @param string $skinname
	 * @return array
	 */
	public function getActiveConfig( $skinname = '' ): array;

	/**
	 * Configuration that is actually in effect: this wikis own configuration on top of the
	 * inherited styling, if it inherits any
	 *
	 * @param string $skinname
	 * @return array
	 */
	public function getEffectiveConfig( $skinname = '' ): array;

	/**
	 * Whether this wiki inherits its styling, see hook `FlexiSkinGetInheritedSkin`
	 *
	 * @param string $skinname
	 * @return bool
	 */
	public function inheritsStyling( $skinname = '' ): bool;

	/**
	 * @param string $skinname
	 * @return IFlexiSkin|null if there is nothing to inherit
	 */
	public function getInheritedSkin( $skinname = '' ): ?IFlexiSkin;

	/**
	 * Load a skin from the data directory of another wiki
	 *
	 * @param string $dataDirectory
	 * @param string $skinname
	 * @return IFlexiSkin|null
	 */
	public function loadFromDataDirectory( string $dataDirectory, string $skinname ): ?IFlexiSkin;

	/**
	 * @param string $pluginKey
	 * @param IPlugin $plugin
	 * @param string $skinname
	 * @return bool
	 */
	public function isPluginValidForSkin( string $pluginKey, IPlugin $plugin, string $skinname ): bool;

	/**
	 * Controls a skin cannot apply, as declared in `FlexiSkinSkinRegistry`
	 *
	 * @param string $skinname
	 * @return string[] control paths, e.g. `color_settings/sidebar_colors`
	 */
	public function getUnsupportedControls( string $skinname ): array;
}
