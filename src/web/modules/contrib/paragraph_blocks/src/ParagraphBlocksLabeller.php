<?php

namespace Drupal\paragraph_blocks;

use Drupal\Core\Entity\EntityFieldManagerInterface;
use Drupal\Core\Form\FormStateInterface;
use Drupal\Core\StringTranslation\StringTranslationTrait;

/**
 * Labels the paragraph blocks once the entity context is known.
 */
class ParagraphBlocksLabeller {

  use StringTranslationTrait;

  /**
   * The plugin type id.
   *
   * @var string
   */
  const PLUGIN_TYPE_ID = 'paragraph_field';

  /**
   * The label format.
   *
   * @var string
   */
  const LABEL_FORMAT = 'Page: @label';

  /**
   * The current entity, or NULL.
   *
   * @var \Drupal\Core\Entity\EntityInterface|null
   */
  protected $entity;

  /**
   * The entity field manager.
   *
   * @var \Drupal\Core\Entity\EntityFieldManagerInterface
   */
  protected $entityFieldManager;

  /**
   * PanelsParagraphsPanelsIpeManager constructor.
   *
   * @param \Drupal\paragraph_blocks\ParagraphBlocksEntityManager $paragraph_blocks_entity_manager
   *   The Paragraph blocks entity manager.
   * @param \Drupal\Core\Entity\EntityFieldManagerInterface $entity_field_manager
   *   The interface for an entity field manager.
   */
  public function __construct(ParagraphBlocksEntityManager $paragraph_blocks_entity_manager, EntityFieldManagerInterface $entity_field_manager) {
    $this->entity = $paragraph_blocks_entity_manager->getRefererEntity();
    $this->entityFieldManager = $entity_field_manager;
  }

  /**
   * Change the title on the add/edit form.
   *
   * @param array &$form
   *   The form.
   * @param \Drupal\Core\Form\FormStateInterface $form_state
   *   The form state.
   *
   * @see hook_form_FORM_ID_alter()
   */
  public function hookFormPanelsIpeBlockPluginFormAlter(array &$form, FormStateInterface $form_state) {
    $title = $this->getTitle($form['plugin_id']['#value'], $enabled);
    if (!empty($title) && $enabled) {
      $section = &$form['flipper']['front']['settings'];
      $section['admin_label']['#type'] = 'hidden';
      $section['label']['#type'] = 'hidden';
      $section['label']['#required'] = FALSE;
      $section['label_display']['#type'] = 'hidden';
      $section['label_display']['#value'] = 0;
      $section['label']['#value'] = $title;
    }
  }

  /**
   * Removes unused paragraphs and update the panels title.
   *
   * @param array $blocks
   *   The blocks.
   *
   * @see hook_panels_ipe_blocks_alter()
   */
  public function hookPanelsIpeBlocksAlter(array &$blocks) {
    // Loop through all of the blocks.
    foreach ($blocks as $delta => $block) {
      $title = $this->getTitle($block['plugin_id'], $enabled);
      if ($title === FALSE) {
        // Remove the block if there is no paragraph data for the delta.
        unset($blocks[$delta]);
      }
      elseif ($title !== NULL) {
        // Replace the title.
        $blocks[$delta]['label'] = $title;
      }
    }
  }

  /**
   * Removed unused paragraphs and update the layout builder title.
   *
   * @param array $definitions
   *   The plugin definitions.
   */
  public function hookLayoutBuilderChooseBlocksAlter(array &$definitions) {
    // Loop through all of the plugin definitions.
    foreach ($definitions as $plugin_id => $definition) {
      $enabled = TRUE;
      $title = $this->getTitle($plugin_id, $enabled);
      if ($title === FALSE) {
        // Remove the block if there is no paragraph data for the delta.
        if ($this->entity || !$enabled) {
          unset($definitions[$plugin_id]);
        }
      }
      elseif ($title) {
        // Replace the title.
        $definitions[$plugin_id]['admin_label'] = $title;
      }
    }
  }

  /**
   * Returns the plugin's paragraph title.
   *
   * @param string $plugin_id
   *   The plugin id.
   * @param bool $enabled
   *   Return if the field is enabled or disabled.
   *
   * @return string
   *   The paragraph title.
   */
  public function getTitle($plugin_id, &$enabled) {
    $enabled = TRUE;

    $plugin_parts = explode(':', $plugin_id);
    if (count($plugin_parts) < 4) {
      return NULL;
    }
    list($plugin_type_id, $plugin_entity_type_id, $plugin_field_name, $plugin_field_delta) = $plugin_parts;
    if ($plugin_type_id != self::PLUGIN_TYPE_ID) {
      return NULL;
    }
    $plugin_field_bundle = count($plugin_parts) ? $plugin_parts[4] : '';

    // Only check the field bundle if it exists. This is new to the 2.x branch.
    // So this check exists for backwards compatability with plugins saved
    // using the 1.x branch.
    if ($plugin_field_bundle) {
      // Remove if this paragraph field is not enabled.
      $field_definitions = $this->entityFieldManager->getFieldDefinitions($plugin_entity_type_id, $plugin_field_bundle);
      $field_config = $field_definitions[$plugin_field_name]->getConfig($plugin_field_bundle);
      if (!$field_config->getThirdPartySetting('paragraph_blocks', 'status', TRUE)) {
        $enabled = FALSE;
        return FALSE;
      }
    }

    // Return if this plugin should be removed from the list.
    if (!$this->entity || $this->entity->bundle() != $plugin_field_bundle || $plugin_field_delta >= $this->entity->get($plugin_field_name)->count()) {
      return FALSE;
    }

    // Get the referenced paragraph.
    /** @var \Drupal\paragraphs\Entity\Paragraph $paragraph */
    $paragraph = $this->entity->get($plugin_field_name)->referencedEntities()[$plugin_field_delta];

    // Change the label to match admin_label from the referenced paragraph.
    return $this->t('Paragraph: @label', [
      '@label' => $paragraph->getSummary(),
    ]);
  }

}
