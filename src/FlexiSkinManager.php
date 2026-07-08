<?php

namespace MediaWiki\Extension\FlexiSkin;

use MediaWiki\Context\RequestContext;
use MediaWiki\Json\FormatJson;
use MediaWiki\Registration\ExtensionRegistry;
use MWStake\MediaWiki\Component\FileStorageUtilities\StorageHandler;
use WANObjectCache;

class FlexiSkinManager implements IFlexiSkinManager {

	private const CACHE_TTL = WANObjectCache::TTL_INDEFINITE;
	private const CACHE_VERSION = 1;

	/** @var IFlexiSkin|null */
	private $currentSkin = null;
	/** @var IFlexiSkin[] */
	private $loadedSkins = [];

	/**
	 * @param StorageHandler $storageHandler
	 * @param WANObjectCache $cache
	 */
	public function __construct(
		private readonly StorageHandler $storageHandler,
		private readonly WANObjectCache $cache
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
	 * @return int
	 */
	public function delete() {
		$skinname = $this->currentSkin ? $this->currentSkin->getName() : 'default';
		$status = $this->storageHandler->newTransaction()
			->delete( $this->getFilename(), 'flexiskin' )
			->commit();
		if ( $status->isOK() ) {
			$this->cache->delete( $this->getCacheKey( $skinname ) );
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
	 * @param string $skinname
	 * @return array
	 */
	public function getActiveLessVars( $skinname = '' ): array {
		if ( empty( $skinname ) ) {
			$skinname = $this->getCurrentSkinname();
		}
		$active = $this->getActive( $skinname );
		if ( !$active instanceof IFlexiSkin ) {
			return [];
		}
		$vars = [];
		foreach ( $this->getPlugins() as $pluginKey => $plugin ) {
			$vars = array_merge( $vars, $plugin->getLessVars( $active ) );
		}
		return $vars;
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
