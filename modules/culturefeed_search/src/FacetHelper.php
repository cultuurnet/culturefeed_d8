<?php

namespace Drupal\culturefeed_search;

use CultuurNet\SearchV3\ValueObjects\FacetResultItem;
use CultuurNet\SearchV3\ValueObjects\FacetResults;
use Drupal\culturefeed_search\Facet\Facet;
use Drupal\culturefeed_search\Facet\FacetBucket;

/**
 * Provides a facet helper service.
 */
class FacetHelper {

  const FACET_SORT_ALPHABETICALLY = 1;
  const FACET_SORT_TOTAL_RESULTS = 2;

  /**
   * Build generic facets from the Culturefeed facet results.
   *
   * @param \CultuurNet\SearchV3\ValueObjects\FacetResults $facetResults
   *   The facet results to parse.
   *
   * @return \Drupal\culturefeed_search\Facet\Facet[]
   *   The parsed facets.
   */
  public function buildFacetsFromResult(FacetResults $facetResults) {
    $facets = [];

    /** @var \CultuurNet\SearchV3\ValueObjects\FacetResult $facetResult */
    foreach ($facetResults->getFacetResults() as $facetResult) {
      $facet = new Facet($facetResult->getField());

      // Parse the facet result and children.
      /** @var \CultuurNet\SearchV3\ValueObjects\FacetResultItem $facetResultItem */
      foreach ($facetResult->getResults() as $facetResultItem) {
        $bucket = $this->buildFacetBucket($facetResultItem);
        $facet->addBucket($bucket);
      }

      $facets[$facet->getId()] = $facet;
    }

    return $facets;
  }

  /**
   * Recursively parse a facet result item to a facet bucket.
   *
   * @param \CultuurNet\SearchV3\ValueObjects\FacetResultItem $facetResultItem
   *   The facet result item to buidl.
   *
   * @return \Drupal\culturefeed_search\Facet\FacetBucket
   *   The built facet bucket.
   */
  protected function buildFacetBucket(FacetResultItem $facetResultItem): FacetBucket {
    $bucket = new FacetBucket($facetResultItem->getValue(), $facetResultItem->getName()->getValueForLanguage(\Drupal::languageManager()->getCurrentLanguage()->getId()), $facetResultItem->getCount());

    if (!empty($facetResultItem->getChildren())) {
      foreach ($facetResultItem->getChildren() as $resultItemChild) {
        $childBucket = $this->buildFacetBucket($resultItemChild);
        $bucket->addChild($childBucket);
      }
    }

    return $bucket;
  }

  /**
   * Limit a facet result to a certain start and end depth.
   *
   * @param \Drupal\culturefeed_search\Facet\Facet $facet
   *   The facet to manipulate.
   * @param int $minDepth
   *   The minimum (starting) depth of the facet.
   * @param int|null $maxDepth
   *   The maximum depth of the facet (relative to the min depth).
   *
   * @return \Drupal\culturefeed_search\Facet\Facet
   *   The limited facet.
   */
  public function limitFacet(Facet $facet, int $minDepth = 1, int $maxDepth = NULL) {
    $buckets = $facet->getBuckets();

    if ($minDepth !== 1) {
      // Limit the facet tree to the minimum depth.
      $buckets = $this->getFacetItemsAtLevel($buckets, $minDepth);
    }

    if (!empty($maxDepth)) {
      // Limit the facet tree to the maximum depth.
      $buckets = $this->limitFacetDepth($buckets, $maxDepth);
    }

    $facet->setBuckets($buckets);

    return $facet;
  }

  /**
   * Limit a tree of facet buckets to a given starting level.
   *
   * @param array $items
   *   The result items tree to limit.
   * @param int $level
   *   The starting level.
   * @param array $newItems
   *   Array of new items.
   * @param int $currentLevel
   *   The level currently being traversed.
   *
   * @return array
   *   Array of facet bucket items, starting at the requested level.
   */
  protected function getFacetItemsAtLevel(array $items, int $level = 1, array &$newItems = [], int $currentLevel = 1) {
    if ($currentLevel == $level) {
      $newItems = array_merge($newItems, $items);
    }
    else {
      /** @var \Drupal\culturefeed_search\Facet\FacetBucket $item */
      foreach ($items as $item) {
        if ($item->hasChildren()) {
          $this->getFacetItemsAtLevel($item->getChildren(), $level, $newItems, ($currentLevel + 1));
        }
      }
    }

    return $newItems;
  }

  /**
   * Limit a tree of facet bucket items to a given starting level.
   *
   * @param array $items
   *   The result items tree to limit.
   * @param int $maxDepth
   *   The maximum allowed depth of the tree.
   * @param int $currentLevel
   *   The level currently being traversed.
   *
   * @return array
   *   Array of facet bucket items, starting at the requested level.
   */
  protected function limitFacetDepth(array &$items, int $maxDepth, int $currentLevel = 1) {
    if ($maxDepth < $currentLevel) {
      return [];
    }

    /** @var \Drupal\culturefeed_search\Facet\FacetBucket $item */
    foreach ($items as &$item) {
      if ($item->hasChildren()) {
        $children = $item->getChildren();
        $item->setChildren($this->limitFacetDepth($children, $maxDepth, ($currentLevel + 1)));
      }
    }

    return $items;
  }

}
