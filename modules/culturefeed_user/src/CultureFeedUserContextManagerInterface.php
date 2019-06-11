<?php

namespace Drupal\culturefeed_user;

/**
 * Provides a context class for CultureFeed "UiTID" users.
 */
interface CultureFeedUserContextManagerInterface {

  /**
   * Initialize the user context.
   */
  public function init();

  /**
   * Get the user context object from the session.
   */
  public function getUserContext(): CultureFeedUserContextInterface;

  /**
   * Save changes in the user context to the temp store.
   */
  public function persist();

  /**
   * Set the CultureFeed "UiTID" user Id.
   *
   * @param string $id
   *   The user Id to set.
   */
  public function setUserId(string $id);

  /**
   * Get the CultureFeed "UiTID" user Id of the currently active user.
   *
   * @return string|null
   *   The user Id or null.
   */
  public function getUserId();

  /**
   * Get the user access token.
   *
   * @return string|null
   *   The user access token.
   */
  public function getUserAccessToken();

  /**
   * Set the CultureFeed "UiTID" user access token.
   *
   * @param string $token
   *   The user access tokenId to set.
   */
  public function setUserAccessToken(string $token);

  /**
   * Get the user access secret.
   *
   * @return string|null
   *   The user access secret.
   */
  public function getUserAccessSecret();

  /**
   * Set the CultureFeed "UiTID" user access secret.
   *
   * @param string $secret
   *   The user access secret to set.
   */
  public function setUserAccessSecret(string $secret);

  /**
   * Get the Entry API Web Token.
   *
   * @return string|null
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
