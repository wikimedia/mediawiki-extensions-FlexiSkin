flexiskin.ui.plugin.Colors = function(){
	flexiskin.ui.plugin.Colors.parent.call( this );
};

OO.inheritClass( flexiskin.ui.plugin.Colors, flexiskin.ui.plugin.Plugin );

flexiskin.registry.Plugin.register( 'colors', flexiskin.ui.plugin.Colors );

flexiskin.ui.plugin.Colors.prototype.provideControls = function() {
	return {
		colors: {
			label: 'Colors',
			custom_menu: {
				label: "Custom menu",
				items: {
					bg1: {
						label: 'Background color',
						widget: new OOJSPlus.ui.widget.HexColorPickerWidget( { enableCustomPicker: true } ),
						visibility: flexiskin.ui.Configurator.static.VISIBILITY_BASIC
					},
					bg2: {
						label: 'Background color on hover',
						widget: new OOJSPlus.ui.widget.HexColorPickerWidget( { enableCustomPicker: true } )
					},
					fg1: {
						label: 'Background color on hover',
						widget: new OOJSPlus.ui.widget.HexColorPickerWidget( { enableCustomPicker: true } )
					},
					fg2: {
						label: 'Background color on hover',
						widget: new OOJSPlus.ui.widget.HexColorPickerWidget( { enableCustomPicker: true } )
					},
					fg3: {
						label: 'Text colors',
						items: {
							tc1: {
								label: 'Unselected',
								widget: new OOJSPlus.ui.widget.HexColorPickerWidget( { enableCustomPicker: true } )
							},
							tc2: {
								label: 'Selected',
								widget: new OOJSPlus.ui.widget.HexColorPickerWidget( { enableCustomPicker: true } )
							}
						}
					}
				}
			}
		}
	};
};
