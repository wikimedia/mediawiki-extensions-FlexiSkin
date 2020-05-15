( function( $, mw ) {
	window.flexiskin = window.flexiskin || {};
	window.flexiskin.ui = window.flexiskin.ui || {};

	flexiskin.ui.Management = function( cfg ) {
		cfg = cfg || {};

		this.$element = cfg.$element;

		this.availableSkins = mw.config.get( 'flexiskins' );
		var activeId = mw.config.get( 'flexiskinId' );
		this.activeSkin = null;
		if ( activeId ) {
			this.selectActive( activeId );
		}

		this.selector = new flexiskin.ui.Selector( {
			manager: this
		} );
		this.selector.connect( this, {
			skinSelected: 'onSkinSelected'
		} );
		this.$element.append( this.selector.$element );
		this.selector.initialize();
	};

	OO.initClass( flexiskin.ui.Management );

	flexiskin.ui.Management.prototype.selectActive = function( id ) {
		if ( this.availableSkins.hasOwnProperty( id ) ) {
			this.activeSkin = this.availableSkins[id];
			return;
		}

		this.activeSkin = null;
	};

	flexiskin.ui.Management.prototype.isActive = function( id ) {
		id = parseInt( id );
		return this.activeSkin && id === this.activeSkin.id;
	};

	flexiskin.ui.Management.prototype.onSkinSelected = function( skin ) {
		if ( !skin.hasOwnProperty( 'id' ) ) {
			return;
		}
		if ( !this.availableSkins.hasOwnProperty( skin.id ) )  {
			return;
		}
		this.configureSkin( skin.id );
	};

	flexiskin.ui.Management.prototype.getAvailableSkins = function() {
		return this.availableSkins;
	};

	flexiskin.ui.Management.prototype.getSkin = function( id ) {
		id = parseInt( id );
		if ( this.availableSkins.hasOwnProperty( id ) ) {
			return this.availableSkins[id];
		}

		return null;
	};

	flexiskin.ui.Management.prototype.addSkin = function( skin ) {
		if ( skin.hasOwnProperty( 'id' ) ) {
			if ( this.availableSkins.hasOwnProperty( skin.id ) ) {
				return false;
			}
			this.availableSkins[skin.id] = skin;
			return true;
		}
		return false;
	};

	flexiskin.ui.Management.prototype.getActiveSkin = function() {
		return this.activeSkin;
	};

	flexiskin.ui.Management.prototype.configureSkin = function( id ) {
		var skin = this.availableSkins[id];
		if ( this.configurator ) {
			// TODO: Check for dirty... prompt...
			this.configurator.$element.remove();
		}
		if ( skin.deleted ) {
			return;
		}
		this.configurator = new flexiskin.ui.Configurator( {
			skin: skin,
			manager: this
		} );
		this.configurator.$element.insertAfter( this.selector.$element );
	};

} )( jQuery, mediaWiki );
