<?php

namespace Drupal\paragraph_blocks\Form;

use Drupal\Core\Form\ConfigFormBase;
use Drupal\Core\Form\FormStateInterface;

/**
 * Form for Paragraph Blocks settings.
 */
class ParagraphBlocksSettingsForm extends ConfigFormBase {

  /**
   * {@inheritdoc}
   */
  public function getFormId() {
    return 'paragraph_blocks_settings';
  }

  /**
   * {@inheritdoc}
   */
  protected function getEditableConfigNames() {
    return ['paragraph_blocks.settings'];
  }

  /**
   * {@inheritdoc}
   */
  public function buildForm(array $form, FormStateInterface $form_state) {
    $config = $this->config('paragraph_blocks.settings');
    $form['max_cardinality'] = [
      '#type' => 'number',
      '#title' => $this->t('Max cardinality of paragraphs you want to see in Layout Builder.'),
      '#default_value' => $config->get('max_cardinality'),
      '#description' => $this->t('Layout Builder allows you to place each item in a multi-value paragraphs field as its own block. This sets the max number you think you need to see. You can change it later.'),
    ];

    return parent::buildForm($form, $form_state);
  }

  /**
   * {@inheritdoc}
   */
  public function submitForm(array &$form, FormStateInterface $form_state) {
    parent::submitForm($form, $form_state);

    $config = $this->config('paragraph_blocks.settings');
    $config->set('max_cardinality', $form_state->getValue('max_cardinality'));
    $config->save();
  }

}
