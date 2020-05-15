window.flexiskin = window.flexiskin || {};
window.flexiskin.ui = window.flexiskin.ui || {};
window.flexiskin.ui.plugin = window.flexiskin.ui.plugin || {};
return;
flexiskin.ui.plugin.WikiBackground = function( config ){

    config = config || {};

    config.items = [
        new OO.ui.FieldLayout( bgColor, {
            label: 'Background color',
            align: 'top',
            id: 'body-bg',
            classes: [ 'fs-plugin-wikibackground-field-layout' ]
        } )
    ];

    flexiskin.ui.plugin.WikiBackground.parent.call( this, config );
};


OO.inheritClass( flexiskin.ui.plugin.WikiBackground, OO.ui.FormLayout );

flexiskin.ui.plugin.WikiBackground.static.name = 'WikiBackground';
flexiskin.ui.plugin.WikiBackground.static.title = 'WikiBackground';  /* TODO: use msg */

flexiskin.ui.plugin.WikiBackground.prototype.setPluginData = function( config ) {
    config = config || {};

    bgColor.setValue( '' );
    if ( config.hasOwnProperty( 'WikiBackground' ) ) {
        var values = config.WikiBackground;

        if ( values.hasOwnProperty( 'bg' ) ) {
            bgColor.setValue( values.bg );
        }
    }
}

flexiskin.ui.plugin.WikiBackground.prototype.getPluginData = function() {
    return {
        bg: bgColor.getValue()
    };
}

flexiskin.ui.plugin.WikiBackground.prototype.initialize = function( config ) {
};

var bgColor = new OO.ui.TextInputWidget( {
    id: 'wikibackground-bg-input'
} );

