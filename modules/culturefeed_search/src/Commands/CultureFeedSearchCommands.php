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
   * CultureFeedSearchCommands constructor.
   *
   * @param \Drupal\Core\Cache\CacheTagsInvalidatorInterface $cacheTagsInvalidator
   *   The cache tags invalidator.
   */
  public function __construct(
    protected readonly CacheTagsInvalidatorInterface $cacheTagsInvalidator
  ) {
  }

  /**
   * Invalidate CultureFeed Search event related cache.
   *
   * @command culturefeed-search:clear-event-cache
   * @aliases cfs-cec
   * @usage culturefeed-search:clear-event-cache
   *   Clear CultureFeed Search event related cache.
   */
  public function clearSearchEventCache(): void {

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
