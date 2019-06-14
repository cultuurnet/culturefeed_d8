<?php

namespace Drupal\culturefeed_api;

use Drupal\Core\Session\AccountInterface;
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
   * The user context.
   *
   * @var \Drupal\culturefeed_api\CultureFeedUserContextInterface
   */
  protected $userContext;

  /**
   * The php session.
   *
   * @var \Symfony\Component\HttpFoundation\Session\SessionInterface
   */
  protected $session;

  /**
   * Is the session initialized.
   *
   * @var bool
   */
  protected $initialized = FALSE;

  /**
   * CultureFeedUserContextManager constructor.
   *
   * @param \Drupal\Core\Session\AccountInterface $account
   *   The current user.
   * @param \Symfony\Component\HttpFoundation\Session\SessionInterface $session
   *   The active session (if any).
   */
  public function __construct(AccountInterface $account, SessionInterface $session) {
    $this->currentUser = $account;
    $this->userContext = new CultureFeedUserContext();
    $this->session = $session;

    // Initialize the CultureFeed "UiTID" user context.
    $this->init();
  }

  /**
   * {@inheritdoc}
   */
  public function init() {
    if (!$this->initialized) {
      $this->initialized = TRUE;

      // Only try to get the user context if a session has been started.
      if ($this->session->isStarted() && $this->currentUser->isAuthenticated()) {
        if ($context = $this->session->get(self::SESSION_KEY)) {
          $this->userContext = $context;
        }
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
    if ($this->currentUser->isAuthenticated()) {
      $this->session->set(self::SESSION_KEY, $this->userContext);
    }
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
