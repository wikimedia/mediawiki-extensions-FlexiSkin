window.flexiskin = window.flexiskin || {};
window.flexiskin.ui = window.flexiskin.ui || {};
window.flexiskin.ui.plugin = window.flexiskin.ui.plugin || {};
return;
var logo = {
        name: '',
        type: '',
        data: ''
    };
var favicon = {
        name: '',
        type: '',
        data: ''
    };

flexiskin.ui.plugin.LogoAndFavicon = function( config ){

    config = config || {};

    config.items = [
        new OO.ui.FieldsetLayout( {
            label: 'Logo and favicon',
            classes: [ 'fs-plugin-LogoAndFavicon' ],
            items: [
                new OO.ui.FieldLayout( LogoAndFaviconInputLogo, {
                    label: 'Logo',
                    align: 'top'
                } ),
                new OO.ui.FieldLayout( LogoAndFaviconInputFavicon, {
                    label: 'Favicon',
                    align: 'top'
                } )
            ]
        } )
    ];

    flexiskin.ui.plugin.LogoAndFavicon.parent.call( this, config );
};

OO.inheritClass( flexiskin.ui.plugin.LogoAndFavicon, OO.ui.FormLayout );

flexiskin.ui.plugin.LogoAndFavicon.static.name = 'LogoAndFavicon';
flexiskin.ui.plugin.LogoAndFavicon.static.title = 'LogoAndFavicon'; /* TODO: use msg */

flexiskin.ui.plugin.LogoAndFavicon.prototype.setPluginData = function( config ) {
    config = config || {};

    if ( config.hasOwnProperty( 'LogoAndFavicon' ) ) {
        var values = config.LogoAndFavicon;

        if ( values.hasOwnProperty( 'logo' ) ) {
            if ( values.logo.hasOwnProperty( 'data' ) ) {
                logo.data = values.logo.data;
                $('#fs-logo-input input').css( 'background-image', 'url("' + logo.data + '")' );
            }
            if ( values.logo.hasOwnProperty( 'name' ) ) {
                logo.name = values.logo.name;
            }
            if ( values.logo.hasOwnProperty( 'type' ) ) {
                logo.type = values.logo.type;
            }
        }

        if ( values.hasOwnProperty( 'favicon' ) ) {
            if ( values.favicon.hasOwnProperty( 'data' ) ) {
                favicon.data = values.favicon.data;
                $('#fs-logo-input input').css( 'background-image', 'url("' + favicon.data + '")' );
            }
            if ( values.logo.hasOwnProperty( 'name' ) ) {
                favicon.name = values.favicon.name;
            }
            if ( values.logo.hasOwnProperty( 'type' ) ) {
                favicon.type = values.favicon.type;
            }
        }
    }
}

flexiskin.ui.plugin.LogoAndFavicon.prototype.getPluginData = function( config ) {
    return {
        logo: {
            name: logo.name,
            type: logo.type,
            data: logo.data
        }
    };
}
flexiskin.ui.plugin.LogoAndFavicon.prototype.initialize = function( config ) {
};

var LogoAndFaviconInputLogo = new OO.ui.TextInputWidget( {
    placeholder: 'Logo',
    id: 'fs-logo-input',
    readOnly: true,
    placeholder: '',
    classes: []
} );
var LogoAndFaviconInputFavicon = new OO.ui.TextInputWidget( {
    placeholder: 'Favicon',
    id: 'fs-favicon-input',
    readOnly: true,
    placeholder: '',
    classes: []
} );

$( function($) {
    /* logo */
    var $logoDropZone = $('#fs-logo-input input');
    $logoDropZone.on( 'drop', function( event ) {
        event.stopPropagation();
        event.preventDefault();

        event.dataTransfer = event.originalEvent.dataTransfer;
        event.dataTransfer.effectAllowed = 'copy';
        event.dataTransfer.dropEffect = 'copy';

        var files = event.dataTransfer.files;
        var file = files[0];

        logo.name = file.name;
        logo.type = file.type;

        var reader = new FileReader();
        reader.onloadend = function () {
            logo.data = reader.result;
            $logoDropZone.css( 'background-image', 'url("' + logo.data + '")' );
            LogoAndFaviconInputLogo.logo = logo;
         }
        var dataUrl = reader.readAsDataURL( file );

        }
     );
     $logoDropZone.on( 'dragover', function( event ) {
            event.stopPropagation();
            event.preventDefault();
        }
    );

    /* favicon */
    var $faviconDropZone = $('#fs-favicon-input input');
    $faviconDropZone.on( 'drop', function( event ) {
        event.stopPropagation();
        event.preventDefault();

        event.dataTransfer = event.originalEvent.dataTransfer;
        event.dataTransfer.effectAllowed = 'copy';
        event.dataTransfer.dropEffect = 'copy';

        var files = event.dataTransfer.files;
        var file = files[0];

        favicon.name = file.name;
        favicon.type = file.type;

        var reader = new FileReader();
        reader.onloadend = function () {
            favicon.data = reader.result;
            $faviconDropZone.css( 'background-image', 'url("' + favicon.data + '")' );
            LogoAndFaviconInputLogo.favicon = favicon;
         }
        var dataUrl = reader.readAsDataURL( file );

        }
     );
     $faviconDropZone.on( 'dragover', function( event ) {
            event.stopPropagation();
            event.preventDefault();
        }
    );
} );
