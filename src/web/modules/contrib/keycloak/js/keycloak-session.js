/**
 * @file
 * Keycloak session check Drupal behavior.
 *
 * Checks the Keycloak session ID and triggers logout, if the session has
 * been expired externally.
 *
 * Remarks: In order to not being dependent on a certain version of Keycloak,
 * we borrowed the important parts from the Keycloak JavaScript adapter. Thus,
 * we're also using the homegrown Keycloak promise objects. Later versions of
 * this script may be rewritten to use ES6 native promises and an improved
 * handling for race conditions on expired sessions.
 *
 * @link http://www.keycloak.org/docs/3.1/securing_apps/topics/oidc/javascript-adapter.html
 */

(function ($, Drupal, drupalSettings) {
  'use strict';

  /**
   * Keycloak session check.
   *
   * @type {Drupal~behavior}
   *
   * @property {Drupal~behaviorAttach} attach
   *   Adds an iframe element to the document that loads the Keycloak
   *   session check script and initializes a periodic check for session
   *   ID changes.
   */
  Drupal.behaviors.keycloak = {
    attach: function (context, settings) {

      /**
       * Session iframe settings.
       *
       * @type {object}
       *
       * @property {boolean} initialized
       *   Whether the session check iframe was initialized.
       * @property {boolean} enable
       *   Whether the session check is enabled.
       * @property {string} iframeUrl
       *   The URL to the Keycloak session check script. This is usually
       *   an endpoint URL of the Keycloak authentication server.
       * @property {number} interval
       *   Session check interval in seconds.
       * @property {string} logoutUrl
       *   Drupal logout URL provided by the Keycloak module. This URL
       *   will be redirected to, if the session has been expired externally.
       * @property {boolean} logout
       *   Whether a logout action is in progress. If true, no new session
       *   check promises will be created.
       * @property {string} clientId
       *   Keycloak client ID.
       * @property {string} sessionId
       *   Keycloak session ID.
       * @property {Array} callbackList
       *   Array of active promises (FIFO).
       */
      var sessionIframe = {
        initialized: false,
        enable: drupalSettings.keycloak.enableSessionCheck,
        iframeUrl: drupalSettings.keycloak.sessionCheckIframeUrl,
        interval: isNaN(drupalSettings.keycloak.sessionCheckInterval) ? 2 : Number(drupalSettings.keycloak.sessionCheckInterval),
        logoutUrl: drupalSettings.keycloak.logoutUrl,
        logout: drupalSettings.keycloak.logout,
        clientId: drupalSettings.keycloak.clientId,
        sessionId: drupalSettings.keycloak.sessionId,
        callbackList: []
      };

      /**
       * Return a promise.
       *
       * @return {object}
       *   Keycloak promise.
       */
      function createPromise() {
        var p = {
          setSuccess: function (result) {
            p.success = true;
            p.result = result;
            if (p.successCallback) {
              p.successCallback(result);
            }
          },

          setError: function (result) {
            p.error = true;
            p.result = result;
            if (p.errorCallback) {
              p.errorCallback(result);
            }
          },

          promise: {
            success: function (callback) {
              if (p.success) {
                callback(p.result);
              }
              else if (!p.error) {
                p.successCallback = callback;
              }
              return p.promise;
            },
            error: function (callback) {
              if (p.error) {
                callback(p.result);
              }
              else if (!p.success) {
                p.errorCallback = callback;
              }
              return p.promise;
            }
          }
        };

        return p;
      }

      /**
       * Return window location origin.
       *
       * @return {string}
       *   Location origin.
       */
      function getOrigin() {
        if (!window.location.origin) {
          return window.location.protocol + '//' + window.location.hostname + (window.location.port ? ':' + window.location.port : '');
        }
        else {
          return window.location.origin;
        }
      }

      /**
       * Initialize session check iframe.
       *
       * @return {object}
       *   Keycloak promise.
       */
      function setupSessionCheckIframe() {
        var promise = createPromise();

        if (!sessionIframe.enable) {
          promise.setSuccess();
          return promise.promise;
        }

        if (sessionIframe.iframe) {
          promise.setSuccess();
          return promise.promise;
        }

        var iframe = document.createElement('iframe');
        sessionIframe.iframe = iframe;

        iframe.onload = function () {
          var iframeUrl = sessionIframe.iframeUrl;
          if (iframeUrl.charAt(0) === '/') {
            sessionIframe.iframeOrigin = getOrigin();
          }
          else {
            sessionIframe.iframeOrigin = iframeUrl.substring(0, iframeUrl.indexOf('/', 8));
          }
          promise.setSuccess();

          setTimeout(check, sessionIframe.interval * 1000);
        };

        iframe.setAttribute('src', sessionIframe.iframeUrl);
        iframe.setAttribute('title', 'keycloak-session-iframe');
        iframe.style.display = 'none';
        document.body.appendChild(iframe);

        var messageCallback = function (event) {
          if ((event.origin !== sessionIframe.iframeOrigin) || (sessionIframe.iframe.contentWindow !== event.source)) {
            return;
          }

          if (!(event.data === 'unchanged' || event.data === 'changed' || event.data === 'error')) {
            return;
          }

          if (event.data !== 'unchanged') {
            sessionIframe.logout = true;
          }

          var callbacks = sessionIframe.callbackList.splice(0, sessionIframe.callbackList.length);

          for (var i = callbacks.length - 1; i >= 0; --i) {
            var promise = callbacks[i];
            if (event.data === 'unchanged') {
              promise.setSuccess();
            }
            else {
              promise.setError();
            }
          }
        };

        window.addEventListener('message', messageCallback, false);

        var sessionExpiredCallback = function () {
          // For now, we simply redirect to the logout page.
          // To meeting OpenID Connect specifications, we should first
          // try to refresh the session by triggering a sign on request
          // without prompt.
          window.location.href = sessionIframe.logoutUrl;
        };

        var check = function () {
          checkSessionIframe().error(sessionExpiredCallback);
          if (!sessionIframe.logout) {
            setTimeout(check, sessionIframe.interval * 1000);
          }
        };

        return promise.promise;
      }

      function checkSessionIframe() {
        var promise = createPromise();

        if (sessionIframe.iframe && sessionIframe.iframeOrigin) {
          var msg = sessionIframe.clientId + ' ' + sessionIframe.sessionId;
          sessionIframe.callbackList.push(promise);
          var origin = sessionIframe.iframeOrigin;
          if (sessionIframe.callbackList.length === 1) {
            sessionIframe.iframe.contentWindow.postMessage(msg, origin);
          }
        }
        else {
          promise.setSuccess();
        }

        return promise.promise;
      }

      // Initialize the session check.
      $(document).once('keycloak').each(function () {
        if (sessionIframe.enable && !sessionIframe.initialized) {
          setupSessionCheckIframe()
            .success(function () {
              sessionIframe.initialized = true;
            })
            .error(function () {
              // console.log('[KEYCLOAK SSO] Error initializing session check iframe.');
            });
        }
      });

    }
  };
})(jQuery, Drupal, drupalSettings);
