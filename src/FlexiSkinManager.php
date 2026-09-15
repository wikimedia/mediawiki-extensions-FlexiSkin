<?php

namespace MediaWiki\Extension\FlexiSkin;

use MediaWiki\Context\RequestContext;
use MediaWiki\HookContainer\HookContainer;
use MediaWiki\Json\FormatJson;
use MediaWiki\Registration\ExtensionRegistry;
use MWStake\MediaWiki\Component\FileStorageUtilities\StorageHandler;
use Wikimedia\ObjectCache\WANObjectCache;

class FlexiSkinManager implements IFlexiSkinManager {

	private const CACHE_TTL = WANObjectCache::TTL_INDEFINITE;
	private const CACHE_VERSION = 1;

	/** @var IFlexiSkin|null */
	private $currentSkin = null;
	/** @var IFlexiSkin[] */
	private $loadedSkins = [];
	/** @var array<string, array{inherits: bool, skin: IFlexiSkin|null}> */
	private $inheritance = [];

	/**
	 * @param StorageHandler $storageHandler
	 * @param WANObjectCache $cache
	 * @param HookContainer $hookContainer
	 */
	public function __construct(
		private readonly StorageHandler $storageHandler,
		private readonly WANObjectCache $cache,
		private readonly HookContainer $hookContainer
	) {
	}

	/**
	 * @param string $skinname
	 * @return string
	 */
	private function getCacheKey( string $skinname ): string {
		return $this->cache->makeKey( 'flexiskin', self::CACHE_VERSION, $skinname );
	}

	/**
	 * @param IFlexiSkin $flexiSkin
	 * @return bool
	 */
	public function save( IFlexiSkin $flexiSkin ): bool {
		$filename = $this->getFilename( $flexiSkin->getName() );

		if ( $flexiSkin->getId() === null ) {
			$flexiSkin = new FlexiSkin(
				1,
				$flexiSkin->getName(),
				$flexiSkin->getConfig(),
				$flexiSkin->isActive()
			);
		}

		$json = FormatJson::encode( $flexiSkin );

		$status = $this->storageHandler->newTransaction()
			->create( $filename, $json, 'flexiskin', [ 'overwrite' => true ] )
			->commit();
		if ( $status->isOK() ) {
			$this->currentSkin = $flexiSkin;
			$this->cache->delete( $this->getCacheKey( $flexiSkin->getName() ) );
		}

		return $status->isOK();
	}

	/**
	 * @inheritDoc
	 */
	public function delete( $skinname = '' ) {
		if ( empty( $skinname ) ) {
			$skinname = $this->currentSkin ? $this->currentSkin->getName() : 'default';
		}
		$status = $this->storageHandler->newTransaction()
			->delete( $this->getFilename( $skinname ), 'flexiskin', [ 'ignoreMissingSource' => true ] )
			->commit();
		if ( $status->isOK() ) {
			$this->cache->delete( $this->getCacheKey( $skinname ) );
			unset( $this->loadedSkins[$skinname] );
			$this->currentSkin = null;
		}
		return $status->isOK();
	}

	/**
	 * @param string $skinname
	 * @return IFlexiSkin|null
	 */
	public function getFlexiSkin( $skinname = '' ): ?IFlexiSkin {
		$this->load( false, $skinname );
		return $this->currentSkin;
	}

	/**
	 * @param bool|null $reload
	 * @param string $skinname
	 * @return void
	 */
	private function load( $reload = false, $skinname = '' ) {
		if ( !empty( $skinname ) ) {
				$this->currentSkin = isset( $this->loadedSkins[$skinname] )
					? $this->loadedSkins[$skinname] : null;
		}
		if ( $this->currentSkin === null || $reload ) {
			$filename = $this->getFilename( $skinname );
			$cacheKey = $this->getCacheKey( $skinname ?: 'default' );

			$data = $this->cache->getWithSetCallback(
				$cacheKey,
				self::CACHE_TTL,
				function () use ( $filename ) {
					$file = $this->storageHandler->getFile( $filename, 'flexiskin' );
					if ( !$file ) {
						return false;
					}
					$json = file_get_contents( $file->getPath() );
					return FormatJson::decode( $json, 1 );
				}
			);

			if ( $data === false ) {
				return;
			}

			$newSkin = FlexiSkin::newFromData( $data );
			$skinname = $newSkin->getName();
			$this->loadedSkins[$skinname] = $newSkin;
			$this->currentSkin = $this->loadedSkins[$skinname];
		}
	}

