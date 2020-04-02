<?php

namespace MediaWiki\Extension\FlexiSkin;

abstract class PluginBase implements IPlugin {

	/**
	 * @return IPlugin
	 */
	public static function factory() {
		return new static();
	}
}
