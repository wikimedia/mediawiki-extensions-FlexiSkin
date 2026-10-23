<?php

namespace MediaWiki\Extension\FlexiSkin;

use MediaWiki\Context\RequestContext;
use MediaWiki\HookContainer\HookContainer;
use MediaWiki\Json\FormatJson;
use MediaWiki\Registration\ExtensionRegistry;
use MediaWiki\WikiMap\WikiMap;
use Psr\Log\LoggerInterface;
use Throwable;
use Wikimedia\Rdbms\ILoadBalancer;

class FlexiSkinManager {

	private const TABLE_NAME = 'flexiskin';

	/** @var FlexiSkin|null */
	private ?FlexiSkin $localSkin = null;
	/** @var bool */
	private bool $activeSkinLoaded = false;
	private ?FlexiSkin $activeSkin = null;

	/**
	 * @param ILoadBalancer $lb
	 * @param HookContainer $hookContainer
	 * @param LoggerInterface $logger
	 */
	public function __construct(
		private readonly ILoadBalancer $lb,
		private readonly HookContainer $hookContainer,
		private readonly LoggerInterface $logger
	) {
	}

	/**
	 * @param FlexiSkin $flexiSkin
	 * @return FlexiSkin|null
	 */
	public function save( FlexiSkin $flexiSkin ): ?FlexiSkin {
		$row = [
			'fs_skin' => $this->getCurrentSkinName(),
			'fs_active' => $flexiSkin->isActive(),
			'fs_config' => FormatJson::encode( $flexiSkin->getConfig() ),
			'fs_wiki_id' => WikiMap::getCurrentWikiId(),
		];

		$db = $this->lb->getConnection( DB_PRIMARY );
		if ( $flexiSkin->getId() === null ) {
			$query = $db->newInsertQueryBuilder()
				->insert( self::TABLE_NAME )
				->row( $row )
				->caller( __METHOD__ );
		} else {
			$query = $db->newUpdateQueryBuilder()
				->update( self::TABLE_NAME )
				->set( $row )
				->where( [ 'fs_id' => $flexiSkin->getId() ] )
				->caller( __METHOD__ );
		}

		try {
			$query->execute();
			return $this->getSkinForWiki( WikiMap::getCurrentWikiId() );
		} catch ( Throwable $e ) {
			$this->logger->error( "Failed to save FlexiSkin: {$e->getMessage()}" );
			return null;
		}
	}

	/**
	 * @return bool
	 */
	public function delete(): bool {
		$db = $this->lb->getConnection( DB_PRIMARY );
		try {
			$db->newDeleteQueryBuilder()
				->delete( self::TABLE_NAME )
				->where( [ 'fs_skin' => $this->getCurrentSkinName(), 'fs_wiki_id' => WikiMap::getCurrentWikiId() ] )
				->caller( __METHOD__ )
				->execute();
		} catch ( Throwable $e ) {
			$this->logger->error( "Failed to delete FlexiSkin: {$e->getMessage()}" );
			return false;
		}

		return true;
	}

	/**
	 * @return FlexiSkin|null
	 */
	public function getLocalSkin(): ?FlexiSkin {
		$this->load();
		return $this->localSkin;
	}

	/**
	 * @param bool|null $reload
	 * @return void
	 */
	private function load( bool $reload = false ): void {
		if ( $this->localSkin === null || $reload ) {
			$this->localSkin = $this->getSkinForWiki( WikiMap::getCurrentWikiId() );
		}
	}

