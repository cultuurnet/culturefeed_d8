<?php

namespace Drupal\culturefeed_agenda\Plugin\Block;

use Drupal\Core\Cache\Cache;

/**
 * Provides an 'AgendaSearchResultCountBlock' block.
 *
 * @Block(
 *  id = "culturefeed_agenda_search_result_count",
 *  admin_label = @Translation("Agenda search result count")
 * )
 */
class AgendaSearchResultCountBlock extends AgendaSearchPageBlockBase {

  /**
   * {@inheritdoc}
   */
  public function build() {
    return [
      '#theme' => 'culturefeed_agenda_search_result_count',
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
