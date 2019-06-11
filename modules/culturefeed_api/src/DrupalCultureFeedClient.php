<?php

namespace Drupal\culturefeed_api;

use CultureFeed;
use CultureFeed_DefaultOAuthClient;
use CultureFeed_ICultureFeedDecoratorBase;
use Drupal\Component\Utility\Crypt;
use Drupal\Core\Cache\CacheBackendInterface;
use Drupal\Core\Cache\CacheTagsInvalidatorInterface;
use Drupal\Core\Config\ConfigException;
use Drupal\Core\Config\ConfigFactory;
use Drupal\Core\Language\LanguageManagerInterface;
use Drupal\Core\Logger\LoggerChannelFactoryInterface;
use Drupal\culturefeed_user\CultureFeedUserContextManagerInterface;

/**
 * Provides a Drupal wrapper around the CultureFeed API client.
 */
class DrupalCultureFeedClient extends CultureFeed_ICultureFeedDecoratorBase {

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
   * The cache tags invalidator.
   *
   * @var \Drupal\Core\Cache\CacheTagsInvalidatorInterface
   */
  protected $cacheTagsInvalidator;

  /**
   * The cache tags that were used for caching the last request.
   *
   * @var array
   */
  protected $lastUsedCacheTags;

  /**
   * The CultureFeed user context manager.
   *
   * @var \Drupal\culturefeed_user\CultureFeedUserContextManagerInterface
   */
  protected $cultureFeedUserContextManager;

  /**
   * The CultureFeed API client config.
   *
   * @var \Drupal\Core\Config\Config|\Drupal\Core\Config\ImmutableConfig
   */
  protected $config;

  /**
   * DrupalCultureFeedClient constructor.
   *
   * @param \Drupal\Core\Config\ConfigFactory $configFactory
   *   The config factory.
   * @param \Drupal\Core\Logger\LoggerChannelFactoryInterface $loggerChannelFactory
   *   The logger channel factory.
   * @param \Drupal\Core\Cache\CacheBackendInterface $cacheBackend
   *   The cache backend.
   * @param \Drupal\Core\Language\LanguageManagerInterface $languageManager
   *   The language manager.
   * @param \Drupal\Core\Cache\CacheTagsInvalidatorInterface $cacheTagsInvalidator
   *   The cache tags invalidator.
   * @param \Drupal\culturefeed_user\CultureFeedUserContextManagerInterface $cultureFeedUserContextManager
   *   The user context manager.
   */
  public function __construct(ConfigFactory $configFactory, LoggerChannelFactoryInterface $loggerChannelFactory, CacheBackendInterface $cacheBackend, LanguageManagerInterface $languageManager, CacheTagsInvalidatorInterface $cacheTagsInvalidator, CultureFeedUserContextManagerInterface $cultureFeedUserContextManager) {

    $this->config = $configFactory->get('culturefeed_api.settings');

    if (!$this->config->get('api_location') || !$this->config->get('application_key') || !$this->config->get('shared_secret')) {
      throw new ConfigException('The culturefeed_api module is not configured.');
    }

    $this->languageManager = $languageManager;
    $this->cacheBackend = $cacheBackend;
    $this->cacheEnabled = $this->config->get('enable_cache') === NULL ? TRUE : $this->config->get('enable_cache');
    $this->cultureFeedUserContextManager = $cultureFeedUserContextManager;
    $this->cacheTagsInvalidator = $cacheTagsInvalidator;

    parent::__construct($this->createClient());
  }

  /**
   * Build a new OAuthClient and set its endpoint.
   *
   * @param string|null $token
   *   OAuth token.
   * @param string|null $secret
   *   OAuth secret.
   *
   * @return \CultureFeed
   *   The main CultureFeed API client.
   */
  protected function createClient($token = NULL, $secret = NULL) {
    if (!$token && !$secret && $this->cultureFeedUserContextManager->getUserContext()) {
      $token = $this->cultureFeedUserContextManager->getUserContext()->getUserAccessToken();
      $secret = $this->cultureFeedUserContextManager->getUserContext()->getUserAccessSecret();
    }

    if ($token && $secret) {
      $client = new CultureFeed_DefaultOAuthClient($this->config->get('application_key'), $this->config->get('shared_secret'), $token, $secret);
    }
    else {
      $client = new CultureFeed_DefaultOAuthClient($this->config->get('application_key'), $this->config->get('shared_secret'));
    }
    $client->setEndpoint($this->config->get('api_location'));
    return new \CultureFeed($client);
  }

