<?php

namespace MediaWiki\Extension\FlexiSkin\Tests\Unit;

use MediaWiki\Extension\FlexiSkin\FlexiSkin;
use PHPUnit\Framework\TestCase;

class FlexiSkinTest extends TestCase {

	/**
	 * @covers \MediaWiki\Extension\FlexiSkin\FlexiSkin::getId
	 * @covers \MediaWiki\Extension\FlexiSkin\FlexiSkin::getConfig
	 * @covers \MediaWiki\Extension\FlexiSkin\FlexiSkin::isActive
	 * @covers \MediaWiki\Extension\FlexiSkin\FlexiSkin::activate
	 * @covers \MediaWiki\Extension\FlexiSkin\FlexiSkin::deactivate
	 */
	public function testGettersAndActivationState(): void {
		$skin = new FlexiSkin( 15, [ 'colors' => [ 'background' => [ 'primary' => '#112233' ] ] ] );

		$this->assertSame( 15, $skin->getId() );
		$this->assertSame(
			[ 'colors' => [ 'background' => [ 'primary' => '#112233' ] ] ],
			$skin->getConfig()
		);
		$this->assertFalse( $skin->isActive() );

		$skin->activate();
		$this->assertTrue( $skin->isActive() );

		$skin->deactivate();
		$this->assertFalse( $skin->isActive() );
	}

	/**
	 * @covers \MediaWiki\Extension\FlexiSkin\FlexiSkin::getValueForPath
	 */
	public function testGetValueForPathReturnsNestedValue(): void {
		$skin = new FlexiSkin( null, [
			'colors' => [
				'background' => [
					'primary' => '#abcdef'
				]
			]
		] );

		$this->assertSame( '#abcdef', $skin->getValueForPath( 'colors/background/primary' ) );
	}

	/**
	 * @covers \MediaWiki\Extension\FlexiSkin\FlexiSkin::getValueForPath
	 */
	public function testGetValueForPathReturnsNullForMissingOrEmptyValues(): void {
		$skin = new FlexiSkin( null, [
			'colors' => [
				'background' => [
					'primary' => '#abcdef',
					'secondary' => ''
				]
			]
		] );

		$this->assertNull( $skin->getValueForPath( 'colors/background/unknown' ) );
		$this->assertNull( $skin->getValueForPath( 'colors/background/secondary' ) );
	}

	/**
	 * @covers \MediaWiki\Extension\FlexiSkin\FlexiSkin::jsonSerialize
	 */
	public function testJsonSerializeReturnsExpectedStructure(): void {
		$skin = new FlexiSkin( 7, [ 'images' => [ 'logo' => [ 'url' => 'https://example.test/logo.svg' ] ] ] );
		$skin->activate();

		$this->assertSame(
			[
				'id' => 7,
				'config' => [ 'images' => [ 'logo' => [ 'url' => 'https://example.test/logo.svg' ] ] ],
				'active' => true
			],
			$skin->jsonSerialize()
		);
	}
}
