<?php

namespace MediaWiki\Extension\FlexiSkin;

interface FlexiSkinGetInheritedSkinHook {
	/**
	 * Let this wiki inherit its styling from another source, e.g. another wiki.
	 * Its own configuration is applied on top of the inherited one.
	 *
	 * @param string $skinname
	 * @param bool &$inherits true if this wiki inherits its styling, even if there
	 *  currently is nothing to inherit
	 * @param IFlexiSkin|null &$inheritedSkin
	 * @return void
	 */
	public function onFlexiSkinGetInheritedSkin( string $skinname, bool &$inherits, ?IFlexiSkin &$inheritedSkin );
}
