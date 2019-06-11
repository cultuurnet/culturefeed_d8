<?php

namespace Drupal\culturefeed_agenda\Plugin\Block;

use Drupal\Core\Cache\Cache;

/**
 * Provides a 'AgendaSearchResultsBlock' block.
 *
 * @Block(
 *  id = "culturefeed_agenda_search_page_results",
 *  admin_label = @Translation("Agenda search page results")
 * )
 */
class AgendaSearchResultsBlock extends AgendaSearchPageBlockBase {

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
      '#theme' => 'culturefeed_agenda_search_results',
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
