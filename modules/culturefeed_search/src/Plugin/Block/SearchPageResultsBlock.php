<?php

namespace Drupal\culturefeed_search\Plugin\Block;

use Drupal\Core\Cache\Cache;

/**
 * Provides a 'SearchPageResultsBlock' block.
 *
 * @Block(
 *  id = "culturefeed_search_search_page_results",
 *  admin_label = @Translation("Search page results")
 * )
 */
class SearchPageResultsBlock extends SearchPageBlockBase {

  /**
   * {@inheritdoc}
   */
  public function build() {
    $build = [];

    $results = $this->searchPageService->getSearchResultItems();

    if ($this->searchPageService->searchHasFailed()) {
      $build['#cache']['max-age'] = 0;
    }

    $build += [
      '#theme' => 'culturefeed_search_search_results',
      '#results' => $results,
      '#empty' => !empty($this->searchPageService->getSearchResultItems()) ? NULL : $this->t('Your search yielded no results.', [], ['context' => 'culturefeed_agenda']),
    ];

    return $build;
  }

  /**
   * {@inheritdoc}
   */
  public function getCacheContexts() {
    return Cache::mergeContexts(parent::getCacheContexts(), ['url']);
  }

}
