window.flexiskin = window.flexiskin || {};
window.flexiskin.ui = window.flexiskin.ui || {};
window.flexiskin.ui.plugin = window.flexiskin.ui.plugin || {};
window.flexiskin.ui.plugin.dialog = window.flexiskin.ui.plugin.dialog || {};

var windowManager = OO.ui.getWindowManager();

flexiskin.pluginFactory = new OO.Factory();

var flexiskins = mw.config.get( 'flexiskins' );
var activeId = mw.config.get( 'flexiskinId' );
var activeName = '';
var previewId = 0;

/**
 * flexiskin ui base
 */
var fieldsets = [];
for( var name in flexiskinRegistry ) {
    fieldsets.push( flexiskinRegistry[name] );
}

var form = new OO.ui.FormLayout( {
    items: fieldsets, //Gets dynamically created by RL module classe on the serverside
    action: '/api/formhandler',
    method: 'get'
} )
$( '#fs-configure' ).append( form.$element );


/**
 * flexiskin config
 */
var config = {};

/**
 * Dropdown with available flexiskins
 */
var flexiskinList = [];
flexiskins.forEach( function( value, index, array ) {
    if ( value['id'] == activeId ) {
        activeName = value['name'];
        config = value['config'];
    }
    flexiskinList.push(
        new OO.ui.MenuOptionWidget( {
            data: value,
            label: value['name'],
        } )
    );
});
var flexiskinSelectionDropDown = new OO.ui.DropdownWidget( {
    label: 'empty', /* TODO: use msg */
    menu: {
        items: flexiskinList
    }
});
$('#fs-select').append( flexiskinSelectionDropDown.$element );

/**
 * Buttons
 */
btnNew = new OO.ui.ButtonWidget( {
    label: 'New' /* TODO: use msg */
} ),
btnPreview = new OO.ui.ButtonWidget( {
    label: 'Preview' /* TODO: use msg */
} ),
btnEdit = new OO.ui.ButtonWidget( {
    label: 'Edit' /* TODO: use msg */
} ),
btnSelect = new OO.ui.ButtonWidget( {
    label: 'Select' /* TODO: use msg */
} ),
btnSave = new OO.ui.ButtonWidget( {
    label: 'Save' /* TODO: use msg */
} ),
btnDelete = new OO.ui.ButtonWidget( {
    label: 'Delete' /* TODO: use msg */
} ),
buttonGroup = new OO.ui.ButtonGroupWidget( {
    items: [ btnSelect, btnPreview, btnNew, btnSave, btnDelete ]
} );
$('#fs-select').append( buttonGroup.$element );

/**
 * Subroutines
 */
// Trigger an event when a flexiskin in the dropdonw menu is selected.
itemSelected = function(){
    var flexiskinSelected = flexiskinSelectionDropDown.getMenu().findSelectedItem().getData();
    config = flexiskinSelected.config;
    setPluginData( config );
    makePreview( config );
};
// Trigger a button event
getPluginData = function() {
    var jsonData = {};
    for( var name in flexiskinRegistry ) {
        jsonData[name] = flexiskinRegistry[name].getPluginData();
    }
    return JSON.stringify( jsonData );
}
setPluginData = function( config ) {
    for( var name in flexiskinRegistry ) {
        flexiskinRegistry[name].setPluginData( config );
    }
}
var previewId = 0;
makePreview = function( config ) {
    if ( previewId != 0 ) {
        var bdyClsList = document.getElementsByTagName('body')[0].classList.remove( previewId );
    }

    previewId = 'fs-preview-' + Math.floor(Math.random() * 100) + 1;
    var deferred = $.Deferred();
    var res = new mw.Api().get( {
        action: 'flexiskin-preview',
        id: previewId,
        config: getPluginData(),
    } ).done( function( response ) {
        if ( response.hasOwnProperty( 'preview' ) && response.id !== null ) {
            deferred.resolve( response.preview );
            var style = document.createElement( 'style' );

            if ( style.styleSheet ) {
                style.styleSheet.cssText = response.preview;
            } else {
                style.appendChild( document.createTextNode( response.preview ) );
                document.getElementsByTagName( 'head' )[0].appendChild( style );

                if ( previewId != 0 ) {
                    var bdyCls = document.getElementsByTagName( 'body' )[0].className;
                    bdyCls = bdyCls + ' ' + previewId;
                    document.getElementsByTagName( 'body' )[0].className =  bdyCls;
                }
            }
        }
        deferred.resolve( null );
    } ).fail( function() {
        deferred.reject();
    } );

    return deferred.promise();
};


