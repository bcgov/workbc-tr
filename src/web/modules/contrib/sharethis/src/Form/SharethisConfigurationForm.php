<?php

namespace Drupal\sharethis\Form;

use Drupal\Component\Utility\Xss;
use Drupal\Core\Config\ConfigFactoryInterface;
use Drupal\Core\Entity\EntityDisplayRepositoryInterface;
use Drupal\Core\Entity\EntityFieldManagerInterface;
use Drupal\Core\Entity\EntityTypeBundleInfoInterface;
use Drupal\Core\Extension\ModuleHandlerInterface;
use Drupal\Core\Form\ConfigFormBase;
use Drupal\Core\Form\FormStateInterface;
use Drupal\Core\Url;
use Drupal\sharethis\SharethisManagerInterface;
use Symfony\Component\DependencyInjection\ContainerInterface;

/**
 * Provides a settings for sharethis module.
 */
class SharethisConfigurationForm extends ConfigFormBase {

  /**
   * The module handler.
   *
   * @var \Drupal\Core\Extension\ModuleHandlerInterface
   */
  protected $moduleHandler;

  /**
   * The entity type Bundle Information.
   *
   * @var \Drupal\Core\Entity\EntityTypeBundleInfoInterface
   */
  protected $entityTypeBundleInfo;

  /**
   * The entity display Repository.
   *
   * @var \Drupal\Core\Entity\EntityDisplayRepositoryInterface
   */
  protected $entityDisplayRepository;

  /**
   * The entity field Manager.
   *
   * @var \Drupal\Core\Entity\EntityFieldManagerInterface
   */
  protected $entityFieldManager;

  /**
   * The sharethis manager.
   *
   * @var \Drupal\sharethis\SharethisManagerInterface
   */
  protected $sharethisManager;

  /**
   * Constructs a \Drupal\user\SharethisConfigurationForm object.
   *
   * @param \Drupal\Core\Config\ConfigFactoryInterface $config_factory
   *   The factory for configuration objects.
   * @param \Drupal\Core\Extension\ModuleHandlerInterface $module_handler
   *   The module handler.
   * @param \Drupal\Core\Entity\EntityTypeBundleInfoInterface $entity_type_bundle_info
   *   The entity type bundle information.
   * @param \Drupal\Core\Entity\EntityDisplayRepositoryInterface $entity_display_repository
   *   The entity display Repository.
   * @param \Drupal\Core\Entity\EntityFieldManagerInterface $entity_field_manager
   *   The entity field Manager.
   * @param \Drupal\sharethis\SharethisManagerInterface $sharethis_manager
   *   The sharethis Manager.
   */
  public function __construct(ConfigFactoryInterface $config_factory, ModuleHandlerInterface $module_handler, EntityTypeBundleInfoInterface $entity_type_bundle_info, EntityDisplayRepositoryInterface $entity_display_repository, EntityFieldManagerInterface $entity_field_manager, SharethisManagerInterface $sharethis_manager) {
    parent::__construct($config_factory);

    $this->moduleHandler = $module_handler;
    $this->entityTypeBundleInfo = $entity_type_bundle_info;
    $this->entityDisplayRepository = $entity_display_repository;
    $this->entityFieldManager = $entity_field_manager;
    $this->sharethisManager = $sharethis_manager;
  }

  /**
   * {@inheritdoc}
   */
  public static function create(ContainerInterface $container) {
    return new static(
      $container->get('config.factory'),
      $container->get('module_handler'),
      $container->get('entity_type.bundle.info'),
      $container->get('entity_display.repository'),
      $container->get('entity_field.manager'),
      $container->get('sharethis.manager')
    );
  }

  /**
   * {@inheritdoc}
   */
  public function getEditableConfigNames() {
    return ['sharethis.settings'];
  }

  /**
   * {@inheritdoc}
   */
  public function getFormId() {
    return 'sharethis_settings';
  }

