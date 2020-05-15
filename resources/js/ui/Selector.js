( function( $, mw ) {
	window.flexiskin = window.flexiskin || {};
	window.flexiskin.ui = window.flexiskin.ui || {};

	flexiskin.ui.Selector = function( cfg ) {
		cfg = cfg || {};
		this.manager = cfg.manager;

		flexiskin.ui.Selector.parent.call( this );
		this.$element = cfg.$element || $( '<div>' ).attr( 'id', 'fs-select' );
		this.actionButtons = {};
	};

	OO.inheritClass( flexiskin.ui.Selector, OO.ui.Widget );

	flexiskin.ui.Selector.static.actions = {
		new: {
			label: 'New skin',
			framed: false,
			flags: [
				'primary',
				'progressive'
			]
		},
		delete: {
			label: 'Delete',
			flags: [
				'primary',
				'destructive'
			]
		},
		activate: {
			label: 'Activate',
			flags: [
				'primary',
				'progressive'
			]
		},
		restore: {
			label: 'Restore'
		}
	};

	flexiskin.ui.Selector.prototype.initialize = function() {
		this.makeActiveSkinLabel();
		this.makeButtons();
		this.makeSkinSelector();
		this.skinSelectorLayout =  new OO.ui.FieldLayout( this.skinSelector, {
			label: 'Manage skins',
			align: 'top'
		} );
		this.$element.append( this.skinSelectorLayout.$element );
		this.addActions();
		if ( this.manager.getAvailableSkins().length === 0 ) {
			this.skinSelector.setDisabled( true );
		}
	};

	flexiskin.ui.Selector.prototype.makeActiveSkinLabel = function() {
		var $container = $( '<div>' ).addClass( 'active-skin-container' ),
			text, label;

		if ( $.isEmptyObject( this.manager.getAvailableSkins() ) ) {
			text = mw.message( 'flexiskin-ui-no-skins' ).text();
		} else if ( this.manager.getActiveSkin() ) {
			text = mw.message( 'flexisin-ui-active-skin', this.manager.getActiveSkin()['name'] ).text();
		} else {
			text = mw.message( 'flexiskin-ui-no-active-skin' ).text();
		}

		label = new OO.ui.LabelWidget( {
			label: text
		} );

		$container.append( label.$element );
		this.$element.append( $container );
	};

	flexiskin.ui.Selector.prototype.makeSkinSelector = function() {
		this.skinSelector = null;
		var availableSkins = this.manager.getAvailableSkins(),
			menuItems = [];
		if ( !$.isEmptyObject( availableSkins ) ) {
			for ( var id in availableSkins ) {
				if ( !availableSkins.hasOwnProperty( id ) ) {
					continue;
				}
				var optionData = {
					data: parseInt( id ),
					label: availableSkins[id].name,
					classes: availableSkins[id].deleted ? [ 'option-disabled' ] : []
				};
				if ( this.manager.getActiveSkin() && parseInt( id ) === this.manager.getActiveSkin().id ) {
					optionData.icon = 'check';
					optionData.classes = [ 'fs-option-active' ];
				}
				menuItems.push( new OO.ui.MenuOptionWidget( optionData ) );
			}
		}

		this.skinSelector = new OO.ui.DropdownWidget( {
			menu: { items: menuItems },
			label: 'Select skin' // TODO: i18n
		} );
		if ( this.manager.getActiveSkin() !== null ) {
			this.skinSelector.getMenu().selectItemByData( this.manager.getActiveSkin().id );
			this.onSkinSelected( this.skinSelector.getMenu().findItemFromData( this.manager.getActiveSkin().id ) );
		}
		this.skinSelector.getMenu().connect( this, {
			select: 'onSkinSelected'
		} );

		this.setAbilities();
	};

	flexiskin.ui.Selector.prototype.onSkinSelected = function( item ) {
		if ( !item ) {
			return;
		}
		var id = item.getData(),
			skin = this.manager.getSkin( id );

		if ( !skin ) {
			return;
		}
		this.emit( 'skinSelected', skin );

		this.setAbilities();
	};

	flexiskin.ui.Selector.prototype.addActions = function() {
		var layout = new OO.ui.HorizontalLayout( {
			classes: [ 'skin-selector-actions' ]
		} );
		for ( var id in this.actionButtons ) {
			if ( !this.actionButtons.hasOwnProperty( id ) ) {
				continue;
			}
			layout.$element.append( this.actionButtons[id].$element );
		}

		this.$element.append( layout.$element );
	};

	flexiskin.ui.Selector.prototype.setAbilities = function() {
		var selected = this.getCurrent();
		if ( !selected ) {
			return this.doSetAbilitites( [ 'new' ] );
		}
		if ( selected.deleted ) {
			return this.doSetAbilitites( [ 'new', 'restore' ] );
		}
		if ( this.manager.isActive( selected.id ) ) {
			return this.doSetAbilitites( [ 'new', 'delete' ] );
		}
		return this.doSetAbilitites( [ 'new', 'delete', 'activate' ] );
	};

	flexiskin.ui.Selector.prototype.doSetAbilitites = function( abilities ) {
		for ( var id in this.actionButtons ) {
			if ( !this.actionButtons.hasOwnProperty( id ) ) {
				continue;
			}
			if ( abilities.indexOf( id ) === -1 ) {
				this.actionButtons[id].setDisabled( true );
			} else {
				this.actionButtons[id].setDisabled( false );
			}
		}
	};

	flexiskin.ui.Selector.prototype.makeButtons = function() {
		var me = this, config, id, button;
		for ( id in flexiskin.ui.Selector.static.actions ) {
			config = flexiskin.ui.Selector.static.actions[id];

			button = new OO.ui.ButtonWidget( $.extend( {
				data: {
					action: id
				}
			}, config ) );
			button.connect( button, {
				click: function() {
					var data = this.getData();
					if ( data.hasOwnProperty( 'action' ) ) {
						me.executeAction( data.action );
					}
				}
			} );
			this.actionButtons[id] = button;
		}
	};

	flexiskin.ui.Selector.prototype.executeAction = function( action ) {
		switch ( action ) {
			case 'new':
				this.makeSkin();
				break;
			case 'delete':
				this.operationOnCurrent( 'flexiskin-delete', true, 'Failed to delete the skin!' );
				break;
			case 'activate':
				this.operationOnCurrent( 'flexiskin-activate', false, 'Failed to activate the skin!' );
				break;
			case 'restore':
				this.operationOnCurrent( 'flexiskin-restore', true, 'Failed to restore the skin!' );
				break;
		}
	};

	flexiskin.ui.Selector.prototype.makeSkin = function() {
		var dialog = new flexiskin.ui.dialog.NewSkin ( {
			actionId: 'flexiskin-new',
			name: ''
		} );

		var windowManager = OO.ui.getWindowManager();
		windowManager.addWindows( [ dialog ] );
		var newDlg = windowManager.openWindow( dialog );

		newDlg.closed.then( function ( data ) {
			if ( typeof data === 'undefined' ) {
				return;
			}
			if ( data === null ) {
				OO.ui.alert( "Failed to create skin!" );
				return;
			}
			var item = new OO.ui.MenuOptionWidget( {
				data: data.id,
				label: data.name
			} );

			if ( this.manager.addSkin( data ) ) {
				this.skinSelector.getMenu().addItems( [ item ] );
				this.skinSelector.getMenu().selectItemByData( data.id );
				this.skinSelector.setDisabled( false );
				this.onSkinSelected( item );
			}

		}.bind( this ) );
	};

	flexiskin.ui.Selector.prototype.operationOnCurrent = function( action, prompt, failMessage ) {
		prompt = prompt || false;
		failMessage = failMessage || '';
		if ( prompt ) {
			OO.ui.confirm( 'Are you sure?' ).done( function ( confirmed ) {
				if ( confirmed ) {
					this.executeOperationOnCurrent( action, failMessage );
				}
			}.bind( this ) );
		} else {
			this.executeOperationOnCurrent( action, failMessage );
		}
	};

	flexiskin.ui.Selector.prototype.executeOperationOnCurrent = function( action, failMessage ) {
		var currentId = this.getCurrentId();
		if ( !currentId ) {
			// Sanity
			return;
		}
		this.doSetAbilitites( [] );
		new mw.Api().get( {
			action: action,
			id: currentId
		} ).done( function( response ) {
			if ( response.hasOwnProperty( 'success' ) && !!response.success ) {
				window.location.reload();
			}
		} ).fail( function() {
			this.setAbilities();
			OO.ui.alert( failMessage );
		}.bind ( this ) );
	};

	flexiskin.ui.Selector.prototype.getCurrentId = function() {
		var selected = this.skinSelector.getMenu().findSelectedItem();
		return selected ? selected.getData() : null;
	};

	flexiskin.ui.Selector.prototype.getCurrent = function() {
		var id = this.getCurrentId(),
			availableSkins = {};
		if ( !id ) {
			return null;
		}
		availableSkins = this.manager.getAvailableSkins();
		for ( var skinId in availableSkins ) {
			if ( !availableSkins.hasOwnProperty( skinId ) ) {
				continue;
			}
			if ( parseInt( skinId ) === id ) {
				return availableSkins[id];
			}
		}

		return null;
	};
} )( jQuery, mediaWiki );
