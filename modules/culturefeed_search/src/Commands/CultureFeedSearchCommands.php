<?php

namespace Drupal\culturefeed_search\Commands;

use Drupal\Core\Cache\CacheTagsInvalidatorInterface;
use Drupal\Core\StringTranslation\StringTranslationTrait;
use Drush\Commands\DrushCommands;

/**
 * CultureFeed Search drush commands.
 */
class CultureFeedSearchCommands extends DrushCommands {

  use StringTranslationTrait;

  /**
   * The cache tags invalidator.
   *
   * @var \Drupal\Core\Cache\CacheTagsInvalidatorInterface
   */
  protected $cacheTagsInvalidator;

  /**
   * CultureFeedSearchCommands constructor.
   *
   * @param \Drupal\Core\Cache\CacheTagsInvalidatorInterface $cacheTagsInvalidator
   *   The cache tags invalidator.
   */
  public function __construct(
    CacheTagsInvalidatorInterface $cacheTagsInvalidator
  ) {
    $this->cacheTagsInvalidator = $cacheTagsInvalidator;
  }

  /**
   * Invalidate CultureFeed Search event related cache.
   *
   * @command culturefeed-search:clear-event-cache
   * @aliases cfs-cec
   * @usage culturefeed-search:clear-event-cache
   *   Clear CultureFeed Search event related cache.
   */
  public function clearSearchEventCache() {

    $tagsToInvalidate = [
      'culturefeed_search_api',
      'culturefeed_entry_api',
    ];

    $this->cacheTagsInvalidator->invalidateTags($tagsToInvalidate);

    $this->output()
      ->writeln($this->t('CultureFeed Search event cache has been cleared.')
        ->__toString());
  }

}
