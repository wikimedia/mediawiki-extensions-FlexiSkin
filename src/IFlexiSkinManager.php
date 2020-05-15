<?php

namespace MediaWiki\Extension\FlexiSkin;

interface IFlexiSkinManager {

	/**
	 * @param IFlexiSkin $flexiSkin
	 * @return int
	 */
	public function save( IFlexiSkin $flexiSkin );

	/**
	 * @param int $id
	 * @return IFlexiSkin|null
	 */
	public function loadFromId( $id );

	/**
	 * @param IFlexiSkin $flexiSkin
	 * @return bool
	 */
	public function setActive( IFlexiSkin $flexiSkin );

	/**
	 * @return int|null
	 */
	public function getActive();

	/**
	 * @param IFlexiSkin $flexiSkin
	 * @return bool
	 */
	public function delete( IFlexiSkin $flexiSkin );

	/**
	 * @param bool|null $includeDeleted
	 * @return IFlexiSkin[]
	 */
	public function getList( $includeDeleted = false ): array;

	/**
	 * @param string $name
	 * @param array $config
	 * @return IFlexiSkin
	 */
	public function create( $name, $config ) : IFlexiSkin;

	/**
	 * Un-delete the skin
	 *
	 * @param IFlexiSkin $skin
	 * @return bool
	 */
	public function restore( IFlexiSkin $skin ) : bool;
}
