<?php

namespace MediaWiki\Extension\FlexiSkin\Hook\LoadExtensionSchemaUpdates;

use DatabaseUpdater;

class FlexiSkinShemaUpdates {
	/**
	 *
	 * @param DatabaseUpdater $updater
	 */
	public function callback( DatabaseUpdater $updater ) {
		$dir = self::getExtensionPath() . '/maintenance/db';

		$updater->addExtensionTable(
			'flexiskin',
			"$dir/flexiskin.sql"
		);
	}

	/**
	 *
	 * @return string
	*/
	protected function getExtensionPath() {
		return dirname( dirname( dirname( __DIR__ ) ) );
	}
}
