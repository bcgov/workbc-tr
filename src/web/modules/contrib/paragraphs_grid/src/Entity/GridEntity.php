<?php

namespace Drupal\paragraphs_grid\Entity;

use Drupal\Core\Config\Entity\ConfigEntityBase;

/**
 * Defines the Grid entity.
 *
 * @ConfigEntityType(
 *   id = "grid_entity",
 *   label = @Translation("Grid"),
 *   handlers = {
 *     "view_builder" = "Drupal\Core\Entity\EntityViewBuilder",
 *   },
 *   config_prefix = "grid_entity",
 *   admin_permission = "administer site configuration",
 *   entity_keys = {
 *     "id" = "id",
 *     "label" = "label",
 *     "uuid" = "uuid"
 *   },
 *   config_export = {
 *     "id",
 *     "label",
 *     "breakpoints",
 *     "library",
 *     "wrapper",
 *     "cell-fallback",
 *     "cell-properties"
 *   }
 * )
 */
class GridEntity extends ConfigEntityBase implements GridEntityInterface {

  /**
   * The Grid ID.
   *
   * @var string
   */
  protected $id;

  /**
   * The Grid label.
   *
   * @var string
   */
  protected $label;

}
