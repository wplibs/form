'use strict';

/* global POST_FINDER_CONFIG, pfPerPage, ajaxurl, _ */

( function( window, $, _, undefined ) {

	var cache = {};

	$.postFinder = function( element, options ) {
		var defaults, mainTemplate, itemTemplate, $li;

		if ( 'mainTemplate' in cache ) {
			mainTemplate = cache.mainTemplate;
		} else {
			mainTemplate = cache.mainTemplate = $( '#tmpl-post-finder-main' ).html();
		}

		if ( 'itemTemplate' in cache ) {
			itemTemplate = cache.itemTemplate;
		} else {
			itemTemplate = cache.itemTemplate = $( '#tmpl-post-finder-item' ).html();
		}

		defaults = {
			template:        mainTemplate,
			fieldSelector:   'input[type=hidden]',
			typesfieldSelector: 'input#types[type=hidden]',
			selectSelector:  'select',
			listSelector:    '.list',
			searchSelector:  '.search',
			resultsSelector: '.results',
			querySelector:   'input[type=text]',
			nonceSelector:   '#post_finder_nonce'
		};

		var plugin = this;

		plugin.settings = {}; // empty object to store extended settings

		var $element = $( element ); // store jquery object of el

		plugin.init = function() {
			// over write defaults with passed options
			plugin.settings = $.extend( {}, defaults, options );

			// all jquery objects are fetched once and stored in the plugin object
			plugin.$field = $element.find( plugin.settings.fieldSelector );
			plugin.$typefield = $element.find( plugin.settings.typesfieldSelector );
			plugin.$select = $element.find( plugin.settings.selectSelector );
			plugin.$list = $element.find( plugin.settings.listSelector );
			plugin.$search = $element.find( plugin.settings.searchSelector );
			plugin.$results = plugin.$search.find( plugin.settings.resultsSelector );
			plugin.$query = plugin.$search.find( plugin.settings.querySelector );
			plugin.nonce = $( plugin.settings.nonceSelector ).val();

			// bind select
			plugin.$select.on( 'change', function() {
				plugin.addItem(
					$( this ).val(),
					$( 'option:selected', this ).text(),
					$( 'option:selected', this ).data( 'permalink' )
				);
			} );

			// bind search button
			plugin.$search.on( 'click', '.button', function( e ) {
				e.preventDefault();
				plugin.search( e );
			} );

			// search on enter key press
			plugin.$search.on( 'keypress', 'input[type="text"]', function( e ) {
				if ( e.which === 13 ) {
					e.preventDefault();
					plugin.search( e );
				}
			} );

			// bind list
			plugin.$list.sortable( {
				placeholder: 'placeholder',
				update: function() {
					plugin.serialize();
				}
			} );

			// remove button
			plugin.$list.on( 'click', '.delete', function( e ) {
				e.preventDefault();
				plugin.removeItem( $( this ).closest( 'li' ).data( 'id' ) );
			} );

			// add button
			plugin.$results.on( 'click', '.add', function( e ) {
				e.preventDefault();
				$li = $( this ).closest( 'li' );
				plugin.addItem(
					$li.data( 'id' ),
					$li.find( 'span' ).text(),
					$li.data( 'permalink' )
				);
			} );

			// bind number inputs
			plugin.$list.on( 'keypress', 'li input', function( e ) {
				if ( e.which === 13 ) {
					e.preventDefault();
					$( this ).trigger( 'blur' );
				}
			} );

			plugin.$list.on( 'blur', 'li input', function() {
				plugin.moveItem( $( this ).closest( 'li' ), $( this ).val() );
			} );
		};

		// move an element to a specific position if possible
		plugin.moveItem = function( $el, pos ) {

			var $lii = plugin.$list.find( 'li' ),
				len = $lii.length,
				$clone;

			// has to be a position that's available
			if ( pos > len || pos < 1 ) {
				alert( 'Please pick a position between 1 and ' + len );
				return false;
			}

			// don't move it if were already there
			if ( pos - 1 === $el.index() ) {
				return false;
			}

			// clone the element
			$clone = $el.clone();

			// first position
			if ( pos === 1 ) {

				plugin.$list.prepend( $clone );

			// middle positions
			} else if ( pos > 1 && pos < len ) {

				plugin.$list.find( 'li' ).eq( pos - 1 ).before( $clone );

			// last position
			} else if ( pos === len ) {

				plugin.$list.append( $clone );

			}

			// remove the original element
			$el.remove();

			plugin.serialize();

		};

		plugin.addItem = function( id, title, permalink ) { //private method

			var template = _.template( plugin.settings.template );

			// make sure we have an id
			if ( id === 0 ) {
				return;
			}

			if ( plugin.$list.find( 'li' ).length >= $element.data( 'limit' ) ) {
				alert( POST_FINDER_CONFIG.max_number_allowed );
				return;
			}

			// see if item already exists
			if ( plugin.$list.find( 'li[data-id="' + id + '"]' ).length ) {
				alert( POST_FINDER_CONFIG.already_added );
				return;
			}

			// add item
			plugin.$list.append( template( {
				id:        id,
				title:     title,
				editUrl:  POST_FINDER_CONFIG.adminurl + 'post.php?post=' + id + '&action=edit',
				permalink: permalink,
				pos:       plugin.$list.length + 1
			} ) );

			// hide notice
			plugin.$list.find( '.notice' ).hide();

			// remove from select if there
			plugin.$select.find( 'option[value="' + id + '"]' ).remove();

			// update the input
			plugin.serialize();
		};

		// Private method to remove an item
		plugin.removeItem = function( id ) {

			plugin.$list.find( 'li[data-id="' + id + '"]' ).remove();

			plugin.serialize();

			// show notice if no posts
			if ( plugin.$list.find( 'li' ).length === 0 ) {
				plugin.$list.find( '.notice' ).show();
			}
		};

		plugin.search = function( e ) {

			var html = '',
				page = e.currentTarget.getAttribute( 'data-page' ) ?
					Number( e.currentTarget.getAttribute( 'data-page' ) ) :
					1,
				data = {
					action: 'pf_search_posts',
					s: plugin.$query.val(),
					page: page,
					_ajax_nonce: plugin.nonce
				},
				nextPage = page + 1,
				template = _.template( itemTemplate );

			// merge the default args in
			data = $.extend( data, $element.data( 'args' ) );

			// display loading
			plugin.$search.addClass( 'loading' );

			$.ajax(
				ajaxurl,
				{
					type: 'POST',
					data: data,
					dataType: 'json',
					success: function( response ) {
						if ( typeof response.posts !== 'undefined' ) {
							if ( response.posts.length > 0 ) {
								for ( var i in response.posts ) {
									html += template( response.posts[ i ] );
								}

								// If the # of results equals our per page setting (default 10), show the Next button
								if ( response.posts.length === pfPerPage ) {
									html += '<li class="next">';
									html += '<a href="#" class="button" data-page="' + nextPage + '">';
									html += POST_FINDER_CONFIG.next;
									html += '</a>';
									html += '</li>';
								}
							} else {
								html = '<li>' + POST_FINDER_CONFIG.nothing_found + '</li>';
							}

							// Hide Loader
							plugin.$search.removeClass( 'loading' );

							// Show results
							plugin.$results.html( html );
						}
					}
				}
			);
		};

		plugin.serialize = function() {
			var ids = [],
				types = [],
				i = 1;

			plugin.$list.find( 'li' ).each( function() {
				$( this ).find( 'input' ).val( i );
				ids.push( $( this ).data( 'id' ) );
				types.push( $( this ).data( 'type' ) );
				i++;
			} );

			plugin.$field.val( ids.join( ',' ) );
			plugin.$typefield.val( types.join( ',' ) );

			$( document ).trigger( 'updatePostfinder', {
				'idField': plugin.$field,
				'typeField': plugin.$typefield
			} );
		};

		plugin.init();

	};

	$.fn.postFinder = function( options ) {

		return this.each( function() {
			if ( undefined === $( this ).data( 'postFinder' ) ) {
				var plugin = new $.postFinder( this, options );

				$( this ).data( 'postFinder', plugin );
			}
		} );

	};

} )( window, jQuery, _ );
