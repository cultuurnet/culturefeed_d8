<?php

namespace Drupal\culturefeed_search_v2_api;

use CultuurNet\Auth\ConsumerCredentials;
use CultuurNet\Search\Guzzle\Service;
use CultuurNet\Search\ServiceInterface;
use Drupal\Core\Cache\CacheBackendInterface;
use Drupal\Core\Config\ConfigException;
use Drupal\Core\Config\ConfigFactoryInterface;
use Drupal\Core\DependencyInjection\ContainerInjectionInterface;
use Symfony\Component\DependencyInjection\ContainerInterface;

/**
 * Provides a drupal wrapper around the culturefeed api client.
 */
class DrupalCultureFeedSearchV2Client implements ServiceInterface, ContainerInjectionInterface {

  /**
   * The culturefeed search service.
   *
   * @var \CultuurNet\Search\Guzzle\Service
   */
  protected $service;

  /**
   * The cache backend to use.
   *
   * @var \Drupal\Core\Cache\CacheBackendInterface
   */
  protected $cacheBackend;

  /**
   * Is the cache enabled.
   *
   * @var bool
   */
  protected $cacheEnabled;

  /**
   * DrupalCultureFeedClient constructor.
   *
   * @param \Drupal\Core\Config\ConfigFactoryInterface $configFactory
   *   The drupal config factory.
   * @param \Drupal\Core\Cache\CacheBackendInterface $cacheBackend
   *   The cache backend.
   */
  public function __construct(ConfigFactoryInterface $configFactory, CacheBackendInterface $cacheBackend) {

    $config = $configFactory->get('culturefeed_search_v2_api.settings');
    $this->cacheBackend = $cacheBackend;

    $this->cacheEnabled = $config->get('enable_cache') === NULL ? TRUE : $config->get('enable_cache');

    if (!$config->get('endpoint_url')) {
      throw new ConfigException('The culturefeed_search_v2_api.settings.endpoint_url is not configured.');
    }
    $endpoint = $config->get('endpoint_url');
    $auth = $config->get('authorization_key');
    $secret = $config->get('secret');
    $consumerCredentials = new ConsumerCredentials($auth, $secret);

    $service = new Service(
      $endpoint,
      $consumerCredentials,
      NULL,
      '3.2'
    );
    $this->service = $service;
  }

  /**
   * {@inheritdoc}
   */
  public static function create(ContainerInterface $container) {
    return new static(
      $container->get('config.factory'),
      $container->get('cache.data')
    );
  }

  /**
   * {@inheritdoc}
   */
  public function search($parameters = []) {

    $cid = 'search:' . md5(serialize($parameters));
    if ($this->cacheEnabled && $cache = $this->cacheBackend->get($cid)) {
      $result = $cache->data;
      return $result;
    }

    $result = $this->service->search($parameters);

    if ($this->cacheEnabled) {
      $this->cacheBackend->set($cid, $result, strtotime('+4 hours'), ['culturefeed_cache']);
    }

    return $result;
  }

  /**
   * {@inheritdoc}
   */
  public function searchPages($parameters = []) {
    return $this->service->searchPages($parameters);
  }

  /**
   * {@inheritdoc}
   */
  public function searchSuggestions($search, $types = [], $past = FALSE, $extra_parameters = [], $max = NULL) {
    return $this->service->searchSuggestions($search, $types, $past, $extra_parameters, $max);
  }

  /**
   * {@inheritdoc}
   */
  public function detail($type, $id) {
    return $this->service->detail($type, $id);
  }

  /**
   * {@inheritdoc}
   */
  public function getDeletions($deleted_since = NULL, $rows = NULL, $start = NULL) {
    return $this->service->getDeletions($deleted_since, $rows, $start);
  }

}
