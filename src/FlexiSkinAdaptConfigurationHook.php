<?php

namespace MediaWiki\Extension\FlexiSkin;

interface FlexiSkinAdaptConfigurationHook {

	/**
	 * @param FlexiSkin $skin
	 * @param array &$config
	 * @return void
	 */
	public function onFlexiSkinAdaptConfiguration( FlexiSkin $skin, array &$config ): void;
}
