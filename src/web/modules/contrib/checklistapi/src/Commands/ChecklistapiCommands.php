<?php

namespace Drupal\checklistapi\Commands;

use Consolidation\OutputFormatters\FormatterManager;
use Consolidation\OutputFormatters\Options\FormatterOptions;
use Consolidation\OutputFormatters\StructuredData\RowsOfFields;
use Drupal\Core\Logger\LoggerChannelFactoryInterface;
use Drupal\Core\Render\Element;
use Drupal\user\Entity\User;
use Drush\Commands\DrushCommands;
use Drush\Commands\help\ListCommands;
use Psr\Log\LoggerAwareInterface;

/**
 * Checklist API Drush command fileA Drush commandfile.
 */
class ChecklistapiCommands extends DrushCommands implements LoggerAwareInterface {


  /**
   * The logger channel.
   *
   * @var \Drupal\Core\Logger\LoggerChannelInterface
   */
  protected $logger;

  /**
   * Constructs an instance.
   *
   * @param \Drupal\Core\Logger\LoggerChannelFactoryInterface $logger_channel
   *   The logger channel factory.
   */
  public function __construct(LoggerChannelFactoryInterface $logger_channel) {
    $this->logger = $logger_channel->get('drush');
  }

  /**
   * Get an overview of your installed checklists with progress details.
   *
   * @command checklistapi:list
   * @aliases capi-list,capil,checklistapi-list
   */
  public function listCommand() {
    $definitions = checklistapi_get_checklist_info();

    if (empty($definitions)) {
      return $this->logger->alert(dt('No checklists available.'));
    }

    // Build table rows.
    $rows = [];
    // The first row is the table header.
    $rows[] = [
      dt('Checklist'),
      dt('Progress'),
      dt('Last updated'),
      dt('Last updated by'),
    ];
    foreach ($definitions as $id => $definition) {
      $checklist = checklistapi_checklist_load($id);
      $row = [];
      $row[] = dt('!title (@id)', [
        '!title' => strip_tags($checklist->title),
        '@id' => $id,
      ]);
      $row[] = dt('@completed of @total (@percent%)', [
        '@completed' => $checklist->getNumberCompleted(),
        '@total' => $checklist->getNumberOfItems(),
        '@percent' => round($checklist->getPercentComplete()),
      ]);
      $row[] = $checklist->getLastUpdatedDate();
      $row[] = $checklist->getLastUpdatedUser();
      $rows[] = $row;
    }

    $formatter_manager = new FormatterManager();
    $opts = [
      FormatterOptions::INCLUDE_FIELD_LABELS => FALSE,
      FormatterOptions::TABLE_STYLE => 'compact',
      FormatterOptions::TERMINAL_WIDTH => ListCommands::getTerminalWidth(),
    ];
    $formatter_options = new FormatterOptions([], $opts);

    $formatter_manager->write($this->output(), 'table', new RowsOfFields($rows), $formatter_options);
  }

  /**
   * Show detailed info for a given checklist.
   *
   * @param string $checklist_id
   *   The checklist machine name, e.g., "example_checklist".
   *
   * @return string|void
   *   The command output.
   *
   * @command checklistapi:info
   * @aliases capi-info,capii,checklistapi-info
   */
  public function infoCommand($checklist_id) {
    $checklist = checklistapi_checklist_load($checklist_id);

    // Make sure the given checklist exists.
    if (!$checklist) {
      return $this->logger->error(dt('No such checklist "@id".', [
        '@id' => $checklist_id,
      ]));
    }

    $output = [];

    // Print the help.
    if (!empty($checklist->help)) {
      $output[] = strip_tags($checklist->help);
    }

    // Print last updated and progress details.
    if ($checklist->hasSavedProgress()) {
      $output[] = '';
      $output[] = dt('Last updated @date by @user', [
        '@date' => $checklist->getLastUpdatedDate(),
        '@user' => $checklist->getLastUpdatedUser(),
      ]);
      $output[] = dt('@completed of @total (@percent%) complete', [
        '@completed' => $checklist->getNumberCompleted(),
        '@total' => $checklist->getNumberOfItems(),
        '@percent' => round($checklist->getPercentComplete()),
      ]);
    }

    // Loop through groups.
    $groups = $checklist->items;
    foreach (Element::children($groups) as $group_key) {
      $group = &$groups[$group_key];

      // Print group title.
      $output[] = '';
      $output[] = strip_tags($group['#title']) . ':';

      // Loop through items.
      foreach (Element::children($group) as $item_key) {
        $item = &$group[$item_key];
        $saved_item = !empty($checklist->savedProgress['#items'][$item_key]) ? $checklist->savedProgress['#items'][$item_key] : 0;
        // Build title.
        $title = strip_tags($item['#title']);
        if ($saved_item) {
          // Append completion details.
          $user = User::load($saved_item['#uid']);
          $title .= ' - ' . dt('Completed @time by @user', [
            '@time' => \Drupal::service('date.formatter')->format($saved_item['#completed'], 'short'),
            '@user' => $user->getAccountName(),
          ]);
        }
        // Print the list item.
        $output[] = dt(' [@x] !title', [
          '@x' => ($saved_item) ? 'x' : ' ',
          '!title' => $title,
        ]);
      }
    }
    $output[] = '';

    return implode(PHP_EOL, $output);
  }

}
