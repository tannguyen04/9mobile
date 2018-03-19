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
 *   id = "add_range_size_creen",
 *   label = @Translation("Range size screen"),
 *   description = @Translation("Adds the item's range size screen for index the data"),
 *   stages = {
 *     "add_properties" = 0,
 *   },
 *   locked = true,
 *   hidden = true,
 * )
 */
class AddRangeSizeScreen extends ProcessorPluginBase {

  /**
   * {@inheritdoc}
   */
  public function getPropertyDefinitions(DatasourceInterface $datasource = NULL) {
    $properties = [];

    if (!$datasource) {
      $definition = [
        'label' => $this->t('Product Range size screenn'),
        'description' => $this->t('Range size screen of Product'),
        'type' => 'string',
        'processor_id' => $this->getPluginId(),
      ];
      $properties['product_range_size_creen'] = new ProcessorProperty($definition);
    }

    return $properties;
  }

  /**
   * {@inheritdoc}
   */
  public function addFieldValues(ItemInterface $item) {
    $origin_object = $item->getOriginalObject();
    $product = $origin_object->getValue();
    // Add value for product_range_capacity_power.
    if ($product->hasField('field_size_screen') && !$product->get('field_size_screen')->isEmpty()) {
      $size_screen = $product->get('field_size_screen')->first()->getValue();
      $size_screen = floatval($size_screen['value']);
      if ($size_screen <= 4) {
        $range_size_screen = '<=4';
      }
      elseif ($size_screen >= 4 && $size_screen <= 6) {
        $range_size_screen = '4-6';
      }
      else {
        $range_size_screen = '>=6';
      }
      if (isset($range_size_screen)) {
        $fields = $this->getFieldsHelper()
          ->filterForPropertyPath($item->getFields(), NULL, 'product_range_size_creen');
        foreach ($fields as $field) {
          if (!$field->getDatasourceId()) {
            $field->addValue($range_size_screen);
          }
        }
      }
    }
  }

}
