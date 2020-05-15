window.flexiskin = window.flexiskin || {};
window.flexiskin.ui = window.flexiskin.ui || {};
window.flexiskin.ui.plugin = window.flexiskin.ui.plugin || {};
return;
flexiskin.ui.plugin.FreeCSS = function( config ){

    config = config || {};
    config.FreeCSS = config.FreeCSS || '';

    var input1 = new OO.ui.MultilineTextInputWidget( {
        id: 'fs-freecss',
        value: '',
        multiline: true,
        autosize: false,
        rows: 25,
        spellcheck: false,
        autocomplete: false
    } );

    config.items = [
        new OO.ui.FieldLayout( input1, {
            label: 'FreeCSS',
            align: 'top'
        } )
    ];


    flexiskin.ui.plugin.FreeCSS.parent.call( this, config );
};


OO.inheritClass( flexiskin.ui.plugin.FreeCSS, OO.ui.FormLayout );

flexiskin.ui.plugin.FreeCSS.static.name = 'FreeCSS';
flexiskin.ui.plugin.FreeCSS.static.title = 'FreeCSS'; /* TODO: use msg */

var editorTmp = '';
flexiskin.ui.plugin.FreeCSS.prototype.setPluginData = function( config ) {
    config = config || {};
    if ( config.hasOwnProperty( 'FreeCSS' ) ) {
        if ( !editor ) {
            editorTmp = config.FreeCSS;
        } else {
           editor.setValue( config.FreeCSS );
        }
    }
}

flexiskin.ui.plugin.FreeCSS.prototype.getPluginData = function( config ) {
    return editor.getValue();
}

flexiskin.ui.plugin.FreeCSS.prototype.initialize = function( config ) {
};
var editor;
$( function($) {
    /* code editor */
    var $area = $( '#fs-freecss' ).attr( 'dir', 'ltr' );
    var aceEditorModuleState = mw.loader.getState( 'ext.codeEditor.ace' );
    if ( aceEditorModuleState != null ) {
        mw.loader.using( 'ext.codeEditor.ace' ).done( function() {
            editor = ace.edit(
                $area[ 0 ],
                {
                    mode: "ace/mode/css",
                    selectionStyle: "css"
                }
            );
            editor.setOption( "showPrintMargin", false );
            editor.setHighlightActiveLine( true );
            var basePath = mw.config.get( 'wgExtensionAssetsPath', '' );
				if ( basePath.slice( 0, 2 ) === '//' ) {
					// ACE uses web workers, which have importScripts, which don't like relative links.
					// This is a problem only when the assets are on another server, so this rewrite should suffice
					// Protocol relative
					basePath = window.location.protocol + basePath;
				}
			ace.config.set( 'basePath', basePath + '/CodeEditor/modules/ace' );
            editor.session.setMode("ace/mode/css");
            editor.$blockScrolling = Infinity;

            editor.setValue ( editorTmp );
        } );
    }
} );
