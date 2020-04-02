window.flexiskin = window.flexiskin || {};
window.flexiskin.ui = window.flexiskin.ui || {};
window.flexiskin.ui.plugin = window.flexiskin.ui.plugin || {};

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
        new OO.ui.FieldLayout( LogoAndFaviconInputLogo, {
            label: 'Logo',
            align: 'top'
        } ),
        new OO.ui.FieldLayout( LogoAndFaviconInputFavicon, {
            label: 'Favicon',
            align: 'top'
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
        var values = JSON.parse(config.LogoAndFavicon);

        if ( values.hasOwnProperty( 'logo' ) ) {
            if ( values.logo.hasOwnProperty( 'data' ) ) {
                logo.data.setValue( values.logo.data );
                $('#logo-input-zone').css( 'background-image', 'url("' + logo.data + '")' );
            } else {
                logo.data.setValue( '' );
            }
            if ( values.logo.hasOwnProperty( 'name' ) ) {
                logo.name.setValue( values.logo.name );
            } else {
                logo.name.setValue( '' );
            }
            if ( values.logo.hasOwnProperty( 'type' ) ) {
                logo.type.setValue( values.logo.type );
            } else {
                logo.type.setValue( '' );
            }
        }

        if ( values.hasOwnProperty( 'favicon' ) ) {
            if ( values.set1.hasOwnProperty( 'data' ) ) {
                favicon.setValue( values.favicon.data );
            } else {
                favicon.setValue( '' );
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
    classes: ['fg-neutral2', 'bg-neutral2']
} );
var LogoAndFaviconInputFavicon = new OO.ui.TextInputWidget( {
    placeholder: 'Favicon',
    id: 'fs-favicon-input',
    disabled: true,
    placeholder: ''
} );

$( function($) {
    /* logo */
    var $logoDropZone = $('#logo-input input');
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
} );