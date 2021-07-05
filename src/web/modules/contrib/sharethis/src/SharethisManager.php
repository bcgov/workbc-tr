<?php

namespace Drupal\sharethis;

use Drupal\Core\Config\ConfigFactoryInterface;
use Drupal\Core\Routing\RouteMatchInterface;
use Drupal\Core\Url;
use Drupal\node\Entity\NodeType;
use Drupal\Component\Utility\Html;
use Drupal\Component\Serialization\Json;
use Drupal\Core\Controller\TitleResolverInterface;
use Symfony\Component\HttpFoundation\RequestStack;

/**
 * Defines an SharethisManager service.
 */
class SharethisManager implements SharethisManagerInterface {

  /**
   * The config object.
   *
   * @var \Drupal\Core\Config\ConfigFactoryInterface
   */
  protected $configFactory;

  /**
   * The Title Resolver object.
   *
   * @var \Drupal\Core\Controller\TitleResolverInterface
   */
  protected $titleResolver;

  /**
   * The currently active route match object.
   *
   * @var \Drupal\Core\Routing\RouteMatchInterface
   */
  protected $routeMatch;

  /**
   * The request Stack object.
   *
   * @var \Symfony\Component\HttpFoundation\Request
   */
  protected $requestStack;

  /**
   * Stores the status of JS.
   *
   * @var bool
   */
  protected $sharethisJS;

  /**
   * Constructs an SharethisManager object.
   *
   * @param \Drupal\Core\Config\ConfigFactoryInterface $config_factory
   *   The Configuration Factory.
   * @param \Drupal\Core\Controller\TitleResolverInterface $title_resolver
   *   The Title Resolver.
   * @param \Drupal\Core\Routing\RouteMatchInterface $route_match
   *   The current route match.
   * @param \Symfony\Component\HttpFoundation\RequestStack $request_stack
   *   The request stack.
   */
  public function __construct(ConfigFactoryInterface $config_factory, TitleResolverInterface $title_resolver, RouteMatchInterface $route_match, RequestStack $request_stack) {
    $this->configFactory = $config_factory;
    $this->titleResolver = $title_resolver;
    $this->routeMatch = $route_match;
    $this->requestStack = $request_stack;
    $this->sharethisJS = FALSE;
  }

  /**
   * {@inheritdoc}
   */
  public function getOptions() {
    $sharethis_config = $this->configFactory->get('sharethis.settings');

    $view_modes = [];
    foreach (array_keys(NodeType::loadMultiple()) as $type) {
      $view_modes[$type] = ['article' => 'article', 'page' => 'page'];
    }

    return [
      'buttons' => $sharethis_config->get('button_option', 'stbc_button'),
      'publisherID' => $sharethis_config->get('publisherID'),
      'services' => $sharethis_config->get('service_option'),
      'option_extras' => $sharethis_config->get('option_extras'),
      'widget' => $sharethis_config->get('widget_option'),
      'onhover' => $sharethis_config->get('option_onhover'),
      'neworzero' => $sharethis_config->get('option_neworzero'),
      'twitter_suffix' => $sharethis_config->get('twitter_suffix'),
      'twitter_handle' => $sharethis_config->get('twitter_handle'),
      'twitter_recommends' => $sharethis_config->get('twitter_recommends'),
      'late_load' => $sharethis_config->get('late_load'),
      'view_modes' => $view_modes,
      'cns' => $sharethis_config->get('cns'),
      'callesi' => (NULL == $sharethis_config->get('cns')) ? 1 : 0,
      'node_types' => $sharethis_config->get('node_types'),
      'shorten' => $sharethis_config->get('option_shorten'),
    ];
  }

  /**
   * {@inheritdoc}
   */
  public function blockContents() {

    $sharethis_config = $this->configFactory->get('sharethis.settings');
    $config = $this->configFactory->get('system.site');
    if ($sharethis_config->get('location') == 'block') {
      // First Get all of the options for sharethis widget from database.
      $data_options = $this->getOptions();
      $request = $this->requestStack->getCurrentRequest();

      // Get an absolute path to the current page.
      $m_path = $request->getUri();
      $mtitle = $this->titleResolver->getTitle($request, $this->routeMatch->getRouteObject());
      if (!empty($mtitle) && is_object($mtitle)) {
        $m_title = $mtitle->getUntranslatedString();
      }
      elseif (!empty($mtitle) && is_string($mtitle)) {
        $m_title = $mtitle;
      }
      else {
        $m_title = $config->get('name');
      }

      return $this->renderSpans($data_options, $m_title, $m_path);
    }
  }

  /**
   * {@inheritdoc}
   */
  public function widgetContents(array $settings) {
    $mpath = $settings['m_path'];
    $mtitle = $settings['m_title'];
    $data_options = $this->getOptions();
    return $this->renderSpans($data_options, $mtitle, $mpath);
  }

