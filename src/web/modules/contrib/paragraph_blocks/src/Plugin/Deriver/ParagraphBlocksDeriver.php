<?php

namespace Drupal\paragraph_blocks\Plugin\Deriver;

use Drupal\Core\Config\ConfigFactoryInterface;
use Drupal\Core\Entity\EntityTypeManagerInterface;
use Drupal\Core\Entity\EntityFieldManagerInterface;
use Drupal\Core\Entity\EntityTypeRepositoryInterface;
use Drupal\Core\Plugin\Context\ContextDefinition;
use Drupal\Core\Plugin\Context\EntityContextDefinition;
use Drupal\Core\StringTranslation\TranslationInterface;
use Drupal\ctools\Plugin\Deriver\EntityDeriverBase;
use Symfony\Component\DependencyInjection\ContainerInterface;

/**
 * Provides entity field block definitions for every field.
 */
class ParagraphBlocksDeriver extends EntityDeriverBase {

  /**
   * Constructs new EntityViewDeriver.
   *
   * @param \Drupal\Core\Entity\EntityTypeManagerInterface $entity_type_manager
   *   The entity type manager.
   * @param \Drupal\Core\StringTranslation\TranslationInterface $string_translation
   *   The string translation service.
   * @param \Drupal\Core\Entity\EntityFieldManagerInterface $entity_field_manager
   *   The entity field manager.
   * @param \Drupal\Core\Entity\EntityTypeRepositoryInterface $entity_type_repository
   *   The entity type repository.
   * @param \Drupal\Core\Config\ConfigFactoryInterface $config_factory
   *   The config factory.
   */
  public function __construct(EntityTypeManagerInterface $entity_type_manager, TranslationInterface $string_translation, EntityFieldManagerInterface $entity_field_manager, EntityTypeRepositoryInterface $entity_type_repository, ConfigFactoryInterface $config_factory) {
    $this->entityTypeManager = $entity_type_manager;
    $this->stringTranslation = $string_translation;
    $this->entityFieldManager = $entity_field_manager;
    $this->entityTypeRepository = $entity_type_repository;
    $this->maxCardinality = $config_factory->get('paragraph_blocks.settings')->get('max_cardinality') ?: 10;
  }

  /**
   * {@inheritdoc}
   */
  public static function create(ContainerInterface $container, $base_plugin_id) {
    return new static(
      $container->get('entity_type.manager'),
      $container->get('string_translation'),
      $container->get('entity_field.manager'),
      $container->get('entity_type.repository'),
      $container->get('config.factory')
    );
  }

  /**
   * {@inheritdoc}
   */
  public function getDerivativeDefinitions($base_plugin_definition) {
    foreach ($this->entityFieldManager->getFieldMap() as $entity_type_id => $field_info) {
      if ($entity_type_id == 'paragraph') {
        continue;
      }

      /** @var \Drupal\Core\Field\FieldStorageDefinitionInterface $field_storage_definition */
      foreach ($this->entityFieldManager->getFieldStorageDefinitions($entity_type_id) as $field_storage_definition) {
        $field_name = $field_storage_definition->getName();
        $field_storage_type = $field_storage_definition->getType();
        /** @var \Drupal\Core\Field\FieldStorageDefinitionInterface $field_storage_definition */
        if (!isset($field_info[$field_name]) || $field_storage_definition->getType() != 'entity_reference_revisions' || $field_storage_definition->getSettings()['target_type'] != 'paragraph') {
          continue;
        }

        // Create a plugin of maximum number of cardinality this field allows.
        // Unavailable items are removed and labels are overridden in the
        // paragraph_blocks.labeller service.
        $cardinality = $field_storage_definition->getCardinality();
        if ($cardinality == 1) {
          // Skip fields with cardinality one. This can be handled as a field.
          continue;
        }
        if ($cardinality === -1) {
          $cardinality = $this->maxCardinality;
        }
        $bundles = $field_info[$field_name]['bundles'];
        foreach ($bundles as $bundle) {
          for ($delta = 0; $delta < $cardinality; $delta++) {
            $admin_label = $this->t('Paragraph item @delta', [
              '@delta' => $delta,
            ]);
            if (count($bundles) > 1) {
              $bundle_field_definitions = $this->entityFieldManager->getFieldDefinitions($entity_type_id, $bundle);
              $bundle_label = $bundle_field_definitions[$field_name]->getLabel();
              $admin_label .= '(' . $bundle_label . ')';
            }
            $plugin_id = "$entity_type_id:$field_name:$delta:$bundle";
            $this->derivatives[$plugin_id] = [
              'context_definitions' => [
                'entity' => EntityContextDefinition::fromEntityTypeId($entity_type_id),
              ],
              'admin_label' => $admin_label,
            ] + $base_plugin_definition;
          }
        }
      }
    }
    return $this->derivatives;
  }

}
