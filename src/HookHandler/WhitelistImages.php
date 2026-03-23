<?php

namespace MediaWiki\Extension\FlexiSkin\HookHandler;

use MediaWiki\Title\Title;

class WhitelistImages {

	/**
	 * @param Title $title
	 * @param string $path
	 * @param string $name
	 * @param bool &$whitelisted
	 * @return bool
	 */
	public function onCheckFileWhitelisted( Title $title, string $path, string $name, bool &$whitelisted ): bool {
		if ( str_starts_with( $name, 'flexiskin-image' ) ) {
			// $whitelisted = true;
			return false;
		}
		return true;
	}
}
