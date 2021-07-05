<?php

namespace Drupal\Tests\sharethis\Functional\Views;

use Drupal\views\Views;
use Drupal\views\Tests\ViewTestData;
use Drupal\Tests\views\Functional\ViewTestBase;

/**
 * Tests the sharethis links appearing on node.
 *
 * @group sharethis
 *
 * @see \Drupal\sharethis\Plugin\views\field\SharethisNode.
 */
class SharethisViewsPluginTest extends ViewTestBase {

  /**
   * {@inheritdoc}
   */
  protected $defaultTheme = 'classy';

  /**
   * The privileged_user object.
   *
   * @var object
   */
  protected $privilegedUser;

  /**
   * Modules to enable.
   *
   * @var array
   */
  public static $modules = [
    'node', 'system_test', 'views', 'user', 'sharethis', 'sharethis_test_views',
  ];

  /**
   * Views used by this test.
   *
   * @var array
   */
  public static $testViews = ['test_sharethis'];

  /**
   * {@inheritdoc}
   */
  protected function setUp($import_test_views = TRUE): void {
    parent::setUp($import_test_views);

    // Create and login user.
    $this->privilegedUser = $this->drupalCreateUser(['administer site configuration', 'access administration pages']);
    $this->drupalLogin($this->privilegedUser);
    ViewTestData::createTestViews(get_class($this), ['sharethis_test_views']);

  }

  /**
   * Tests the handlers.
   */
  public function testHandlers() {
    $this->drupalCreateNode();
    $this->drupalCreateNode();

    // Test the sharethis field.
    $view = Views::getView('test_sharethis');
    $view->setDisplay('page_1');
    $this->executeView($view);
    $this->assertEqual(count($view->result), 2);
    $this->drupalGet('test-sharethis');
    $result = $this->xpath('//div[@class=:class]', [':class' => 'sharethis-wrapper']);
    $this->assertEqual(count($result), 2, 'Sharethis links found');
  }

}
