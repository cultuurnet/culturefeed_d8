<?php

namespace Drupal\culturefeed_search\Plugin\Block;

use Drupal\Core\Cache\Cache;

/**
 * Provides an 'SearchResultCountBlock' block.
 *
 * @Block(
 *  id = "culturefeed_search_result_count",
 *  admin_label = @Translation("Search result count")
 * )
 */
class SearchResultCountBlock extends SearchPageBlockBase {

  /**
   * {@inheritdoc}
   */
  public function build() {
    return [
      '#theme' => 'culturefeed_search_search_result_count',
      '#count' => $this->searchPageService->getTotalResults(),
      '#summary' => TRUE,
    ];
  }

  /**
   * {@inheritdoc}
   */
  public function getCacheContexts() {
    return Cache::mergeContexts(parent::getCacheContexts(), ['url']);
  }

}
