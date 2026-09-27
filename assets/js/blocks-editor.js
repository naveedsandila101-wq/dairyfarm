/**
 * Dairyfarm section blocks – editor UI.
 *
 * One generic implementation for every section block. The settings sidebar is generated from
 * the `dairyfarm.fields` schema in each block.json (passed in via window.dairyfarmEditor), and
 * the canvas shows a live server-side render, so the editor preview is pixel-identical to the site.
 *
 * Supported field types: text, url, textarea, number, toggle, select, icon, image, term, repeater.
 */
( function ( wp, config ) {
	'use strict';

	if ( ! wp || ! config || ! Array.isArray( config.blocks ) ) {
		return;
	}

	const { registerBlockType } = wp.blocks;
	const { createElement: el, Fragment } = wp.element;
	const { InspectorControls, MediaUpload, MediaUploadCheck, useBlockProps } = wp.blockEditor;
	const {
		PanelBody,
		TextControl,
		TextareaControl,
		ToggleControl,
		SelectControl,
		RangeControl,
		Button,
		BaseControl,
		Disabled,
		Spinner,
	} = wp.components;
	const ServerSideRender = wp.serverSideRender;
	const { useSelect } = wp.data;
	const { __, sprintf } = wp.i18n;

	const common = { __nextHasNoMarginBottom: true };

	/** Fields every section gets, appended as their own panel. */
	const SECTION_FIELDS = [
		{
			key: 'tone',
			type: 'select',
			label: __( 'Background', 'dairyfarm' ),
			panel: __( 'Section Settings', 'dairyfarm' ),
			options: [
				{ label: __( 'White', 'dairyfarm' ), value: 'white' },
				{ label: __( 'Cream', 'dairyfarm' ), value: 'cream' },
				{ label: __( 'Mint', 'dairyfarm' ), value: 'mint' },
				{ label: __( 'Dark green', 'dairyfarm' ), value: 'dark' },
				{ label: __( 'Brand green', 'dairyfarm' ), value: 'brand' },
			],
		},
		{
			key: 'sectionId',
			type: 'text',
			label: __( 'Section anchor (ID)', 'dairyfarm' ),
			help: __( 'Used for menu links, e.g. "products" → /#products', 'dairyfarm' ),
			panel: __( 'Section Settings', 'dairyfarm' ),
		},
	];

	function blankValue( field ) {
		if ( Object.prototype.hasOwnProperty.call( field, 'default' ) ) {
			return field.default;
		}
		switch ( field.type ) {
			case 'toggle':
				return false;
			case 'number':
				return field.min || 0;
			case 'image':
				return {};
			case 'repeater':
				return [];
			default:
				return '';
		}
	}

	function ImageField( { field, value, onChange } ) {
		const image = value && typeof value === 'object' ? value : {};

		return el(
			BaseControl,
			Object.assign( { label: field.label, help: field.help }, common ),
			el(
				MediaUploadCheck,
				null,
				el( MediaUpload, {
					allowedTypes: [ 'image' ],
					value: image.id,
					onSelect: ( media ) =>
						onChange( { id: media.id, url: media.url, alt: media.alt || '' } ),
					render: ( { open } ) =>
						el(
							'div',
							{ className: 'df-field-image' },
							image.url
								? el(
										'button',
										{ type: 'button', className: 'df-field-image__preview', onClick: open },
										el( 'img', { src: image.url, alt: '' } )
								  )
								: null,
							el(
								'div',
								{ className: 'df-field-image__actions' },
								el(
									Button,
									{ variant: 'secondary', onClick: open },
									image.url ? __( 'Replace image', 'dairyfarm' ) : __( 'Select image', 'dairyfarm' )
								),
								image.url
									? el(
											Button,
											{ variant: 'link', isDestructive: true, onClick: () => onChange( {} ) },
											__( 'Remove', 'dairyfarm' )
									  )
									: null
							)
						),
				} )
			)
		);
	}

	function TermField( { field, value, onChange } ) {
		const terms = useSelect(
			( select ) =>
				select( 'core' ).getEntityRecords( 'taxonomy', field.taxonomy, {
					per_page: 100,
					hide_empty: false,
				} ),
			[ field.taxonomy ]
		);

		if ( terms === null ) {
			return el( Spinner );
		}

		const options = [ { label: field.allLabel || __( 'All', 'dairyfarm' ), value: '' } ].concat(
			( terms || [] ).map( ( term ) => ( { label: term.name, value: term.slug } ) )
		);

		return el(
			SelectControl,
			Object.assign(
				{ label: field.label, help: field.help, value: value || '', options, onChange },
				common
			)
		);
	}

	function RepeaterField( { field, value, onChange } ) {
		const items = Array.isArray( value ) ? value : [];
		const itemLabel = field.itemLabel || __( 'Item', 'dairyfarm' );

		const update = ( index, key, next ) =>
			onChange(
				items.map( ( item, i ) => ( i === index ? Object.assign( {}, item, { [ key ]: next } ) : item ) )
			);

		const move = ( index, delta ) => {
			const target = index + delta;
			if ( target < 0 || target >= items.length ) {
				return;
			}
			const next = items.slice();
			const tmp = next[ index ];
			next[ index ] = next[ target ];
			next[ target ] = tmp;
			onChange( next );
		};

		const remove = ( index ) => onChange( items.filter( ( _, i ) => i !== index ) );

		const add = () => {
			const blank = {};
			field.fields.forEach( ( sub ) => {
				blank[ sub.key ] = blankValue( sub );
			} );
			onChange( items.concat( [ blank ] ) );
		};

		const titleOf = ( item, index ) => {
			const raw = field.titleKey && typeof item[ field.titleKey ] === 'string' ? item[ field.titleKey ] : '';
			return raw ? raw.replace( /<[^>]+>/g, '' ) : sprintf( '%s %d', itemLabel, index + 1 );
		};

		const canAdd = ! field.max || items.length < field.max;

		return el(
			'div',
			{ className: 'df-repeater' },
			el( 'p', { className: 'df-repeater__label' }, field.label ),
			field.help ? el( 'p', { className: 'df-repeater__help' }, field.help ) : null,
			items.map( ( item, index ) =>
				el(
					PanelBody,
					{ key: index, title: titleOf( item, index ), initialOpen: false, className: 'df-repeater__item' },
					field.fields.map( ( sub ) =>
						el( Field, {
							key: sub.key,
							field: sub,
							value: item[ sub.key ],
							onChange: ( next ) => update( index, sub.key, next ),
						} )
					),
					el(
						'div',
						{ className: 'df-repeater__tools' },
						el( Button, {
							icon: 'arrow-up-alt2',
							label: __( 'Move up', 'dairyfarm' ),
							disabled: index === 0,
							onClick: () => move( index, -1 ),
						} ),
						el( Button, {
							icon: 'arrow-down-alt2',
							label: __( 'Move down', 'dairyfarm' ),
							disabled: index === items.length - 1,
							onClick: () => move( index, 1 ),
						} ),
						el(
							Button,
							{ variant: 'link', isDestructive: true, onClick: () => remove( index ) },
							sprintf( __( 'Remove %s', 'dairyfarm' ), itemLabel.toLowerCase() )
						)
					)
				)
			),
			canAdd
				? el(
						Button,
						{ variant: 'secondary', icon: 'plus-alt2', onClick: add, className: 'df-repeater__add' },
						sprintf( __( 'Add %s', 'dairyfarm' ), itemLabel.toLowerCase() )
				  )
				: null
		);
	}

	function Field( { field, value, onChange } ) {
		const base = Object.assign( { label: field.label, help: field.help }, common );

		switch ( field.type ) {
			case 'textarea':
				return el( TextareaControl, Object.assign( base, { value: value || '', rows: field.rows || 3, onChange } ) );
			case 'toggle':
				return el( ToggleControl, Object.assign( base, { checked: !! value, onChange } ) );
			case 'select':
				return el( SelectControl, Object.assign( base, { value, options: field.options || [], onChange } ) );
			case 'icon':
				return el( SelectControl, Object.assign( base, { value, options: config.icons || [], onChange } ) );
			case 'number':
				return el(
					RangeControl,
					Object.assign( base, {
						value: typeof value === 'number' ? value : blankValue( field ),
						min: field.min || 0,
						max: field.max || 12,
						onChange,
					} )
				);
			case 'image':
				return el( ImageField, { field, value, onChange } );
			case 'term':
				return el( TermField, { field, value, onChange } );
			case 'repeater':
				return el( RepeaterField, { field, value, onChange } );
			case 'url':
				return el(
					TextControl,
					Object.assign( base, { value: value || '', type: 'text', placeholder: 'https:// or #section', onChange } )
				);
			default:
				return el( TextControl, Object.assign( base, { value: value || '', onChange } ) );
		}
	}

	function groupIntoPanels( fields ) {
		const panels = [];
		const byTitle = {};

		fields.forEach( ( field ) => {
			const title = field.panel || __( 'Content', 'dairyfarm' );
			if ( ! byTitle[ title ] ) {
				byTitle[ title ] = { title, fields: [] };
				panels.push( byTitle[ title ] );
			}
			byTitle[ title ].fields.push( field );
		} );

		return panels;
	}

	function makeEdit( block ) {
		const fields = ( block.fields || [] ).concat(
			SECTION_FIELDS.filter( ( f ) => block.attributes && block.attributes[ f.key ] )
		);
		const panels = groupIntoPanels( fields );

		return function Edit( { attributes, setAttributes } ) {
			const blockProps = useBlockProps( { className: 'df-editor-preview' } );

			return el(
				Fragment,
				null,
				el(
					InspectorControls,
					null,
					panels.map( ( panel, index ) =>
						el(
							PanelBody,
							{ key: panel.title, title: panel.title, initialOpen: index === 0 },
							panel.fields.map( ( field ) =>
								el( Field, {
									key: field.key,
									field,
									value: attributes[ field.key ],
									onChange: ( next ) => setAttributes( { [ field.key ]: next } ),
								} )
							)
						)
					)
				),
				el(
					'div',
					blockProps,
					el(
						Disabled,
						null,
						el( ServerSideRender, {
							block: block.name,
							attributes,
							httpMethod: 'POST',
						} )
					)
				)
			);
		};
	}

	config.blocks.forEach( ( block ) => {
		registerBlockType( block.name, {
			apiVersion: 3,
			title: block.title,
			description: block.description,
			category: block.category,
			icon: block.icon,
			keywords: block.keywords,
			attributes: block.attributes,
			supports: block.supports,
			edit: makeEdit( block ),
			save: () => null,
		} );
	} );
} )( window.wp, window.dairyfarmEditor );
