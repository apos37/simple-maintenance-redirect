( function () {
    'use strict';

    try {
        var meta = document.querySelector( 'meta[name="smredirect-url"]' );
        if ( ! meta ) return;
        var url = meta.getAttribute( 'content' );
        if ( ! url ) return;
        // Use replace to avoid adding the redirect to session history
        window.location.replace( url );
    } catch ( e ) {
        // Fail silently; nothing else we can do in this minimal fallback
        console && console.error && console.error( 'smredirect fallback error', e );
    }
} )();
