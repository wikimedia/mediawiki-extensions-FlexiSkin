<?php

namespace MediaWiki\Extension\FlexiSkin\Special;

use MediaWiki\Extension\FlexiSkin\FlexiSkinManager;
use MediaWiki\Html\Html;
use MediaWiki\Html\TemplateParser;
use MediaWiki\SpecialPage\SpecialPage;

class FlexiSkin extends SpecialPage {

	/** @var TemplateParser */
	protected $templateParser;

	public function __construct(
		private readonly FlexiSkinManager $flexiSkinManager
	) {
		parent::__construct( 'FlexiSkin' );

		$this->templateParser = new TemplateParser(
			dirname( __DIR__, 2 ) . '/resources/templates'
		);
	}

	/**
	 * @inheritDoc
	 */
	public function getDescription() {
		return $this->msg( 'flexiskin-special-title' );
	}

	/**
	 * @return string
	 */
	public function getRestriction(): string {
		return 'flexiskin-viewspecialpage';
	}

	/**
	 * @param string $par
	 * @return void
	 */
	public function execute( $par ) {
		parent::execute( $par );

		$this->setHeaders();
		$this->getOutput()->enableOOUI();
		$this->buildSkeleton();
		$this->getOutput()->addModuleStyles( [ 'ext.flexiskin.specialpage.styles' ] );
		$this->getOutput()->addModules( [ 'ext.flexiskin.specialpage.scripts' ] );
		$this->getOutput()->addHTML( Html::element( 'div', [ 'id' => 'fs-container' ] ) );

		$localSkin = $this->flexiSkinManager->getLocalSkin();
		if ( !$localSkin ) {
			$localSkin = $this->flexiSkinManager->createNew();
		}
		$activeSkin = $this->flexiSkinManager->getActive();
		$inherited = false;
		$usedSkin = $localSkin;
		if ( ( !$localSkin->getId() || !$localSkin->isActive() ) && $activeSkin?->getId() ) {
			$usedSkin = $activeSkin;
			$inherited = true;
		}
		$config = $usedSkin->getConfig();

		foreach ( $this->flexiSkinManager->getPlugins() as $plugin ) {
			$plugin->adaptConfiguration( $config );
			$plugin->setDefaults( $usedSkin, $config );
		}
		$this->getHookContainer()->run( 'FlexiSkinAdaptConfiguration', [ $usedSkin, &$config ] );

		$adaptedSkin = new \MediaWiki\Extension\FlexiSkin\FlexiSkin(
			$localSkin->getId(),
			$config,
			$localSkin->isActive()
		);

		$this->getOutput()->addJsConfigVars( 'wgFlexiSkin', $adaptedSkin );
		$this->getOutput()->addJsConfigVars(
			'wgFlexiSkinColorPresets', $this->getConfig()->get( 'FlexiSkinColorPresets' )
		);

		$this->getOutput()->addJsConfigVars( 'wgFlexiSkinInheritance', [
			'inherits' => $inherited,
		] );

		$this->getOutput()->addJsConfigVars(
			'wgFlexiSkinUnsupportedControls', $this->flexiSkinManager->getUnsupportedControls()
		);
	}

	/**
	 * @return void
	 */
	protected function buildSkeleton() {
		$skeleton = $this->templateParser->processTemplate(
			'flexiskin-skeleton',
			[]
		);
		$skeletonCnt = Html::openElement( 'div', [
			'id' => 'fs-skeleton-cnt'
		] );
		$skeletonCnt .= $skeleton;
		$skeletonCnt .= Html::closeElement( 'div' );
		$this->getOutput()->addHTML( $skeletonCnt );
	}

	/**
	 * @return string groupname
	 */
	protected function getGroupName() {
		return 'other';
	}
}
