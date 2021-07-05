<?php

namespace Drupal\Tests\sharethis\Functional;

use Drupal\Tests\node\Functional\NodeTestBase;

/**
 * Tests the SystemConfigFormTestBase class.
 *
 * @group sharethis
 */
class SharethisConfigFormTest extends NodeTestBase {


  /**
   * {@inheritdoc}
   */
  protected $defaultTheme = 'stark';

  /**
   * The User object.
   *
   * @var object
   */
  protected $adminUser;

  /**
   * The config object.
   *
   * @var object
   */
  protected $config;

  /**
   * Modules to enable.
   *
   * @var array
   */
  public static $modules = ['node', 'system_test', 'user', 'sharethis'];

  /**
   * {@inheritdoc}
   */
  public function setUp(): void {
    parent::setUp();
    $this->config = \Drupal::configFactory()->getEditable('sharethis.settings');
    // Create and log in admin user.
    $this->adminUser = $this->drupalCreateUser(['administer sharethis', 'administer nodes']);
    $this->drupalLogin($this->adminUser);
  }

  /**
   * Tests the SharethisConfigForm.
   */
  public function testSharethisConfigForm() {
    // Test that out of range values are picked up.
    $edit['location'] = 'content';
    $this->drupalPostForm('admin/config/services/sharethis', $edit, t('Save configuration'));
    $this->assertText(t('The configuration options have been saved.'));
    $settings = [
      'body' => [['value' => 'Lorem ipsum dolor sit amet, consectetur adipiscing elit. Etiam vitae arcu at leo cursus laoreet. Curabitur dui tortor, adipiscing malesuada tempor in, bibendum ac diam. Cras non tellus a libero pellentesque condimentum. What is a Drupalism? Suspendisse ac lacus libero. Ut non est vel nisl faucibus interdum nec sed leo. Pellentesque sem risus, vulputate eu semper eget, auctor in libero. Ut fermentum est vitae metus convallis scelerisque. Phasellus pellentesque rhoncus tellus, eu dignissim purus posuere id. Quisque eu fringilla ligula. Morbi ullamcorper, lorem et mattis egestas, tortor neque pretium velit, eget eleifend odio turpis eu purus. Donec vitae metus quis leo pretium tincidunt a pulvinar sem. Morbi adipiscing laoreet mauris vel placerat. Nullam elementum, nisl sit amet scelerisque malesuada, dolor nunc hendrerit quam, eu ultrices erat est in orci. Curabitur feugiat egestas nisl sed accumsan.']],
      'promote' => 1,
    ];
    $node = $this->drupalCreateNode($settings);

    // Render the node.
    $this->drupalGet('node/' . $node->id());
    $result = $this->xpath('//div[@class=:class]', [':class' => 'sharethis-wrapper']);
    $this->assertEqual(count($result), 1, 'Sharethis links found');
  }

  /**
   * Tests the SharethisConfigForm.
   */
  public function testSharethisConfigFormlinks() {

    // Testing sharelinks on links present in a node.
    $edit['location'] = 'links';
    $edit['article_options[full]'] = 'full';
    $edit['page_options[full]'] = 'full';

    $this->drupalPostForm('admin/config/services/sharethis', $edit, t('Save configuration'));
    $this->assertText(t('The configuration options have been saved.'));
    $settings = [
      'body' => [['value' => 'Lorem ipsum dolor sit amet, consectetur www.drupal.org']],
      'promote' => 1,
    ];
    $node = $this->drupalCreateNode($settings);

    // Render the node.
    $this->drupalGet('node/' . $node->id());
    $result = $this->xpath('//div[@class=:class]', [':class' => 'sharethis-wrapper']);
    $this->assertEqual(count($result), 1, 'Sharethis links found');
    $result = $this->xpath('//span[@class=:class]', [':class' => 'st_facebook_button']);
    $this->assertEqual(count($result), 1, 'Facebook button found');
    $result = $this->xpath('//span[@class=:class]', [':class' => 'st_twitter_button']);
    $this->assertEqual(count($result), 1, 'Twitter button found');
    $result = $this->xpath('//span[@class=:class]', [':class' => 'st_linkedin_button']);
    $this->assertEqual(count($result), 1, 'LinkedIn button found');
    $result = $this->xpath('//span[@class=:class]', [':class' => 'st_email_button']);
    $this->assertEqual(count($result), 1, 'Email button found');
    $result = $this->xpath('//span[@class=:class]', [':class' => 'st_sharethis_button']);
    $this->assertEqual(count($result), 1, 'Sharethis button links found');
    $result = $this->xpath('//span[@class=:class]', [':class' => 'st_pinterest_button']);
    $this->assertEqual(count($result), 1, 'Pinterest button found');
  }

}
