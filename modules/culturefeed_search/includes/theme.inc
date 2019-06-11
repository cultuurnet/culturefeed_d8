<?php

/**
 * @file
 * Template preprocessors for Culturefeed search templates.
 */

use Drupal\Core\Template\Attribute;
use Drupal\Core\Url;
use Drupal\culturefeed_search\Facet\Facet;

/**
 * Preprocess a Culturefeed search facet.
 *
 * @param array $variables
 *   Array of currently known variables.
 */
function template_preprocess_culturefeed_search_facet(array &$variables) {
  /** @var \Drupal\culturefeed_search\Facet\Facet $facet */
  $facet = $variables['facet'];

  $variables['field'] = $facet->getId();
  $variables['items'] = _culturefeed_search_build_facet_items($facet->getBuckets(), $facet);
}

/**
 * Recursively build the facet item render arrays from the facet tree.
 *
 * @param \Drupal\culturefeed_search\Facet\FacetBucket[] $items
 *   Facet items to build.
 * @param \Drupal\culturefeed_search\Facet\Facet $facet
 *   The facet the items belong to. Usefull for rendering links, etc.
 *
 * @return array
 *   Facet items render array.
 */
function _culturefeed_search_build_facet_items(array $items, Facet $facet) {
  $facetItems = [];

  foreach ($items as $item) {
    // Only 1 active bucket allowed.
    // This should be removed when multiple sibling terms are allowed.
    if ($item->isActive()) {
      $facetItems = [];
    }

    $facetItems[] = [
      'content' => [
        '#theme' => 'culturefeed_search_facet_item',
        '#facet_bucket' => $item,
        '#facet_id' => $facet->getId(),
      ],
      'attributes' => new Attribute(),
      'children' => ($item->isActive() || $item->hasActiveChildren()) ? (!empty($item->getChildren()) ? _culturefeed_search_build_facet_items($item->getChildren(), $facet) : []) : NULL,
    ];

    if ($item->isActive()) {
      break;
    }
  }

  return $facetItems;
}

/**
 * Preprocess a single Culturefeed search facet item.
 *
 * @param array $variables
 *   Array of currently known variables.
 */
function template_preprocess_culturefeed_search_facet_item(array &$variables) {
  /** @var \Drupal\culturefeed_search\Facet\FacetBucket $bucket */
  $bucket = $variables['facet_bucket'];
  $facetId = $variables['facet_id'];

  $variables['count'] = $bucket->getCount();
  $variables['label'] = $bucket->getLabel();

  // Get all query params and add or remove the current facet from them.
  $query = \Drupal::request()->query->all();

  // Remove the page parameter, this can break new searches.
  if (isset($query['page'])) {
    unset($query['page']);
  }

  $params = $query[$facetId] ?? [];

  $variables['remove_url'] = NULL;
  $variables['url'] = NULL;

  $variables['active'] = $bucket->isActive();
  $variables['has_active_children'] = $bucket->hasActiveChildren();
  $variables['active_trail'] = $bucket->isActive() || $bucket->hasActiveChildren();

  if ($bucket->isActive()) {
    // Remove URL.
    unset($query[$facetId]);
    $variables['remove_url'] = Url::fromRoute('<current>', [], ['query' => array_filter($query)]);
  }
  else {
    $query[$facetId][$bucket->getId()] = $bucket->getLabel();

    // Select URL.
    $variables['url'] = Url::fromRoute('<current>', [], ['query' => array_filter($query)]);
  }

}
