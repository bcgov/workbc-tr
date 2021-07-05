<?php

namespace Drupal\Tests\sharethis\Functional;

use Drupal\Tests\BrowserTestBase;

/**
 * ShareThis functional tests.
 *
 * @group sharethis
 */
class ShareThisTest extends BrowserTestBase {


  /**
   * {@inheritdoc}
   */
  protected $defaultTheme = 'classy';

  /**
   * Modules to enable.
   *
   * @var array
   */
  public static $modules = ['node', 'sharethis'];

  /**
   * {@inheritdoc}
   */
  protected function setUp(): void {
    parent::setUp();

    // Create a basic page content type.
    $this->drupalCreateContentType([
      'type' => 'page',
      'name' => 'Basic page',
      'display_submitted' => FALSE,
    ]);

    // Create a user that can create basic pages and login as them.
    $web_user = $this->drupalCreateUser(['create page content', 'edit own page content']);
    $this->drupalLogin($web_user);
  }

  /**
   * Create a Basic Page and verify the preview works.
   */
  public function testNodePreview() {
    $this->drupalGet('node/add/page');
    $edit = [];
    $edit['title[0][value]'] = $this->randomMachineName(8);
    $edit['body[0][value]'] = $this->randomMachineName(16);
    $this->drupalPostForm('node/add/page', $edit, t('Preview'));
    $this->assertResponse(200);
  }

}
