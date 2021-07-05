<?php

namespace Drupal\sharethis;

/**
 * Interface for SharethisManager.
 */
interface SharethisManagerInterface {

  /**
   * Determine if connection should be refreshed.
   *
   * @return array
   *   Returns the list of options that sharethis provides.
   */
  public function getOptions();

  /**
   * Custom html block.
   *
   * @return array
   *   Return array renderable by renderSpans().
   */
  public function blockContents();

  /**
   * Custom html markup for widget.
   *
   * @param array $array
   *   Settings array.
   *
   * @return array
   *   Return array renderable by renderSpans().
   */
  public function widgetContents(array $array);

  /**
   * Include st js scripts.
   */
  public function sharethisIncludeJs();

  /**
   * Function is creating options to be passed to stLight.
   *
   * @param array $data_options
   *   The settings selected by publisher in admin panel.
   *
   * @return array
   *   An array of options.
   */
  public function getShareThisLightOptions(array $data_options);

  /**
   * Converts given value to boolean.
   *
   * @param string $val
   *   Which value to convert to boolean.
   *
   * @return bool
   *   Return TRUE or FALSE.
   *
   * @todo To be replaced with bool
   */
  public function toBoolean($val);

  /**
   * Custom html block.
   *
   * @param array $array
   *   Settings array.
   * @param string $title
   *   Title string.
   * @param string $string
   *   Sharethis path.
   *
   * @return array
   *   Array with options, title and path.
   */
  public function renderSpans(array $array, $title, $string);

}
