flexiskin.ui.ConfigGroup = function ( name, cfg ) {
	cfg = Object.assign( {}, cfg, { expanded: false } );

	flexiskin.ui.ConfigGroup.parent.call( this, name, cfg );

	this.name = name;
	this.items = cfg.items || [];

	this.$itemContainer = $( '<div>' ).addClass( 'fs-group-item-container' );
	for ( let i = 0; i < this.items.length; i++ ) {
		this.$itemContainer.append( this.items[ i ].$element );
	}

	this.$element.addClass( [ 'fs-group', 'fs-group-' + name.replace( /_/g, '-' ) ] ) // eslint-disable-line mediawiki/class-doc
		.append( this.$itemContainer );
};

OO.inheritClass( flexiskin.ui.ConfigGroup, OO.ui.TabPanelLayout );

flexiskin.ui.ConfigGroup.prototype.getName = function () {
	return this.name;
};

flexiskin.ui.ConfigGroup.prototype.getItems = function () {
	return this.items;
};
