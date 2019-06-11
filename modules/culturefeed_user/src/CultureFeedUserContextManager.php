<?php

namespace Drupal\culturefeed_user;

use Drupal\Core\Session\AccountInterface;
use Drupal\Core\TempStore\PrivateTempStoreFactory;
use Symfony\Component\HttpFoundation\Session\SessionInterface;

/**
 * Provides a context service for CultureFeed "UiTID" users.
 */
class CultureFeedUserContextManager implements CultureFeedUserContextManagerInterface {

  const SESSION_KEY = 'culturefeed_uitid_user_context';
  const CULTUREFFEED_UITID_EXTERNAL_AUTH_PROVIDER = 'culturefeed_uitid';

  /**
   * The current user.
   *
   * @var \Drupal\Core\Session\AccountInterface
   */
  protected $currentUser;

  /**
   * The temp store.
   *
   * @var \Drupal\Core\TempStore\PrivateTempStore
   */
  protected $privateTempStore;

  /**
   * The user context.
   *
   * @var \Drupal\culturefeed_user\CultureFeedUserContextInterface
   */
  protected $userContext;

  /**
   * The php session.
   *
   * @var \Symfony\Component\HttpFoundation\Session\SessionInterface
   */
  protected $session;

  /**
   * CultureFeedUserContextManager constructor.
   *
   * @param \Drupal\Core\Session\AccountInterface $account
   *   The current user.
   * @param \Drupal\Core\TempStore\PrivateTempStoreFactory $privateTempStoreFactory
   *   The temp store.
   * @param \Symfony\Component\HttpFoundation\Session\SessionInterface $session
   *   The active session (if any).
   */
  public function __construct(AccountInterface $account, PrivateTempStoreFactory $privateTempStoreFactory, SessionInterface $session) {
    $this->currentUser = $account;
    $this->privateTempStore = $privateTempStoreFactory->get(self::SESSION_KEY);
    $this->userContext = new CultureFeedUserContext();
    $this->session = $session;

    // Initialize the CultureFeed "UiTID" user context.
    $this->init();
  }

  /**
   * {@inheritdoc}
   */
  public function init() {
    // Only try to get the user context from the private temp store if a session has been started
    // If no session was started, the empty CultureFeedUserContext has already been set as fallback.
    if ($this->session->isStarted()) {
      if ($context = $this->privateTempStore->get(self::SESSION_KEY)) {
        $this->userContext = $context;
      }
    }
  }

  /**
   * {@inheritdoc}
   */
  public function getUserContext(): CultureFeedUserContextInterface {
    $this->init();
    return $this->userContext;
  }

  /**
   * {@inheritdoc}
   */
  public function persist() {
    $this->privateTempStore->set(self::SESSION_KEY, $this->userContext);
  }

  /**
   * {@inheritdoc}
   */
  public function getUserId() {
    $this->init();
    return $this->userContext->getUserId();
  }

  /**
   * {@inheritdoc}
   */
  public function setUserId(string $id) {
    $this->userContext->setUserId($id);
    $this->persist();
  }

  /**
   * {@inheritdoc}
   */
  public function getUserAccessToken() {
    $this->init();
    return $this->userContext->getUserAccessToken();
  }

  /**
   * {@inheritdoc}
   */
  public function setUserAccessToken(string $token) {
    $this->userContext->setUserAccessToken($token);
    $this->persist();
  }

  /**
   * {@inheritdoc}
   */
  public function getUserAccessSecret() {
    $this->init();
    return $this->userContext->getUserAccessSecret();
  }

  /**
   * {@inheritdoc}
   */
  public function setUserAccessSecret(string $secret) {
    $this->userContext->setUserAccessSecret($secret);
    $this->persist();
  }

  /**
   * {@inheritdoc}
   */
  public function getUserEntryApiWebToken() {
    $this->init();
    return $this->userContext->getUserEntryApiWebToken();
  }

  /**
   * {@inheritdoc}
   */
  public function setUserEntryApiWebToken(string $token) {
    $this->userContext->setUserEntryApiWebToken($token);
    $this->persist();
  }

}
