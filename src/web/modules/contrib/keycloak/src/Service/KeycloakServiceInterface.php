<?php

namespace Drupal\keycloak\Service;

use Drupal\Core\Config\ConfigFactoryInterface;
use Drupal\Core\Language\LanguageManagerInterface;
use Drupal\Core\Logger\LoggerChannelFactoryInterface;
use Drupal\Core\Session\AccountProxyInterface;
use Drupal\openid_connect\Plugin\OpenIDConnectClientManager;
use Drupal\Core\TempStore\PrivateTempStoreFactory;

/**
 * Keycloak service interface.
 */
interface KeycloakServiceInterface {

  /**
   * Default Keycloak OpenID configuration endpoint URI.
   */
  const KEYCLOAK_CONFIG_ENDPOINT_URI = '/.well-known/openid-configuration';

  /**
   * Default Keycloak authorization endpoint URI.
   */
  const KEYCLOAK_AUTH_ENDPOINT_URI = '/protocol/openid-connect/auth';

  /**
   * Default Keycloak token endpoint URI.
   */
  const KEYCLOAK_TOKEN_ENDPOINT_URI = '/protocol/openid-connect/token';

  /**
   * Default Keycloak userinfo endpoint URI.
   */
  const KEYCLOAK_USERINFO_ENDPOINT_URI = '/protocol/openid-connect/userinfo';

  /**
   * Default Keycloak end session endpoint URI for single sign-out propagation.
   */
  const KEYCLOAK_END_SESSION_ENDPOINT_URI = '/protocol/openid-connect/logout';

  /**
   * Default Keycloak check session iframe URI.
   */
  const KEYCLOAK_CHECK_SESSION_IFRAME_URI = '/protocol/openid-connect/login-status-iframe.html';

  /**
   * Keycloak access token.
   */
  const KEYCLOAK_SESSION_ACCESS_TOKEN = 'access_token';

  /**
   * Keycloak refresh token.
   */
  const KEYCLOAK_SESSION_REFRESH_TOKEN = 'refresh_token';

  /**
   * Keycloak ID token.
   */
  const KEYCLOAK_SESSION_ID_TOKEN = 'id_token';

  /**
   * Keycloak client ID.
   */
  const KEYCLOAK_SESSION_CLIENT_ID = 'client_id';

  /**
   * Keycloak session ID.
   */
  const KEYCLOAK_SESSION_SESSION_ID = 'session_id';

  /**
   * Constructor for Drupal\keycloak\Service\KeycloakService.
   *
   * @param \Drupal\Core\Config\ConfigFactoryInterface $config_factory
   *   The config factory.
   * @param \Drupal\openid_connect\Plugin\OpenIDConnectClientManager $oidc_client_manager
   *   Client plugin manager of the OpenID Connect module.
   * @param \Drupal\Core\Language\LanguageManagerInterface $language_manager
   *   A language manager instance.
   * @param \Drupal\Core\Session\AccountProxyInterface $current_user
   *   Account proxy for the currently logged-in user.
   * @param \Drupal\Core\TempStore\PrivateTempStoreFactory $private_tempstore
   *   A private tempstore factory instance.
   * @param \Drupal\Core\Logger\LoggerChannelFactoryInterface $logger
   *   A logger channel factory instance.
   */
  public function __construct(
    ConfigFactoryInterface $config_factory,
    OpenIDConnectClientManager $oidc_client_manager,
    LanguageManagerInterface $language_manager,
    AccountProxyInterface $current_user,
    PrivateTempStoreFactory $private_tempstore,
    LoggerChannelFactoryInterface $logger
  );

  /**
   * Whether the Keycloak client is enabled.
   *
   * @return bool
   *   TRUE, if the Keycloak client is enabled, FALSE otherwise.
   */
  public function isEnabled();

  /**
   * Return the Keycloak base URL.
   *
   * @return string
   *   Keycloak base URL.
   */
  public function getBaseUrl();

  /**
   * Return the Keycloak realm.
   *
   * @return string
   *   Keycloak realm.
   */
  public function getRealm();

  /**
   * Return the available Keycloak endpoints.
   *
   * @return array
   *   Associative array with Keycloak endpoints:
   *   - authorization:         Authorization endpoint.
   *   - token:                 Token endpoint.
   *   - userinfo:              User info endpoint.
   *   - end_session:           End session endpoint.
   *   - session_iframe:        Session iframe URL.
   */
  public function getEndpoints();

  /**
   * Whether the currently logged in user was logged in using Keycloak.
   *
   * @return bool
   *   TRUE, if user was logged in using Keycloak, FALSE otherwise.
   */
  public function isKeycloakUser();

