<?php

namespace Drupal\culturefeed_agenda\Plugin\Block;

use Drupal\Core\Cache\Cache;

/**
 * Provides an 'AgendaSearchPagerBlock' block.
 *
 * @Block(
 *  id = "culturefeed_agenda_search_page_pager",
 *  admin_label = @Translation("Agenda search page pager")
 * )
 */
class AgendaSearchPagerBlock extends AgendaSearchPageBlockBase {

  /**
   * Maximum number of pager links to show.
   */
  const PAGER_MAX_LINKS = 5;

  /**
   * {@inheritdoc}
   */
  public function build() {
    // Initialize the pager.
    pager_default_initialize($this->searchPageService->getTotalResults(), $this->searchPageService->getItemsPerPage());

    return [
      '#theme' => 'culturefeed_agenda_search_pager',
      '#pager' => [
        '#type' => 'pager',
        '#quantity' => self::PAGER_MAX_LINKS,
      ],
    ];
  }

  /**
   * {@inheritdoc}
   */
  public function getCacheContexts() {
    return Cache::mergeContexts(parent::getCacheContexts(), ['url']);
  }

}
