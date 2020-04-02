window.flexiskin = window.flexiskin || {};
window.flexiskin.ui = window.flexiskin.ui || {};
window.flexiskin.ui.plugin = window.flexiskin.ui.plugin || {};

flexiskin.ui.plugin.Colors = function( config ){

    config = config || {};

    config.items = [
        new OO.ui.FieldLayout( set1bg1, {
            label: 'Background color',
            align: 'top',
            id: 'set-1-bg-1',
            classes: [ 'fs-plugin-colors-field-layout' ]
        } ),
        new OO.ui.FieldLayout( set1bg2, {
            label: 'Background color hover',
            align: 'top',
            id: 'set-1-bg-2',
            classes: [ 'fs-plugin-colors-field-layout' ]
        } ),
        new OO.ui.FieldLayout( set1fg1, {
            label: 'Foreground color',
            align: 'top',
            id: 'set-1-fg-1',
            classes: [ 'fs-plugin-colors-field-layout' ]
        } ),
        new OO.ui.FieldLayout( set1fg2, {
            label: 'Foreground color hover',
            align: 'top',
            id: 'set-1-fg-2',
            classes: [ 'fs-plugin-colors-field-layout' ]
        } )
    ];

    flexiskin.ui.plugin.Colors.parent.call( this, config );
};


OO.inheritClass( flexiskin.ui.plugin.Colors, OO.ui.FormLayout );

flexiskin.ui.plugin.Colors.static.name = 'Colors';
flexiskin.ui.plugin.Colors.static.title = 'Colors';  /* TODO: use msg */

flexiskin.ui.plugin.Colors.prototype.setPluginData = function( config ) {
    config = config || {};

    if ( config.hasOwnProperty( 'Colors' ) ) {
        var values = config.Colors;

        if ( values.hasOwnProperty( 'set1' ) ) {
            if ( values.set1.hasOwnProperty( 'bg1' ) ) {
                set1bg1.setValue( values.set1.bg1 );
            } else {
                set1bg1.setValue( '' );
            }
            if ( values.set1.hasOwnProperty( 'bg2' ) ) {
                set1bg2.setValue( values.set1.bg2 );
            } else {
                set1bg2.setValue( '' );
            }
            if ( values.set1.hasOwnProperty( 'fg1' ) ) {
                set1fg1.setValue( values.set1.fg1 );
            } else {
                set1fg1.setValue( '' );
            }
            if ( values.set1.hasOwnProperty( 'fg2' ) ) {
                set1fg2.setValue( values.set1.fg2 );
            } else {
                set1fg2.setValue( '' );
            }
        }
    }
}

flexiskin.ui.plugin.Colors.prototype.getPluginData = function() {
    return {
        set1: {
            bg1: set1bg1.getValue(),
            bg2: set1bg2.getValue(),
            fg1: set1fg1.getValue(),
            fg2: set1fg2.getValue()
        }
    };
}

flexiskin.ui.plugin.Colors.prototype.initialize = function( config ) {
};

var set1bg1 = new OO.ui.TextInputWidget( {
    classes: ['base'],
    id: 'set1-bg-1-input'
} );
var set1bg2 = new OO.ui.TextInputWidget( {
    classes: ['hover'],
    id: 'set1-bg-2-input'
} );
var set1fg1 = new OO.ui.TextInputWidget( {
    classes: ['base'],
    id: 'set1-fg-1-input'
} );
var set1fg2 = new OO.ui.TextInputWidget( {
    classes: ['hover'],
    id: 'set1-fg-2-input'
} );

var set2bg1 = new OO.ui.TextInputWidget( {
    classes: ['base'],
    id: 'set2-bg'
} );
var set2bg2 = new OO.ui.TextInputWidget( {
    classes: ['hover'],
    id: 'set2-bg-hover'
} );
var set2fg1 = new OO.ui.TextInputWidget( {
    classes: ['base'],
    id: 'set2-fg'
} );
var set2fg2 = new OO.ui.TextInputWidget( {
    classes: ['hover'],
    id: 'set2-fg-hover'
} );

var set3bg1 = new OO.ui.TextInputWidget( {
    classes: ['base'],
    id: 'set3-bg'
} );
var set3bg2 = new OO.ui.TextInputWidget( {
    classes: ['hover'],
    id: 'set3-bg-hover'
} );
var set3fg1 = new OO.ui.TextInputWidget( {
    classes: ['base'],
    id: 'set3-fg'
} );
var set3fg2 = new OO.ui.TextInputWidget( {
    classes: ['hover'],
    id: 'set3-fg-hover'
} );

var set4bg1 = new OO.ui.TextInputWidget( {
    classes: ['base'],
    id: 'set4-bg'
} );
var set4bg2 = new OO.ui.TextInputWidget( {
    classes: ['hover'],
    id: 'set4-bg-hover'
} );
var set4fg1 = new OO.ui.TextInputWidget( {
    classes: ['base'],
    id: 'set4-fg'
} );
var set4fg2 = new OO.ui.TextInputWidget( {
    classes: ['hover'],
    id: 'set4-fg-hover'
} );
