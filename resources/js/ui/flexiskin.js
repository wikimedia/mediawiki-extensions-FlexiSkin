$( () => {
	$( '#fs-sp-loading' ).remove();
	$( '#fs-container' ).append(
		new flexiskin.ui.Configurator( {
			skin: mw.config.get( 'wgFlexiSkin' ),
			inheritance: mw.config.get( 'wgFlexiSkinInheritance' ),
			unsupportedControls: mw.config.get( 'wgFlexiSkinUnsupportedControls' )
		}
		).$element );

	if ( $( document ).find( '#fs-skeleton-cnt' ) ) {
		$( '#fs-skeleton-cnt' ).empty();
	}
} );
