<?php

namespace MediaWiki\Extension\FlexiSkin\Tests\Unit;

use MediaWiki\Extension\FlexiSkin\FlexiSkin;
use MediaWiki\Extension\FlexiSkin\PluginBase;
use PHPUnit\Framework\TestCase;

class PluginBaseTest extends TestCase {

	/**
	 * @covers \MediaWiki\Extension\FlexiSkin\PluginBase::factory
	 */
	public function testFactoryReturnsPluginInstance(): void {
		$plugin = $this->newFactoryPlugin()::factory();

		$this->assertInstanceOf( PluginBase::class, $plugin );
		$this->assertSame( $this->newFactoryPlugin()::class, $plugin::class );
	}

	/**
	 * @covers \MediaWiki\Extension\FlexiSkin\PluginBase::getLessVars
	 */
	public function testGetLessVarsMapsOnlyConfiguredValues(): void {
		$skin = new FlexiSkin( null, [
			'colors' => [
				'background' => [ 'primary' => '#123456' ],
				'foreground' => [ 'primary' => '#fefefe' ]
			]
		] );
		$plugin = new class extends PluginBase {
			public function getJSFiles() {
				return [];
			}

			public function getCSSFiles() {
				return [];
			}

			public function getPluginName() {
				return 'test.less-vars';
			}

			public function getValidSkins() {
				return [];
			}

			public function getLessVarsMap(): array {
				return [
					'primary-bg' => 'colors/background/primary',
					'primary-fg' => 'colors/foreground/primary',
					'secondary-bg' => 'colors/background/secondary'
				];
			}
		};

		$this->assertSame(
			[
				'primary-bg' => '#123456',
				'primary-fg' => '#fefefe'
			],
			$plugin->getLessVars( $skin )
		);
	}

	private function newFactoryPlugin(): PluginBase {
		return new class extends PluginBase {
			public function getJSFiles() {
				return [];
			}

			public function getCSSFiles() {
				return [];
			}

			public function getPluginName() {
				return 'test.factory';
			}

			public function getValidSkins() {
				return [];
			}
		};
	}
}
