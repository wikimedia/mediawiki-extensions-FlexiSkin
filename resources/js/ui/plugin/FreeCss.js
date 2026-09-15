flexiskin.ui.plugin.FreeCss = function () {
	flexiskin.ui.plugin.FreeCss.parent.call( this );
};

OO.inheritClass( flexiskin.ui.plugin.FreeCss, flexiskin.ui.plugin.Plugin );

flexiskin.registry.Plugin.register( 'free-css', flexiskin.ui.plugin.FreeCss );

flexiskin.ui.plugin.FreeCss.prototype.provideControls = function () {
	return {
		free_css: {
			label: mw.message( 'flexiskin-ui-plugin-free-css-label' ).text(),
			items: {
				css: {
					label: '',
					widget: new OO.ui.MultilineTextInputWidget( {
						rows: 10,
						classes: [ 'flexiskin-plugin-free-css-input' ]
					} )
				}
			}
		}
	};
};
