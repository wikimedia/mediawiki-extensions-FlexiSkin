<?php

namespace MediaWiki\Extension\FlexiSkin;

interface FlexiSkinGetActiveSkinHook {

	/**
	 * Allow loading external skin as fallback
	 * @param FlexiSkinManager $manager
	 * @param FlexiSkin|null &$skin
	 * @return void
	 */
	public function onFlexiSkinGetActiveSkin( FlexiSkinManager $manager, ?FlexiSkin &$skin ): void;
}
