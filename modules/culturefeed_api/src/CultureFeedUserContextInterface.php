<?php

namespace Drupal\culturefeed_api;

/**
 * Defines a CultureFeed "UiTID" user context object.
 */
interface CultureFeedUserContextInterface {

  /**
   * Get the user Id.
   *
   * @return string
   *   The user Id.
   */
  public function getUserId();

  /**
   * Set the user Id.
   *
   * @param string $userId
   *   The user Id to set.
   */
  public function setUserId(string $userId);

  /**
   * Get the user access token.
   *
   * @return string
   *   The user access token.
   */
  public function getUserAccessToken();

  /**
   * Set the user access token.
   *
   * @param string $userAccessToken
   *   The access token to set.
   */
  public function setUserAccessToken(string $userAccessToken);

  /**
   * Get the user access secret.
   *
   * @return string
   *   The user access secret.
   */
  public function getUserAccessSecret();

  /**
   * Set the user access secret.
   *
   * @param string $userAccessSecret
   *   The user access secret to set.
   */
  public function setUserAccessSecret(string $userAccessSecret);

  /**
   * Get the Entry API Web Token.
   *
   * @return string
   *   The Entry API Web Token.
   */
  public function getUserEntryApiWebToken();

  /**
   * Set the Entry API Web Token.
   *
   * @param string $token
   *   The Entry API Web Token to set.
   */
  public function setUserEntryApiWebToken(string $token);

}
