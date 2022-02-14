<?php

namespace Drupal\culturefeed_agenda\Plugin\Block;

use Drupal\Core\Cache\Cache;
use Drupal\culturefeed_search\Plugin\Block\SearchPageBlockBase;
use Drupal\culturefeed_search\Plugin\Block\SearchPagePagerBlock;
use Symfony\Component\DependencyInjection\ContainerInterface;

/**
 * Provides an 'AgendaSearchPagerBlock' block.
 *
 * @deprecated
 *
 * @Block(
 *  id = "culturefeed_agenda_search_page_pager",
 *  admin_label = @Translation("Deprecated: Agenda search page pager")
 * )
 */
class AgendaSearchPagerBlock extends SearchPagePagerBlock {

  /**
   * {@inheritdoc}
   */
  public static function create(ContainerInterface $container, array $configuration, $plugin_id, $plugin_definition) {
    return new static(
      $configuration,
      $plugin_id,
      $plugin_definition,
      $container->get('pager.manager'),
      $container->get('culturefeed_search.search_page_service_manager'),
      $container->get('culturefeed_agenda.search_page_service'),
    );
  }

}
