<?php

namespace MediaWiki\Extension\FlexiSkin\HookHandler;

use MediaWiki\Extension\FlexiSkin\GlobalActionsAdministration;
use MWStake\MediaWiki\Component\CommonUserInterface\Hook\MWStakeCommonUIRegisterSkinSlotComponents;

class DiscoverySkin implements MWStakeCommonUIRegisterSkinSlotComponents {

	/**
	 * @inheritDoc
	 */
	public function onMWStakeCommonUIRegisterSkinSlotComponents( $registry ): void {
		$registry->register(
			'GlobalActionsAdministration',
			[
				'bs-special-flexiskin' => [
					'factory' => static function () {
						return new GlobalActionsAdministration();
					}
				]
			]
		);
	}
}
