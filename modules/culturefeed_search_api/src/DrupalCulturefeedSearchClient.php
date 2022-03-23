<?php

namespace Drupal\culturefeed_search_api;

use CultuurNet\SearchV3\Parameter\AudienceType;
use CultuurNet\SearchV3\Parameter\Id;
use CultuurNet\SearchV3\Parameter\Query;
use CultuurNet\SearchV3\SearchClient;
use CultuurNet\SearchV3\SearchQuery;
use CultuurNet\SearchV3\SearchQueryInterface;
use CultuurNet\SearchV3\Serializer\Serializer;
use CultuurNet\SearchV3\ValueObjects\PagedCollection;
use Drupal\Component\Utility\Crypt;
use Drupal\Core\Cache\CacheBackendInterface;
use Drupal\Core\Config\ConfigException;
use Drupal\Core\Config\ConfigFactory;
use Drupal\Core\Extension\ModuleHandlerInterface;
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

  public const ITEM_ENDPOINTS = [
    'event' => 'searchEvents',
    'organizer' => 'searchOrganizers',
  ];

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
   * The module handler.
   *
   * @var \Drupal\Core\Extension\ModuleHandlerInterface
   */
  protected $moduleHandler;

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
   * @param \Drupal\Core\Extension\ModuleHandlerInterface $moduleHandler
   *   The module handler.
   */
  public function __construct(ConfigFactory $configFactory, LoggerChannelFactoryInterface $loggerChannelFactory, CacheBackendInterface $cacheBackend, LanguageManagerInterface $languageManager, ModuleHandlerInterface $moduleHandler) {
    $this->config = $configFactory->get('culturefeed_search_api.settings');

    $this->languageManager = $languageManager;
    $this->moduleHandler = $moduleHandler;
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
  public function getClient(): ClientInterface {
    return $this->client->getClient();
  }

  /**
   * {@inheritdoc}
   */
  public function searchEvents(SearchQueryInterface $searchQuery): PagedCollection {
    $this->alterQuery($searchQuery, 'events');
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
      $this->cacheBackend->set(
        $cid,
        $this->staticCache[$cid],
        strtotime('+2 hours'),
        ['culturefeed_search_api', 'culturefeed_search_api.search_events']
      );
    }

    return $this->staticCache[$cid];
  }

  /**
   * {@inheritdoc}
   */
  public function searchEvent(string $eventId, bool $reset = FALSE) {
    return $this->searchItem('event', $eventId, $reset);
  }

  /**
   * {@inheritdoc}
   */
  public function searchOrganizer(string $organizerId, bool $reset = FALSE) {
    return $this->searchItem('organizer', $organizerId, $reset);
  }

  /**
   * Search for a single item (eg. organizer, event).
   *
   * @param string $type
   *   The item type.
   * @param string $id
   *   The Id to search for.
   * @param bool $reset
   *   Indicates if the cache should be reset.
   *
   * @return mixed|null
   *   The item or null.
   */
  private function searchItem(string $type, string $id, bool $reset = FALSE) {
    if (!isset(self::ITEM_ENDPOINTS[$type])) {
      throw new \Exception('Invalid search type specified');
    }

    $cid = sprintf('culturefeed_search_api.search_%s:%s', $type, $id);

    if (!$reset && isset($this->staticCache[$cid])) {
      return $this->staticCache[$cid];
    }

    if (!$reset && $this->cacheEnabled && ($cache = $this->cacheBackend->get($cid))) {
      $this->staticCache[$cid] = $cache->data;
      return $cache->data;
    }

    $searchQuery = new SearchQuery(TRUE);

    // @todo: Remove when the organizer endpoint supports the ID parameter.
    if ($type === 'organizer') {
      $searchQuery->addParameter(new Query('id:' . $id));
    } else {
      $searchQuery->addParameter(new AudienceType('*'));
      $searchQuery->addParameter(new Id($id));
    }

    $this->alterQuery($searchQuery, $type);

    $method = self::ITEM_ENDPOINTS[$type];
    $items = $this->client->{$method}($searchQuery);
    $items = $items->getMember()->getItems() ?? [];

    $this->staticCache[$cid] = !empty($items) ? reset($items) : NULL;

    if ($this->cacheEnabled) {
      $this->cacheBackend->set($cid, $this->staticCache[$cid], strtotime('+2 hours'), [
        'culturefeed_search_api',
        'culturefeed_search_api.search_' . $type,
        $cid,
      ]);
    }

    return $this->staticCache[$cid];
  }

  /**
   * {@inheritdoc}
   */
  public function searchPlaces(SearchQueryInterface $searchQuery): PagedCollection {
    $this->alterQuery($searchQuery, 'places');
    return $this->client->searchPlaces($searchQuery);
  }

  /**
   * {@inheritdoc}
   */
  public function searchOffers(SearchQueryInterface $searchQuery): PagedCollection {
    $this->alterQuery($searchQuery, 'offers');
    return $this->client->searchOffers($searchQuery);
  }

  /**
   * {@inheritdoc}
   */
  public function searchOrganizers(SearchQueryInterface $searchQuery): PagedCollection {
    $this->alterQuery($searchQuery, 'organizers');
    return $this->client->searchOrganizers($searchQuery);
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

    $curl = curl_init();
    curl_setopt($curl, CURLOPT_URL, $jsonLocation);
    curl_setopt($curl, CURLOPT_RETURNTRANSFER, 1);
    $data = curl_exec($curl);
    curl_close($curl);

    if (!$data) {
      return [];
    }

    $this->staticCache[$cid] = json_decode($data);

    if ($this->cacheEnabled) {
      $this->cacheBackend->set(
        $cid,
        $this->staticCache[$cid],
        strtotime('+24 hours'),
        ['culturefeed_search_api', 'culturefeed_search_api.regions_list']
      );
    }

    return $this->staticCache[$cid];
  }

  /**
   * Alter a Culturefeed search query before it is executed.
   *
   * @param \CultuurNet\SearchV3\SearchQueryInterface $searchQuery
   *   The search query to alter.
   * @param string $type
   *   The type of query that is executed. Can be one of the following:
   *   - events
   *   - event
   *   - places
   *   - offers.
   */
  protected function alterQuery(SearchQueryInterface $searchQuery, $type = 'events') {
    $this->moduleHandler->alter('culturefeed_search_api_query', $searchQuery, $type);
  }

}