  /**
   * {@inheritdoc}
   */
  public function buildForm(array $form, FormStateInterface $form_state) {

    global $base_url;
    $my_path = drupal_get_path('module', 'sharethis');

    // First, setup variables we will need.
    // Get the path variables setup.
    // Load the css and js for our module's configuration.
    $config = $this->config('sharethis.settings');

    $current_options_array = $this->sharethisManager->getOptions();

    // Create the variables related to button choice.
    $button_choice = $current_options_array['buttons'];
    // Create the variables related to services chosen.
    $service_string = $current_options_array['services'];
    $service_string_markup = [];
    foreach (explode(",", $service_string) as $string) {
      $key = explode(":", mb_substr($string, 0, -1));
      $key = $key[1];
      $service_string_markup[] = $key;
    }

    // Create the variables for publisher keys.
    $publisher = $current_options_array['publisherID'];
    // Create the variables for teasers.
    $form = [];
    $form['options'] = [
      '#type' => 'fieldset',
      '#title' => $this->t('Display'),
    ];
    $form['options']['button_option'] = [
      '#required' => TRUE,
      '#type' => 'radios',
      '#options' => [
        'stbc_large' => $this->t('Large Chicklets'),
        'stbc_' => $this->t('Small Chicklets'),
        'stbc_button' => $this->t('Classic Buttons'),
        'stbc_vcount' => $this->t('Vertical Counters'),
        'stbc_hcount' => $this->t('Horizontal Counters'),
        'stbc_custom' => $this->t('Custom Buttons via CSS'),
      ],
      '#default_value' => $button_choice,
      '#title' => $this->t('Choose a button style:'),
      '#prefix' => '<div class="st_widgetContain"><div class="st_spriteCover"><img id="stb_sprite" class="st_buttonSelectSprite ' . $button_choice . '" src="' . $base_url . '/' . $my_path . '/img/preview_sprite.png" /></div><div class="st_widgetPic"><img class="st_buttonSelectImage" src="' . $base_url . '/' . $my_path . '/img/preview_bg.png" /></div>',
      '#suffix' => '</div>',
    ];
    $form['options']['service_option'] = [
      '#description' => $this->t('<b>Add</b> a service by selecting it on the right and clicking the <i>left arrow</i>.  <b>Remove</b> it by clicking the <i>right arrow</i>.<br /><b>Change the order</b> of services under "Selected Services" by using the <i>up</i> and <i>down</i> arrows.'),
      '#required' => TRUE,
      '#type' => 'textfield',
      '#prefix' => '<div>',
      '#suffix' => '</div><div id="myPicker"></div>',
      '#title' => $this->t('Choose Your Services.'),
      '#default_value' => $service_string,
      '#maxlength' => 1024,
    ];
    $form['options']['option_extras'] = [
      '#title' => $this->t('Extra services'),
      '#description' => $this->t('Select additional services which will be available. These are not officially supported by ShareThis, but are available.'),
      '#type' => 'checkboxes',
      '#options' => [
        'Google Plus One:plusone' => $this->t('Google Plus One'),
        'Facebook Like:fblike' => $this->t('Facebook Like'),
      ],
      '#default_value' => $config->get('option_extras'),
    ];

    $form['options']['callesi'] = [
      '#type' => 'hidden',
      '#default_value' => $current_options_array['callesi'],
    ];

    $form['additional_settings'] = [
      '#type' => 'vertical_tabs',
    ];

    $form['context'] = [
      '#type' => 'details',
      '#title' => $this->t('Context'),
      '#group' => 'additional_settings',
      '#description' => $this->t('Configure where the ShareThis widget should appear.'),
    ];

    $form['context']['location'] = [
      '#title' => $this->t('Location'),
      '#type' => 'radios',
      '#options' => [
        'content' => $this->t('Node content'),
        'block' => $this->t('Block'),
        'links' => $this->t('Links area'),
      ],
      '#default_value' => $config->get('location'),
    ];

    // Add an information section for each location type, each dependent on the
    // currently selected location.
    foreach (['links', 'content', 'block'] as $location_type) {
      $form['context'][$location_type]['#type'] = 'container';
      $form['context'][$location_type]['#states']['visible'][':input[name="location"]'] = ['value' => $location_type];
    }

    // Add help text for the 'content' location.
    $form['context']['content']['help'] = [
      '#markup' => $this->t('When using the Content location, you must place the ShareThis links in the <a href="@url">Manage Display</a> section of each content type.', ['@url' => Url::fromRoute('entity.node_type.collection')->toString()]),
      '#weight' => 10,
      '#prefix' => '<em>',
      '#suffix' => '</em>',
    ];
    // Add help text for the 'block' location.
    $form['context']['block']['#children'] = 'You must choose which region to display the in from the Blocks administration';
    $entity_bundles = $this->entityTypeBundleInfo->getBundleInfo('node');
    // Add checkboxes for each view mode of each bundle.
    $entity_modes = $this->entityDisplayRepository->getViewModes('node');
    ;
    $modes = [];
    foreach ($entity_modes as $mode => $mode_info) {
      $modes[$mode] = $mode_info['label'];
    }
    // Get a list of content types and view modes.
    foreach ($entity_bundles as $bundle => $bundle_info) {
      $form['context']['links'][$bundle . '_options'] = [
        '#title' => $this->t('%label View Modes', ['%label' => $bundle_info['label']]),
        '#description' => $this->t('Select which view modes the ShareThis widget should appear on for %label nodes.', ['%label' => $bundle_info['label']]),
        '#type' => 'checkboxes',
        '#options' => $modes,
        '#default_value' => $config->get('sharethisnodes.' . $bundle) ?: [],
      ];
    }
    // Allow the user to choose which content types will have ShareThis added
    // when using the 'Content' location.
    $content_types = [];
    $enabled_content_types = $current_options_array['node_types'];
    foreach ($entity_bundles as $bundle => $bundle_info) {
      $content_types[$bundle] = $this->t('@label', ['@label' => $bundle_info['label']]);
    }

    $form['context']['content']['node_types'] = [
      '#title' => $this->t('Node Types'),
      '#description' => $this->t('Select which node types the ShareThis widget should appear on.'),
      '#type' => 'checkboxes',
      '#options' => $content_types,
      '#default_value' => $enabled_content_types,
    ];
    $form['context']['comments'] = [
      '#title' => $this->t('Comments'),
      '#type' => 'checkbox',
      '#default_value' => $config->get('comments'),
      '#description' => $this->t('Display ShareThis on comments.'),
      '#access' => $this->moduleHandler->moduleExists('comment'),
    ];
    $sharethis_weight_list = [-100, -50, -25, -10, 0, 10, 25, 50, 100];
    $form['context']['weight'] = [
      '#title' => $this->t('Weight'),
      '#description' => $this->t('The weight of the widget determines the location on the page where it will appear.'),
      '#required' => FALSE,
      '#type' => 'select',
      '#options' => array_combine($sharethis_weight_list, $sharethis_weight_list),
      '#default_value' => $config->get('weight'),
    ];
    $form['advanced'] = [
      '#type' => 'details',
      '#title' => $this->t('Advanced'),
      '#group' => 'additional_settings',
      '#description' => $this->t('The advanced settings can usually be ignored if you have no need for them.'),
    ];
    $form['advanced']['publisherID'] = [
      '#title' => $this->t('Insert a publisher key (optional).'),
      '#description' => $this->t("When you install the module, we create a random publisher key.  You can register the key with ShareThis by contacting customer support.  Otherwise, you can go to <a href='http://www.sharethis.com/account'>ShareThis</a> and create an account.<br />Your official publisher key can be found under 'My Account'.<br />It allows you to get detailed analytics about sharing done on your site."),
      '#type' => 'textfield',
      '#default_value' => $publisher,
    ];
    $form['advanced']['late_load'] = [
      '#title' => $this->t('Late Load'),
      '#description' => $this->t("You can change the order in which ShareThis widget loads on the user's browser. By default the ShareThis widget loader loads as soon as the browser encounters the JavaScript tag; typically in the tag of your page. ShareThis assets are generally loaded from a CDN closest to the user. However, if you wish to change the default setting so that the widget loads after your web-page has completed loading then you simply tick this option."),
      '#type' => 'checkbox',
      '#default_value' => $config->get('late_load'),
    ];
    $form['advanced']['twitter_suffix'] = [
      '#title' => $this->t('Twitter Suffix'),
      '#description' => $this->t("Optionally append a Twitter handle, or text, so that you get pinged when someone shares an article. Example: <em>via @YourNameHere</em>"),
      '#type' => 'textfield',
      '#default_value' => $config->get('twitter_suffix'),
    ];
    $form['advanced']['twitter_handle'] = [
      '#title' => $this->t('Twitter Handle'),
      '#description' => $this->t('Twitter handle to use when sharing.'),
      '#type' => 'textfield',
      '#default_value' => $config->get('twitter_handle'),
    ];
    $form['advanced']['twitter_recommends'] = [
      '#title' => $this->t('Twitter recommends'),
      '#description' => $this->t('Specify a twitter handle to be recommended to the user.'),
      '#type' => 'textfield',
      '#default_value' => $config->get('twitter_recommends'),
    ];
    $form['advanced']['option_onhover'] = [
      '#type' => 'checkbox',
      '#title' => $this->t('Display ShareThis widget on hover'),
      '#description' => $this->t('If disabled, the ShareThis widget will be displayed on click instead of hover.'),
      '#default_value' => $config->get('option_onhover'),
    ];
    $form['advanced']['option_neworzero'] = [
      '#type' => 'checkbox',
      '#title' => $this->t('Display count "0" instead of "New"'),
      '#description' => $this->t('Display a zero (0) instead of "New" in the count for content not yet shared.'),
      '#default_value' => $config->get('option_neworzero'),
    ];
    $form['advanced']['option_shorten'] = [
      '#type' => 'checkbox',
      '#title' => $this->t('Display short URL'),
      '#description' => $this->t('Display either the full or the shortened URL.'),
      '#default_value' => $config->get('option_shorten'),
    ];
    $form['advanced']['cns'] = [
      '#title' => $this->t('<b>CopyNShare </b><sup>(<a href="http://support.sharethis.com/customer/portal/articles/517332-share-widget-faqs#copynshare" target="_blank">?</a>)</sup>'),
      '#type' => 'checkboxes',
      '#prefix' => '<div id="st_cns_settings">',
      '#suffix' => '</div><div class="st_cns_container">
				<p>CopyNShare is the new ShareThis widget feature that enables you to track the shares that occur when a user copies and pastes your website\'s <u>URL</u> or <u>Content</u>. <br/>
				<u>Site URL</u> - ShareThis adds a special #hashtag at the end of your address bar URL to keep track of where your content is being shared on the web.<br/>
				<u>Site Content</u> - It enables the pasting of "See more: YourURL#SThashtag" after user copies-and-pastes text. When a user copies text within your site, a "See more: yourURL.com#SThashtag" will appear after the pasted text. <br/>
				Please refer the <a href="http://support.sharethis.com/customer/portal/articles/517332-share-widget-faqs#copynshare" target="_blank">CopyNShare FAQ</a> for more details.</p>
			</div>',
      '#options' => [
        'donotcopy' => $this->t("Measure copy & shares of your site's Content"),
        'hashaddress' => $this->t("Measure copy & shares of your site's URLs"),
      ],
      '#default_value' => $config->get('cns'),
    ];
    $form['#attached']['drupalSettings']['sharethis']['service_string_markup'] = $service_string_markup;
    $form['#attached']['library'][] = 'sharethis/drupal.sharethisform';
    $form['#attached']['library'][] = 'sharethis/drupal.sharethispicker';
    $form['#attached']['library'][] = 'sharethis/drupal.sharethispickerexternal';
    return parent::buildForm($form, $form_state);
  }

