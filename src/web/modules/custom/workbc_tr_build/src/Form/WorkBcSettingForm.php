<?php

namespace Drupal\workbc_tr_build\Form;

use Drupal\Core\Form\ConfigFormBase;
use Drupal\Core\Form\FormStateInterface;
use Drupal\Core\Config\ConfigFactoryInterface;
use Drupal\Core\File\FileSystemInterface;
use Drupal\Core\Extension\ModuleHandlerInterface;
use Symfony\Component\DependencyInjection\ContainerInterface;

/**
 * Defines a form that configures forms module settings.
 */
class WorkBcSettingForm extends ConfigFormBase {

  /**
   * The module handler.
   *
   * @var \Drupal\Core\Extension\ModuleHandlerInterface
   */
  protected $moduleHandler;

  /**
   * Constructs a ThemeSettingsForm object.
   *
   * @param \Drupal\Core\Config\ConfigFactoryInterface $config_factory
   *   The factory for configuration objects.
   * @param \Drupal\Core\Extension\ModuleHandlerInterface $module_handler
   *   The module handler instance to use.
   * @param \Drupal\Core\File\FileSystemInterface $file_system
   *   The file system.
   */
  public function __construct(ConfigFactoryInterface $config_factory, ModuleHandlerInterface $module_handler, FileSystemInterface $file_system) {
    parent::__construct($config_factory);

    $this->moduleHandler = $module_handler;
    $this->fileSystem = $file_system;
  }

  /**
   * {@inheritdoc}
   */
  public static function create(ContainerInterface $container) {
    return new static(
      $container->get('config.factory'),
      $container->get('module_handler'),
      $container->get('file_system')
    );
  }

  /**
   * Config settings.
   *
   * @var string
   */
  const SETTINGS = 'workbc_tr_build.settings';

  /**
   * {@inheritdoc}
   */
  public function getFormId() {
    return 'workbc_settings';
  }

  /**
   * {@inheritdoc}
   */
  protected function getEditableConfigNames() {
    return [
      static::SETTINGS,
    ];
  }

  /**
   * {@inheritdoc}
   */
  public function buildForm(array $form, FormStateInterface $form_state) {
    $config = $this->config(static::SETTINGS);
    // dd($config->get('search_page.banner_image'));

    $validators = [
      'file_validate_extensions' => ['png', 'jpeg', 'jpg'],
    ];

    $form['search_page'] = [
      '#type' => 'details',
      '#title' => $this->t('Search page'),
      '#open' => TRUE,
    ];

    $form['search_page']['banner_image'] = [
      '#type' => 'file',
      '#title' => $this->t('Upload banner image'),
      '#maxlength' => 40,
      '#description' => $this->t("Upload Search page Hero banner image"),
      '#autoupload' => TRUE,
      '#upload_validators' => [
        'file_validate_is_image' => [],
      ],
      '#upload_location' => 'public://',
    ];

    $form['search_page']['banner_mobile_image'] = [
      '#type' => 'file',
      '#title' => $this->t('Upload banner mobile image'),
      '#maxlength' => 40,
      '#description' => $this->t("Upload Search page Hero banner mobile image"),
      '#autoupload' => TRUE,
      '#upload_validators' => [
        'file_validate_is_image' => [],
      ],
      '#upload_location' => 'public://',
    ];
    return parent::buildForm($form, $form_state);
  }

   /**
   * {@inheritdoc}
   */
  public function validateForm(array &$form, FormStateInterface $form_state) {
    parent::validateForm($form, $form_state);

    if ($this->moduleHandler->moduleExists('file')) {

      // Check for a new uploaded logo.
      if (isset($form['search_page'])) {
        $file = _file_save_upload_from_form($form['search_page']['banner_image'], $form_state, 0);
        if ($file) {
          // Put the temporary file in form_values so we can save it on submit.
          $form_state->setValue('banner_image', $file);
        }
        $file_mobile = _file_save_upload_from_form($form['search_page']['banner_mobile_image'], $form_state, 0);
        if ($file_mobile) {
          // Put the temporary file in form_values so we can save it on submit.
          $form_state->setValue('banner_mobile_image', $file_mobile);
        }
      }
    }
  }

  /**
   * {@inheritdoc}
   */
  public function submitForm(array &$form, FormStateInterface $form_state) {
    parent::submitForm($form, $form_state);

    $values = $form_state->getValues();
    try {
      if (!empty($values['banner_image']) && !empty($values['banner_mobile_image'])) {
        $filename = $this->fileSystem->copy($values['banner_image']->getFileUri(), 'public://');
        $filename_mobile = $this->fileSystem->copy($values['banner_mobile_image']->getFileUri(), 'public://');
        // Retrieve the configuration.
        $this->configFactory->getEditable(static::SETTINGS)
          // Set the submitted configuration setting.
          ->set('search_page.banner_image', $filename)
          ->set('search_page.banner_mobile_image', $filename_mobile)
          // You can set multiple configurations at once by making
          // multiple calls to set().
          ->save();
      }
    }
    catch (FileException $e) {
      // Ignore.
    }

  }

}
