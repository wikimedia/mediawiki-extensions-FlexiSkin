flexiskin.ui.plugin.Layout = function () {
	flexiskin.ui.plugin.Layout.parent.call( this );
};

OO.inheritClass( flexiskin.ui.plugin.Layout, flexiskin.ui.plugin.Plugin );

flexiskin.registry.Plugin.register( 'layout', flexiskin.ui.plugin.Layout );

flexiskin.ui.plugin.Layout.prototype.provideControls = function () {
	return {
		layout: {
			label: mw.message( 'flexiskin-ui-plugin-layout-label' ).text(),
			items: {
				content_width: {
					label: mw.message( 'flexiskin-ui-plugin-layout-content-width-label' ).text(),
					widget: new flexiskin.ui.StyleSizeWidget()
				}
			}
		}
	};
};
