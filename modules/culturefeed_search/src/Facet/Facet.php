<?php

namespace Drupal\culturefeed_search\Facet;

/**
 * Provides a catalog search facet.
 */
class Facet {

  /**
   * The facet Id.
   *
   * @var string
   */
  protected $id;

  /**
   * The buckets for this facet.
   *
   * @var FacetBucket[]
   */
  protected $buckets = [];

  /**
   * Facet constructor.
   *
   * @param string $id
   *   The facet Id.
   */
  public function __construct(string $id) {
    $this->id = $id;
  }

  /**
   * Get the Id of the facet.
   *
   * @return string
   *   The facet Id.
   */
  public function getId(): string {
    return $this->id;
  }

  /**
   * Set the Id of the facet.
   *
   * @param string $id
   *   The Id to set.
   *
   * @return Facet
   *   The current facet.
   */
  public function setId(string $id): Facet {
    $this->id = $id;
    return $this;
  }

  /**
   * Add the given bucket to the facet.
   *
   * @param FacetBucket $bucket
   *   The bucket to add.
   */
  public function addBucket(FacetBucket $bucket) {
    $this->buckets[$bucket->getId()] = $bucket;
  }

  /**
   * Return the facet buckets.
   *
   * @return FacetBucket[]
   *   The buckets in the facet.
   */
  public function getBuckets() {
    return $this->buckets;
  }

  /**
   * Set the facet buckets.
   *
   * @param array $buckets
   *   The buckets to set.
   */
  public function setBuckets(array $buckets) {
    $this->buckets = $buckets;
  }

  /**
   * Get the currently active buckets in this facet.
   *
   * @return \Drupal\culturefeed_search\Facet\FacetBucket[]
   *   The active buckets.
   */
  public function getActiveBuckets() {
    $activeBuckets = [];

    foreach ($this->buckets as $bucket) {
      $activeBuckets += $this->getActiveBucketsRecursively($bucket);
    }

    return $activeBuckets;
  }

  /**
   * Recursively get the active buckets of a given Facet bucket.
   *
   * @param \Drupal\culturefeed_search\Facet\FacetBucket $facetBucket
   *   The bucket to check.
   *
   * @return \Drupal\culturefeed_search\Facet\FacetBucket[]
   *   The active buckets.
   */
  private function getActiveBucketsRecursively(FacetBucket $facetBucket) {
    $activeBuckets = [];

    if ($facetBucket->isActive()) {
      $activeBuckets[$facetBucket->getId()] = $facetBucket;
    }

    if ($facetBucket->hasChildren()) {
      foreach ($facetBucket->getChildren() as $child) {
        $activeBuckets += $this->getActiveBucketsRecursively($child);
      }
    }

    return $activeBuckets;
  }

  /**
   * Set the currently active buckets.
   *
   * @param array $activeBuckets
   *   Buckets to set active.
   */
  public function setActiveBuckets(array $activeBuckets) {
    foreach ($this->buckets as $bucket) {
      $this->checkActiveBucket($bucket, $activeBuckets);

      foreach ($bucket->getChildren() as $child) {
        if ($child->isActive() || $child->hasActiveChildren()) {
          $bucket->setHasActiveChildren();
        }
      }
    }
  }

  /**
   * Check if a single bucket is active and set it's active state.
   *
   * @param \Drupal\culturefeed_search\Facet\FacetBucket $bucket
   *   The bucket to check.
   * @param array $activeBuckets
   *   The active buckets to check against.
   */
  private function checkActiveBucket(FacetBucket $bucket, array $activeBuckets) {
    if (isset($activeBuckets[$bucket->getId()])) {
      $bucket->setActive();
      $bucket->setLabel($activeBuckets[$bucket->getId()]);
    }

    // Traverse the child buckets.
    if ($bucket->hasChildren()) {
      foreach ($bucket->getChildren() as $child) {
        $this->checkActiveBucket($child, $activeBuckets);

        if ($child->isActive() || $child->hasActiveChildren()) {
          $bucket->setHasActiveChildren();
        }
      }
    }
  }

}
