<?php

namespace Drupal\paragraph_blocks\Controller;

use Drupal\panels\Controller\Panels;
use Symfony\Component\HttpFoundation\Request;
use Drupal\Core\Render\Element;

/**
 * Overrides the Panels controller class.
 */
class ParagraphBlocksPanelsController extends Panels {

  /**
   * {@inheritdoc}
   *
   * This is called from panels.select_block, while selecting a block to put
   * into the default page layout. Filter out blocks here that are also filtered
   * in the panels IPE.
   *
   * @todo: Should this be a panels IPE patch?
   *
   * @todo: Is there a way to let blockManager contexts filter this so that we
   * don't need to override the controller class?
   */
  public function selectBlock(Request $request, $machine_name, $tempstore_id) {
    $build = parent::selectBlock($request, $machine_name, $tempstore_id);

    // Create blocks from build array.
    $available_plugins = $this->blockManager->getDefinitions();
    $blocks = [];
    foreach ($build as $category => $item) {
      foreach (array_keys($item['content']['#links']) as $plugin_id) {
        $plugin_definition = $available_plugins[$plugin_id];
        $blocks[] = [
          'plugin_id' => $plugin_id,
          'category' => $plugin_definition['category'],
          'id' => $plugin_definition['id'],
          'provider' => $plugin_definition['provider'],
          'label' => $plugin_definition['admin_label'],
        ];
      }
    }

    // Trigger hook_panels_ipe_blocks_alter(). Allows other modules to change
    // the list of blocks that are visible.
    $original_blocks = $blocks;
    \Drupal::moduleHandler()->alter('panels_ipe_blocks', $blocks);

    // Key the blocks array for speed.
    $keyed_blocks = [];
    foreach ($blocks as $block) {
      $plugin_id = $block['plugin_id'];
      $keyed_blocks[$plugin_id] = $block;
    }

    // Update the build array from the altered blocks.
    foreach (Element::children($build) as $category) {
      $item = &$build[$category];
      foreach (array_keys($item['content']['#links']) as $plugin_id) {
        if (isset($keyed_blocks[$plugin_id])) {
          $item['content']['#links'][$plugin_id]['title'] = $keyed_blocks[$plugin_id]['label'];
        }
        else {
          unset($item['content']['#links'][$plugin_id]);
        }
      }
      if (empty($item['content']['#links'])) {
        unset($build[$category]);
      }
    }

    return $build;
  }

}
