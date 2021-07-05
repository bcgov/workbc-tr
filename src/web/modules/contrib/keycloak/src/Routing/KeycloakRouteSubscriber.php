<?php

namespace Drupal\keycloak\Routing;

use Drupal\Core\Routing\RouteSubscriberBase;
use Drupal\Core\Routing\RoutingEvents;
use Drupal\keycloak\Service\KeycloakServiceInterface;
use Symfony\Component\Routing\RouteCollection;

/**
 * Listens to dynamic route events.
 */
class KeycloakRouteSubscriber extends RouteSubscriberBase {

  /**
   * The Keycloak service.
   *
   * @var \Drupal\keycloak\Service\KeycloakServiceInterface
   */
  protected $keycloak;

  /**
   * Construct a KeycloakRouteSubscriber object.
   *
   * @param \Drupal\keycloak\Service\KeycloakServiceInterface $keycloak
   *   A Keycloak service instance.
   */
  public function __construct(KeycloakServiceInterface $keycloak) {
    $this->keycloak = $keycloak;
  }

  /**
   * {@inheritdoc}
   */
  protected function alterRoutes(RouteCollection $collection) {
    // Whether the Keycloak client is disabled.
    if (!$this->keycloak->isEnabled()) {
      return;
    }

    // Whether Keycloak single sign-on is enabled.
    if ($this->keycloak->isSsoEnabled() && $route = $collection->get('user.login')) {
      $route
        ->setDefaults([
          '_controller' => '\Drupal\keycloak\Controller\KeycloakController::login',
        ])
        ->setOptions([
          '_maintenance_access' => TRUE,
          'no_cache' => TRUE,
        ]);
    }

    // Always grant access to '/user/logout' and delegate its
    // handling to our own controller.
    if (($this->keycloak->isKeycloakSignOutEnabled() || $this->keycloak->isCheckSessionEnabled()) && $route = $collection->get('user.logout')) {
      $route
        ->setDefaults([
          '_controller' => '\Drupal\keycloak\Controller\KeycloakController::logout',
        ])
        ->setRequirements([
          '_access' => 'TRUE',
        ])
        ->setOptions([
          'no_cache' => TRUE,
        ]);
    }
  }

  /**
   * {@inheritdoc}
   */
  public static function getSubscribedEvents() {
    // Come after field_ui.
    $events[RoutingEvents::ALTER] = ['onAlterRoutes', -200];

    return $events;
  }

}
