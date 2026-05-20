( function() {
    'use strict';

    // Safety check: make sure pvcData was injected by PHP
    if ( typeof pvcData === 'undefined' ) {
        return;
    }

    // Prevent counting the same visit twice in one session
    var sessionKey = 'pvc_tracked_' + pvcData.postId;
    var readingSessionKey = 'pvc_reading_tracked_' + pvcData.postId;

    if ( sessionStorage.getItem( sessionKey ) ) {
        return;
    }

    var pageLoadTime = Date.now();
    var hasTrackedReading = false;

    // Wait for DOM to be ready
    document.addEventListener( 'DOMContentLoaded', function() {

        // 2 second bounce filter before tracking view
        setTimeout( function() {

            // Build the POST body for view tracking
            var formData = new FormData();
            formData.append( 'action',  'pvc_track_view' );
            formData.append( 'nonce',   pvcData.nonce );
            formData.append( 'post_id', pvcData.postId );

            fetch( pvcData.ajaxUrl, {
                method: 'POST',
                body:   formData,
            })
            .then( function( response ) {
                return response.json();
            })
            .then( function( data ) {
                if ( data.success ) {
                    // Mark as tracked so refresh won't recount
                    sessionStorage.setItem( sessionKey, '1' );
                }
            })
            .catch( function() {
                // Silently fail — never show errors to visitors
            });

        }, 2000 );

    });

    // Track reading time when user leaves the page (or after 30 minutes max)
    function trackReadingTime() {
        if ( hasTrackedReading || !sessionStorage.getItem( sessionKey ) ) {
            return;
        }

        hasTrackedReading = true;

        // Calculate time spent on page in seconds
        var timeSpent = Math.round( ( Date.now() - pageLoadTime ) / 1000 );

        // Don't track if less than 3 seconds (likely just bounced)
        if ( timeSpent < 3 ) {
            return;
        }

        // Cap at 30 minutes (1800 seconds) to avoid outliers
        if ( timeSpent > 1800 ) {
            timeSpent = 1800;
        }

        // Only track once per session
        if ( sessionStorage.getItem( readingSessionKey ) ) {
            return;
        }

        var formData = new FormData();
        formData.append( 'action',        'pvc_track_reading_time' );
        formData.append( 'nonce',         pvcData.nonce );
        formData.append( 'post_id',       pvcData.postId );
        formData.append( 'time_spent',    timeSpent );

        fetch( pvcData.ajaxUrl, {
            method: 'POST',
            body:   formData,
            keepalive: true  // Keep request alive even if page is unloading
        })
        .then( function( response ) {
            return response.json();
        })
        .then( function( data ) {
            if ( data.success ) {
                sessionStorage.setItem( readingSessionKey, '1' );
            }
        })
        .catch( function() {
            // Silently fail
        });
    }

    // Track reading time when user leaves the page
    window.addEventListener( 'beforeunload', trackReadingTime );

    // Also track if page is idle (tab switch, minimize, etc) using visibilitychange
    document.addEventListener( 'visibilitychange', function() {
        if ( document.hidden ) {
            trackReadingTime();
        }
    });

} )();