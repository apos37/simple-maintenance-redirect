( function ( $ ) {
    'use strict';

    $( document ).on( 'click', '.smredirect-install-plugin', function () {
        var button = $( this );
        var slug = button.data( 'slug' );
        var installedFile = button.data( 'installed-file' );

        if ( button.prop( 'disabled' ) ) return;

        button.prop( 'disabled', true ).addClass( 'smredirect-button-loading' ).html( '<span class="smredirect-spinner"></span> ' + smredirectRecommended.installing );

        wp.updates.installPlugin( {
            slug: slug,
            success: function () {
                var footer = button.closest( '.smredirect-recommended-card-footer' );
                footer.html( '<button type="button" class="button smredirect-activate-plugin" data-file="' + installedFile + '">' + smredirectRecommended.activate + '</button>' );
            },
            error: function ( errorResponse ) {
                button.prop( 'disabled', false ).removeClass( 'smredirect-button-loading' ).text( smredirectRecommended.installFailed );
                window.alert( errorResponse.errorMessage || smredirectRecommended.installFailed );
            }
        } );
    } );

    $( document ).on( 'click', '.smredirect-activate-plugin', function () {
        var button = $( this );
        var pluginFile = button.data( 'file' );

        if ( button.prop( 'disabled' ) ) return;

        button.prop( 'disabled', true ).addClass( 'smredirect-button-loading' ).html( '<span class="smredirect-spinner"></span> ' + smredirectRecommended.activating );

        $.post( smredirectRecommended.ajaxUrl, {
            action: 'smredirect_activate_plugin',
            nonce: smredirectRecommended.nonce,
            plugin_file: pluginFile
        } ).done( function ( response ) {
            if ( !response.success ) {
                button.prop( 'disabled', false ).removeClass( 'smredirect-button-loading' ).text( smredirectRecommended.activate );
                window.alert( response.data || smredirectRecommended.activateFailed );
                return;
            }

            var settingsUrl = button.data( 'settings-page' );
            var footer = button.closest( '.smredirect-recommended-card-footer' );
            var html = '<button type="button" class="button" disabled>' + smredirectRecommended.active + '</button>';

            if ( settingsUrl ) {
                html += '<a href="' + settingsUrl + '" class="smredirect-recommended-external-link" title="' + smredirectRecommended.goToSettings + '"><span class="dashicons dashicons-external"></span></a>';
            }

            footer.html( html );
        } );
    } );

} )( jQuery );