  /**
   * {@inheritdoc}
   */
  public function sharethisIncludeJs() {
    $has_run = $this->sharethisJS;
    if (!$has_run) {
      // These are the ShareThis scripts:
      $data_options = $this->getOptions();
      $st_js_options = [];
      $st_js_options['switchTo5x'] = $data_options['widget'] == 'st_multi' ? TRUE : FALSE;
      if ($data_options['late_load']) {
        $st_js_options['__st_loadLate'] = TRUE;
      }
      $st_js = '';
      foreach ($st_js_options as $name => $value) {
        $st_js .= 'var ' . $name . ' = ' . Json::decode($value) . ';';

      }
      $st_js = $this->getShareThisLightOptions($data_options);
      $has_run = TRUE;
      $this->sharethisJS = $has_run;
      return $st_js;
    }
  }

  /**
   * {@inheritdoc}
   */
  public function getShareThisLightOptions(array $data_options) {
    // Provide the publisher ID.
    $params_stlight = [
      'publisher' => $data_options['publisherID'],
    ];
    $params_stlight['version'] = ($data_options['widget'] == 'st_multi') ? '5x' : '4x';
    if ($data_options['callesi'] == 0) {
      $params_stlight['doNotCopy'] = !$this->toBoolean($data_options['cns']['donotcopy']);
      $params_stlight['hashAddressBar'] = $this->toBoolean($data_options['cns']['hashaddress']);
      if (!($params_stlight['hashAddressBar']) && $params_stlight['doNotCopy']) {
        $params_stlight['doNotHash'] = TRUE;
      }
      else {
        $params_stlight['doNotHash'] = FALSE;
      }
    }
    if (isset($data_options['onhover']) && $data_options['onhover'] == FALSE) {
      $params_stlight['onhover'] = FALSE;
    }
    if ($data_options['neworzero']) {
      $params_stlight['newOrZero'] = 'zero';
    }
    if (!$data_options['shorten']) {
      $params_stlight['shorten'] = 'false';
    }

    return $params_stlight;
  }

  /**
   * {@inheritdoc}
   */
  public function toBoolean($val) {
    if (strtolower(trim($val)) === 'false') {
      return FALSE;
    }
    else {
      return (boolean) $val;
    }
  }

  /**
   * {@inheritdoc}
   */
  public function renderSpans(array $data_options, $mtitle, $mpath) {
    foreach ($data_options['option_extras'] as $service) {
      $data_options['services'] .= ',"' . $service . '"';
    }

    // Share buttons are simply spans of the form class='st_SERVICE_BUTTONTYPE'
    // where -- "st" stands for ShareThis.
    $type = mb_substr($data_options['buttons'], 4);
    $type = $type == '_' ? '' : Html::escape($type);
    $service_array = explode(',', $data_options['services']);
    $st_spans = [];
    foreach ($service_array as $service_full) {
      // Strip the quotes from element in array (They are there for javascript).
      $service = explode(':', $service_full);

      // Service names are expected to be parsed by Name:machine_name. If only
      // one element in the array is given, it's an invalid service.
      if (count($service) < 2) {
        continue;
      }

      // Find the service code name.
      $service_code_name = mb_substr($service[1], 0, -1);

      // Switch the title on a per-service basis if required.
      // $mtitle = $mtitle;.
      switch ($service_code_name) {
        case 'twitter':
          $mtitle = empty($data_options['twitter_suffix']) ? Html::escape($mtitle) : Html::escape($mtitle) . ' ' . Html::escape($data_options['twitter_suffix']);
          break;
      }

      // Sanitize the service code for display.
      $display = Html::escape($service_code_name);

      // Put together the span attributes.
      $attributes = [
        'st_url' => $mpath,
        'st_title' => $mtitle,
        'class' => 'st_' . $display . $type,
      ];
      if ($service_code_name == 'twitter') {
        if (!empty($data_options['twitter_handle'])) {
          $attributes['st_via'] = $data_options['twitter_handle'];
          $attributes['st_username'] = $data_options['twitter_recommends'];
        }
      }
      // Only show the display text if the type is set.
      if (!empty($type)) {
        $attributes['displayText'] = Html::escape($display);
      }
      $span_element = [
        '#type' => 'html_tag',
        '#tag' => 'span',
        '#attributes' => $attributes,
        // It's an empty span tag.
        '#value' => '',
      ];
      // Render the span tag.
      $st_spans[] = $span_element;
    }

    return [
      'data_options' => $data_options,
      'm_path' => $mpath,
      'm_title' => $mtitle ,
      'st_spans' => $st_spans,
    ];
  }

}
