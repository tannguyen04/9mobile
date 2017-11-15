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
 *   id = "add_range_camera",
 *   label = @Translation("Range camera Field"),
 *   description = @Translation("Adds the item's range price for index the data"),
 *   stages = {
 *     "add_properties" = 0,
 *   },
 *   locked = true,
 *   hidden = true,
 * )
 */
class AddRangeCamera extends ProcessorPluginBase {

  /**
   * {@inheritdoc}
   */
  public function getPropertyDefinitions(DatasourceInterface $datasource = NULL) {
    $properties = [];

    if (!$datasource) {
      $definition = [
        'label' => $this->t('Product Front Camera'),
        'description' => $this->t('Range Front Camera of Product'),
        'type' => 'string',
        'processor_id' => $this->getPluginId(),
      ];
      $properties['product_range_front_camera'] = new ProcessorProperty($definition);

      $definition = [
        'label' => $this->t('Product Back Camera'),
        'description' => $this->t('Range Back Camera of Product'),
        'type' => 'string',
        'processor_id' => $this->getPluginId(),
      ];
      $properties['product_range_back_camera'] = new ProcessorProperty($definition);
    }

    return $properties;
  }

  /**
   * {@inheritdoc}
   */
  public function addFieldValues(ItemInterface $item) {
    $origin_object = $item->getOriginalObject();
    $product = $origin_object->getValue();
    //Add value for product_range_front_camera
    $range_front_camera = '';
    if ($product->hasField('field_front_camera') && !$product->get('field_front_camera')->isEmpty()) {
      $front_camera_pixel = $product->get('field_front_camera')->first()->getValue();
      $front_camera_pixel = floatval($front_camera_pixel);
      if ($front_camera_pixel <= 3) {
        $range_front_camera = '<=3';
      }elseif ($price >= 3 && $price <= 8) {
        $range_front_camera = '3-8';
      }else {
        $range_front_camera = '>=8';
      }
      if (!empty($range_front_camera)) {
        $fields = $this->getFieldsHelper()
          ->filterForPropertyPath($item->getFields(), NULL, 'product_range_front_camera');
        foreach ($fields as $field) {
          if (!$field->getDatasourceId()) {
            $field->addValue($range_front_camera);
          }
        }
      }
    }
    //Add value for product_range_back_camera
    $range_back_camera = '';
    if ($product->hasField('field_back_camera') && !$product->get('field_back_camera')->isEmpty()) {
      $front_camera_pixel = $product->get('field_back_camera')->first()->getValue();
      $front_camera_pixel = floatval($front_camera_pixel);
      if ($front_camera_pixel <= 5) {
        $range_back_camera = '<=5';
      }elseif ($price >= 5 && $price <= 12) {
        $range_back_camera = '5-12';
      }else {
        $range_back_camera = '>=12';
      }
      if (!empty($range_back_camera)) {
        $fields = $this->getFieldsHelper()
          ->filterForPropertyPath($item->getFields(), NULL, 'product_range_back_camera');
        foreach ($fields as $field) {
          if (!$field->getDatasourceId()) {
            $field->addValue($range_back_camera);
          }
        }
      }
    }

  }
}