  /**
   * Return an array of available Keycloak session info keys.
   *
   * @return array
   *   Keycloak session info keys.
   */
  public function getSessionInfoDefaultKeys();

  /**
   * Return an associative array of Keycloak session information.
   *
   * @param array|null $keys
   *   (optional) Array of session info keys to retrieve or NULL. If no
   *   keys are provided, the entire session info will be returned.
   *   Defaults to NULL.
   *
   * @return array
   *   Associative array of Keycloak session information.
   */
  public function getSessionInfo($keys = NULL);

  /**
   * Store the Keycloak session information to the user session.
   *
   * @param array $info
   *   Associative array with session information. The information that
   *   will be stored is limited to the allowed keys returned by
   *   self::getSessionInfoDefaultKeys().
   *
   * @return bool
   *   TRUE, if the information was set, FALSE otherwise.
   */
  public function setSessionInfo(array $info);

  /**
   * Whether Keycloak multi-language support is enabled.
   *
   * @return bool
   *   TRUE, if multi-language support is enabled, FALSE otherwise.
   */
  public function isI18nEnabled();

  /**
   * Return the Keycloak i18n locale code mapping.
   *
   * This mapping is required for some languages, as Drupal uses IETF
   * script codes, while Keycloak may use IETF region codes for its
   * localization.
   *
   * @param bool $reverse
   *   (optional) Whether to use Drupal language IDs as keys (FALSE), or
   *   Keycloak locales (TRUE).
   *   Defaults to FALSE.
   * @param bool $include_enabled
   *   (optional) Whether to include non-mapped, but in Drupal enabled
   *   languages. If no mapping is set for an enabled language, the Drupal
   *   language ID will be used as Keycloak locale. (Which most often
   *   matches the Keycloak locales by default.)
   *   Defaults to TRUE.
   *
   * @return array
   *   Associative array with i18n locale mappings with keys as specified
   *   with the $reverse parameter and an associative locale map array as
   *   value, having the following keys:
   *   - language_id:           Drupal language ID.
   *   - locale:                Keycloak locale.
   *   - label:                 Localized human-readable language label.
   */
  public function getI18nMapping($reverse = FALSE, $include_enabled = TRUE);

  /**
   * Whether Keycloak single sign-on (SSO) is enabled.
   *
   * @return bool
   *   TRUE, if single sign-on is enabled, FALSE otherwise.
   */
  public function isSsoEnabled();

  /**
   * Whether RP (Drupal) initiated Single Sing-Out is enabled.
   *
   * @return bool
   *   TRUE, if RP inititated sign out is enabled, FALSE otherwise.
   */
  public function isKeycloakSignOutEnabled();

  /**
   * Return the Keycloak Single Sing-Out endpoint.
   *
   * @return string
   *   Keycloak Single Sing-Out endpoint.
   */
  public function getKeycloakSignOutEndpoint();

  /**
   * Return a RP (Drupal) initiated single sign-out response.
   *
   * @param array $session_information
   *   Session information array holding the required id_token.
   *
   * @return \Symfony\Component\HttpFoundation\RedirectResponse
   *   Redirect response redirecting to the sign out target route:
   *   - '&lt;front&gt;' if Keycloak single sign-out is disabled.
   *   - 'keycloak.logout' if Keycloak single sign-out is enabled.
   */
  public function getKeycloakSignoutResponse(array $session_information);

  /**
   * Whether OP (Keycloak) initiated Single Sing-Out is enabled.
   *
   * @return bool
   *   TRUE, if OP inititated sign out is enabled, FALSE otherwise.
   */
  public function isCheckSessionEnabled();

  /**
   * Return the check session interval.
   *
   * @return int
   *   The interval for check session requests in seconds.
   */
  public function getCheckSessionInterval();

  /**
   * Return the check session iframe URL.
   *
   * @return string
   *   The URL of the Keycloak check session iframe.
   */
  public function getCheckSessionIframeUrl();

  /**
   * Return a configured Keycloak client plugin for the openid_connect module.
   *
   * @return \Drupal\openid_connect\Plugin\OpenIDConnectClientInterface
   *   Keycloak client plugin for the openid_connect module.
   */
  public function getClientInstance();

  /**
   * Return Keycloak logger.
   *
   * @return \Psr\Log\LoggerInterface
   *   Logger instance for the Keycloak module.
   */
  public function getLogger();

  /**
   * Whether the Keycloak client is in verbose debug mode.
   *
   * @return bool
   *   TRUE, if debug mode is enabled, FALSE otherwise.
   */
  public function isDebugMode();

}
