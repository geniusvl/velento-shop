/**
 * Velento Brands — scroll reveal.
 *
 * Progressive enhancement only: adds the ".vb-js" class that brands.css
 * uses to hide ".vb-reveal" elements in the first place, then reveals
 * each one via IntersectionObserver as it enters the viewport. If this
 * script never runs, brands.css never hides anything — there is no
 * broken/invisible state possible without JS.
 */
( function () {
	'use strict';

	var root = document.querySelector( '.velento-brands' );
	if ( ! root ) {
		return;
	}

	var prefersReducedMotion = window.matchMedia && window.matchMedia( '(prefers-reduced-motion: reduce)' ).matches;
	if ( prefersReducedMotion || typeof IntersectionObserver === 'undefined' ) {
		return;
	}

	root.classList.add( 'vb-js' );

	var targets = root.querySelectorAll( '.vb-reveal' );

	var observer = new IntersectionObserver(
		function ( entries ) {
			entries.forEach( function ( entry ) {
				if ( entry.isIntersecting ) {
					entry.target.classList.add( 'is-visible' );
					observer.unobserve( entry.target );
				}
			} );
		},
		{
			threshold: 0.15,
			rootMargin: '0px 0px -60px 0px',
		}
	);

	targets.forEach( function ( el ) {
		observer.observe( el );
	} );
} )();
