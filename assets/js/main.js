/**
 * Dairyfarm front-end behaviour. Dependency-free, progressively enhanced:
 * every section is fully usable without JavaScript.
 */
( function () {
	'use strict';

	const reduceMotion = window.matchMedia( '(prefers-reduced-motion: reduce)' ).matches;

	/* Header: shadow once scrolled. */
	const header = document.querySelector( '[data-header]' );
	if ( header ) {
		const onScroll = () => header.classList.toggle( 'is-scrolled', window.scrollY > 8 );
		onScroll();
		window.addEventListener( 'scroll', onScroll, { passive: true } );
	}

	/* Mobile navigation. */
	const toggle = document.querySelector( '[data-nav-toggle]' );
	const nav = document.querySelector( '[data-nav]' );
	if ( toggle && nav ) {
		const setOpen = ( open ) => {
			toggle.setAttribute( 'aria-expanded', String( open ) );
			document.body.classList.toggle( 'nav-open', open );
		};

		toggle.addEventListener( 'click', () => setOpen( toggle.getAttribute( 'aria-expanded' ) !== 'true' ) );

		nav.addEventListener( 'click', ( event ) => {
			if ( event.target.closest( 'a' ) ) {
				setOpen( false );
			}
		} );

		document.addEventListener( 'keydown', ( event ) => {
			if ( event.key === 'Escape' && document.body.classList.contains( 'nav-open' ) ) {
				setOpen( false );
				toggle.focus();
			}
		} );

		window.matchMedia( '(min-width: 1024px)' ).addEventListener( 'change', ( mq ) => mq.matches && setOpen( false ) );
	}

	/* Reveal-on-scroll. */
	const revealables = document.querySelectorAll( '[data-reveal]' );
	if ( 'IntersectionObserver' in window && ! reduceMotion ) {
		const observer = new IntersectionObserver(
			( entries ) => {
				entries.forEach( ( entry ) => {
					if ( entry.isIntersecting ) {
						entry.target.classList.add( 'is-visible' );
						observer.unobserve( entry.target );
					}
				} );
			},
			{ rootMargin: '0px 0px -8% 0px', threshold: 0.08 }
		);
		revealables.forEach( ( el ) => observer.observe( el ) );
	} else {
		revealables.forEach( ( el ) => el.classList.add( 'is-visible' ) );
	}

	/* Count-up numbers. */
	const counters = document.querySelectorAll( '[data-count]' );
	if ( counters.length && 'IntersectionObserver' in window && ! reduceMotion ) {
		const format = new Intl.NumberFormat( document.documentElement.lang || undefined );
		const run = ( el ) => {
			const target = parseInt( el.getAttribute( 'data-count' ), 10 ) || 0;
			const duration = 1400;
			const start = performance.now();
			const step = ( now ) => {
				const progress = Math.min( 1, ( now - start ) / duration );
				const eased = 1 - Math.pow( 1 - progress, 3 );
				el.textContent = format.format( Math.round( target * eased ) );
				if ( progress < 1 ) {
					requestAnimationFrame( step );
				}
			};
			requestAnimationFrame( step );
		};
		const counterObserver = new IntersectionObserver(
			( entries ) => {
				entries.forEach( ( entry ) => {
					if ( entry.isIntersecting ) {
						run( entry.target );
						counterObserver.unobserve( entry.target );
					}
				} );
			},
			{ threshold: 0.6 }
		);
		counters.forEach( ( el ) => counterObserver.observe( el ) );
	}

	/* Product category filter. */
	document.querySelectorAll( '[data-filter-scope]' ).forEach( ( scope ) => {
		const buttons = scope.querySelectorAll( '[data-filter]' );
		const items = scope.querySelectorAll( '[data-category]' );

		buttons.forEach( ( button ) => {
			button.addEventListener( 'click', () => {
				const filter = button.getAttribute( 'data-filter' );

				buttons.forEach( ( b ) => {
					const active = b === button;
					b.classList.toggle( 'is-active', active );
					b.setAttribute( 'aria-pressed', String( active ) );
				} );

				items.forEach( ( item ) => {
					const cats = ( item.getAttribute( 'data-category' ) || '' ).split( ' ' );
					item.hidden = filter !== '*' && cats.indexOf( filter ) === -1;
				} );
			} );
		} );
	} );

	/* Gallery lightbox (native <dialog>). */
	const galleries = document.querySelectorAll( '[data-lightbox]' );
	if ( galleries.length && typeof HTMLDialogElement === 'function' ) {
		const dialog = document.createElement( 'dialog' );
		dialog.className = 'df-lightbox';
		dialog.setAttribute( 'aria-label', 'Image viewer' );
		dialog.innerHTML =
			'<button type="button" class="df-lightbox__close" aria-label="Close">&times;</button>' +
			'<button type="button" class="df-lightbox__nav df-lightbox__prev" aria-label="Previous image">&#8249;</button>' +
			'<figure><img alt="" /><figcaption></figcaption></figure>' +
			'<button type="button" class="df-lightbox__nav df-lightbox__next" aria-label="Next image">&#8250;</button>';
		document.body.appendChild( dialog );

		const img = dialog.querySelector( 'img' );
		const caption = dialog.querySelector( 'figcaption' );
		let links = [];
		let index = 0;

		const show = ( i ) => {
			index = ( i + links.length ) % links.length;
			const link = links[ index ];
			const thumb = link.querySelector( 'img' );
			img.src = link.href;
			img.alt = thumb ? thumb.alt : '';
			caption.textContent = link.getAttribute( 'data-caption' ) || '';
		};

		galleries.forEach( ( gallery ) => {
			gallery.addEventListener( 'click', ( event ) => {
				const link = event.target.closest( '.df-gallery__link' );
				if ( ! link ) {
					return;
				}
				event.preventDefault();
				links = Array.from( gallery.querySelectorAll( '.df-gallery__link' ) );
				show( links.indexOf( link ) );
				dialog.showModal();
			} );
		} );

		dialog.querySelector( '.df-lightbox__close' ).addEventListener( 'click', () => dialog.close() );
		dialog.querySelector( '.df-lightbox__prev' ).addEventListener( 'click', () => show( index - 1 ) );
		dialog.querySelector( '.df-lightbox__next' ).addEventListener( 'click', () => show( index + 1 ) );
		dialog.addEventListener( 'click', ( event ) => event.target === dialog && dialog.close() );
		dialog.addEventListener( 'keydown', ( event ) => {
			if ( event.key === 'ArrowLeft' ) {
				show( index - 1 );
			} else if ( event.key === 'ArrowRight' ) {
				show( index + 1 );
			}
		} );
	}
} )();
