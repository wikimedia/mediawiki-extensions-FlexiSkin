( function( $, mw ) {
	window.flexiskin = window.flexiskin || {};
	window.flexiskin.ui = window.flexiskin.ui || {};

	flexiskin.ui.Configurator = function( cfg ) {
		cfg = cfg || {};
		this.manager = cfg.manager;
		this.skin = cfg.skin;
		this.skin.config = this.skin.config || {};
		// Items can be grouped based on several criteria:
		// - plugin - plugin names are keys
		// - group - root groups of controls are keys
		// - visibility - group by basic/advanced
		this.grouping = cfg.grouping || 'group';

		flexiskin.ui.Configurator.parent.call( this );
		this.$element = cfg.$element || $( '<div>' ).attr( 'id', 'fs-configure' );

		this.initPlugins();
		this.setValue( this.skin.config );
		this.makeForm();
		this.makeButtons();
	};

	OO.inheritClass( flexiskin.ui.Configurator, OO.ui.Widget );


	flexiskin.ui.Configurator.static.VISIBILITY_BASIC = 'basic';
	flexiskin.ui.Configurator.static.VISIBILITY_ADVANCED = 'advanced';

	flexiskin.ui.Configurator.prototype.initPlugins = function() {
		this.plugins = {};
		this.controls = {};
		this.items = {};
		for ( var pluginId in flexiskin.registry.Plugin.registry ) {
			if ( !flexiskin.registry.Plugin.registry.hasOwnProperty( pluginId ) ) {
				continue;
			}

			this.plugins[pluginId] = new flexiskin.registry.Plugin.registry[ pluginId ]();
			this.items = $.extend( true, {}, this.items, this.plugins[pluginId].getFlatList() );
			this.controls = $.extend( true, {}, this.controls, this.plugins[pluginId].getControls() );
		}
	};

	flexiskin.ui.Configurator.prototype.makeForm = function() {
		var grouped = this.groupItems(),
			pages = [];
		for ( var groupId in grouped ) {
			if ( !grouped.hasOwnProperty( groupId ) ) {
				continue;
			}
			var layouts = this.getGroupLayouts( grouped[groupId] );
			pages.push( new flexiskin.ui.BookletPage( groupId, {
				label: this.getItemGroupLabel( groupId ),
				items: layouts
			} ) );
		}

		var booklet = new OO.ui.BookletLayout( {
			outlined: true,
			expanded: false
		} );

		booklet.addPages( pages );
		this.$element.append( booklet.$element );
	};

	flexiskin.ui.Configurator.prototype.getGroupLayouts = function( items ) {
		var fieldLayouts = {},
			roots = [];
		for ( var id in items ) {
			if ( !items.hasOwnProperty( id ) ) {
				continue;
			}
			var groups = this.getGroupsForItem( id ),
				rootGroup;
			if ( this.grouping === 'group' ) {
				rootGroup = groups[0];
				// Remove root group since we already group by it
				if ( groups.length > 1 ) {
					groups.shift();
				}
			}
			var lastGroup;
			for ( var i = 0; i < groups.length; i++ ) {
				if ( i === 0 ) {
					roots.push( groups[i] );
				}
				var layout = fieldLayouts[groups[i]] || new OO.ui.FieldsetLayout( {
					label: rootGroup && rootGroup === groups[i] ? '' : this.getItemGroupLabel( groups[i] )
				} );
				if ( i === ( groups.length - 1 ) ) {
					var data = items[id].getData(), label = '';
					if ( data && data.hasOwnProperty( 'label' ) ) {
						label = data.label;
					}
					layout.addItems( new OO.ui.FieldLayout( items[id], {
						label: label,
						align: 'left'
					} ) );
				}
				if ( !fieldLayouts.hasOwnProperty( groups[i] ) ) {
					fieldLayouts[groups[i]] = layout;
					if ( lastGroup && fieldLayouts.hasOwnProperty( lastGroup ) ) {
						fieldLayouts[lastGroup].addItems( [ layout ] );
					}
				}
				lastGroup = groups[i];
			}
		}

		for ( var layoutGroupId in fieldLayouts ) {
			if ( roots.indexOf( layoutGroupId ) === -1 ) {
				delete( fieldLayouts[layoutGroupId] );
			}
		}
		return Object.values( fieldLayouts );
	};

	flexiskin.ui.Configurator.prototype.groupItems = function() {
		var grouped = {},
			pluginId, itemId, groups, root, visibility;
		switch ( this.grouping ) {
			case 'plugin':
				for ( pluginId in this.plugins ) {
					if ( !this.plugins.hasOwnProperty( pluginId ) ) {
						continue;
					}
					grouped[pluginId] = this.plugins[pluginId].getFlatList();
				}
				break;
			case 'group':
				for ( itemId in this.items ) {
					if ( !this.items.hasOwnProperty( itemId ) ) {
						continue;
					}
					groups = this.getGroupsForItem( itemId );
					root = groups.shift();

					if ( !grouped.hasOwnProperty( root ) ) {
						grouped[root] = {};
					}
					grouped[root][itemId] = this.items[itemId];
				}
				break;
			case 'visibility':
				for ( itemId in this.items ) {
					if ( !this.items.hasOwnProperty( itemId ) ) {
						continue;
					}
					visibility = this.getVisibilityForItem( itemId );

					if ( !grouped.hasOwnProperty( visibility ) ) {
						grouped[visibility] = {};
					}
					grouped[visibility][itemId] = this.items[itemId];
				}

				break;
		}

		return grouped;
	};

	flexiskin.ui.Configurator.prototype.getGroupsForItem = function( id ) {
		var bits = id.split( '/' );
		bits.pop();
		if ( bits.length === 0 ) {
			return [];
		}

		return bits;
	};

	flexiskin.ui.Configurator.prototype.getVisibilityForItem = function( id ) {
		var widget = this.items[id];
		var data = widget.getData();
		if ( data && data.hasOwnProperty( 'visibility' ) ) {
			return data.visibility;
		}
		return flexiskin.ui.Configurator.static.VISIBILITY_ADVANCED;
	};

	flexiskin.ui.Configurator.prototype.getItemGroupLabel = function( group ) {
		var item = this.findGroupByKey( this.controls, group );

		if ( item && item.hasOwnProperty( 'label' ) ) {
			return item.label;
		}

		return '';
	};

	flexiskin.ui.Configurator.prototype.findGroupByKey = function( obj, key ) {
		var keys = Object.keys( obj ),
			value;

		for ( var i = 0; i < keys.length; i++ ) {
			if ( keys[i] === key ) {
				return obj[key];
			} else if ( !( obj[keys[i]] instanceof OO.ui.Widget ) && $.type( obj[keys[i]] ) === 'object' ) {
				value = this.findGroupByKey( obj[keys[i]], key );
			}
		}

		return value;
	};

	flexiskin.ui.Configurator.prototype.getValue = function() {
		var value = {};
		for ( var itemId in this.items ) {
			this.setToPath( value, itemId.split( '/' ) , this.items[itemId].getValue() );
		}

		return value;
	};

	flexiskin.ui.Configurator.prototype.setValue = function( value ) {
		if ( !value ) {
			return;
		}

		for ( var itemId in this.items ) {
			var itemValue = this.getByPath( $.extend( true, {}, value ), itemId.split( '/' ) );
			if ( itemValue ) {
				this.items[itemId].setValue( itemValue );
			}
		}
	};

	flexiskin.ui.Configurator.prototype.getByPath = function( obj, path ) {
		var bit = path.shift();
		if ( $.type( obj ) !== 'object' || !obj.hasOwnProperty( bit ) ) {
			return null;
		}
		if ( path.length === 0  ) {
			return obj[bit];
		}
		obj = obj[bit];
		return this.getByPath( obj, path );
	};

	flexiskin.ui.Configurator.prototype.setToPath = function( obj, path, value, append ) {
		var bit = path.shift();
		if ( path.length === 0 ) {
			if ( append ) {
				obj[bit] = obj[bit] || [];
				obj[bit].push( value );
			} else {
				obj[bit] = value;
			}
			return obj;
		}
		obj[bit] = obj[bit] || {};
		this.setToPath( obj[bit], path, value, append );
	};

	flexiskin.ui.Configurator.prototype.makeButtons = function() {
		this.previewButton = new OO.ui.ButtonWidget( {
			label: 'Preview'
		} );

		this.saveButton = new OO.ui.ButtonWidget( {
			label: 'Save',
			flags: [
				'progressive',
				'primary'
			]
		} );

		this.previewButton.connect( this, { click: 'doPreview' } );
		this.saveButton.connect( this, { click: 'doSave' } );

		this.$element.append( new OO.ui.HorizontalLayout( {
			items: [ this.previewButton, this.saveButton ],
			classes: [ 'fs-configurator-buttons' ]
		} ).$element );
	};

	flexiskin.ui.Configurator.prototype.doPreview = function() {
		this.clearPreview();

		this.previewId = 'fs-preview-' + Math.floor(Math.random() * 100) + 1;

		new mw.Api().get( {
			action: 'flexiskin-preview',
			id: this.previewId,
			config: JSON.stringify( this.getValue() ),
		} ).done( function( response ) {
			if ( response.hasOwnProperty( 'preview' ) ) {
				$( 'body' ).append( $( '<style>' ).html( response.preview ) ).addClass( this.previewId );
			}
		}.bind( this ) ).fail( function() {
			OO.ui.alert( 'Failed to preview the skin' );
		} );
	};

	flexiskin.ui.Configurator.prototype.doSave = function() {
		this.clearPreview();

		new mw.Api().get( {
			action: 'flexiskin-save',
			id: this.skin.id,
			config: JSON.stringify( this.getValue() )
		} ).done( function( response ) {
			if ( !response.hasOwnProperty( 'success' ) || !response.success ) {
				this.saveError();
				return;
			}
			if ( this.manager.getActiveSkin() && this.manager.getActiveSkin().id === this.skin.id ) {
				// Refresh to apply changes
				window.location.reload();
			}
		}.bind( this ) ).fail( function() {
			this.saveError();
		}.bind( this ) );
	};

	flexiskin.ui.Configurator.prototype.clearPreview = function() {
		if ( this.previewId ) {
			$( 'body' ).removeClass( this.previewId );
		}
	};

	flexiskin.ui.Configurator.prototype.saveError = function() {
		OO.ui.alert( 'Changes cannot be saved!' );
	};

} )( jQuery, mediaWiki );
