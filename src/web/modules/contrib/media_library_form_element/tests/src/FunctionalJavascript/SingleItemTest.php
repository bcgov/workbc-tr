<?php

namespace Drupal\Tests\media_library_form_element\FunctionalJavascript;

use Drupal\Tests\TestFileCreationTrait;
use Drupal\Tests\media_library\FunctionalJavascript\MediaLibraryTestBase;

/**
 * Test using the media library element with cardinality 1.
 *
 * @group media_library
 */
class SingleItemTest extends MediaLibraryTestBase {

  use TestFileCreationTrait;

  /**
   * The modules to load to run the test.
   *
   * @var array
   */
  protected static $modules = [
    'media_library_test',
    'media_library_form_element',
    'media_library_form_element_test',
  ];

  /**
   * Use the 'standard' installation profile.
   *
   * @var string
   */
  protected $profile = 'standard';

  /**
   * Specify the theme to be used in testing.
   *
   * @var string
   */
  protected $defaultTheme = 'classy';

  /**
   * {@inheritdoc}
   */
  protected function setUp() {
    // Bypass the need in the test module to define schema.
    $this->strictConfigSchema = NULL;

    parent::setUp();
    $this->createMediaItems([
      'type_one' => [
        'Horse',
        'Bear',
        'Cat',
        'Dog',
      ],
      'type_two' => [
        'Crocodile',
        'Lizard',
        'Snake',
        'Turtle',
      ],
    ]);

    // Create a user that can only add media of type one.
    $user = $this->drupalCreateUser([
      'access administration pages',
      'access content',
      'create type_one media',
      'view media',
    ]);
    $this->drupalLogin($user);
  }

  /**
   * Tests the setting form.
   */
  public function testForm() {
    $assert = $this->assertSession();
    $page = $this->getSession()->getPage();
    $this->drupalGet('media-library-form-element-test-form');
    // The form element correctly displays description text.
    $assert->elementContains('css', '#media-media-library-wrapper--description', 'Upload or select your profile image');
    // Cardinality is limited to 1, and no item is populated by default.
    $assert->elementContains('css', '#media-media-library-wrapper--description', 'One media item remaining');

    // Enter the media library.
    $page->pressButton('Add media');
    $assert->assertWaitOnAjaxRequest();
    // Only allowed media types as defined by the form element
    // are displayed in the Media Library menu.
    $assert->elementContains('css', '.media-library-menu a', 'Type One');
    $assert->pageTextContains('Type Two');
    $assert->pageTextNotContains('Type Three');

    // This form element provides access to 'type_one' media.
    $assert->pageTextContains('Horse');
    $assert->pageTextContains('Bear');

    $page->find('css', 'input[name="media_library_select_form[0]"]')->setValue('1');
    $assert->assertWaitOnAjaxRequest();
    $assert->checkboxChecked('media_library_select_form[0]');

    $assert->elementExists('css', '.ui-dialog-buttonset')->pressButton('Insert selected');
    $assert->assertWaitOnAjaxRequest();

    // The item displays in the form element preview.
    $assert->elementContains('css', '.media-library-item__name', 'Dog');
    // Cardinality of 1 is reflected.
    $assert->elementContains('css', '#media-media-library-wrapper--description', 'The maximum number of media items have been selected.');

    $page->pressButton('Save configuration');
    // Verify the selection is saved in configuration
    // and loads as the default value.
    $assert->pageTextContains('Dog');
    $assert->elementContains('css', '.media-library-item__name', 'Dog');

    // Remove the item.
    $page->pressButton('Remove');
    $this->waitForNoText('Dog');
    $page->pressButton('Save configuration');
    $assert->pageTextNotContains('Dog');
    $assert->elementContains('css', '#media-media-library-wrapper--description', 'One media item remaining');
  }

}
