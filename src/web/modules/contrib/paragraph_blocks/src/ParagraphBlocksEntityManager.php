<?php

namespace Drupal\paragraph_blocks;

use Drupal\Core\Config\ConfigFactoryInterface;
use Drupal\Core\DependencyInjection\ContainerInjectionInterface;
use Drupal\Core\Entity\EntityTypeManagerInterface;
use Drupal\path_alias\AliasManagerInterface;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Symfony\Component\HttpFoundation\RequestStack;

/**
 * Class ParagraphBlocksEntityManager.
 */
class ParagraphBlocksEntityManager implements ContainerInjectionInterface {

  /**
   * The path alias manager.
   *
   * @var \Drupal\path_alias\AliasManagerInterface
   */
  protected $aliasManager;

  /**
   * The entity type manager.
   *
   * @var \Drupal\Core\Entity\EntityTypeManagerInterface
   */
  protected $entityTypeManager;

  /**
   * The request stack.
   *
   * @var \Symfony\Component\HttpFoundation\RequestStack
   */
  protected $requestStack;

  /**
   * The config factory.
   *
   * @var \Drupal\Core\Config\ConfigFactoryInterface
   */
  protected $configFactory;

  /**
   * ParagraphBlocksEntityManager constructor.
   */
  public function __construct(AliasManagerInterface $alias_manager, EntityTypeManagerInterface $entity_type_manager, RequestStack $request_stack, ConfigFactoryInterface $config_factory) {
    $this->aliasManager = $alias_manager;
    $this->entityTypeManager = $entity_type_manager;
    $this->requestStack = $request_stack;
    $this->configFactory = $config_factory;
  }

  /**
   * {@inheritdoc}
   */
  public static function create(ContainerInterface $container) {
    return new static(
      $container->get('path_alias.manager'),
      $container->get('entity_type.manager'),
      $container->get('request_stack'),
      $container->get('config.factory')
    );
  }

  /**
   * Return the entity of the path.
   *
   * @param string $path
   *   (optional) The path to lookup, defaults to the current path.
   *
   * @return \Drupal\Core\Entity\EntityInterface|null
   *   The entity of the current path, or null if none.
   *
   * @todo: This implements something similar to menu_get_object() which was
   * likely removed intentionally, so is this a bad idea?
   *
   * @see entity_css_get_entity()
   */
  public function getEntity($path = NULL) {
    // Get the current path if none is specified.
    if (!$path) {
      $path = parse_url(\Drupal::requestStack()->getCurrentRequest()->getRequestUri(), PHP_URL_PATH);
    }
    $base_path = base_path();
    if (empty($path) || $path == $base_path || $path == '<front>') {
      $path = \Drupal::config('system.site')->get('page.front');
    }

    // Convert the specified path to an internal path.
    $source_path = \Drupal::service('path_alias.manager')->getPathByAlias($path);
    $source_path = preg_replace(":^$base_path:", '', $source_path);

    // Check if this is the entity path.
    // @todo: this makes assumption that are possibly not valid. For example, is
    // the entity path always "/entity_type_id/entity_id"?
    $parts = explode('/', trim($source_path, '/'));
    if (count($parts) >= 2) {
      list($entity_type_id, $entity_id) = $parts;
      $entity_type_id = str_replace('-', '_', $entity_type_id);
      // Check that the path is for an entity.
      $entity_type_manager = \Drupal::entityTypeManager();
      /** @var \Drupal\Core\Entity\EntityTypeManagerInterface $entity_type_manager */
      $entity_types = $entity_type_manager->getDefinitions();
      if (is_numeric($entity_id) && isset($entity_types[$entity_type_id])) {
        /** @var \Drupal\Core\Entity\EntityStorageInterface $entity_storage */
        $entity_storage = $entity_type_manager->getStorage($entity_type_id);
        if ($entity_storage) {
          // Load and return the entity.
          return $entity_storage->load($entity_id);
        }
      }
    }
    return NULL;
  }

  /**
   * Return the entity of the referer, useful when called via AJAX.
   *
   * @return \Drupal\Core\Entity\EntityInterface|null
   *   The entity of the referer, or null if none.
   */
  public function getRefererEntity() {
    $referer = $this->requestStack->getCurrentRequest()->server->get('HTTP_REFERER');
    if ($referer) {
      $path = parse_url($referer, PHP_URL_PATH);
      if ($path) {
        return $this->getEntity($path);
      }
    }
    return NULL;
  }

}
