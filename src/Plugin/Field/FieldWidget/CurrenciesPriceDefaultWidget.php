<?php

namespace Drupal\commerce_currencies_price\Plugin\Field\FieldWidget;

use Drupal\Core\Field\FieldItemListInterface;
use Drupal\Core\Field\WidgetBase;
use Drupal\Core\Form\FormStateInterface;

/**
 * Plugin implementation of the 'address_country_default' widget.
 *
 * @FieldWidget(
 *   id = "commerce_currencies_price_default",
 *   label = @Translation("Commerce currencies price"),
 *   field_types = {
 *     "commerce_currencies_price"
 *   },
 * )
 */
class CurrenciesPriceDefaultWidget extends WidgetBase {
  /**
   * {@inheritdoc}
   */
  public function formElement(FieldItemListInterface $items, $delta, array $element, array &$form, FormStateInterface $form_state) {
    $item = $items[$delta];

    $default = $item->getEntity()->isNew() ? [] : $item->toArray();

    $element['prices'] = [
      '#type' => 'commerce_currencies_price',
      '#default_value' => $default,
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

}
