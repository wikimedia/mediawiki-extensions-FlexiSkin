flexiskin.ui.plugin.LogoAndFavicon = function(){
	flexiskin.ui.plugin.Colors.parent.call( this );
};

OO.inheritClass( flexiskin.ui.plugin.LogoAndFavicon, flexiskin.ui.plugin.Plugin );

flexiskin.registry.Plugin.register( 'logoAndFavicon', flexiskin.ui.plugin.LogoAndFavicon );

flexiskin.ui.plugin.LogoAndFavicon.prototype.provideControls = function() {
	return {
		logoAndFavicon: {
			label: 'Logo and Favicon',
			items: {
				logo: {
					label: "Logo",
					widget: new OO.ui.SelectFileWidget( {
						showDropTarget: true
					} )
				},
				favicon: {
					label: "Favicon",
					widget: new OO.ui.SelectFileWidget( {
						showDropTarget: true
					} )
				}
			}
		},
		colors: {
			test: {
				label:'Test',
				items: {
					free: {
						label: "From different plugin",
						widget: new OOJSPlus.ui.widget.HexColorPickerWidget( { enableCustomPicker: true } )
					}
				}
			}
		}
	};
};
