/**
 * "Home Page Sections" meta box: template-aware visibility, image picker and repeaters.
 * Markup comes from inc/home-sections.php.
 */
( function ( $, cfg ) {
	'use strict';

	var BOX = '#dairyfarm-home-sections';
	var counter = 0;

	/* Show the box only while the Home Page template is selected. ------------ */

	function toggleBox( template ) {
		$( BOX ).toggleClass( 'df-home-hidden', template !== cfg.template );
	}

	// Classic edit screen (also used for pages already on the template).
	$( document ).on( 'change', '#page_template', function () {
		toggleBox( this.value );
	} );

	// Block editor.
	if ( document.body.classList.contains( 'block-editor-page' ) && window.wp && wp.data ) {
		var last;
		wp.data.subscribe( function () {
			var editor = wp.data.select( 'core/editor' );
			var template = editor ? editor.getEditedPostAttribute( 'template' ) : undefined;
			if ( template !== undefined && template !== last ) {
				last = template;
				toggleBox( template );
			}
		} );
	}

	/* Section on/off. -------------------------------------------------------- */

	$( document ).on( 'change', '.df-home-enable', function () {
		$( this ).closest( '.df-home-section' ).toggleClass( 'is-disabled', ! this.checked );
	} );

	/* Image picker. ---------------------------------------------------------- */

	$( document ).on( 'click', '.df-image__select, .df-image__preview', function ( e ) {
		e.preventDefault();

		var $field = $( this ).closest( '.df-image' );
		var frame = wp.media( {
			title: cfg.i18n.choose,
			button: { text: cfg.i18n.use },
			library: { type: 'image' },
			multiple: false,
		} );

		frame.on( 'select', function () {
			var image = frame.state().get( 'selection' ).first().toJSON();
			var size = image.sizes && ( image.sizes.medium || image.sizes.large || image.sizes.full );

			$field.find( 'input[type="hidden"]' ).val( image.id );
			$field.find( 'img' ).attr( 'src', size ? size.url : image.url ).prop( 'hidden', false );
			$field.addClass( 'has-image' );
		} );

		frame.open();
	} );

	$( document ).on( 'click', '.df-image__remove', function ( e ) {
		e.preventDefault();

		var $field = $( this ).closest( '.df-image' );
		$field.find( 'input[type="hidden"]' ).val( '' );
		$field.find( 'img' ).attr( 'src', '' ).prop( 'hidden', true );
		$field.removeClass( 'has-image' );
	} );

	/* Repeaters. ------------------------------------------------------------- */

	$( document ).on( 'click', '.df-repeater__add', function ( e ) {
		e.preventDefault();

		var $repeater = $( this ).closest( '.df-repeater' );
		var $rows = $repeater.children( '.df-repeater__rows' );
		var max = parseInt( $repeater.data( 'max' ), 10 ) || 0;

		if ( max && $rows.children().length >= max ) {
			window.alert( cfg.i18n.max ); // eslint-disable-line no-alert
			return;
		}

		// Unique key per new row; PHP re-indexes rows on save.
		var key = 'n' + Date.now() + '_' + counter++;
		$rows.append( $repeater.children( 'template.df-repeater__tpl' ).html().replace( /__i__/g, key ) );
	} );

	// Buttons sit inside <summary>, so stop them from also toggling the row.
	$( document ).on( 'click', '.df-repeater__remove', function ( e ) {
		e.preventDefault();
		if ( window.confirm( cfg.i18n.remove ) ) { // eslint-disable-line no-alert
			$( this ).closest( '.df-repeater__row' ).remove();
		}
	} );

	$( document ).on( 'click', '.df-repeater__up', function ( e ) {
		e.preventDefault();
		var $row = $( this ).closest( '.df-repeater__row' );
		$row.prev( '.df-repeater__row' ).before( $row );
	} );

	$( document ).on( 'click', '.df-repeater__down', function ( e ) {
		e.preventDefault();
		var $row = $( this ).closest( '.df-repeater__row' );
		$row.next( '.df-repeater__row' ).after( $row );
	} );

	// Keep the row heading in sync with its title field.
	$( document ).on( 'input', '.df-repeater__fields input, .df-repeater__fields textarea', function () {
		var key = $( this ).closest( '.df-repeater' ).data( 'titleKey' );
		if ( key && this.name.slice( -( key.length + 2 ) ) === '[' + key + ']' ) {
			$( this ).closest( '.df-repeater__row' ).find( '> summary .df-repeater__title' ).text( this.value );
		}
	} );
}( jQuery, window.dairyfarmHome ) );
