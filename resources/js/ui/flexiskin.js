$( () => {
	$( '#fs-sp-loading' ).remove();
	$( '#fs-container' ).append(
		new flexiskin.ui.Configurator( {
			skin: mw.config.get( 'wgFlexiSkin' )
		}
		).$element );
} );
