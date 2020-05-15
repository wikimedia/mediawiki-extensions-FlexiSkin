window.flexiskin = window.flexiskin || {};
window.flexiskin.ui = window.flexiskin.ui || {};
window.flexiskin.ui.dialog = window.flexiskin.ui.dialog || {};

flexiskin.ui.dialog.NewSkin = function( config ){
	flexiskin.ui.dialog.NewSkin.parent.call( this, config );

	this.$element.addClass( 'fs-dlg-new' );

	this.actionId = config.actionId || 'default';
	this.source = config.source || null;
	this.name = config.name || '';
	this.newId =  null;
};

OO.inheritClass( flexiskin.ui.dialog.NewSkin, OO.ui.ProcessDialog );

flexiskin.ui.dialog.NewSkin.static.name = 'flexiskin-new-skin';
flexiskin.ui.dialog.NewSkin.static.title = 'New FlexiSkin'; /* TODO: use msg */
flexiskin.ui.dialog.NewSkin.static.actions = [
	{ action: 'save', label: 'Done', flags: 'primary' },
	{ label: 'Cancel', flags: 'safe' }
];

flexiskin.ui.dialog.NewSkin.prototype.initialize = function() {
	flexiskin.ui.dialog.NewSkin.parent.prototype.initialize.call( this, arguments );

	this.content = new OO.ui.PanelLayout( {
		padded: true,
		expanded: false
	} );

	this.nameInput = new OO.ui.TextInputWidget( {
		type: 'text',
		validate: 'non-empty'
	} );
	this.nameInput.connect( this, {
		change: 'onInputChange'
	} );

	this.nameLayout = new OO.ui.FieldLayout( this.nameInput, {
		label: 'Name',  /* TODO: use msg */
 		align: 'left'
	} );

	this.content.$element.append( this.nameLayout.$element );

	// add to body
	this.$body.append( this.content.$element );
};

flexiskin.ui.dialog.NewSkin.prototype.getSetupProcess = function ( data ) {
	return flexiskin.ui.dialog.NewSkin.parent.prototype.getSetupProcess.call( this, data )
		.next( function() {
			this.getActions().setAbilities( { save: false } );
		}.bind( this ) );
};

flexiskin.ui.dialog.NewSkin.prototype.onInputChange = function( value ) {
	this.nameInput.getValidity()
		.done( function() {
			this.getActions().setAbilities( { save: true } );
		}.bind( this ) )
		.fail( function () {
			this.getActions().setAbilities( { save: false } );
		}.bind( this ) );
};

flexiskin.ui.dialog.NewSkin.prototype.callApi = function() {
	return new mw.Api().get( {
		action: this.actionId,
		name: this.nameInput.getValue(),
		source: this.source
	} );
};

flexiskin.ui.dialog.NewSkin.prototype.getActionProcess = function( action ) {
	if ( action === 'save' ) {
		return new OO.ui.Process( function () {
			this.createNewInstance();
		}.bind( this ) );
	}

	return flexiskin.ui.dialog.NewSkin.parent.prototype.getActionProcess.call( this, action );
};

flexiskin.ui.dialog.NewSkin.prototype.createNewInstance = function() {
	this.callApi()
		.done( function( response ) {
			if ( response.hasOwnProperty( 'skin' ) ) {
				this.close( JSON.parse( response.skin ) );
			}
			this.close( null );
		}.bind( this ) )
		.fail( function( error ) {
			this.close( null );
		}.bind( this ) );
};
