<?php

namespace Drupal\culturefeed_agenda\ParamConverter;

use Drupal\Core\ParamConverter\ParamConverterInterface;
use Drupal\cultuurkuur_entry\DrupalEntryApiClient;
use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Component\Routing\Route;

/**
 * Parameter converter for upcasting catalog items.
 */
class CulturefeedEventParamConverter implements ParamConverterInterface {

  /**
   * The Drupal Entry API client.
   *
   * @var \Drupal\cultuurkuur_entry\DrupalEntryApiClient
   */
  protected $entryApiClient;

  /**
   * The current request.
   *
   * @var null|\Symfony\Component\HttpFoundation\Request
   */
  protected $request;

  /**
   * CulturefeedEventParamConverter constructor.
   *
   * @param \Drupal\cultuurkuur_entry\DrupalEntryApiClient $entryApiClient
   *   The CultureFeed Entry API client.
   * @param \Symfony\Component\HttpFoundation\RequestStack $requestStack
   *   The request stack.
   */
  public function __construct(DrupalEntryApiClient $entryApiClient, RequestStack $requestStack) {
    $this->entryApiClient = $entryApiClient;
    $this->request = $requestStack->getCurrentRequest();
  }

  /**
   * {@inheritdoc}
   */
  public function convert($value, $definition, $name, array $defaults) {
    // Don't trigger if the value equals the route param placeholder.
    if (strpos($value, '{') !== FALSE) {
      return NULL;
    }

    try {
      return $this->entryApiClient->getEvent($value);
    }
    catch (\Throwable $t) {
      return NULL;
    }
  }

  /**
   * {@inheritdoc}
   */
  public function applies($definition, $name, Route $route) {
    return (!empty($definition['type']) && $definition['type'] === 'culturefeed_event');
  }

}
