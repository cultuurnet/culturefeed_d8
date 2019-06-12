<?php

namespace Drupal\culturefeed_search_api;

use Drupal\Core\DependencyInjection\ContainerBuilder;
use Drupal\Core\DependencyInjection\ServiceProviderBase;
use Symfony\Component\DependencyInjection\Reference;

/**
 * Provide optional logging providers.
 */
class CulturefeedCoreServiceProvider extends ServiceProviderBase {

  /**
   * {@inheritdoc}
   */
  public function alter(ContainerBuilder $container) {
    // Allow existing Drupal loggers to be added as handlers.
    if ($container->has('logger.dblog')) {
      $definition = $container->register('monolog.handler.untranslated_drupal_log', 'Drupal\culturefeed_search_api\Logger\UntranslatedDatabaseLogHandler');
      $definition->addArgument(new Reference('logger.dblog'));
    }
  }

}
