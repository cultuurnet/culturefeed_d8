<?php

namespace Drupal\culturefeed_search\Facet;

/**
 * A single facet bucket.
 */
class FacetBucket {

  /**
   * The bucket Id.
   *
   * @var string
   */
  protected $id;

  /**
   * The bucket count.
   *
   * @var int
   */
  protected $count = 0;

  /**
   * The bucket label.
   *
   * @var string
   */
  protected $label;

  /**
   * The bucket status.
   *
   * @var bool
   */
  protected $active = FALSE;

  /**
   * The facet bucket children.
   *
   * @var \Drupal\culturefeed_search\Facet\FacetBucket[]
   */
  protected $children = [];

  /**
   * Indicates if the bucket has active children.
   *
   * @var bool
   */
  protected $hasActiveChildren = FALSE;

  /**
   * Constructs a FacetBucket.
   *
   * @param string $id
   *   Id of the bucket.
   * @param string|null $label
   *   Label to use for this bucket.
   * @param int $count
   *   Total items found.
   */
  public function __construct(string $id, string $label = NULL, int $count = 0) {
    $this->id = $id;
    $this->label = $label;
    $this->count = $count;
  }

  /**
   * Get the bucket Id.
   *
   * @return string
   *   The bucket id.
   */
  public function getId() {
    return $this->id;
  }

  /**
   * Set the bucket Id.
   *
   * @param string $id
   *   The bucket id.
   *
   * @return FacetBucket
   *   The facet bucket.
   */
  public function setId($id): FacetBucket {
    $this->id = $id;
    return $this;
  }

  /**
   * Get the number of items in the bucket.
   *
   * @return int
   *   The facet bucket item count.
   */
  public function getCount(): int {
    return $this->count;
  }

  /**
   * Set the number of items in the bucket.
   *
   * @param int $count
   *   The number of items in the bucket.
   *
   * @return FacetBucket
   *   The facet bucket.
   */
  public function setCount($count): FacetBucket {
    $this->count = $count;
    return $this;
  }

  /**
   * Get the bucket label.
   *
   * @return string
   *   The bucket label.
   */
  public function getLabel() {
    return $this->label;
  }

  /**
   * Set the bucket label.
   *
   * @param string $label
   *   The bucket label.
   *
   * @return FacetBucket
   *   The facet bucket.
   */
  public function setLabel($label): FacetBucket {
    $this->label = $label;
    return $this;
  }

  /**
   * Check if the bucket is active.
   *
   * @return bool
   *   Boolean indicating active state.
   */
  public function isActive(): bool {
    return $this->active;
  }

  /**
   * Set the active state of the bucket.
   *
   * @param bool $active
   *   The state.
   *
   * @return FacetBucket
   *   The facet bucket.
   */
  public function setActive(bool $active = TRUE): FacetBucket {
    $this->active = $active;
    return $this;
  }

  /**
   * Check if the bucket is has active children.
   *
   * @return bool
   *   Boolean indicating active children.
   */
  public function hasActiveChildren(): bool {
    return $this->hasActiveChildren;
  }

  /**
   * Indicate if the bucke has active children.
   *
   * @param bool $active
   *   The state.
   *
   * @return FacetBucket
   *   The facet bucket.
   */
  public function setHasActiveChildren(bool $active = TRUE): FacetBucket {
    $this->hasActiveChildren = $active;
    return $this;
  }

  /**
   * Check if the bucket has children.
   *
   * @return bool
   *   Boolean indicating if the bucket has children.
   */
  public function hasChildren() {
    return !empty($this->children);
  }

  /**
   * Get the children of the bucket.
   *
   * @return \Drupal\culturefeed_search\Facet\FacetBucket[]
   *   The child buckets.
   */
  public function getChildren() {
    return $this->children;
  }

  /**
   * Set the children of the bucket.
   *
   * @param array $children
   *   Buckets to set as child.
   */
  public function setChildren(array $children) {
    $this->children = $children;
  }

  /**
   * Add a child to the bucket.
   *
   * @param \Drupal\culturefeed_search\Facet\FacetBucket $bucket
   *   The bucket to add.
   */
  public function addChild(FacetBucket $bucket) {
    $this->children[] = $bucket;
  }

}
