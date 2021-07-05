<?php

namespace Drupal\paragraphs_grid\Plugin\Field\FieldType;

use Drupal\Component\Utility\Random;
use Drupal\Core\Field\FieldDefinitionInterface;
use Drupal\Core\Field\FieldItemBase;
use Drupal\Core\Field\FieldStorageDefinitionInterface;
use Drupal\Core\Form\FormStateInterface;
use Drupal\Core\StringTranslation\TranslatableMarkup;
use Drupal\Core\TypedData\DataDefinition;
use Drupal\Core\TypedData\DataDefinitionInterface;
use Drupal\Core\TypedData\TypedDataInterface;

/**
 * Plugin implementation of the 'grid_field_type' field type.
 *
 * @FieldType(
 *   id = "grid_field_type",
 *   label = @Translation("Paragraphs grid"),
 *   description = @Translation("Provides a field where bootstrap grid classes can be defined for the parent entity."),
 *   category = @Translation("Reference revisions"),
 *   default_widget = "grid_widget",
 *   default_formatter = "grid_field_formatter",
 *   group = "default",
 *   cardinality = 1,
 *   target_types = {"paragraph"},
 * )
 */
class GridFieldType extends FieldItemBase {

  /**
   * The module config.
   *
   * @var \Drupal\Core\Config\ImmutableConfig
   */
  protected $moduleConfig;

  /**
   * The configured grid_entity.
   *
   * @var \Drupal\Core\Config\ImmutableConfig
   */
  protected $gridConfig;

  /**
   * {@inheritdoc}
   */
  public function __construct(DataDefinitionInterface $definition, $name = NULL, TypedDataInterface $parent = NULL) {
    parent::__construct($definition, $name, $parent);
    /* @ToDo use dependencies injection when https://www.drupal.org/node/2053415 is fixed */
    $config_factory = \Drupal::service('config.factory');
    $this->moduleConfig = $config_factory->get('paragraphs_grid.settings');
    $this->gridConfig = $config_factory->get($this->moduleConfig->get('gridtype'));
  }

  /**
   * {@inheritdoc}
   */
  public static function defaultStorageSettings() {
    return [
      'optional' => ['offset'],
      'view_modes_enabled' => TRUE,
    ] + parent::defaultStorageSettings();
  }

  /**
   * Return optional options for form element.
   *
   * @return array
   *   The optional options.
   */
  protected function cellPropsOptions() {
    $options = [];
    foreach ($this->gridConfig->get('cell-properties') as $def) {
      if ($def['optional']) {
        $options[$def['name']] = $def['label'];
      }
    };
    return $options;
  }

  /**
   * {@inheritdoc}
   */
  public function storageSettingsForm(array &$form, FormStateInterface $form_state, $has_data) {
    $elements = [];

    $settings = $this->getSetting('optional');
    $elements['optional'] = [
      '#type' => 'checkboxes',
      '#title' => $this->t('Grid properties'),
      '#description' => $this->t('WARNING: Limit the number of props to a minimum to avoid overloaded form elements.'),
      '#default_value' => $settings,
      '#options' => $this->cellPropsOptions(),
    ];
    $elements['view_modes_enabled'] = [
      '#type' => 'checkbox',
      '#title' => $this->t('View modes enabled'),
      '#description' => $this->t('Enable view mode selector in paragraph edit form. (View mode selector is only visible if there are view modes to select.)'),
      '#default_value' => $this->getSetting('view_modes_enabled'),
    ];
    return $elements;
  }

  /**
   * {@inheritdoc}
   */
  public static function propertyDefinitions(FieldStorageDefinitionInterface $field_definition) {
    // Prevent early t() calls by using the TranslatableMarkup.
    $properties['value'] = DataDefinition::create('string')
      ->setLabel(new TranslatableMarkup('Grid classes'))
      ->setSetting('case_sensitive', TRUE)
      ->setRequired(FALSE);
    $properties['view_mode'] = DataDefinition::create('string')
      ->setLabel(new TranslatableMarkup('View mode'))
      ->setSetting('case_sensitive', TRUE)
      ->setRequired(FALSE);

    return $properties;
  }

  /**
   * {@inheritdoc}
   */
  public static function schema(FieldStorageDefinitionInterface $field_definition) {
    $schema = [
      'columns' => [
        'value' => [
          'type' => 'varchar',
          'length' => 512,
        ],
        'view_mode' => [
          'type' => 'varchar',
          'length' => 64,
        ],
      ],
    ];

    return $schema;
  }

  /**
   * {@inheritdoc}
   */
  public function getConstraints() {
    $constraints = parent::getConstraints();
    return $constraints;
  }

  /**
   * {@inheritdoc}
   */
  public static function generateSampleValue(FieldDefinitionInterface $field_definition) {
    $random = new Random();
    $values['value'] = $random->word(mt_rand(1, 511));
    $values['view_mode'] = $random->word(mt_rand(1, 63));
    return $values;
  }

  /**
   * {@inheritdoc}
   */
  public function isEmpty() {
    $value = $this->get('value')->getValue();
    $view_mode = $this->get('view_mode')->getValue();
    return ($value === NULL || $value === '') && ($view_mode === NULL || $view_mode === '');
  }

}
