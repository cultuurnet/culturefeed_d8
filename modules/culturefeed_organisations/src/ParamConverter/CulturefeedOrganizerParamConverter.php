<?php

namespace Drupal\culturefeed_organisations\ParamConverter;

use Drupal\Core\ParamConverter\ParamConverterInterface;
use Drupal\culturefeed_search_api\DrupalCulturefeedSearchClientInterface;
use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Component\Routing\Route;

/**
 * Parameter converter for upcasting organizer items.
 */
class CulturefeedOrganizerParamConverter implements ParamConverterInterface {

  /**
   * The Culturefeed search client.
   *
   * @var \Drupal\culturefeed_search_api\DrupalCulturefeedSearchClientInterface
   */
  protected $searchClient;

  /**
   * The current request.
   *
   * @var null|\Symfony\Component\HttpFoundation\Request
   */
  protected $request;

  /**
   * CulturefeedEventParamConverter constructor.
   *
   * @param \Drupal\culturefeed_search_api\DrupalCulturefeedSearchClientInterface $searchClient
   *   The Culturefeed search client.
   * @param \Symfony\Component\HttpFoundation\RequestStack $requestStack
   *   The request stack.
   */
  public function __construct(DrupalCulturefeedSearchClientInterface $searchClient, RequestStack $requestStack) {
    $this->searchClient = $searchClient;
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
      return $this->searchClient->searchOrganizer($value, $this->request->query->has('reset'));
    }
    catch (\Throwable $t) {
      return NULL;
    }
  }

  /**
   * {@inheritdoc}
   */
  public function applies($definition, $name, Route $route) {
    return (!empty($definition['type']) && $definition['type'] === 'culturefeed_organizer');
  }

}
