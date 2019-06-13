<?php

namespace Drupal\culturefeed_search_api;

use CultuurNet\SearchV3\Parameter\AudienceType;
use CultuurNet\SearchV3\Parameter\Id;
use CultuurNet\SearchV3\SearchClient;
use CultuurNet\SearchV3\SearchQuery;
use CultuurNet\SearchV3\SearchQueryInterface;
use CultuurNet\SearchV3\Serializer\Serializer;
use Drupal\Component\Utility\Crypt;
use Drupal\Core\Cache\CacheBackendInterface;
use Drupal\Core\Config\ConfigException;
use Drupal\Core\Config\ConfigFactory;
use Drupal\Core\Language\LanguageManagerInterface;
use Drupal\Core\Logger\LoggerChannelFactoryInterface;
use Drupal\monolog\Logger\Logger;
use GuzzleHttp\Client;
use GuzzleHttp\ClientInterface;
use GuzzleHttp\HandlerStack;
use GuzzleHttp\MessageFormatter;
use GuzzleHttp\Middleware;
use Monolog\Logger as MonologLogger;

/**
 * Provides a drupal wrapper around the search client.
 */
class DrupalCulturefeedSearchClient implements DrupalCulturefeedSearchClientInterface {

  /**
   * The search client.
   *
   * @var \CultuurNet\SearchV3\SearchClient
   */
  protected $client;

  /**
   * The cache backend.
   *
   * @var \Drupal\Core\Cache\CacheBackendInterface
   */
  protected $cacheBackend;

  /**
   * The language manager.
   *
   * @var \Drupal\Core\Language\LanguageManagerInterface
   */
  protected $languageManager;

  /**
   * Is cache enabled.
   *
   * @var bool
   */
  protected $cacheEnabled;

  /**
   * The static cache.
   *
   * @var array
   */
  protected $staticCache;

  /**
   * The search client config.
   *
   * @var \Drupal\Core\Config\Config|\Drupal\Core\Config\ImmutableConfig
   */
  protected $config;

  /**
   * DrupalSearchClient constructor.
   *
   * @param \Drupal\Core\Config\ConfigFactory $configFactory
   *   The config factory.
   * @param \Drupal\Core\Logger\LoggerChannelFactoryInterface $loggerChannelFactory
   *   The logger channel factory.
   * @param \Drupal\Core\Cache\CacheBackendInterface $cacheBackend
   *   The cache backend.
   * @param \Drupal\Core\Language\LanguageManagerInterface $languageManager
   *   The language manager.
   */
  public function __construct(ConfigFactory $configFactory, LoggerChannelFactoryInterface $loggerChannelFactory, CacheBackendInterface $cacheBackend, LanguageManagerInterface $languageManager) {
    $this->config = $configFactory->get('culturefeed_search_api.settings');

    $this->languageManager = $languageManager;
    $this->cacheBackend = $cacheBackend;
    $this->cacheEnabled = $this->config->get('enable_cache') === NULL ? TRUE : $this->config->get('enable_cache');

    $logger = $loggerChannelFactory->get('culturefeed_search_api');

    $handlerStack = HandlerStack::create();
    if ($logger instanceof MonologLogger) {
      // If debug is enabled, set all handlers to debug mode.
      $level = $this->config->get('debug') ? Logger::DEBUG : Logger::NOTICE;

      $handlers = $logger->getHandlers();
      /** @var \Monolog\Handler\HandlerInterface $handler */
      foreach ($handlers as $handler) {
        $handler->setLevel($level);
      }

      $handlerStack->push(
        Middleware::log(
          $logger,
          new MessageFormatter(MessageFormatter::DEBUG)
        )
      );

    }

    $guzzleClient = new Client([
      'base_uri' => $this->config->get('endpoint_url'),
      'headers' => [
        'X-Api-Key' => $this->config->get('api_key'),
      ],
      'handler' => $handlerStack,
    ]);

    $serializer = new Serializer();
    $this->client = new SearchClient($guzzleClient, $serializer);
  }

  /**
   * {@inheritdoc}
   */
  public function setClient(ClientInterface $client) {
    $this->client->setClient($client);
  }

  /**
   * {@inheritdoc}
   */
  public function getClient() {
    return $this->client->getClient();
  }

  /**
   * {@inheritdoc}
   */
  public function searchEvents(SearchQueryInterface $searchQuery) {
    $query = $searchQuery->toArray();
    $hash = Crypt::hashBase64(serialize($query));
    $cid = 'culturefeed_search_api.search_events:' . $hash;

    if (isset($this->staticCache[$cid])) {
      return $this->staticCache[$cid];
    }

    if ($this->cacheEnabled && ($cache = $this->cacheBackend->get($cid))) {
      $this->staticCache[$cid] = $cache->data;
      return $cache->data;
    }

    $this->staticCache[$cid] = $this->client->searchEvents($searchQuery);

    if ($this->cacheEnabled) {
      $this->cacheBackend->set($cid, $this->staticCache[$cid], strtotime('+2 hours'), ['culturefeed_search_api', 'culturefeed_search_api.search_events']);
    }

    return $this->staticCache[$cid];
  }

  /**
   * {@inheritdoc}
   */
  public function searchEvent(string $eventId, bool $reset = FALSE) {
    $cid = 'culturefeed_search_api.search_event:' . $eventId;

    if (!$reset && isset($this->staticCache[$cid])) {
      return $this->staticCache[$cid];
    }

    if (!$reset && $this->cacheEnabled && ($cache = $this->cacheBackend->get($cid))) {
      $this->staticCache[$cid] = $cache->data;
      return $cache->data;
    }

    $searchQuery = new SearchQuery(TRUE);
    $searchQuery->addParameter(new Id($eventId));
    $searchQuery->addParameter(new AudienceType('*'));

    $events = $this->client->searchEvents($searchQuery);
    $items = $events->getMember()->getItems() ?? [];

    $this->staticCache[$cid] = !empty($items) ? reset($items) : NULL;

    if ($this->cacheEnabled) {
      $this->cacheBackend->set($cid, $this->staticCache[$cid], strtotime('+2 hours'), [
        'culturefeed_search_api',
        'culturefeed_search_api.search_event',
        $cid,
      ]);
    }

    return $this->staticCache[$cid];
  }

  /**
   * {@inheritdoc}
   */
  public function searchPlaces(SearchQueryInterface $searchQuery) {
    return $this->client->searchPlaces($searchQuery);
  }

  /**
   * {@inheritdoc}
   */
  public function searchOffers(SearchQueryInterface $searchQuery) {
    return $this->client->searchOffers($searchQuery);
  }

  /**
   * Get the available regions.
   *
   * @return \stdClass[]
   *   Array of autocomplete results.
   */
  public function getRegions() {
    $jsonLocation = $this->config->get('regions_list') ?? NULL;

    if (empty($jsonLocation)) {
      throw new ConfigException('The culturefeed_search_api regions list location is not configured.');
    }

    $cid = 'culturefeed_search_api.regions_list';

    if (isset($this->staticCache[$cid])) {
      return $this->staticCache[$cid];
    }

    if ($this->cacheEnabled && ($cache = $this->cacheBackend->get($cid))) {
      $this->staticCache[$cid] = $cache->data;
      return $cache->data;
    }

    $data = file_get_contents($jsonLocation);
    $this->staticCache[$cid] = json_decode($data);

    if ($this->cacheEnabled) {
      $this->cacheBackend->set($cid, $this->staticCache[$cid], strtotime('+24 hours'), ['culturefeed_search_api', 'culturefeed_search_api.regions_list']);
    }

    return $this->staticCache[$cid];
  }

}
