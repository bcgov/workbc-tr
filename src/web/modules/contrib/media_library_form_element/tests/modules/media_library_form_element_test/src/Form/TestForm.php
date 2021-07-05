<?php

namespace Drupal\media_library_form_element_test\Form;

use Drupal\Core\Form\ConfigFormBase;
use Drupal\Core\Form\FormStateInterface;

/**
 * A form for testing the Media Library Form Element.
 */
class TestForm extends ConfigFormBase {

  /**
   * {@inheritdoc}
   */
  public function getFormId() {
    return 'media_library_form_element_test_form';
  }

  /**
   * Returns a the media library form element.
   */
  public function buildForm(array $form, FormStateInterface $form_state) {
    // @todo: test other cardinality values.
    // @todo: test allowed bundles.
    $form = parent::buildForm($form, $form_state);
    $config = $this->config('media_library_form_element_test.settings');
    $form['media'] = [
      '#type' => 'media_library',
      '#allowed_bundles' => ['type_one', 'type_two'],
      '#title' => $this->t('Upload your image'),
      '#default_value' => $config->get('media') ?? NULL,
      '#description' => $this->t('Upload or select your profile image.'),
      '#cardinality' => 1,
      '#attributes' => ['id' => 'test'],
    ];
    return $form;
  }

  /**
   * {@inheritdoc}
   */
  public function submitForm(array &$form, FormStateInterface $form_state) {
    $config = $this->config('media_library_form_element_test.settings');
    $config->set('media', $form_state->getValue('media'));
    $config->save();
    return parent::submitForm($form, $form_state);
  }

  /**
   * {@inheritdoc}
   */
  protected function getEditableConfigNames() {
    return [
      'media_library_form_element_test.settings',
    ];
  }

}
