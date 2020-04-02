window.flexiskin = window.flexiskin || {};
window.flexiskin.ui = window.flexiskin.ui || {};
window.flexiskin.ui.plugin = window.flexiskin.ui.plugin || {};
window.flexiskin.ui.plugin.dialog = window.flexiskin.ui.plugin.dialog || {};

flexiskin.ui.plugin.dialog.newFlexiSkin = function( config ){
	flexiskin.ui.plugin.dialog.newFlexiSkin.parent.call( this, config );

	this.$element.addClass( 'fs-dlg-new' );

	this.actionId = config.actionId || 'default';
	this.source = config.source || null;
	this.name = config.name || '';
	this.newId =  null;

	flexiskin.ui.plugin.dialog.newFlexiSkin.static.name = 'FlexiSkinDlgNew';
	flexiskin.ui.plugin.dialog.newFlexiSkin.static.title = 'New FlexiSkin'; /* TODO: use msg */
	flexiskin.ui.plugin.dialog.newFlexiSkin.static.actions = [
		{
				action: 'submit',
				label: 'Save',  /* TODO: use msg */
				flags: 'primary'
			},
			{
				action: 'abort',
				label: 'Cancel', /* TODO: use msg */
				flags: 'safe'
			}
	];
};

OO.inheritClass( flexiskin.ui.plugin.dialog.newFlexiSkin, OO.ui.ProcessDialog );

flexiskin.ui.plugin.dialog.newFlexiSkin.prototype.initialize = function() {
	flexiskin.ui.plugin.dialog.newFlexiSkin.parent.prototype.initialize.call( this, arguments );

	this.content = new OO.ui.PanelLayout( {
		padded: true,
		expanded: false
	} );

	// Text inside content
	this.promptLabel = new OO.ui.LabelWidget( {
		label: ''
	} );

	// add to content
	this.content.$element.append(
		this.promptLabel.$element
	);

	// text area for name
	this.nameInput = new OO.ui.TextInputWidget( {
		type: 'text',
		required: true
	} );

	// layout for name
	this.nameLayout = new OO.ui.FieldLayout( this.nameInput, {
		label: 'Name',  /* TODO: use msg */
 		align: 'left'
	} );

	this.content.$element.append(
		this.nameLayout.$element
	);

	// add to body
	this.$body.append( this.content.$element );
};

flexiskin.ui.plugin.dialog.newFlexiSkin.prototype.callApi = function() {
	return new mw.Api().get( {
			action: this.actionId,
			name: this.nameInput.getValue(),
			source: this.source
		} )
};

flexiskin.ui.plugin.dialog.newFlexiSkin.prototype.getActionProcess = function( action ) {
	if ( action === 'submit' ) {
		return new OO.ui.Process( function () {
				this.callApi().done( function( response ) {
					if ( response.hasOwnProperty( 'id' ) && response.id !== null ) {
						this.close( {
							action:  this.actionId,
							id: response.id,
							name: response.name,
							config: response.config
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

	return flexiskin.ui.plugin.dialog.newFlexiSkin.parent.prototype.getActionProcess.call( this, action );
};