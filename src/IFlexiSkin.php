<?php

namespace MediaWiki\Extension\FlexiSkin;

/**
 * @deprecated since 6.0 - use FlexiSkin class directly
 */
interface IFlexiSkin {

	/**
	 * @return int|null
	 */
	public function getId(): ?int;

	/**
	 * @return array|null
	 */
	public function getConfig(): ?array;

	/**
	 * @return bool
	 */
	public function isActive(): bool;

	/**
	 * @return void
	 */
	public function activate(): void;

	/**
	 * @return void
	 */
	public function deactivate(): void;

	/**
	 * @param string $path
	 * @return mixed|null if not found
	 */
	public function getValueForPath( string $path ): mixed;
}