	/**
	 * @param string $wikiId
	 * @param bool $onlyActive
	 * @return FlexiSkin|null
	 */
	public function getSkinForWiki( string $wikiId, bool $onlyActive = false ): ?FlexiSkin {
		$db = $this->lb->getConnection( DB_PRIMARY );
		$query = $db->newSelectQueryBuilder()
			->from( self::TABLE_NAME )
			->select( [ 'fs_id', 'fs_skin', 'fs_config', 'fs_active' ] )
			->where( [ 'fs_skin' => $this->getCurrentSkinName(), 'fs_wiki_id' => $wikiId ] )
			->caller( __METHOD__ );
		if ( $onlyActive ) {
			$query->where( [ 'fs_active' => 1 ] );
		}
		$row = $query->fetchRow();

		if ( !$row ) {
			return null;
		}

		return new FlexiSkin(
			id: (int)$row->fs_id,
			config: FormatJson::decode( $row->fs_config, 1 ),
			active: (bool)$row->fs_active
		);
	}

	/**
	 * @return FlexiSkin|null
	 */
	public function getActive(): ?FlexiSkin {
		if ( $this->activeSkinLoaded ) {
			return $this->activeSkin;
		}
		$this->load();
		if ( $this->localSkin && $this->localSkin->isActive() ) {
			$this->activeSkin = $this->localSkin;
			return $this->localSkin;
		}
		$activeSkin = null;
		$this->hookContainer->run( 'FlexiSkinGetActiveSkin', [ $this, &$activeSkin ] );
		if ( $activeSkin && $activeSkin->isActive() ) {
			$this->activeSkin = $activeSkin;
			$this->activeSkinLoaded = true;
			return $activeSkin;
		}
		$this->activeSkinLoaded = true;
		$this->activeSkin = null;
		return null;
	}

	/**
	 * @param FlexiSkin|null $flexiSkin
	 * @param bool $active
	 * @return FlexiSkin|null
	 */
	public function setActive( ?FlexiSkin $flexiSkin = null, bool $active = true ): ?FlexiSkin {
		if ( !$flexiSkin ) {
			return null;
		}
		$active ? $flexiSkin->activate() : $flexiSkin->deactivate();

		return $this->save( $flexiSkin );
	}

	/**
	 * @param array $config
	 * @return FlexiSkin
	 */
	public function createNew( array $config = [] ): FlexiSkin {
		$existingSkin = $this->getLocalSkin();
		if ( $existingSkin === null ) {
			return new FlexiSkin( null, $config );
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
	 * @return array
	 */
	public function getActiveConfig(): array {
		$active = $this->getActive();
		if ( !$active instanceof FlexiSkin ) {
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
		foreach ( $this->getPlugins() as $plugin ) {
			$plugin->adaptConfiguration( $config );
		}

		$this->hookContainer->run( 'FlexiSkinAdaptConfiguration', [ $active, &$config ] );
		return $config;
	}

	/**
	 * @return array
	 */
	public function getActiveLessVars(): array {
		try {
			$active = $this->getActive();
		} catch ( Throwable $e ) {
			// DB may be unavailable, e.g. during structure tests
			return [];
		}
		if ( !$active ) {
			return [];
		}
		$vars = [];
		foreach ( $this->getPlugins() as $plugin ) {
			$vars = array_merge( $vars, $plugin->getLessVars( $active ) );
		}
		return $vars;
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
	 * @return array
	 */
	public function getUnsupportedControls(): array {
		return $this->getSkinDefinition()['unsupportedControls'] ?? [];
	}

	/**
	 * @return string[]
	 */
	public function getTabOrder(): array {
		return $this->getSkinDefinition()['tabOrder'] ?? [];
	}

	/**
	 * @return array
	 */
	private function getSkinDefinition(): array {
		$skins = ExtensionRegistry::getInstance()->getAttribute( 'FlexiSkinSkinRegistry' );

		return $skins[$this->getCurrentSkinName()] ?? [];
	}

	/**
	 * @return string
	 */
	private function getCurrentSkinName(): string {
		$context = RequestContext::getMain();
		if ( defined( 'MW_NO_SESSION' ) && MW_NO_SESSION ) {
			return $context->getRequest()->getVal( 'skin', '' );
		}
		return $context->getSkin()->getSkinName();
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
