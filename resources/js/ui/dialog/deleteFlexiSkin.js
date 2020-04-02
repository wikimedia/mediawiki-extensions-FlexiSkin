window.flexiskin = window.flexiskin || {};
window.flexiskin.ui = window.flexiskin.ui || {};
window.flexiskin.ui.plugin = window.flexiskin.ui.plugin || {};
window.flexiskin.ui.plugin.dialog = window.flexiskin.ui.plugin.dialog || {};

flexiskin.ui.plugin.dialog.deleteFlexiSkin = function( config ){
	flexiskin.ui.plugin.dialog.deleteFlexiSkin.parent.call( this, config );

	this.$element.addClass( 'fs-dlg-delete' );

	this.actionId = config.actionId || 'default';
	this.id = config.id || null;
	this.name = config.name || null;

	flexiskin.ui.plugin.dialog.newFlexiSkin.static.name = 'FlexiSkinDlgDelete';
	flexiskin.ui.plugin.dialog.newFlexiSkin.static.title = 'Delete FlexiSkin'; /* TODO: use msg */
	flexiskin.ui.plugin.dialog.newFlexiSkin.static.actions = [
		{
				action: 'submit',
				label: 'Delete',  /* TODO: use msg */
				flags: 'primary'
			},
			{
				action: 'abort',
				label: 'Cancel', /* TODO: use msg */
				flags: 'safe'
			}
	];
};

OO.inheritClass( flexiskin.ui.plugin.dialog.deleteFlexiSkin, OO.ui.ProcessDialog );

flexiskin.ui.plugin.dialog.deleteFlexiSkin.prototype.initialize = function() {
	flexiskin.ui.plugin.dialog.deleteFlexiSkin.parent.prototype.initialize.call( this, arguments );

	this.content = new OO.ui.PanelLayout( {
		padded: true,
		expanded: false
	} );

	// Text inside content
	this.promptLabel = new OO.ui.LabelWidget( {
		label: 'Delete?'
	} );

	// add to content
	this.content.$element.append(
		this.promptLabel.$element
	);

	// add to body
	this.$body.append( this.content.$element );
};

flexiskin.ui.plugin.dialog.deleteFlexiSkin.prototype.callApi = function() {
	return new mw.Api().get( {
			action: this.actionId,
			source: this.id
		} )
};

flexiskin.ui.plugin.dialog.deleteFlexiSkin.prototype.getActionProcess = function( action ) {
	if ( action === 'submit' ) {
		return new OO.ui.Process( function () {
				this.callApi().done( function( response ) {
					if ( response.hasOwnProperty( 'id' ) && response.id !== null ) {
						this.close( {
							action:  this.actionId,
							id: response.id
						} );
					}
				}.bind( this ) );
		}.bind( this ) );
	}

	if ( action === 'abort' ) {
		return new OO.ui.Process( function () {
			this.close( { action: action } );
		}.bind( this ) );
	}

	return flexiskin.ui.plugin.dialog.deleteFlexiSkin.parent.prototype.getActionProcess.call( this, action );
};