	/**
	 * @inheritDoc
	 */
	public function getActive( $skinname = '' ): ?IFlexiSkin {
		if ( empty( $skinname ) ) {
			$skinname = $this->getCurrentSkinname();
		}
		$current = $this->getFlexiSkin( $skinname );
		if ( $current && $current->isActive() ) {
			return $current;
		}
		return null;
	}

	/**
	 * @return string
	 */
	private function getCurrentSkinname() {
		$context = RequestContext::getMain();
		if ( defined( 'MW_NO_SESSION' ) && MW_NO_SESSION ) {
			return $context->getRequest()->getVal( 'skin', '' );
		}
		$skinname = $context->getSkin()->getSkinName();
		return $skinname;
	}

	/**
	 * @inheritDoc
	 */
	public function setActive( ?IFlexiSkin $flexiSkin = null, $active = true ): bool {
		$current = $flexiSkin;
		if ( $current === null ) {
			$context = RequestContext::getMain();
			$currentSkin = $context->getSkin()->getSkinName();
			$current = $this->create( $currentSkin, [] );
		}
		$newSkin = new FlexiSkin(
			$current->getId(),
			$current->getName(),
			$current->getConfig(),
			$active
		);

		return (bool)$this->save( $newSkin );
	}

	/**
	 * @param string $name
	 * @param array $config
	 * @return IFlexiSkin
	 */
	public function create( $name, $config ): IFlexiSkin {
		$existingSkin = $this->getFlexiSkin( $name );
		if ( $existingSkin === null ) {
			return new FlexiSkin( null, $name, $config );
		}

		return $existingSkin;
	}

	/**
	 * @return IPlugin[]
	 */
	public function getPlugins(): array {
		return $this->getRegistryInstances( 'FlexiSkinPluginRegistry', IPlugin::class );
	}

	/**
	 * @return IFlexiSkinSubscriber[]
	 */
	public function getSubscribers() {
		return $this->getRegistryInstances(
			'FlexiSkinSubscriberRegistry',
			IFlexiSkinSubscriber::class
		);
	}

	/**
	 * @param string $skinname
	 * @return array
	 */
	public function getActiveConfig( $skinname = '' ): array {
		if ( empty( $skinname ) ) {
			$skinname = $this->getCurrentSkinname();
		}
		$active = $this->getActive( $skinname );
		if ( !$active instanceof IFlexiSkin ) {
			return [];
		}

		$config = $active->getConfig();
		if ( !$config ) {
			return [];
		}
		/**
		 * @var string $pluginKey
		 * @var IPlugin $plugin
		 */
		foreach ( $this->getPlugins() as $pluginKey => $plugin ) {
			$plugin->adaptConfiguration( $config );
		}
		return $config;
	}

	/**
	 * @inheritDoc
	 */
	public function getEffectiveConfig( $skinname = '' ): array {
		if ( empty( $skinname ) ) {
			$skinname = $this->getCurrentSkinname();
		}
		$config = [];
		foreach ( $this->getEffectiveSkins( $skinname ) as $skin ) {
			$skinConfig = $skin->getConfig();
			if ( !$skinConfig ) {
				continue;
			}
			$config = array_replace_recursive( $config, $skinConfig );
		}
		if ( !$config ) {
			return [];
		}
		/**
		 * @var string $pluginKey
		 * @var IPlugin $plugin
		 */
		foreach ( $this->getPlugins() as $pluginKey => $plugin ) {
			$plugin->adaptConfiguration( $config );
		}
		return $config;
	}

	/**
	 * @param string $skinname
	 * @return array
	 */
	public function getActiveLessVars( $skinname = '' ): array {
		if ( empty( $skinname ) ) {
			$skinname = $this->getCurrentSkinname();
		}
		$vars = [];
		foreach ( $this->getEffectiveSkins( $skinname ) as $skin ) {
			foreach ( $this->getPlugins() as $pluginKey => $plugin ) {
				$vars = array_merge( $vars, $plugin->getLessVars( $skin ) );
			}
		}
		return $vars;
	}

