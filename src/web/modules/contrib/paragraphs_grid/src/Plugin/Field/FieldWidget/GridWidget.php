<?php

namespace Drupal\paragraphs_grid\Plugin\Field\FieldWidget;

use Drupal\Core\Config\ConfigFactory;
use Drupal\Core\Entity\EntityDisplayRepository;
use Drupal\Core\Field\FieldDefinitionInterface;
use Drupal\Core\Field\FieldItemListInterface;
use Drupal\Core\Field\WidgetBase;
use Drupal\Core\Form\FormStateInterface;
use Drupal\Core\Link;
use Drupal\Core\Plugin\ContainerFactoryPluginInterface;
use Drupal\Core\Session\AccountProxyInterface;
use Drupal\Core\StringTranslation\TranslatableMarkup;
use Drupal\Core\Template\Attribute;
use Drupal\Core\Url;
use Symfony\Component\DependencyInjection\ContainerInterface;

/**
 * Plugin implementation of the 'grid_widget' widget.
 *
 * @FieldWidget(
 *   id = "grid_widget",
 *   label = @Translation("Grid widget"),
 *   field_types = {
 *     "grid_field_type"
 *   },
 *   multiple_values = FALSE,
 * )
 */
class GridWidget extends WidgetBase implements ContainerFactoryPluginInterface {

  /**
   * The paragraphs_grid settings from config form used for the site.
   *
   * @var \Drupal\Core\Config\ImmutableConfig
   */
  protected $moduleConfig;

  /**
   * The grid config entity used for the site.
   *
   * @var \Drupal\Core\Config\ImmutableConfig
   */
  protected $gridConfig;

  /**
   * The current users account proxy.
   *
   * @var \Drupal\Core\Session\AccountProxyInterface
   */
  protected $currentUser;

  /**
   * The entity display repository from drupal.
   *
   * @var \Drupal\Core\Entity\EntityDisplayRepository
   */
  protected $entityDisplayRepository;

  /**
   * Constructs a WidgetBase object.
   *
   * @param string $plugin_id
   *   The plugin_id for the widget.
   * @param mixed $plugin_definition
   *   The plugin implementation definition.
   * @param \Drupal\Core\Field\FieldDefinitionInterface $field_definition
   *   The definition of the field to which the widget is associated.
   * @param array $settings
   *   The widget settings.
   * @param array $third_party_settings
   *   Any third party settings.
   * @param \Drupal\Core\Config\ConfigFactory $config_factory
   *   Drupal config factory.
   * @param \Drupal\Core\Session\AccountProxyInterface $current_user
   *   Current Drupal user.
   * @param \Drupal\Core\Entity\EntityDisplayRepository $entity_display_repository
   *   Entity display repository, to get view modes by bundle.
   */
  public function __construct(
    $plugin_id,
    $plugin_definition,
    FieldDefinitionInterface $field_definition,
    array $settings,
    array $third_party_settings,
    ConfigFactory $config_factory,
    AccountProxyInterface $current_user,
    EntityDisplayRepository $entity_display_repository
  ) {
    parent::__construct($plugin_id, $plugin_definition, $field_definition, $settings, $third_party_settings);
    $this->moduleConfig = $config_factory->get('paragraphs_grid.settings');
    $this->gridConfig = $config_factory->get($this->moduleConfig->get('gridtype'));
    $this->currentUser = $current_user;
    $this->entityDisplayRepository = $entity_display_repository;
  }

  /**
   * {@inheritdoc}
   */
  public static function create(ContainerInterface $container, array $configuration, $plugin_id, $plugin_definition) {
    return new static(
      $plugin_id,
      $plugin_definition,
      $configuration['field_definition'],
      $configuration['settings'],
      $configuration['third_party_settings'],
      $container->get('config.factory'),
      $container->get('current_user'),
      $container->get('entity_display.repository')
    );
  }

  /**
   * {@inheritdoc}
   */
  public static function defaultSettings() {
    return [
      'excluded_view_modes' => ['preview'],
    ] + parent::defaultSettings();
  }

  /**
   * {@inheritdoc}
   */
  public function settingsForm(array $form, FormStateInterface $form_state) {
    $view_mode_options = $this->getViewModeOptions(FALSE);
    $elements = [
      'excluded_view_modes' => [
        '#type' => 'checkboxes',
        '#title' => $this->t('Excluded view mode'),
        '#default_value' => $this->getSetting('excluded_view_modes'),
        '#options' => $view_mode_options,
        '#description' => $this->t('Checked view modes will NOT be available in the paragraph form.'),
      ],
    ];
    return $elements;
  }

