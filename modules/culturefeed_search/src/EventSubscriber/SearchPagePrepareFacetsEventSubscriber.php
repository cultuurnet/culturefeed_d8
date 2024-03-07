<?php

namespace Drupal\culturefeed_search\EventSubscriber;

use Drupal\Core\KeyValueStore\KeyValueExpirableFactoryInterface;
use Drupal\culturefeed_search\Event\SearchPagePrepareFacetsEvent;
use Drupal\culturefeed_search\Facet\Facet;
use Drupal\culturefeed_search\Facet\FacetBucket;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\HttpFoundation\RequestStack;

/**
 * Provides an event subscriber that responds to the facets being prepared.
 */
class SearchPagePrepareFacetsEventSubscriber implements EventSubscriberInterface {

  /**
   * The current request.
   *
   * @var \Symfony\Component\HttpFoundation\Request
   */
  protected $currentRequest;

  /**
   * The key value store.
   *
   * @var \Drupal\Core\KeyValueStore\KeyValueStoreExpirableInterface
   */
  protected $keyValueStore;

  /**
   * CatalogSearchPagePrepareFacetsEventSubscriber constructor.
   *
   * @param \Symfony\Component\HttpFoundation\RequestStack $requestStack
   *   The current request stack.
   * @param \Drupal\Core\KeyValueStore\KeyValueExpirableFactoryInterface $keyValueExpirableFactory
   *   The 'key value' expirable factory.
   */
  public function __construct(RequestStack $requestStack, KeyValueExpirableFactoryInterface $keyValueExpirableFactory) {
    $this->currentRequest = $requestStack->getCurrentRequest();
    $this->keyValueStore = $keyValueExpirableFactory->get('search_page.facets');
  }

  /**
   * React to the "prepare facets" event.
   *
   * @param \Drupal\culturefeed_search\Event\SearchPagePrepareFacetsEvent $event
   *   The event containing the facets being prepared.
   */
  public function onPrepare(SearchPagePrepareFacetsEvent $event) {

    // The original facets.
    $facets = $event->getFacets();

    // Add facets that are in the query string, but not in the prepared facets.
    // This can happen if a user used a custom filter,
    // combined with a real facet.
    $supportedFacets = [
      'regions',
      'types',
      'facilities',
      'themes',
    ];

    foreach ($supportedFacets as $facetId) {

      if ($this->currentRequest->query->has($facetId)) {

        // Use the already prepared facet if possible.
        // If not, create a new one here.
        $facet = $facets[$facetId] ?? new Facet($facetId);
        $value = $this->currentRequest->query->all()[$facetId];
        if (is_array($value)) {
          $activeValue = $value;
          $id = key($value);
          $label = current($value);
        }
        else {
          $id = $label = $value;
          $activeValue = [
            $id => $label,
          ];
        }
        $facet->addBucket(new FacetBucket($id, $label));

        // Sort the buckets by their original position in the key-value store.
        $facetBuckets = $facet->getBuckets();

        // Set the buckets and set the correct active states.
        $facet->setBuckets($facetBuckets);
        $facet->setActiveBuckets($activeValue);

        $facets[$facet->getId()] = $facet;
      }
    }

    // Set the facets back on the dispatched event.
    $event->setFacets($facets);
  }

  /**
   * {@inheritdoc}
   */
  public static function getSubscribedEvents() {
    $events[SearchPagePrepareFacetsEvent::PREPARE][] = ['onPrepare', 1000];
    return $events;
  }

}
