<?php

namespace MediaWiki\Extension\FlexiSkin;

use JsonSerializable;

class FlexiSkin implements IFlexiSkin, JsonSerializable {

	/**
	 * @param int|null $id
	 * @param array $config
	 * @param bool|null $active
	 */
	public function __construct(
		private ?int $id,
		private array $config,
		private bool $active = false
	) {
	}

	/**
	 * @return int|null
	 */
	public function getId(): ?int {
		return $this->id;
	}

	/**
	 * @return array|null
	 */
	public function getConfig(): ?array {
		return $this->config;
	}

	/**
	 * @return bool
	 */
	public function isActive(): bool {
		return $this->active;
	}

	public function activate(): void {
		$this->active = true;
	}

	public function deactivate(): void {
		$this->active = false;
	}

	/**
	 * @param string $path
	 * @return mixed|null if not found
	 */
	public function getValueForPath( string $path ): mixed {
		$pathBits = explode( '/', $path );
		$searchValue = $this->getConfig();
		foreach ( $pathBits as $bit ) {
			if ( !isset( $searchValue[$bit] ) ) {
				return null;
			}
			$searchValue = $searchValue[$bit];
		}

		if ( empty( $searchValue ) ) {
			return null;
		}
		return $searchValue;
	}

	/**
	 * @return mixed
	 */
	public function jsonSerialize(): mixed {
		return [
			'id' => $this->getId(),
			'config' => $this->getConfig(),
			'active' => $this->isActive()
		];
	}
}
