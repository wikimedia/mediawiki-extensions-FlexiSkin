flexiskin.ui.BookletPage = function( name, cfg ) {
	flexiskin.ui.BookletPage.parent.call( this, name, { expanded: false } );

	this.label = cfg.label;

	var items = cfg.items || [];
	for ( var i = 0; i < items.length; i++ ) {
		this.$element.append(items[i].$element);
	}
};

OO.inheritClass( flexiskin.ui.BookletPage, OO.ui.PageLayout );

flexiskin.ui.BookletPage.prototype.setupOutlineItem = function() {
	this.outlineItem.setLabel( this.label );
};