  /**
   * Update CultureFeed's OAuthClient credentials.
   *
   * @param string|null $token
   *   OAuth token.
   * @param string|null $secret
   *   OAuth secret.
   */
  public function updateClient($token = NULL, $secret = NULL) {
    $this->realCultureFeed = $this->createClient($token, $secret);
  }

  /**
   * Get the cache tags used for caching last request.
   *
   * @return array
   *   The list of cache tags.
   */
  public function getLastUsedCacheTags() {
    return $this->lastUsedCacheTags;
  }

  /**
   * Retrieve the authorization URL.
   *
   * @param string $token
   *   Authorization token.
   * @param string $callback
   *   Callback URL.
   * @param string $type
   *   Type.
   * @param bool $skip_confirmation
   *   Skip confirmation.
   * @param bool $skip_authorization
   *   Skip authorization.
   * @param string $via
   *   Via.
   * @param string $language
   *   Language.
   * @param string $consumerKey
   *   The consumer key.
   *
   * @return mixed
   *   The authorize URL.
   */
  public function getUrlAuthorize(
    $token,
    $callback = '',
    $type = CultureFeed::AUTHORIZE_TYPE_REGULAR,
    $skip_confirmation = FALSE,
    $skip_authorization = FALSE,
    $via = '',
    $language = '',
    $consumerKey = ''
  ) {
    return $this->realCultureFeed->getUrlAuthorize(
      $token,
      $callback,
      $type,
      $skip_confirmation,
      $skip_authorization,
      $via,
      $language,
      $consumerKey
    );
  }

  /**
   * Retrieve an access token.
   *
   * @param string $oauth_verifier
   *   OAuth verifier.
   *
   * @return mixed
   *   The retrieved access token.
   */
  public function getAccessToken($oauth_verifier) {
    return $this->realCultureFeed->getAccessToken($oauth_verifier);
  }

  /**
   * Retrieve a UiTID user by ID.
   *
   * @param string $id
   *   CultureFeed UiTID user ID.
   * @param bool $private
   *   Whether the user is private.
   * @param bool $use_auth
   *   Whether to use authentication.
   * @param bool $reset
   *   Whether to reset the users cache.
   *
   * @return \CultureFeed_User
   *   Retrieved or cached user object.
   */
  public function getUser($id, $private = FALSE, $use_auth = TRUE, bool $reset = FALSE) {
    return $this->realCultureFeed->getUser(
      $id,
      $private,
      $use_auth
    );
  }

  /**
   * Retrieve a Culturefeed page.
   *
   * @param string $id
   *   The Id of the page to retrieve.
   * @param bool $reset
   *   Indicates if the cache should be reset.
   *
   * @return \CultureFeed_Cdb_Item_Page|null
   *   The loaded page or null.
   */
  public function getPage(string $id, bool $reset = FALSE) {
    $hash = Crypt::hashBase64($id);
    $cid = 'culturefeed_api.page:' . $hash;

    if (!$reset && isset($this->staticCache[$cid])) {
      return $this->staticCache[$cid];
    }

    if (!$reset && $this->cacheEnabled && ($cache = $this->cacheBackend->get($cid))) {
      $this->staticCache[$cid] = $cache->data;
      return $cache->data;
    }

    $this->staticCache[$cid] = $this->realCultureFeed->pages()->getPage($id);

    if ($this->cacheEnabled) {
      $this->cacheBackend->set($cid, $this->staticCache[$cid], strtotime('+2 hours'), ['culturefeed_api']);
    }

    return $this->staticCache[$cid];
  }

  /**
   * {@inheritdoc}
   */
  public function pages() {
    return $this->realCultureFeed->pages();
  }

}
