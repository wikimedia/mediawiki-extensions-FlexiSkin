<?php

namespace MediaWiki\Extension\FlexiSkin\HookHandler;

use MediaWiki\Installer\DatabaseUpdater;
use MediaWiki\Installer\Hook\LoadExtensionSchemaUpdatesHook;

class RunDatabaseUpdates implements LoadExtensionSchemaUpdatesHook {

	/**
	 * @param DatabaseUpdater $updater
	 * @return bool|void
	 */
	public function onLoadExtensionSchemaUpdates( $updater ) {
		$baseDir = __DIR__ . '/../../db';
		$dbType = $updater->getDB()->getType();

		$updater->addExtensionTable( 'flexiskin', "$baseDir/$dbType/flexiskin.sql" );
	}
}
