window.flexiskin = window.flexiskin || {};
window.flexiskin.ui = window.flexiskin.ui || {};
window.flexiskin.ui.plugin = window.flexiskin.ui.plugin || {};


flexiskin.ui.plugin.Colors = function( config ){

    config = config || {};

    config.items = [
        new OO.ui.FieldsetLayout( {
            label: 'Theme colors',
            classes: [ 'fs-plugin-Colors' ],
            items: [
                new OO.ui.FieldsetLayout( {
                    label: 'Custom menu',
                    classes: [ 'fs-plugin-Colors-cm' ],
                    items: [
                        new OO.ui.FieldLayout( set1bg1, {
                            label: 'Background-color',
                            align: 'top',
                            id: 'set-1-bg-1',
                            classes: [ 'fs-plugin-colors-field-layout' ]
                        } ),
                        new OO.ui.FieldLayout( set1bg2, {
                            label: 'Background-color on hover',
                            align: 'top',
                            id: 'set-1-bg-2',
                            classes: [ 'fs-plugin-colors-field-layout' ]
                        } ),
                        new OO.ui.FieldLayout( set1fg1, {
                            label: 'Foreground-color',
                            align: 'top',
                            id: 'set-1-fg-1',
                            classes: [ 'fs-plugin-colors-field-layout' ]
                        } ),
                        new OO.ui.FieldLayout( set1fg2, {
                            label: 'Foreground-color on hover',
                            align: 'top',
                            id: 'set-1-fg-2',
                            classes: [ 'fs-plugin-colors-field-layout' ]
                        } )
                    ]
                } ),
                new OO.ui.FieldsetLayout( {
                    label: 'Navbar',
                    classes: [ 'fs-plugin-Colors-navbar' ],
                    items: [
                        new OO.ui.FieldLayout( set2bg1, {
                            label: 'Background-color',
                            align: 'top',
                            id: 'set-2-bg-1',
                            classes: [ 'fs-plugin-colors-field-layout' ]
                        } ),
                        new OO.ui.FieldLayout( set2fg1, {
                            label: 'Foreground-color',
                            align: 'top',
                            id: 'set-2-fg-1',
                            classes: [ 'fs-plugin-colors-field-layout' ]
                        } ),
                        new OO.ui.FieldLayout( set2fg2, {
                            label: 'Foreground-color on hover',
                            align: 'top',
                            id: 'set-2-fg-2',
                            classes: [ 'fs-plugin-colors-field-layout' ]
                        } )
                    ]
                } ),
                new OO.ui.FieldsetLayout( {
                    label: 'Navbar buttons, navbar dropdowns, left navigation',
                    classes: [ 'fs-plugin-Colors-nav-main' ],
                    items: [
                        new OO.ui.FieldLayout( set3bg1, {
                            label: 'Background color',
                            align: 'top',
                            id: 'set-3-bg-1',
                            classes: [ 'fs-plugin-colors-field-layout' ]
                        } ),
                        new OO.ui.FieldLayout( set3bg2, {
                            label: 'Background color on hover',
                            align: 'top',
                            id: 'set-3-bg-2',
                            classes: [ 'fs-plugin-colors-field-layout' ]
                        } ),
                        new OO.ui.FieldLayout( set3fg1, {
                            label: 'Foreground color',
                            align: 'top',
                            id: 'set-3-fg-1',
                            classes: [ 'fs-plugin-colors-field-layout' ]
                        } ),
                        new OO.ui.FieldLayout( set3fg2, {
                            label: 'Foreground on hover',
                            align: 'top',
                            id: 'set-3-fg-2',
                            classes: [ 'fs-plugin-colors-field-layout' ]
                        } )
                    ]
                } )
            ]
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

        if ( values.hasOwnProperty( 'set2' ) ) {
            if ( values.set2.hasOwnProperty( 'bg1' ) ) {
                set2bg1.setValue( values.set2.bg1 );
            } else {
                set2bg1.setValue( '' );
            }
            if ( values.set2.hasOwnProperty( 'fg1' ) ) {
                set2fg1.setValue( values.set2.fg1 );
            } else {
                set2fg1.setValue( '' );
            }
            if ( values.set2.hasOwnProperty( 'fg2' ) ) {
                set2fg2.setValue( values.set2.fg2 );
            } else {
                set2fg2.setValue( '' );
            }
        }

        if ( values.hasOwnProperty( 'set3' ) ) {
            if ( values.set3.hasOwnProperty( 'bg1' ) ) {
                set3bg1.setValue( values.set3.bg1 );
            } else {
                set3bg1.setValue( '' );
            }
            if ( values.set3.hasOwnProperty( 'bg2' ) ) {
                set3bg2.setValue( values.set3.bg2 );
            } else {
                set3bg2.setValue( '' );
            }
            if ( values.set3.hasOwnProperty( 'fg1' ) ) {
                set3fg1.setValue( values.set3.fg1 );
            } else {
                set3fg1.setValue( '' );
            }
            if ( values.set3.hasOwnProperty( 'fg2' ) ) {
                set3fg2.setValue( values.set3.fg2 );
            } else {
                set3fg2.setValue( '' );
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
        },
        set2: {
            bg1: set2bg1.getValue(),
            fg1: set2fg1.getValue(),
            fg2: set2fg2.getValue()
        },
        set3: {
            bg1: set3bg1.getValue(),
            bg2: set3bg2.getValue(),
            fg1: set3fg1.getValue(),
            fg2: set3fg2.getValue()
        }
    };
}

flexiskin.ui.plugin.Colors.prototype.initialize = function( config ) {
};

var  = new OO.ui.TextInputWidget( {
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
    id: 'set2-bg-1-input'
} );
var set2fg1 = new OO.ui.TextInputWidget( {
    classes: ['base'],
    id: 'set2-fg-1-input'
} );
var set2fg2 = new OO.ui.TextInputWidget( {
    classes: ['hover'],
    id: 'set2-fg-2-input'
} );

var set3bg1 = new OO.ui.TextInputWidget( {
    classes: ['base'],
    id: 'set3-bg-1-input'
} );
var set3bg2 = new OO.ui.TextInputWidget( {
    classes: ['hover'],
    id: 'set3-bg-2-input'
} );
var set3fg1 = new OO.ui.TextInputWidget( {
    classes: ['base'],
    id: 'set3-fg-1-input'
} );
var set3fg2 = new OO.ui.TextInputWidget( {
    classes: ['hover'],
    id: 'set3-fg-2-input'
} );
