( function ( $ ) {
    'use strict';

    $( document ).on( 'click', '.smredirect-moved-notice .notice-dismiss', function () {
        $.post( smredirectNotice.ajaxUrl, {
            action: 'smredirect_dismiss_moved_notice',
            nonce: smredirectNotice.nonce
        } );
    } );
} )( jQuery );