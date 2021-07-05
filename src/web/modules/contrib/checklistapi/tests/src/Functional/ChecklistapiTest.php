<?php

namespace Drupal\Tests\checklistapi\Functional;

use Drupal\Tests\BrowserTestBase;

/**
 * Functionally tests Checklist API.
 *
 * @group checklistapi
 *
 * @todo Add tests for vertical tabs progress indicators.
 * @todo Add tests for saving and retrieving checklist progress.
 * @todo Add tests for clearing saved progress.
 */
class ChecklistapiTest extends BrowserTestBase {

  /**
   * {@inheritdoc}
   */
  protected static $modules = [
    'checklistapi',
    'checklistapiexample',
    'help',
    'block',
  ];

  /**
   * {@inheritdoc}
   */
  protected $defaultTheme = 'stark';

  /**
   * A user object with permission to edit any checklist.
   *
   * @var \Drupal\user\Entity\User
   */
  protected $privilegedUser;

  /**
   * {@inheritdoc}
   *
   * @throws \Drupal\Core\Entity\EntityStorageException
   */
  public function setUp() {
    parent::setUp();

    // Create a privileged user.
    $permissions = ['edit any checklistapi checklist'];
    $this->privilegedUser = $this->drupalCreateUser($permissions);
    $this->drupalLogin($this->privilegedUser);

    // Place help block.
    $this->drupalPlaceBlock('help_block', ['region' => 'help']);
  }

  /**
   * Tests checklist access.
   *
   * @throws \Drupal\Core\Entity\EntityStorageException
   */
  public function testChecklistAccess() {
    // Assert that access is granted to a user with "edit any checklistapi
    // checklist" permission.
    $this->drupalGet('admin/config/development/checklistapi-example');
    $this->assertResponse(200);

    // Assert that access is granted to a user with checklist-specific
    // permission.
    $permissions = ['edit example_checklist checklistapi checklist'];
    $semi_privileged_user = $this->drupalCreateUser($permissions);
    $this->drupalLogin($semi_privileged_user);
    $this->drupalGet('admin/config/development/checklistapi-example');
    $this->assertResponse(200);

    // Assert that access is denied to a non-privileged user.
    $this->drupalLogout();
    $this->drupalGet('admin/config/development/checklistapi-example');
    $this->assertResponse(403);
  }

  /**
   * Tests checklist composition.
   */
  public function testChecklistComposition() {
    // Assert that a per-checklist help block is created.
    $this->drupalGet('admin/config/development/checklistapi-example');
    $this->assertRaw('This checklist based on');
  }

  /**
   * Tests permissions.
   */
  public function testPermissions() {
    self::assertTrue($this->checkPermissions([
      'view checklistapi checklists report',
      'view any checklistapi checklist',
      'edit any checklistapi checklist',
    ]), 'Created universal permissions.');
    self::assertTrue($this->checkPermissions([
      'view example_checklist checklistapi checklist',
      'edit example_checklist checklistapi checklist',
    ]), 'Created per-checklist permissions.');
  }

}
