<?php

namespace Drupal\culturefeed_organizers;

use Drupal\culturefeed_search\AbstractCulturefeedSearchPageService;
use Drupal\culturefeed_search\SearchPageServiceInterface;

/**
 * Central OrganizersSearchPageService for search page handling.
 *
 * The OrganizersSearchPageService service is an intermediate service
 * for performing a search and requesting details about the performed search.
 * It ensures all blocks requesting details about a search
 * get the same fully loaded search information.
 */
class OrganizerSearchPageService extends AbstractCulturefeedSearchPageService {

  /**
   * {@inheritdoc}
   */
  protected function executeQuery() {
    $this->searchResult = $this->searchClient->searchOrganizers($this->searchQuery);
  }

}
