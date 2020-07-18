( function( $, mw ) {
	$( function() {
		$( '#fs-container' ).append(
			new flexiskin.ui.Configurator( {
				skin: mw.config.get( 'wgFlexiSkin' )
			}
		).$element );
	} );
} )( jQuery, mediaWiki );