  /**
   * {@inheritdoc}
   */
  public function validateForm(array &$form, FormStateInterface $form_state) {
    $input_values = $form_state->getUserInput();

    // Additional filters for the service option input.
    // Sanitize the publisher ID option.
    // Since it's a text field, remove anything that resembles code.
    $input_values['service_option'] = Xss::filter($input_values['service_option']);

    // Additional filters for the option extras input.
    $input_values['option_extras'] = (isset($input_values['option_extras'])) ? $input_values['option_extras'] : [];

    // Sanitize the publisher ID option. Since it's a text field,
    // remove anything that resembles code.
    $input_values['publisherID'] = Xss::filter($input_values['publisherID']);

    if ($input_values['callesi'] == 1) {
      unset($input_values['cns']);
    }
    unset($input_values['callesi']);

    // Ensure default value for twitter suffix.
    $input_values['twitter_suffix'] = (isset($input_values['twitter_suffix'])) ? $input_values['twitter_suffix'] : '';

    // Ensure default value for twitter handle.
    $input_values['twitter_handle'] = (isset($input_values['twitter_handle'])) ? $input_values['twitter_handle'] : '';

    // Ensure default value for twitter recommends.
    $input_values['twitter_recommends'] = (isset($input_values['twitter_recommends'])) ? $input_values['twitter_recommends'] : '';

    parent::validateForm($form, $form_state);

  }