  /**
   * {@inheritdoc}
   */
  public function settingsSummary() {
    $summary = [];
    $all_view_modes = implode(', ', $this->getSetting('excluded_view_modes'));
    $summary[] = $this->t('Excluded view modes: @view_modes', ['@view_modes' => $all_view_modes]);
    return $summary;
  }

  /**
   * Returns module config settings by name.
   *
   * @param string $name
   *   The config key.
   *
   * @return array|mixed|null
   *   The config value from module config.
   */
  protected function getModuleConfig($name = '') {
    return $this->moduleConfig->get($name);
  }

  /**
   * Returns grid config settings by name.
   *
   * @param string $name
   *   The config key.
   *
   * @return array|mixed|null
   *   The config value from grid config.
   */
  protected function getGridConfig($name = '') {
    return $this->gridConfig->get($name);
  }

  /**
   * Breakoint definition from grid config.
   *
   * @return array|mixed|null
   *   Breakoint definition.
   */
  protected function getBreakpoints() {
    return $this->getGridConfig('breakpoints');
  }

  /**
   * Returns cell property definition (like offset, order ...) from grid config.
   *
   * @param bool|string $key
   *   If set method returns a special property or all if not.
   *
   * @return mixed
   *   Properties definition.
   */
  protected function getCellProperties($key = FALSE) {
    $this->getGridConfig('cell-properties');
    $properties = $this->getGridConfig('cell-properties');
    return ($key) ? $properties[$key] : $properties;
  }

  /**
   * Returns view modes as options for a select form field.
   *
   * @param bool $filtered
   *   If view modes should be filtered from excluded view modes or not.
   *
   * @return array
   *   View mode options for a select form field.
   */
  public function getViewModeOptions($filtered = TRUE) {
    $type = $this->fieldDefinition->getTargetEntityTypeId();
    $bundle = $this->fieldDefinition->getTargetBundle();
    $view_modes = ($filtered) ? ['' => $this->t('default')] : [];
    $view_modes += $this->entityDisplayRepository->getViewModeOptionsByBundle($type, $bundle);
    unset($view_modes['default']);
    if ($filtered) {
      foreach ($this->getSetting('excluded_view_modes') as $exclude) {
        unset($view_modes[$exclude]);
      }
    }
    return $view_modes;
  }

  /**
   * Returns options for a select form field.
   *
   * @param string $breakpoint
   *   Current breakpoint.
   * @param int $col_num
   *   Number of columns to generate classes.
   * @param array $definition
   *   Definition of the cell property.
   *
   * @return array
   *   Form select options.
   */
  protected function getGridOptions($breakpoint, $col_num, array $definition) {
    $options = [];
    if ($definition['asc']) {
      for ($count = 0; $count <= $col_num; $count++) {
        $css_class = str_replace(['%cols', '%bp'], [$count, $breakpoint], $definition['formatter']);
        $options[$css_class] = "$count";
      }
    }
    else {
      $count = $col_num;
      while ($count >= 1) {
        $css_class = str_replace(['%cols', '%bp'], [$count, $breakpoint], $definition['formatter']);
        $options[$css_class] = "$count";
        $count--;
      }
    }
    if (isset($definition['additional'])) {
      foreach ($definition['additional'] as $add_opts) {
        $css_class = str_replace('%bp', $breakpoint, $add_opts['class']);
        $options[$css_class] = $add_opts['name'];
      }
    }
    return $options;
  }

  /**
   * Generate select form field for css classes.
   *
   * @param array $bp_definition
   *   The config definition of the breakpoints.
   * @param array $definition
   *   The grid definition.
   * @param array $defaults
   *   The current field value.
   *
   * @return array
   *   Render array of a select form field for grid classes.
   */
  protected function getGridSelector(array $bp_definition, array $definition, array $defaults = []) {
    $options = $this->getGridOptions($bp_definition['fragment'], $bp_definition['cols'], $definition);
    $default_values = array_intersect($defaults, array_keys($options));

    $select = [
      '#type' => 'select',
      '#title' => $definition['label'],
      '#size' => 1,
      '#default_value' => reset($default_values),
      '#options' => $options,
    ];
    if (!$definition['default']) {
      $select['#empty_value'] = '';
      $select['#empty_option'] = '';
    }
    return $select;
  }

