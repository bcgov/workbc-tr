<?php

namespace Drupal\paragraphs_grid\Form;

use Drupal\Core\Config\ConfigFactoryInterface;
use Drupal\Core\Entity\EntityFieldManagerInterface;
use Drupal\Core\Entity\EntityTypeManagerInterface;
use Drupal\Core\Form\ConfigFormBase;
use Drupal\Core\Form\FormStateInterface;
use Symfony\Component\DependencyInjection\ContainerInterface;

/**
 * Class ParagraphsGridConfigForm.
 */
class ParagraphsGridConfigForm extends ConfigFormBase {

  /**
   * The entity type manager.
   *
   * @var \Drupal\Core\Entity\EntityTypeManagerInterface
   */
  protected $entityTypeManager;

  /**
   * The entity field manager.
   *
   * @var \Drupal\Core\Entity\EntityFieldManagerInterface
   */
  protected $entityFieldManager;

  /**
   * List of all available entities of type grid_entity.
   *
   * @var array
   */
  protected $grids;

  /**
   * Constructs a \Drupal\system\ConfigFormBase object.
   *
   * @param \Drupal\Core\Config\ConfigFactoryInterface $config_factory
   *   The factory for configuration objects.
   * @param \Drupal\Core\Entity\EntityTypeManagerInterface $entity_type_manager
   *   The great and only Entity-Type-Manager.
   * @param \Drupal\Core\Entity\EntityFieldManagerInterface $entity_field_manager
   *   The great and only EntityType Manager.
   */
  public function __construct(ConfigFactoryInterface $config_factory, EntityTypeManagerInterface $entity_type_manager, EntityFieldManagerInterface $entity_field_manager) {
    parent::__construct($config_factory);
    $this->entityTypeManager = $entity_type_manager;
    $this->entityFieldManager = $entity_field_manager;
  }

  /**
   * {@inheritdoc}
   */
  public static function create(ContainerInterface $container) {
    return new static(
      $container->get('config.factory'),
      $container->get('entity_type.manager'),
      $container->get('entity_field.manager')
    );
  }

  /**
   * {@inheritdoc}
   */
  protected function getEditableConfigNames() {
    return [
      'paragraphs_grid.settings',
    ];
  }

  /**
   * Returns grid entities as an array.
   *
   * @return array|\Drupal\Core\Entity\EntityInterface[]
   *   Grid entities.
   *
   * @throws \Drupal\Component\Plugin\Exception\InvalidPluginDefinitionException
   * @throws \Drupal\Component\Plugin\Exception\PluginNotFoundException
   */
  protected function getGrids() {
    if (!$this->grids) {
      $this->grids = $this->entityTypeManager->getStorage('grid_entity')->loadMultiple();
    }
    return $this->grids;
  }

  /**
   * Returns an option pack for select.
   *
   * @return array
   *   The grids as select options.
   *
   * @throws \Drupal\Component\Plugin\Exception\InvalidPluginDefinitionException
   * @throws \Drupal\Component\Plugin\Exception\PluginNotFoundException
   */
  protected function getGridTypeOptions() {
    $options = [];
    foreach ($this->getGrids() as $grid) {
      /** @var \Drupal\paragraphs_grid\Entity\GridEntity $grid */
      $options['paragraphs_grid.grid_entity.' . $grid->id()] = $grid->label();
    }
    return $options;
  }

  /**
   * {@inheritdoc}
   */
  public function getFormId() {
    return 'paragraphs_grid_config_form';
  }

  /**
   * {@inheritdoc}
   *
   * @throws \Drupal\Component\Plugin\Exception\InvalidPluginDefinitionException
   * @throws \Drupal\Component\Plugin\Exception\PluginNotFoundException
   */
  public function buildForm(array $form, FormStateInterface $form_state) {
    $field_map = $this->entityFieldManager->getFieldMapByFieldType('grid_field_type');

    if (count($field_map) && in_array('administrator', $this->currentUser()->getRoles())) {
      $this->messenger()->addWarning('Grid classes of current type are already in use. Data will be lost if you change the grid type.');
      $disable_grid_type = FALSE;
    }
    elseif (count($field_map)) {
      $disable_grid_type = TRUE;
    }
    else {
      $disable_grid_type = FALSE;
    }

    $config = $this->config('paragraphs_grid.settings');

    $form['gridtype'] = [
      '#type' => 'radios',
      '#title' => $this->t('Grid type'),
      '#description' => $this->t('Select the grid type you want to use. If you do not find your grid, you can create your own. Follow instructions on http://drupal.org/project/paragraphs_grid.'),
      '#options' => $this->getGridTypeOptions(),
      '#default_value' => $config->get('gridtype'),
      '#disabled' => $disable_grid_type,
    ];

    $use_css = $this->t('Use CSS delivered from Paragraphs Grid');
    $form['uselibrary'] = [
      '#type' => 'checkbox',
      '#title' => $use_css,
      '#description' => $this->t('Disable this checkbox, if your theme already includes the grid css and javascript.'),
      '#default_value' => $config->get('uselibrary'),
    ];

    $form['use_lib_admin_pages'] = [
      '#type' => 'checkbox',
      '#title' => $this->t('Load grid-CSS even on administration pages.'),
      '#description' => $this->t('Enable if grids are displayed on admin pages. Has no effect if the Option "%above" is disabled', [
        '%above' => $use_css,
      ]),
      '#default_value' => $config->get('use_lib_admin_pages'),
    ];
    return parent::buildForm($form, $form_state);
  }

  /**
   * {@inheritdoc}
   */
  public function submitForm(array &$form, FormStateInterface $form_state) {
    parent::submitForm($form, $form_state);

    $this->config('paragraphs_grid.settings')
      ->set('gridtype', $form_state->getValue('gridtype'))
      ->set('uselibrary', $form_state->getValue('uselibrary'))
      ->set('use_lib_admin_pages', $form_state->getValue('use_lib_admin_pages'))
      ->save();
  }

}
