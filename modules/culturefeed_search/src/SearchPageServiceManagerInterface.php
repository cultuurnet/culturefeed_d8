<?php

namespace Drupal\culturefeed_search;

/**
 * Provides a SearchPageServiceManagerInterface to manage the known search page services.
 */
interface SearchPageServiceManagerInterface {

  /**
   * Appends a search page to the list of known search page services.
   *
   * @param \Drupal\Core\StringTranslation\Translator\TranslatorInterface $translator
   *   The search page to be added.
   * @param int $priority
   *   The priority of the search page being added.
   */
  public function addSearchPage(SearchPageServiceInterface $searchPageService, $priority = 0): SearchPageServiceManagerInterface;

  /**
   * Get the list of known search pages.
   *
   * @return array
   *   The search pages.
   */
  public function getSearchPages(): array;

}