  /**
   * {@inheritdoc}
   */
  public function submitForm(array &$form, FormStateInterface $form_state) {
    $entity_types = '';
    $values = $form_state->getValues();
    $input_values = $form_state->getUserInput();
    $config = $this->config('sharethis.settings');
    // If the location change to/from 'content', clear the Field Info cache.
    $current_location = $config->get('location');
    $new_location = $values['location'];
    if (($current_location == 'content' || $new_location == 'content') && $current_location != $new_location) {
      $this->entityFieldManager->clearCachedFieldDefinitions();
    }
    $entity_info = $this->entityTypeBundleInfo->getAllBundleInfo('node');
    if (isset($entity_info['node'])) {
      $entity_types = $entity_info['node'];
    }
    $config->set('button_option', $values['button_option'])
      ->set('service_option', $values['service_option'])
      ->set('option_extras', $values['option_extras'])
      ->set('callesi', $values['callesi'])
      ->set('location', $values['location'])
      ->set('node_types', $input_values['node_types'])
      ->set('comments', $values['comments'])
      ->set('weight', $values['weight'])
      ->set('publisherID', $values['publisherID'])
      ->set('late_load', $values['late_load'])
      ->set('twitter_suffix', $values['twitter_suffix'])
      ->set('twitter_handle', $values['twitter_handle'])
      ->set('twitter_recommends', $values['twitter_recommends'])
      ->set('option_onhover', $values['option_onhover'])
      ->set('option_neworzero', $values['option_neworzero'])
      ->set('option_shorten', $values['option_shorten'])
      ->set('cns.donotcopy', $input_values['cns']['donotcopy'])
      ->set('cns.hashaddress', $input_values['cns']['hashaddress'])
      ->save();
    if (is_array($entity_types)) {
      foreach ($entity_types as $key => $entity_type) {
        $config->set('sharethisnodes.' . $key, $values[$key . '_options'])->save();
      }
    }
    parent::submitForm($form, $form_state);
  }

}
