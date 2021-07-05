<?php

namespace Drupal\paragraph_blocks\Routing;

use Drupal\Core\Routing\RouteSubscriberBase;
use Symfony\Component\Routing\RouteCollection;

/**
 * Listen to route events to override panels.select_block controller.
 */
class ParagraphBlocksRouteSubscriber extends RouteSubscriberBase {

  /**
   * {@inheritdoc}
   */
  public function alterRoutes(RouteCollection $collection) {
    $route = $collection->get('panels.select_block');
    if ($route) {
      $route->setDefaults([
        '_controller' => '\Drupal\paragraph_blocks\Controller\ParagraphBlocksPanelsController::selectBlock',
      ]);
    }
  }

}
