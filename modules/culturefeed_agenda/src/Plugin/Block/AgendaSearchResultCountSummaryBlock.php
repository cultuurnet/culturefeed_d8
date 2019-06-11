<?php

namespace Drupal\culturefeed_agenda\Plugin\Block;

use Drupal\Core\Cache\Cache;

/**
 * Provides an 'AgendaSearchResultCountSummaryBlock' block.
 *
 * @Block(
 *  id = "culturefeed_agenda_search_result_count_summary",
 *  admin_label = @Translation("Agenda search result count summary")
 * )
 */
class AgendaSearchResultCountSummaryBlock extends AgendaSearchPageBlockBase {

  /**
   * {@inheritdoc}
   */
  public function build() {
    return [
      '#theme' => 'culturefeed_agenda_search_result_count_summary',
      '#total' => $this->searchPageService->getTotalResults(),
      '#current_page' => $this->searchPageService->getCurrentPage(),
      '#items_per_page' => $this->searchPageService->getItemsPerPage(),
    ];
  }

  /**
   * {@inheritdoc}
   */
  public function getCacheContexts() {
    return Cache::mergeContexts(parent::getCacheContexts(), ['url']);
  }

}
