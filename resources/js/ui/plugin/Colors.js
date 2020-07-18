flexiskin.ui.plugin.Colors = function() {
	flexiskin.ui.plugin.Colors.parent.call( this );
};

OO.inheritClass( flexiskin.ui.plugin.Colors, flexiskin.ui.plugin.Plugin );

flexiskin.registry.Plugin.register( 'colors', flexiskin.ui.plugin.Colors );

flexiskin.ui.plugin.Colors.prototype.provideControls = function() {
	return {
		colors: {
			label: mw.message( 'flexiskin-ui-plugin-colors-label' ).text(),
			background: {
				label: mw.message( 'flexiskin-ui-plugin-colors-background-label' ).text(),
				items: {
					primary: {
						label: mw.message( 'flexiskin-ui-plugin-colors-background-primary-label' ).text(),
						widget: new OOJSPlus.ui.widget.HexColorPickerWidget( { enableCustomPicker: true } )
					},
					secondary: {
						label: mw.message( 'flexiskin-ui-plugin-colors-background-secondary-label' ).text(),
						widget: new OOJSPlus.ui.widget.HexColorPickerWidget( { enableCustomPicker: true } )
					},
					neutral: {
						label: mw.message( 'flexiskin-ui-plugin-colors-background-neutral-label' ).text(),
						widget: new OOJSPlus.ui.widget.HexColorPickerWidget( { enableCustomPicker: true } )
					},
					content: {
						label: mw.message( 'flexiskin-ui-plugin-colors-background-content-label' ).text(),
						widget: new OOJSPlus.ui.widget.HexColorPickerWidget( { enableCustomPicker: true } )
					}
				}
			},
			foreground: {
				label: mw.message( 'flexiskin-ui-plugin-colors-foreground-label' ).text(),
				items: {
					primary: {
						label:  mw.message( 'flexiskin-ui-plugin-colors-foreground-primary-label' ).text(),
						widget: new OOJSPlus.ui.widget.HexColorPickerWidget( { enableCustomPicker: true } )
					},
					secondary: {
						label: mw.message( 'flexiskin-ui-plugin-colors-foreground-secondary-label' ).text(),
						widget: new OOJSPlus.ui.widget.HexColorPickerWidget( { enableCustomPicker: true } )
					},
					neutral: {
						label: mw.message( 'flexiskin-ui-plugin-colors-foreground-neutral-label' ).text(),
						widget: new OOJSPlus.ui.widget.HexColorPickerWidget( { enableCustomPicker: true } )
					},
					content: {
						label: mw.message( 'flexiskin-ui-plugin-colors-foreground-content-label' ).text(),
						widget: new OOJSPlus.ui.widget.HexColorPickerWidget( { enableCustomPicker: true } )
					}

				}
			}
		}
	};
};
