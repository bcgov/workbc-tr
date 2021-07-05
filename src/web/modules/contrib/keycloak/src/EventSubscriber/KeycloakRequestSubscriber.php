<?php

namespace Drupal\keycloak\EventSubscriber;

use Drupal\Core\Language\LanguageInterface;
use Drupal\Core\Language\LanguageManagerInterface;
use Drupal\Core\Path\PathMatcherInterface;
use Drupal\Core\PathProcessor\InboundPathProcessorInterface;
use Drupal\Core\Routing\TrustedRedirectResponse;
use Drupal\Core\Url;
use Drupal\keycloak\Service\KeycloakServiceInterface;
use Drupal\openid_connect\OpenIDConnectStateToken;
use Symfony\Component\HttpKernel\KernelEvents;
use Symfony\Component\HttpKernel\Event\GetResponseEvent;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;

/**
 * Redirect subscriber for controller requests.
 */
class KeycloakRequestSubscriber implements EventSubscriberInterface {

  /**
   * The Keycloak service.
   *
   * @var \Drupal\keycloak\KeycloakServiceInterface
   */
  protected $keycloak;

  /**
   * The language manager.
   *
   * @var \Drupal\Core\Language\LanguageManagerInterface
   */
  protected $languageManager;

  /**
   * A path processor manager for resolving the system path.
   *
   * @var \Drupal\Core\PathProcessor\InboundPathProcessorInterface
   */
  protected $pathProcessor;

  /**
   * The path matcher.
   *
   * @var \Drupal\Core\Path\PathMatcherInterface
   */
  protected $pathMatcher;

  /**
   * Constructs a RedirectRequestSubscriber.
   *
   * @param \Drupal\keycloak\Service\KeycloakServiceInterface $keycloak
   *   The Keycloak service.
   * @param \Drupal\Core\Language\LanguageManagerInterface $language_manager
   *   The language manager service.
   * @param \Drupal\Core\PathProcessor\InboundPathProcessorInterface $path_processor
   *   Inbound path processor manager.
   * @param \Drupal\Core\Path\PathMatcherInterface $path_matcher
   *   The path matcher.
   */
  public function __construct(KeycloakServiceInterface $keycloak, LanguageManagerInterface $language_manager, InboundPathProcessorInterface $path_processor, PathMatcherInterface $path_matcher) {
    $this->keycloak = $keycloak;
    $this->languageManager = $language_manager;
    $this->pathProcessor = $path_processor;
    $this->pathMatcher = $path_matcher;
  }

  /**
   * Redirects keycloak logout requests to Keycloak.
   *
   * @param \Symfony\Component\HttpKernel\Event\GetResponseEvent $event
   *   The event to process.
   */
  public function onKernelRequestCheckKeycloakRedirect(GetResponseEvent $event) {
    // Whether Keycloak is enabled and configured for RP initiated
    // Single Sign-Out.
    if (!$this->keycloak->isKeycloakSignOutEnabled()) {
      return;
    }

    $request = clone $event->getRequest();

    // Whether the request is not a GET or redirect request.
    if (!($request->isMethod('GET') || $request->isMethod('HEAD'))) {
      return;
    }

    // Whether the path of the request doesn't match our
    // keycloak.logout route.
    $path = $this->pathProcessor->processInbound($request->getPathInfo(), $request);
    $language_none = $this->languageManager->getLanguage(LanguageInterface::LANGCODE_NOT_APPLICABLE);
    $pattern = Url::fromRoute('keycloak.logout', [], [
      'language' => $language_none,
    ])->toString();
    if (!$this->pathMatcher->matchPath($path, $pattern)) {
      return;
    }

    // Extract query parameters.
    parse_str($request->getQueryString(), $request_query);
    // Whether this is not a Keycloak Single Sign-Out request.
    if (empty($request_query['id_token_hint'])) {
      return;
    }

    // Construct the Keycloak end session endpoint parameters.
    $query = [
      'state' => OpenIDConnectStateToken::create(),
    ] + $request_query;

    // Whether to add language parameter. This is only needed,
    // if Keycloak is configured to ask the user for logout
    // confirmation.
    if ($this->keycloak->isI18nEnabled()) {
      // Get current language.
      $langcode = $this->languageManager->getCurrentLanguage()->getId();
      // Map Drupal language code to Keycloak language identifier.
      // This is required for some languages, as Drupal uses IETF
      // script codes, while Keycloak may use IETF region codes.
      $languages = $this->keycloak->getI18nMapping();
      if (!empty($languages[$langcode])) {
        $langcode = $languages[$langcode]['locale'];
      }
      // Add parameter to request query, so the Keycloak login/register
      // pages will load using the right locale.
      $query['kc_locale'] = $langcode;
    }

    // Generate the endpoint URL including parameters.
    $sign_out_endpoint = Url::fromUri($this->keycloak->getKeycloakSignOutEndpoint(), [
      'query' => $query,
    ])->toString(TRUE)->getGeneratedUrl();

    // Alter the response to redirect to the endpoint.
    $response = new TrustedRedirectResponse(
      $sign_out_endpoint,
      302
    );

    $event->setResponse($response);
  }

  /**
   * {@inheritdoc}
   */
  public static function getSubscribedEvents() {
    $events[KernelEvents::REQUEST][] = ['onKernelRequestCheckKeycloakRedirect', 35];

    return $events;
  }

}
