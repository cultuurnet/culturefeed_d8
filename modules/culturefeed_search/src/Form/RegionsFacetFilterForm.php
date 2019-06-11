<?php

namespace Drupal\culturefeed_search\Form;

use Drupal\Core\Form\FormBase;
use Drupal\Core\Form\FormStateInterface;

/**
 * Provides a regions search form.
 */
class RegionsFacetFilterForm extends FormBase {

  /**
   * {@inheritdoc}
   */
  public function getFormId() {
    return 'culturefeed_search_regions_facet_filter_form';
  }

  /**
   * {@inheritdoc}
   */
  public function buildForm(array $form, FormStateInterface $form_state) {
    $form['region'] = [
      '#type' => 'culturefeed_hidden_value_autocomplete',
      '#autocomplete_route_name' => 'culturefeed_search.regions_autocomplete',
      '#required' => FALSE,
      '#no_validate' => TRUE,
      '#placeholder' => $this->t('Search on municipality', [], ['context' => 'culturefeed_search']),
      '#default_value' => NULL,
    ];

    $form['actions'] = ['#type' => 'actions'];
    $form['actions']['submit'] = [
      '#type' => 'submit',
      '#value' => $this->t('Ok'),
      '#button_type' => 'primary',
    ];

    return $form;
  }

  /**
   * {@inheritdoc}
   */
  public function submitForm(array &$form, FormStateInterface $form_state) {
    $region = $form_state->getValue('region');

    if (!empty($region['value'])) {
      $query = $this->getRequest()->query->all();
      unset($query['regions']);
      $query['regions'][$region['value']] = $region['label'];

      $form_state->setRedirect('<current>', [], ['query' => $query]);
    }
  }

}
