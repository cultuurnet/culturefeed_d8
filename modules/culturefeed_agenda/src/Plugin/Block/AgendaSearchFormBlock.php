<?php

namespace Drupal\culturefeed_agenda\Plugin\Block;

use Drupal\Core\Cache\Cache;
use Drupal\culturefeed_agenda\Form\AgendaSearchForm;

/**
 * Provides an 'AgendaSearchFormBlock' block.
 *
 * @Block(
 *  id = "culturefeed_agenda_search_form",
 *  admin_label = @Translation("Agenda search form")
 * )
 */
class AgendaSearchFormBlock extends AgendaSearchPageBlockBase {

  /**
   * {@inheritdoc}
   */
  public function build() {
    return $this->formBuilder->getForm(AgendaSearchForm::class);
  }

  /**
   * {@inheritdoc}
   */
  public function getCacheContexts() {
    return Cache::mergeContexts(parent::getCacheContexts(), ['url']);
  }

}
