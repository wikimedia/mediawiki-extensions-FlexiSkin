<?php

namespace MediaWiki\Extension\FlexiSkin\Tests\Api;

use ApiMain;
use MediaWiki\Extension\FlexiSkin\Api\ActivateFlexiSkin;
use MediaWiki\Extension\FlexiSkin\IFlexiSkinManager;
use PHPUnit\Framework\TestCase;

class ActivateFlexiSkinTest extends TestCase {

	/**
	 * @covers Flexiskin API
	 */
	public function testExecute() {
		$mainModuleMock = $this->createMock( ApiMain::class );
		$mainModuleMock->expects( $this->once() )
			->method( 'getParameter' )
			->with( 'id' )
			->willReturn( 1 );

		$mockFlexiSkinManager = $this->createMock( IFlexiSkinManager::class );
		$mockFlexiSkinManager->expects( $this->once() )
		->method( 'loadFromId' )
		->with( 1 );

		$mockFlexiSkinManager->expects( $this->once() )
		->method( 'setActive' );

		$apiActivate = new ActivateFlexiSkin( $mainModuleMock, 'activate', '', $mockFlexiSkinManager );
		$apiActivate->execute();
	}
}
