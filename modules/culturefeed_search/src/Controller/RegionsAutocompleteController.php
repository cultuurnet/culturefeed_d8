<?php

namespace Drupal\culturefeed_search\Controller;

use Drupal\Core\Cache\CacheableJsonResponse;
use Drupal\Core\Cache\CacheableMetadata;
use Drupal\Core\Controller\ControllerBase;
use Drupal\Core\Utility\Error;
use Drupal\culturefeed_search_api\DrupalCulturefeedSearchClientInterface;
use Psr\Log\LoggerInterface;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Symfony\Component\HttpFoundation\Request;

/**
 * Provides an autocomplete controller for Culturefeed regions.
 */
class RegionsAutocompleteController extends ControllerBase {

  /**
   * Constructs a RegionsAutocompleteController controller.
   *
   * @param \Drupal\culturefeed_search_api\DrupalCulturefeedSearchClientInterface $searchClient
   *   The Culturfeed search client.
   */
  public function __construct(
    protected readonly DrupalCulturefeedSearchClientInterface $searchClient,
    protected readonly LoggerInterface $logger
  ) {
  }

  /**
   * {@inheritdoc}
   */
  public static function create(ContainerInterface $container) {
    return new static(
      $container->get('culturefeed_search_api.client'),
      $container->get('logger.factory')->get('culturefeed_search_api'),
    );
  }

  /**
   * Provides Google Places suggestions for a given input string.
   *
   * @param \Symfony\Component\HttpFoundation\Request $request
   *   The request object.
   * @param string $type
   *   The type of autocomplete.
   *
   * @return \Symfony\Component\HttpFoundation\JsonResponse
   *   The autocomplete response.
   */
  public function handleAutocomplete(Request $request, string $type) {
    $suggestions = [];

    // Cache by query argument, for 1 day.
    $cache = [
      '#cache' => [
        'contexts' => [
          'url',
        ],
        'max-age' => 86400,
      ],
    ];

    if ($input = $request->query->get('q')) {
      try {
        $searchString = strtolower((string) $input);
        $regions = $this->searchClient->getRegions();

        if (!empty($regions)) {
          foreach ($regions as $region) {
            if (strpos(strtolower($region->name), $searchString) !== FALSE) {
              $suggestions[] = [
                'label' => $region->name,
                'value' => $type === 'label' ? $region->name : $region->key,
              ];
            }
          }
        }
      }
      catch (\Exception $e) {
        Error::logException($this->logger, $e);
        $cache['#cache']['max-age'] = 0;
      }
    }

    $suggestions = array_slice($suggestions, 0, 20);

    // Build the response.
    $response = new CacheableJsonResponse($suggestions);

    // Add cacheable dependencies.
    $response->addCacheableDependency(CacheableMetadata::createFromRenderArray($cache));

    return $response;
  }

}
