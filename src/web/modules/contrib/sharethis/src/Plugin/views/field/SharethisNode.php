<?php

namespace Drupal\sharethis\Plugin\views\field;

use Drupal\sharethis\SharethisManagerInterface;
use Drupal\views\ResultRow;
use Drupal\views\Plugin\views\field\FieldPluginBase;
use Symfony\Component\DependencyInjection\ContainerInterface;

/**
 * Field handler to display the number of new comments.
 *
 * @ingroup views_field_handlers
 *
 * @ViewsField("sharethis_node")
 */
class SharethisNode extends FieldPluginBase {

  /**
   * The Sharethis Manager.
   *
   * @var \Drupal\sharethis\SharethisManager
   */
  protected $sharethisManager;

  /**
   * Constructs an SharethisNode object.
   *
   * @param array $configuration
   *   A configuration array containing information about the plugin instance.
   * @param string $plugin_id
   *   The plugin_id for the plugin instance.
   * @param mixed $plugin_definition
   *   The plugin implementation definition.
   * @param \Drupal\sharethis\SharethisManagerInterface $sharethis_manager
   *   The sharethis manager service.
   */
  public function __construct(array $configuration, $plugin_id, $plugin_definition, SharethisManagerInterface $sharethis_manager) {
    parent::__construct($configuration, $plugin_id, $plugin_definition);
    $this->sharethisManager = $sharethis_manager;
  }

  /**
   * {@inheritdoc}
   */
  public static function create(ContainerInterface $container, array $configuration, $plugin_id, $plugin_definition) {
    return new static(
      $configuration,
      $plugin_id,
      $plugin_definition,
      $container->get('sharethis.manager')
    );
  }

  /**
   * {@inheritdoc}
   */
  public function query() {
    $this->ensureMyTable();
    $this->addAdditionalFields();
  }

  /**
   * {@inheritdoc}
   */
  public function render(ResultRow $values) {
    $sharethis_manager = $this->sharethisManager;
    $node = $values->_entity;
    $m_title = $node->getTitle();
    $m_path = $node->toUrl()->setAbsolute()->toString();
    $data_options = $sharethis_manager->getOptions();
    $st_js = $sharethis_manager->sharethisIncludeJs();
    $content = $sharethis_manager->renderSpans($data_options, $m_title, $m_path);
    return [
      '#theme' => 'sharethis_block',
      '#content' => $content,
      '#attached' => [
        'library' => [
          'sharethis/sharethispickerexternalbuttonsws',
          'sharethis/sharethispickerexternalbuttons',
          'sharethis/sharethis',
        ],
        'drupalSettings' => [
          'sharethis' => $st_js,
        ],
      ],
    ];
  }

}
