<?php

namespace Drupal\culturefeed_user;

/**
 * The context object for the CultureFeed "UiTID" user context.
 */
class CultureFeedUserContext implements CultureFeedUserContextInterface {

  /**
   * The user access token.
   *
   * @var string
   */
  protected $userAccessToken;

  /**
   * The user access token secret.
   *
   * @var string
   */
  protected $userAccessSecret;

  /**
   * The CultureFeed "UiTID" user Id.
   *
   * @var string
   */
  protected $userId;

  /**
   * The CultureFeed Entry API Web Token.
   *
   * @var string
   */
  protected $userEntryApiWebToken;

  /**
   * {@inheritdoc}
   */
  public function getUserId() {
    return $this->userId;
  }

  /**
   * {@inheritdoc}
   */
  public function setUserId(string $userId) {
    $this->userId = $userId;
  }

  /**
   * {@inheritdoc}
   */
  public function getUserAccessToken() {
    return $this->userAccessToken;
  }

  /**
   * {@inheritdoc}
   */
  public function setUserAccessToken(string $userAccessToken) {
    $this->userAccessToken = $userAccessToken;
  }

  /**
   * {@inheritdoc}
   */
  public function getUserAccessSecret() {
    return $this->userAccessSecret;
  }

  /**
   * {@inheritdoc}
   */
  public function setUserAccessSecret(string $userAccessSecret) {
    $this->userAccessSecret = $userAccessSecret;
  }

  /**
   * {@inheritdoc}
   */
  public function getUserEntryApiWebToken() {
    return $this->userEntryApiWebToken;
  }

  /**
   * {@inheritdoc}
   */
  public function setUserEntryApiWebToken(string $userEntryApiWebToken) {
    $this->userEntryApiWebToken = $userEntryApiWebToken;
  }

}