	/**
	 * @inheritDoc
	 */
	public function inheritsStyling( $skinname = '' ): bool {
		return $this->getInheritance( $skinname )['inherits'];
	}

	/**
	 * @inheritDoc
	 */
	public function getInheritedSkin( $skinname = '' ): ?IFlexiSkin {
		return $this->getInheritance( $skinname )['skin'];
	}

	/**
	 * @param string $skinname
	 * @return array{inherits: bool, skin: IFlexiSkin|null}
	 */
	private function getInheritance( $skinname ): array {
		if ( empty( $skinname ) ) {
			$skinname = $this->getCurrentSkinname();
		}
		if ( !isset( $this->inheritance[$skinname] ) ) {
			$inherits = false;
			$inheritedSkin = null;
			$this->hookContainer->run(
				'FlexiSkinGetInheritedSkin',
				[ $skinname, &$inherits, &$inheritedSkin ]
			);
			$this->inheritance[$skinname] = [
				'inherits' => $inherits,
				'skin' => $inherits ? $inheritedSkin : null
			];
		}

		return $this->inheritance[$skinname];
	}

	/**
	 * @inheritDoc
	 */
	public function loadFromDataDirectory( string $dataDirectory, string $skinname ): ?IFlexiSkin {
		$filename = "$dataDirectory/flexiskin/" . $this->getFilename( $skinname );
		if ( !is_readable( $filename ) ) {
			return null;
		}
		$json = file_get_contents( $filename );
		if ( $json === false ) {
			return null;
		}
		$data = FormatJson::decode( $json, 1 );
		if ( !is_array( $data ) ) {
			return null;
		}

		return FlexiSkin::newFromData( $data );
	}

	/**
	 * @inheritDoc
	 */
	public function isPluginValidForSkin( string $pluginKey, IPlugin $plugin, string $skinname ): bool {
		$validSkins = $plugin->getValidSkins();
		if ( in_array( '*', $validSkins ) || in_array( $skinname, $validSkins ) ) {
			return true;
		}

		return in_array( $pluginKey, $this->getSkinDefinition( $skinname )['plugins'] ?? [] );
	}

	/**
	 * @inheritDoc
	 */
	public function getUnsupportedControls( string $skinname ): array {
		return $this->getSkinDefinition( $skinname )['unsupportedControls'] ?? [];
	}

	/**
	 * @param string $skinname
	 * @return array
	 */
	private function getSkinDefinition( string $skinname ): array {
		$skins = ExtensionRegistry::getInstance()->getAttribute( 'FlexiSkinSkinRegistry' );

		return $skins[$skinname] ?? [];
	}

	/**
	 * @param string $skinname
	 * @return IFlexiSkin[]
	 */
	private function getEffectiveSkins( string $skinname ): array {
		$skins = [];

		$inheritedSkin = $this->getInheritedSkin( $skinname );
		if ( $inheritedSkin instanceof IFlexiSkin && $inheritedSkin->isActive() ) {
			// Images are resolved through the local file repo, they cannot be inherited
			$config = $inheritedSkin->getConfig() ?? [];
			unset( $config['images'] );
			$skins[] = new FlexiSkin(
				$inheritedSkin->getId(),
				$inheritedSkin->getName(),
				$config,
				true
			);
		}

		$own = $this->getActive( $skinname );
		if ( $own instanceof IFlexiSkin ) {
			$skins[] = $own;
		}

		return $skins;
	}

	/**
	 * @param string $skinname
	 * @return string
	 */
	private function getFilename( $skinname = 'default' ) {
		return $skinname . '.json';
	}

	/**
	 * @param string $name
	 * @param string $targetClass
	 * @return array
	 */
	public function getRegistryInstances( $name, $targetClass ) {
		$values = ExtensionRegistry::getInstance()->getAttribute( $name );
		$items = [];
		foreach ( $values as $key => $factory ) {
			$item = call_user_func_array( $factory, [] );
			if ( $item instanceof $targetClass ) {
				$items[$key] = $item;
			}
		}

		return $items;
	}
}
