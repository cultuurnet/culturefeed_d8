<?php

namespace Drupal\culturefeed_search\Plugin\Derivative;

use Drupal\Component\Plugin\Derivative\DeriverBase;
use Drupal\Core\StringTranslation\StringTranslationTrait;

/**
 * Provides block plugin definitions for Culturefeed search facet blocks.
 *
 * @see \Drupal\culturefeed_search\Plugin\Block\FacetBlock
 */
class FacetBlock extends DeriverBase {

  use StringTranslationTrait;

  /**
   * {@inheritdoc}
   */
  public function getDerivativeDefinitions($base_plugin_definition) {
    $facets = [
      'types' => $this->t('What', [], ['context' => 'culturefeed_search']),
      'facilities' => $this->t('Facilities', [], ['context' => 'culturefeed_search']),
      'themes' => $this->t('Themes', [], ['context' => 'culturefeed_search']),
    ];

    foreach ($facets as $key => $label) {
      $this->derivatives[$key] = $base_plugin_definition;
      $this->derivatives[$key]['admin_label'] = $this->t('Facet block: @label', ['@label' => $label], ['context' => 'culturefeed_search']);
    }

    return $this->derivatives;
  }

}
