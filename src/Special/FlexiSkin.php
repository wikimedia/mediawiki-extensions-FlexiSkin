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

		$this->getOutput()->addModules( 'ext.flexiskin.specialpage.scripts' );

		$html = Html::element( 'div', [ 'id' => 'fs-select' ] );
		$html .= Html::element( 'div', [ 'id' => 'fs-configure' ] );

		$this->getOutput()->addHTML( $html );
	}

	/**
	 * @return string groupname
	 */
	protected function getGroupName() {
		return 'other';
	}
}
