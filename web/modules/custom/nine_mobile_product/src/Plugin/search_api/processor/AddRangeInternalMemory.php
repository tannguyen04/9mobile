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
 *   id = "add_range_internal_memory",
 *   label = @Translation("Range Internal Memory"),
 *   description = @Translation("Adds the item's range internal memory for index the data"),
 *   stages = {
 *     "add_properties" = 0,
 *   },
 *   locked = true,
 *   hidden = true,
 * )
 */
class AddRangeInternalMemory extends ProcessorPluginBase {

  /**
   * {@inheritdoc}
   */
  public function getPropertyDefinitions(DatasourceInterface $datasource = NULL) {
    $properties = [];

    if (!$datasource) {
      $definition = [
        'label' => $this->t('Product Range Internal memory'),
        'description' => $this->t('Range Internal memory of Product'),
        'type' => 'string',
        'processor_id' => $this->getPluginId(),
      ];
      $properties['product_range_internal_memory'] = new ProcessorProperty($definition);
    }

    return $properties;
  }

  /**
   * {@inheritdoc}
   */
  public function addFieldValues(ItemInterface $item) {
    $origin_object = $item->getOriginalObject();
    $product = $origin_object->getValue();
    // Add value for product_range_internal_memory.
    if ($product->hasField('field_internal_memory') && !$product->get('field_internal_memory')->isEmpty()) {
      $internal_memory = $product->get('field_internal_memory')->first()->getValue();
      $internal_memory = (int) $internal_memory['value'];
      if ($internal_memory <= 32) {
        $range_internal_memory = '<=32';
      }
      elseif ($internal_memory >= 32 && $internal_memory <= 128) {
        $range_internal_memory = '32-128';
      }
      else {
        $range_internal_memory = '>=128';
      }
      if (isset($range_internal_memory)) {
        $fields = $this->getFieldsHelper()
          ->filterForPropertyPath($item->getFields(), NULL, 'product_range_internal_memory');
        foreach ($fields as $field) {
          if (!$field->getDatasourceId()) {
            $field->addValue($range_internal_memory);
          }
        }
      }
    }
  }

}