  /**
   * {@inheritdoc}
   */
  public function formElement(
    FieldItemListInterface $items,
    $delta,
    array $element,
    array &$form,
    FormStateInterface $form_state
  ) {

    // The grid widget toggle button.
    $element['open_button'] = [
      '#theme' => 'pg_button',
      '#icon' => 'view_quilt',
      '#label' => $this->t('Grid'),
      '#attributes' => new Attribute([
        'data-toggle' => 'pg-widget-container',
        'class' => [
          'btn-toggle-widget',
        ],
      ]),
    ];
    $element['opener'] = [
      '#type' => 'hidden',
      '#default_value' => $form_state->get('opener'),
    ];

    // The grid widget container.
    $element['subform_container'] = [
      '#type' => 'container',
      '#attributes' => [
        'class' => ['pg-widget-container'],
      ],
    ];

    $defaults = explode(' ', $items[$delta]->value);

    $cell_properties = $this->getCellProperties();
    $view_properties = array_values($this->getFieldSetting('optional'));

    foreach ($this->getBreakpoints() as $breakpoint => $bp_definition) {
      $element['subform_container'][$breakpoint] = [
        '#type' => 'container',
        '#attributes' => [
          'class' => ['pg-widget-bpoint', 'bp-' . $breakpoint],
        ],
        'header' => [
          '#theme' => 'pg_bpoint_col_header',
          '#name' => $bp_definition['name'],
          '#size' => $bp_definition['bpoint'],
          '#attributes' => new Attribute(['class' => ['pg-bp-header']]),
          '#icon_attributes' => new Attribute(['class' => ['pg-icon', $bp_definition['icon']]]),
        ],
      ];
      $name = 'col_' . $bp_definition['cols'];
      foreach ($cell_properties as $option => $definition) {
        if ($definition['optional'] === FALSE || in_array($option, $view_properties, TRUE)) {
          $element['subform_container'][$breakpoint][$name . "_$option"] = $this
            ->getGridSelector($bp_definition, $definition, $defaults);
        }
      }
    }

    // Conditions to display view mode selector.
    $view_modes_enabled = $this->fieldDefinition->getFieldStorageDefinition()
      ->getSetting('view_modes_enabled');

    // Display view mode selector if conditions full filled.
    if ($view_modes_enabled) {
      // Link to create new view modes.
      $vm_link = '';
      if ($this->currentUser->hasPermission('administer display modes')) {
        $vm_link = new Link(
          $this->t('Add view mode'),
          new Url('entity.entity_view_mode.add_form', ['entity_type_id' => 'paragraph'])
        );
      }

      $view_mode_options = $this->getViewModeOptions();

      // Display view mode selector.
      $element['subform_container']['view_mode_wrap'] = [
        '#type' => 'container',
        '#attributes' => [
          'class' => ['pg-widget-view-mode'],
        ],
      ];

      if (count($view_mode_options) > 1) {
        $full_value = $items[$delta]->getValue();
        $element['subform_container']['view_mode_wrap']['view_mode'] = [
          '#type' => 'select',
          '#title' => $this->t('View mode'),
          '#size' => 1,
          '#default_value' => isset($full_value['view_mode']) ? $full_value['view_mode'] : NULL,
          '#options' => $view_mode_options,
          '#description' => $this->t('Select the view mode for this paragraph. @link', [
            '@link' => ($vm_link) ? $vm_link->toString() : '',
          ]),
        ];
      }
      else {
        $element['subform_container']['view_mode_wrap']['view_mode_remark'] = [
          '#markup' => new TranslatableMarkup(
            '<p>No view modes to select found (except "default") for paragraph type %type. @link</p>', [
              '%type' => $this->fieldDefinition->getTargetBundle(),
              '@link' => ($vm_link) ? $vm_link->toString() : '',
            ]
          ),
        ];
      }
    }

    return $element;
  }

  /**
   * {@inheritdoc}
   */
  public function massageFormValues(array $values, array $form, FormStateInterface $form_state) {
    $result = [];
    $collector = [];
    foreach ($values as $key => $value) {
      foreach ($value['subform_container'] as $breakpoint => $cols) {
        if ($breakpoint == 'view_mode_wrap') {
          $result[$key]['view_mode'] = $cols['view_mode'] ?: NULL;
          continue;
        }
        $collector = array_merge($collector, array_values(array_filter($cols)));
      }
      $result[$key]['value'] = implode(' ', $collector);
    }

    return $result;
  }

}
