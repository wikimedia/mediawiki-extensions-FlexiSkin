<?php

namespace MediaWiki\Extension\FlexiSkin\Special;

use Html;
use SpecialPage;

class FlexiSkin extends SpecialPage {

	public function __construct() {
		parent::__construct( 'FlexiSkin', 'flexiskin-viewspecialpage', true );
	}

	/**
	 *
	 * @param string $par
	 * @return void
	 */
	public function execute( $par ) {
		parent::execute( $par );

		$this->setHeaders();
		$this->getOutput()->addModuleStyles( 'ext.flexiskin.specialpage.styles' );
		$this->getOutput()->addModules( 'ext.flexiskin.specialpage.scripts' );
		$this->getOutput()->addHTML( Html::element( 'div', [ 'id' => 'fs-container' ] ) );
	}

	/**
	 * @return string groupname
	 */
	protected function getGroupName() {
		return 'other';
	}
}
