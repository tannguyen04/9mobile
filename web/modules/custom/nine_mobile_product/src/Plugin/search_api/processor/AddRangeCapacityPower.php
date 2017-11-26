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
 *   id = "add_range_capacity_power",
 *   label = @Translation("Range Capacity Power"),
 *   description = @Translation("Adds the item's range capacity power for index the data"),
 *   stages = {
 *     "add_properties" = 0,
 *   },
 *   locked = true,
 *   hidden = true,
 * )
 */
class AddRangeCapacityPower extends ProcessorPluginBase {

  /**
   * {@inheritdoc}
   */
  public function getPropertyDefinitions(DatasourceInterface $datasource = NULL) {
    $properties = [];

    if (!$datasource) {
      $definition = [
        'label' => $this->t('Product Range capacity power'),
        'description' => $this->t('Range capacity power of Product'),
        'type' => 'string',
        'processor_id' => $this->getPluginId(),
      ];
      $properties['product_range_capacity_power'] = new ProcessorProperty($definition);
    }

    return $properties;
  }

  /**
   * {@inheritdoc}
   */
  public function addFieldValues(ItemInterface $item) {
    $origin_object = $item->getOriginalObject();
    $product = $origin_object->getValue();
    //Add value for product_range_capacity_power
    if ($product->hasField('field_capacity_power') && !$product->get('field_capacity_power')->isEmpty()) {
      $capacity_power = $product->get('field_capacity_power')->first()->getValue();
      $capacity_power = (int) $capacity_power['value'];
      if ($capacity_power <= 3000) {
        $range_capacity_power = '<=3000';
      }elseif ($capacity_power >= 3000 && $capacity_power <= 5000) {
        $range_capacity_power = '3000-5000';
      }else {
        $range_capacity_power = '>=5000';
      }
      if (isset($range_capacity_power)) {
        $fields = $this->getFieldsHelper()
          ->filterForPropertyPath($item->getFields(), NULL, 'product_range_capacity_power');
        foreach ($fields as $field) {
          if (!$field->getDatasourceId()) {
            $field->addValue($range_capacity_power);
          }
        }
      }
    }
  }
}
