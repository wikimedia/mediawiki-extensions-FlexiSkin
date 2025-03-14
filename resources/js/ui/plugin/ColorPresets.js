flexiskin.ui.plugin.ColorPresets = function () {
	flexiskin.ui.plugin.ColorPresets.parent.call( this );
};

OO.inheritClass( flexiskin.ui.plugin.ColorPresets, flexiskin.ui.plugin.Plugin );

flexiskin.registry.Plugin.register( 'colorPresets', flexiskin.ui.plugin.ColorPresets );

flexiskin.ui.plugin.ColorPresets.prototype.provideControls = function () {
	return {
		colors: {
			label: mw.message( 'flexiskin-ui-plugin-colors-label' ).text(),
			expanded: true,
			color_presets: {
				label: mw.message( 'flexiskin-ui-plugin-color-presets-label' ).text(),
				items: {
					preset: {
						label: '',
						widget: new flexiskin.ui.ColorPresetWidget(),
						actionCallback: {
							init: this.onInit
						}
					}
				}
			}
		}
	};
};

flexiskin.ui.plugin.ColorPresets.prototype.onInit = function ( data ) {
	data = data || {};

	if ( data instanceof flexiskin.ui.Configurator ) {
		data.connect( this, {
			makeFormComplete: function ( formItems ) {
				const colorItems = data.getItems( 'colors' ),
					colorsLayout = formItems.colors || null;

				if ( !colorsLayout ) {
					return;
				}
				const colorsLayoutItems = colorsLayout.getItems();

				for ( let i = 0; i < colorsLayoutItems.length; i++ ) {
					const itemData = colorsLayoutItems[ i ].getData();
					if ( itemData.hasOwnProperty( 'group' ) && itemData.group !== 'color_presets' ) {
						this.addCustomColorLayout( colorsLayoutItems[ i ] );
					}
				}
				this.setColorItems( colorItems );
				this.init();
			}
		} );
	}
};
