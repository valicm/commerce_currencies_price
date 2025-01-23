<?php

namespace Drupal\commerce_currencies_price\Plugin\Field\FieldFormatter;

use Drupal\Core\Field\Attribute\FieldFormatter;
use Drupal\Core\Field\FieldItemListInterface;
use Drupal\Core\Field\FormatterBase;
use Drupal\Core\StringTranslation\TranslatableMarkup;

/**
 * Plugin implementation of the 'commerce_currencies_price_formatter' formatter.
 */
#[FieldFormatter(
  id: "commerce_currencies_price_formatter",
  label: new TranslatableMarkup("Commerce price currencies"),
  field_types: ["commerce_currencies_price"],
)]
class CurrenciesPriceFormatter extends FormatterBase {

  /**
   * {@inheritdoc}
   */
  public function viewElements(FieldItemListInterface $items, $langcode) {
    // Does not actually output anything for now.
    return [];
  }

}
