<?php

namespace Drupal\nine_mobile_product\Plugin\search_api\processor;

use Drupal\search_api\Datasource\DatasourceInterface;
use Drupal\search_api\Item\ItemInterface;
use Drupal\search_api\Processor\ProcessorPluginBase;
use Drupal\search_api\Processor\ProcessorProperty;

/**
 * Adds the item's URL to the indexed data.
 *
 * @SearchApiProcessor(
 *   id = "add_range_price",
 *   label = @Translation("Range Price Field"),
 *   description = @Translation("Adds the item's range price for index the data"),
 *   stages = {
 *     "add_properties" = 0,
 *   },
 *   locked = true,
 *   hidden = true,
 * )
 */
class AddRangePrice extends ProcessorPluginBase {

  /**
   * {@inheritdoc}
   */
  public function getPropertyDefinitions(DatasourceInterface $datasource = NULL) {
    $properties = [];

    if (!$datasource) {
      $definition = [
        'label' => $this->t('Product Range Price'),
        'description' => $this->t('Range Price of Product'),
        'type' => 'string',
        'processor_id' => $this->getPluginId(),
      ];
      $properties['product_range_price'] = new ProcessorProperty($definition);
      // $properties['product_range_front_camera'] = new ProcessorProperty($definition);
    }

    return $properties;
  }

  /**
   * {@inheritdoc}
   */
  public function addFieldValues(ItemInterface $item) {
    $prices = [];
    $product = $item->getOriginalObject();
    $product_variations = $product->getValue()->getVariations();
    // Add range price.
    $range_price = '';
    foreach ($product_variations as $variation) {
      if (!$variation->isActive()) {
        continue;
      }
      $price = (int) $variation->getPrice()->getNumber();
      if ($price <= 3000000) {
        $range_price = '<=3000000';
      }
      elseif ($price >= 3000000 && $price <= 6000000) {
        $range_price = '3000000-6000000';
      }
      elseif ($price >= 6000000 && $price <= 9000000) {
        $range_price = '6000000-9000000';
      }
      else {
        $range_price = '>=9000000';
      }
      if (!empty($range_price)) {
        $fields = $this->getFieldsHelper()
          ->filterForPropertyPath($item->getFields(), NULL, 'product_range_price');
        foreach ($fields as $field) {
          if (!$field->getDatasourceId()) {
            $field->addValue($range_price);
          }
        }
      }
    }
  }

}
