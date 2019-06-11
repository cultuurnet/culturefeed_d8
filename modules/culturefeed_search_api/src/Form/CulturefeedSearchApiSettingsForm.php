<?php

namespace Drupal\culturefeed_search_api\Form;

use Drupal\Core\Cache\Cache;
use Drupal\Core\Form\ConfigFormBase;
use Drupal\Core\Form\FormStateInterface;

/**
 * Provides a settings form for the search api settings.
 */
class CulturefeedSearchApiSettingsForm extends ConfigFormBase {

  /**
   * {@inheritdoc}
   */
  public function getFormId() {
    return 'culturefeed_search_api_settings_form';
  }

  /**
   * {@inheritdoc}
   */
  protected function getEditableConfigNames() {
    return ['culturefeed_search_api.settings'];
  }

  /**
   * {@inheritdoc}
   */
  public function buildForm(array $form, FormStateInterface $form_state) {
    $config = $this->config('culturefeed_search_api.settings');

    $form['culturefeed_search_api']['endpoint_url'] = [
      '#type' => 'url',
      '#title' => $this->t('Endpoint URL'),
      '#required' => TRUE,
      '#default_value' => $config->get('endpoint_url') ?: '',
    ];

    $form['culturefeed_search_api']['api_key'] = [
      '#type' => 'textfield',
      '#title' => $this->t('API key'),
      '#required' => TRUE,
      '#default_value' => $config->get('api_key') ?: '',
    ];

    $form['culturefeed_search_api']['regions_list'] = [
      '#type' => 'textfield',
      '#title' => $this->t('Regions list JSON location'),
      '#required' => TRUE,
      '#default_value' => $config->get('regions_list') ?: '',
    ];

    $form['culturefeed_search_api']['enable_cache'] = [
      '#type' => 'checkbox',
      '#title' => $this->t('Enable cache'),
      '#default_value' => $config->get('enable_cache') === NULL ? TRUE : $config->get('enable_cache'),
    ];

    $form['culturefeed_search_api']['debug'] = [
      '#type' => 'checkbox',
      '#title' => $this->t('Enable debug'),
      '#default_value' => $config->get('debug') === NULL ? FALSE : $config->get('debug'),
      '#description' => $this->t('When enabling debug mode. All API calls will be logged.'),
    ];

    return parent::buildForm($form, $form_state);
  }

  /**
   * {@inheritdoc}
   */
  public function submitForm(array &$form, FormStateInterface $form_state) {
    $config = $this->config('culturefeed_search_api.settings');
    $config->set('endpoint_url', $form_state->getValue('endpoint_url'));
    $config->set('api_key', $form_state->getValue('api_key'));
    $config->set('enable_cache', $form_state->getValue('enable_cache'));
    $config->set('debug', $form_state->getValue('debug'));
    $config->set('regions_list', $form_state->getValue('regions_list'));
    $config->save();

    Cache::invalidateTags(['culturefeed_search_api']);

    parent::submitForm($form, $form_state);
  }

}
