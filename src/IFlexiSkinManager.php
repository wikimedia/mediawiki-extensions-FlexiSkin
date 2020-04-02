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
	 * @return IFlexiSkin[]
	 */
	public function getList(): array;

	/**
	 * @param string $name
	 * @param array $config
	 * @return IFlexiSkin
	 */
	public function create( $name, $config ) : IFlexiSkin;
}