/**
 * Handler
 */
/* Select a skin */

// on select
flexiskinSelectionDropDown.getMenu().on('select', itemSelected );
// Initial value
flexiskinSelectionDropDown.getMenu().selectItemByLabel( activeName );

btnPreview.on( 'click', function ( config ) {
    config = getPluginData();
    makePreview( config );
});
btnEdit.on( 'click', function () {
});
btnNew.on( 'click', function () {
    var flexiskinSelected = flexiskinSelectionDropDown.getMenu().findSelectedItem();
    var sourceData = [];
    var sourceId = null;
    if ( flexiskinSelected !== null ) {
        sourceData = flexiskinSelected.getData();
        sourceId = sourceData['id'];
    }
    // crate dialog
    var dialog = new flexiskin.ui.plugin.dialog.newFlexiSkin ( {
        actionId: 'flexiskin-new',
        name: '', /* TODO: use msg */
        source: sourceId
    }); /* TODO: use msg */
    windowManager.addWindows( [ dialog ] );
    var newDlg = windowManager.openWindow( dialog );

    newDlg.closed.then( function ( data ) {
        item = new OO.ui.MenuOptionWidget( {
            data: {
                id: data['id'],
                name: data['name'],
                config: data['config']
            },
            label: data['name'],
        } );

        flexiskinSelectionDropDown.getMenu().addItems( [item] );
        flexiskinSelectionDropDown.getMenu().selectItemByLabel( data['name'] );
    } );
});
btnSave.on( 'click', function () {
    if ( previewId != 0 ) {
        var bdyClsList = document.getElementsByTagName('body')[0].classList.remove( previewId );
    }
    var flexiskinSelected = flexiskinSelectionDropDown.getMenu().findSelectedItem().getData();

    var deferred = $.Deferred();
    var res = new mw.Api().get( {
        action: 'flexiskin-save',
        id: flexiskinSelected.id,
        name: flexiskinSelected.name,
        config: getPluginData()
    } ).done( function( response ) {
        if ( response.hasOwnProperty( 'id' ) && response.id !== null ) {
            deferred.resolve( response.preview );
            var style = document.createElement( 'style' );

            if ( style.styleSheet ) {
                style.styleSheet.cssText = response.preview;
            } else {
                style.appendChild( document.createTextNode( response.preview ) );
                document.getElementsByTagName( 'head' )[0].appendChild( style );

                if ( previewId != 0 ) {
                    var bdyCls = document.getElementsByTagName( 'body' )[0].className;
                    bdyCls = bdyCls + ' fs-' + flexiskinSelected.id;
                    document.getElementsByTagName( 'body' )[0].className =  bdyCls;

                    flexiskinSelected.config = data.config;
                }
            }
        }
        deferred.resolve( null );
    } ).fail( function() {
        deferred.reject();
    } );

    return deferred.promise();
});
btnDelete.on( 'click', function () {
    var flexiskinSelected = flexiskinSelectionDropDown.getMenu().findSelectedItem().getData();

    var deferred = $.Deferred();
    var res = new mw.Api().get( {
        action: 'flexiskin-delete',
        id: flexiskinSelected.id,
        name: flexiskinSelected.name
    } ).done( function( response ) {
        if ( response.hasOwnProperty( 'id' ) && response.id !== null ) {
            deferred.resolve( response.id );
            alert( 'Deleted!' );
        }
        deferred.resolve( null );
    } ).fail( function() {
        deferred.reject();
    } );

    return deferred.promise();
});
btnSelect.on( 'click', function () {
    var flexiskinSelected = flexiskinSelectionDropDown.getMenu().findSelectedItem().getData();

    var deferred = $.Deferred();
    var res = new mw.Api().get( {
        action: 'flexiskin-activate',
        id: flexiskinSelected.id,
    } ).done( function( response ) {
        if ( response.hasOwnProperty( 'id' ) && response.id !== null ) {
            deferred.resolve( '' );
        }
        deferred.resolve( null );
    } ).fail( function() {
        deferred.reject();
    } );

    return deferred.promise();
});