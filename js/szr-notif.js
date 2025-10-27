/**
 * ShiftZoneR - Notifications JavaScript
 */

(function($) {
    'use strict';

    // Système de notifications
    window.szrNotif = {
        show: function(message, type) {
            type = type || 'info';
            var $notif = $('<div class="szr-notif-item szr-notif-' + type + '">' + message + '</div>');

            if (!$('.szr-notif').length) {
                $('body').append('<div class="szr-notif"></div>');
            }

            $('.szr-notif').append($notif);

            setTimeout(function() {
                $notif.fadeOut(function() {
                    $(this).remove();
                });
            }, 3000);
        }
    };

})(jQuery);
