(function($) {
    $.fn.pushNotifications = function(options) {
        const settings = $.extend({
            messageTitle: 'Push Notifications',
            messageText: 'Would you like to receive push notifications?',
            enableText: 'Yes',
            disableText: 'No'
        }, options);

        return this.each(function() {
            const $container = $(this);

            // Check if user has already responded
            const userResponse = localStorage.getItem('pushNotificationResponse');
            if (userResponse === 'accepted' || userResponse === 'denied') {
                return;
            }

            var modalHTML = `
      <div class="modal fade" id="pushNotificationModal" data-bs-backdrop="static" tabindex="-1" aria-labelledby="pushNotificationModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
          <div class="modal-content">
            <div class="modal-header">
              <h5 class="modal-title" id="pushNotificationModalLabel">
                  ${settings.messageTitle}
                </h5>
            </div>
            <div class="modal-body">
              ${settings.messageText}
            </div>
            <div class="modal-footer">
              <button type="button" class="btn btn-secondary deny" data-bs-dismiss="modal">
                ${settings.disableText}
              </button>
              <button type="button" class="btn btn-primary accept" data-bs-dismiss="modal">
                ${settings.enableText}
              </button>
            </div>
          </div>
        </div>
      </div>
    `;

            $container.append(modalHTML);

            const $modal = $('#pushNotificationModal');

            var modal = new bootstrap.Modal($modal[0]);

            modal.show();

            if ('serviceWorker' in navigator && 'PushManager' in window) {
                $modal.find(".accept").on('click', async function() {
                    localStorage.setItem('pushNotificationResponse', 'accepted');
                    try {
                        const reg = await navigator.serviceWorker.register(
                            CCM_APPLICATION_URL + '/sw.js',
                            { scope: '/' }
                        );

                        const res = await fetch(
                            CCM_DISPATCHER_FILENAME + '/api/v1/push_notifications/get_vapid_keys'
                        );
                        const { publicKey } = await res.json();

                        const sub = await reg.pushManager.subscribe({
                            userVisibleOnly: true,
                            applicationServerKey: urlBase64ToUint8Array(publicKey)
                        });

                        await fetch(
                            CCM_DISPATCHER_FILENAME + '/api/v1/push_notifications/register_device',
                            {
                                method: 'POST',
                                body: JSON.stringify(sub),
                                headers: { 'Content-Type': 'application/json' }
                            }
                        );

                    } catch (err) {
                        localStorage.setItem('pushNotificationResponse', 'denied');
                    }
                });

                $modal.find(".deny").on('click', function() {
                    localStorage.setItem('pushNotificationResponse', 'denied')
                });
            }

            function urlBase64ToUint8Array(base64String) {
                const padding = '='.repeat((4 - base64String.length % 4) % 4);
                const base64 = (base64String + padding)
                    .replace(/\-/g, '+')
                    .replace(/_/g, '/');
                const raw = atob(base64);
                return Uint8Array.from([...raw].map(char => char.charCodeAt(0)));
            }
        });
    };
})(jQuery);