<?php


class SpecialFlexiSkin extends SpecialPage {
	protected $templateParser = null;

	public function __construct() {
		parent::__construct(
				'FlexiSkin',
				'flexiskin-viewspecialpage',
				true
			);
	}

	/**
	 *
	 * @param string $par
	 * @return void
	 */
	public function execute( $par ) {
		parent::execute( $par );

		$request = $this->getRequest();
		$output = $this->getOutput();
		$this->setHeaders();

		$output->addModules( 'ext.flexiskin.specialpage.scripts' );

		$html = Html::element( 'div', [ 'id' => 'fs-select' ] );
		$html .= Html::element( 'div', [ 'id' => 'fs-configure' ] );
		$output->addHTML( $html );
	}

	/**
	 * @return string groupname
	 */
	protected function getGroupName() {
		return 'other';
	}
}
