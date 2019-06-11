<?php

namespace Drupal\culturefeed_agenda\Form;

use Drupal\Core\Form\ConfigFormBase;
use Drupal\Core\Form\FormStateInterface;

/**
 * Provides a settings form for the Culturefeed agenda settings.
 */
class AgendaSettingsForm extends ConfigFormBase {

  /**
   * {@inheritdoc}
   */
  public function getFormId() {
    return 'culturefeed_agenda_settings_form';
  }

  /**
   * {@inheritdoc}
   */
  protected function getEditableConfigNames() {
    return ['culturefeed_agenda.settings'];
  }

  /**
   * {@inheritdoc}
   */
  public function buildForm(array $form, FormStateInterface $form_state) {
    $config = $this->config('culturefeed_agenda.settings');

    $form['culturefeed_search_api']['google_maps_api_key'] = [
      '#type' => 'textfield',
      '#title' => $this->t('Google maps API key'),
      '#required' => TRUE,
      '#default_value' => $config->get('google_maps_api_key') ?: '',
    ];

    return parent::buildForm($form, $form_state);
  }

  /**
   * {@inheritdoc}
   */
  public function submitForm(array &$form, FormStateInterface $form_state) {
    $config = $this->config('culturefeed_agenda.settings');
    $config->set('google_maps_api_key', $form_state->getValue('google_maps_api_key'));
    $config->save();

    parent::submitForm($form, $form_state);
  }

}
