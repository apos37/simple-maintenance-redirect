( function () {
    'use strict';

    var searchInput = document.getElementById( 'smredirect_omit_search' );
    if ( !searchInput ) return;

    var items = document.querySelectorAll( '.smredirect-omit-item' );

    searchInput.addEventListener( 'input', function () {
        var term = searchInput.value.toLowerCase();

        items.forEach( function ( item ) {
            var matches = item.getAttribute( 'data-title' ).indexOf( term ) !== -1;
            item.classList.toggle( 'is-hidden', !matches );
        } );
    } );
} )();