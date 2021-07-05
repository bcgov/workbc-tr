<?php

namespace Drupal\paragraph_blocks\Plugin\Block;

use Drupal\Component\Utility\Html;
use Drupal\Core\Entity\EntityTypeManagerInterface;
use Drupal\Core\Plugin\ContainerFactoryPluginInterface;
use Drupal\Core\Plugin\ContextAwarePluginInterface;
use Drupal\Core\Block\BlockBase;
use Drupal\Core\Cache\CacheableMetadata;
use Symfony\Component\DependencyInjection\ContainerInterface;

/**
 * Provides a block to a paragraph value on an entity.
 *
 * @Block(
 *   id = "paragraph_field",
 *   deriver = "Drupal\paragraph_blocks\Plugin\Deriver\ParagraphBlocksDeriver",
 *   category = @Translation("Content")
 * )
 */
class ParagraphBlock extends BlockBase implements ContextAwarePluginInterface, ContainerFactoryPluginInterface {

  /**
   * The entity type id.
   *
   * @var string
   */
  protected $entityTypeId;

  /**
   * The field name.
   *
   * @var string
   */
  protected $fieldName;

  /**
   * The field delta.
   *
   * @var int
   */
  protected $fieldDelta;

  /**
   * The entity type manager.
   *
   * @var \Drupal\Core\Entity\EntityTypeManagerInterface
   */
  protected $entityTypeManager;

  /**
   * Constructs a new ParagraphBlock.
   *
   * @param array $configuration
   *   A configuration array containing information about the plugin instance.
   * @param string $plugin_id
   *   The plugin ID for the plugin instance.
   * @param mixed $plugin_definition
   *   The plugin implementation definition.
   * @param \Drupal\Core\Entity\EntityTypeManagerInterface $entity_type_manager
   *   The entity type manager.
   */
  public function __construct(array $configuration, $plugin_id, $plugin_definition, EntityTypeManagerInterface $entity_type_manager) {
    $this->entityTypeManager = $entity_type_manager;

    // Get the field delta from the plugin.
    list(, $entity_type_id, $field_name, $field_delta) = explode(':', $plugin_id);
    $this->entityTypeId = $entity_type_id;
    $this->fieldName = $field_name;
    $this->fieldDelta = $field_delta;

    parent::__construct($configuration, $plugin_id, $plugin_definition);
  }

  /**
   * {@inheritdoc}
   */
  public static function create(ContainerInterface $container, array $configuration, $plugin_id, $plugin_definition) {
    return new static(
      $configuration,
      $plugin_id,
      $plugin_definition,
      $container->get('entity_type.manager')
    );
  }

  /**
   * {@inheritdoc}
   */
  public function build() {
    // Get the referencing and referenced entity.
    /** @var \Drupal\Core\Entity\EntityInterface $entity */
    $paragraph = NULL;
    $entity = $this->getContextEntity();
    if ($entity) {
      $referenced_entities = $entity
        ->get($this->fieldName)
        ->referencedEntities();
      if (isset($referenced_entities[$this->fieldDelta])) {
        $paragraph = $referenced_entities[$this->fieldDelta];
      }
    }
    if (!$paragraph) {
      // The Content group block exists on the page, but the page's Content
      // group has been removed.
      return [
        '#markup' => $this->t('This block is broken. The Content group or the paragraph does not exist.'),
      ];
    }

    // Build the render array.
    /** @var \Drupal\Core\Entity\EntityViewBuilder $view_builder */
    $view_builder = $this->entityTypeManager->getViewBuilder($paragraph->getEntityTypeId());
    $build = $view_builder->view($paragraph, 'default');

    // Add geysir contextual links.
    if (function_exists('geysir_contextual_links')) {
      $link_options = [
        'parent_entity_type' => $entity->getEntityType()->id(),
        'parent_entity' => $entity->id(),
        'field' => $this->fieldName,
        'field_wrapper_id' => Html::getUniqueId('geysir--' . $this->fieldName),
        'delta' => $this->fieldDelta,
        'js' => 'nojs',
        'paragraph' => $paragraph->id(),
      ];
      $build['#geysir_field_paragraph_links'] = geysir_contextual_links($link_options);
      $build['#theme_wrappers'][] = 'geysir_field_paragraph_wrapper';
      $build['#attributes']['data-geysir-field-paragraph-field-wrapper'] = $link_options['field_wrapper_id'];
    }

    // Set the cache data appropriately.
    CacheableMetadata::createFromObject($this->getContext('entity'))
      ->applyTo($build);

    return $build;
  }

  /**
   * Return the entity that contains the paragraph.
   *
   * @return \Drupal\Core\Entity\EntityInterface
   *   Returns the entity that holds the paragraph field.
   */
  protected function getContextEntity() {
    return $this->getContextValue('entity');
  }

}
