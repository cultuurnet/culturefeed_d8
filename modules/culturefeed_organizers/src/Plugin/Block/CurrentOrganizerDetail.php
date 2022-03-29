<?php

namespace Drupal\culturefeed_organizers\Plugin\Block;

use Drupal\Core\Block\BlockBase;
use Drupal\Core\Cache\Cache;
use Drupal\Core\Plugin\ContextAwarePluginInterface;

/**
 * Provides a block to view the detail of an organizer (eg via page manager).
 *
 * @Block(
 *  id = "current_organizer_detail",
 *  admin_label = @Translation("Current organizer detail"),
 *  context_definitions = {
 *     "culturefeed_organizer" = @ContextDefinition("culturefeed_organizer", required = TRUE)
 *  }
 * )
 */
class CurrentOrganizerDetail extends BlockBase implements ContextAwarePluginInterface {

  /**
   * {@inheritdoc}
   */
  public function build() {
    return [
      '#theme' => 'culturefeed_organizer',
      '#item' => $this->getContextValue('culturefeed_organizer'),
    ];
  }

  /**
   * {@inheritdoc}
   */
  public function getCacheTags() {
    return Cache::mergeTags(parent::getCacheTags(), ['culturefeed_search_api']);
  }

  /**
   * {@inheritdoc}
   */
  public function getCacheContexts() {
    return Cache::mergeContexts(parent::getCacheContexts(), ['url.path']);
  }

}
