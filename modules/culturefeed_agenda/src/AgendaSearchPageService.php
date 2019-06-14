<?php

namespace Drupal\culturefeed_agenda;

use Drupal\culturefeed_search\AbstractCulturefeedSearchPageService;

/**
 * Central AgendaSearchPageService for search page handling.
 *
 * The AgendaSearchPageService service is an intermediate service
 * for performing a search and requesting details about the performed search.
 * It ensures all blocks requesting details about a search
 * get the same fully loaded search information.
 */
class AgendaSearchPageService extends AbstractCulturefeedSearchPageService {

  /**
   * {@inheritdoc}
   */
  protected function executeQuery() {
    $this->searchResult = $this->searchClient->searchEvents($this->searchQuery);
  }

}
