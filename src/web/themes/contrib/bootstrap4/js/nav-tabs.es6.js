/**
 * @file
 * Responsive navigation tabs (local tasks)
 *
 * Element requires to have class .is-collapsible and attribute [data-drupal-nav-tabs]
 */
(($, Drupal) => {
  function init(i, tab) {
    const $tab = $(tab);
    const $target = $tab.find('[data-drupal-nav-tabs-target]');

    const openMenu = () => {
      $target.toggleClass('is-open');
      const $toggle = $target.find('.tab-toggle');
      $toggle.attr(
        'aria-expanded',
        (_, isExpanded) => !(isExpanded === 'true'),
      );
    };

    $tab.on('click.tabs', '[data-drupal-nav-tabs-toggle]', openMenu);
  }
  /**
   * Initialize the tabs JS.
   */
  Drupal.behaviors.navTabs = {
    attach(context) {
      $(context)
        .find('[data-drupal-nav-tabs].is-collapsible')
        .once('nav-tabs')
        .each((i, value) => {
          $(value).each(init);
        });
    },
  };
})(jQuery, Drupal);
