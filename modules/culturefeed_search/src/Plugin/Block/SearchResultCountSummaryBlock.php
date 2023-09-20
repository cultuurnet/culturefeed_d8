<?php

namespace Drupal\culturefeed_search\Plugin\Block;

use Drupal\Core\Cache\Cache;

/**
 * Provides an 'SearchResultCountSummaryBlock' block.
 *
 * @Block(
 *  id = "culturefeed_search_search_result_count_summary",
 *  admin_label = @Translation("Search result count summary")
 * )
 */
class SearchResultCountSummaryBlock extends SearchPageBlockBase {

  /**
   * {@inheritdoc}
   */
  public function build() {
    return [
      '#theme' => 'culturefeed_search_search_result_count_summary',
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
