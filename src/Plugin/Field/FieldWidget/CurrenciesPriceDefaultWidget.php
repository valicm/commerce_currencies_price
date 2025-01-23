<?php

namespace Drupal\commerce_currencies_price\Plugin\Field\FieldWidget;

use Drupal\Core\Entity\EntityTypeManagerInterface;
use Drupal\Core\Field\Attribute\FieldWidget;
use Drupal\Core\Field\FieldItemListInterface;
use Drupal\Core\Field\WidgetBase;
use Drupal\Core\Form\FormStateInterface;
use Drupal\Core\StringTranslation\TranslatableMarkup;
use Symfony\Component\DependencyInjection\ContainerInterface;

/**
 * Plugin implementation of the 'commerce_currencies_price_default' widget.
 */
#[FieldWidget(
  id: "commerce_currencies_price_default",
  label: new TranslatableMarkup("Commerce currencies price"),
  field_types: ["commerce_currencies_price"],
)]
class CurrenciesPriceDefaultWidget extends WidgetBase {

  /**
   * The entity type manager.
   */
  protected EntityTypeManagerInterface $entityTypeManager;

  /**
   * {@inheritdoc}
   */
  public static function create(ContainerInterface $container, array $configuration, $plugin_id, $plugin_definition) {
    $instance = parent::create($container, $configuration, $plugin_id, $plugin_definition);
    $instance->entityTypeManager = $container->get('entity_type.manager');
    return $instance;
  }

  /**
   * {@inheritdoc}
   */
  public static function defaultSettings() {
    return [
      'required_prices' => FALSE,
      'available_currencies' => [],
    ];
  }

  /**
   * {@inheritdoc}
   */
  public function settingsForm(array $form, FormStateInterface $form_state) {

    $elements = [];
    $elements['required_prices'] = [
      '#type' => 'checkbox',
      '#title' => $this->t('Required prices'),
      '#description' => $this->t('Select if you want that is required to enter price for all currencies'),
      '#default_value' => $this->getSetting('required_prices'),
      '#required' => FALSE,
    ];

    $elements['available_currencies'] = [
      '#type' => 'checkboxes',
      '#title' => $this->t('Available prices'),
      '#description' => $this->t('If none select all enabled currencies will be available'),
      '#options' => $this->getEnabledCurrencies(),
      '#default_value' => $this->getSetting('available_currencies'),
      '#required' => FALSE,
    ];

    return $elements;
  }

  /**
   * {@inheritdoc}
   */
  public function settingsSummary() {
    $summary = [];
    $summary[] = $this->t('Require to enter all prices : @required_prices', ['@required_prices' => $this->getSetting('required_prices') ? $this->t('Yes') : $this->t('No')]);
    $summary[] = $this->t('Enabled currencies : @available_currencies', ['@available_currencies' => implode(',', array_keys($this->getAvailableCurrencies()))]);

    return $summary;
  }

  /**
   * {@inheritdoc}
   */
  public function formElement(FieldItemListInterface $items, $delta, array $element, array &$form, FormStateInterface $form_state) {
    /** @var \Drupal\commerce_currencies_price\Plugin\Field\FieldType\CurrenciesPrice $item */
    $item = $items[$delta];

    $default = $item->getEntity()->isNew() ? [] : $item->toArray();

    $element['prices'] = [
      '#type' => 'commerce_currencies_price',
      '#default_value' => $default,
      '#required_prices' => $this->getSetting('required_prices'),
      '#available_currencies' => $this->getAvailableCurrencies(),
    ];

    return $element;
  }

  /**
   * {@inheritdoc}
   */
  public function massageFormValues(array $values, array $form, FormStateInterface $form_state) {
    $new_values = [];
    foreach ($values as $delta => $value) {
      $new_values[$delta]['prices'] = $value['prices'];
    }
    return $new_values;
  }

  /**
   * Get list of enabled currencies.
   */
  protected function getEnabledCurrencies(): array {
    $currencies = $this->entityTypeManager->getStorage('commerce_currency')->loadByProperties(['status' => TRUE]);
    $options = [];
    foreach ($currencies as $currency) {
      $options[$currency->id()] = $currency->id();
    }

    return $options;
  }

  /**
   * Get chosen currencies.
   */
  protected function getAvailableCurrencies(): array {
    $available = $this->getSetting('available_currencies');
    foreach ($available as $key => $currency) {
      if (empty($currency)) {
        unset($available[$key]);
      }
    }
    return !empty($available) ? $available : $this->getSetting('available_currencies');
  }

}
