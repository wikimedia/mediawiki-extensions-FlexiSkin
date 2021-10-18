<?php

require_once dirname( dirname( dirname( __DIR__ ) ) ) . '/maintenance/Maintenance.php';

class RenameDefaultFile extends LoggedUpdateMaintenance {

	public function __construct() {
		parent::__construct();

		$this->requireExtension( 'FlexiSkin' );
	}

	protected function doDBUpdates() {
		$this->output( 'FlexiSkin - update config file...' );
		$directory = $this->getConfig()->get( 'UploadDirectory' ) . '/flexiskin/';
		$defaultName = $directory . 'default.json';
		if ( file_exists( $defaultName ) ) {
			$calummaName = $directory . 'bluespicecalumma.json';
			rename( $defaultName, $calummaName );
			$this->output( "default.json renamed to bluespicecalumma.json\n" );
		} else {
			$this->output( "no default.json available, skipping\n" );
		}
	}

	protected function getUpdateKey() {
		return 'flexiskin-update-default-file';
	}
}

$maintClass = RenameDefaultFile::class;
require_once RUN_MAINTENANCE_IF_MAIN;
