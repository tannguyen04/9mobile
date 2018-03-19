<?php

namespace Drupal\nine_mobile_product\Plugin\facets\widget;

use Drupal\facets\FacetInterface;
use Drupal\facets\Plugin\facets\widget\CheckboxWidget;

/**
 * The range checkbox / radios widget.
 *
 * @FacetsWidget(
 *   id = "range_checkbox",
 *   label = @Translation("List of range checkboxes"),
 *   description = @Translation("A configurable widget that shows a list of checkboxes"),
 * )
 */
class RangeCheckboxWidget extends CheckboxWidget {

  /**
   * {@inheritdoc}
   */
  public function build(FacetInterface $facet) {
    $new_results = $this->build_new_results($facet);
    $build = parent::build($facet);
    return $build;
  }

  /**
   * Build new result for display range facet.
   */
  public function build_new_results(FacetInterface $facet) {
    // Rebuild new results with new display value.
    $field_identifier = $facet->getFieldIdentifier();
    $new_results = [];
    switch ($field_identifier) {
      case 'product_range_price':
        $results = $facet->getResults();
        foreach ($results as $result) {
          $raw_value = $result->getRawValue();
          $display_value = $raw_value;
          if (strpos($raw_value, '<=') === 0) {
            $range = explode('<=', $raw_value);
            $display_value = t('Nhỏ hơn @price', ['@price' => $range[1]]);
          }
          elseif (strpos($raw_value, '-')) {
            $range = explode('-', $raw_value);
            $display_value = t('Từ @from_price đến @to_price', ['@from_price' => $range[0], '@to_price' => $range[1]]);
          }
          elseif (strpos($raw_value, '>=') === 0) {
            $range = explode('>=', $raw_value);
            $display_value = t('Lớn hơn @price', ['@price' => $range[1]]);
          }
          $result->setDisplayValue($display_value);
          $new_results[] = $result;
        }
        break;

      case 'product_range_front_camera':
      case 'product_range_back_camera':
      case 'product_range_size_creen':
      case 'product_range_internal_memory':
      case 'product_range_capacity_power':
        $results = $facet->getResults();
        foreach ($results as $result) {
          $raw_value = $result->getRawValue();
          $display_value = $raw_value;
          if (strpos($raw_value, '<=') === 0) {
            $range = explode('<=', $raw_value);
            $display_value = t('Nhỏ hơn @camera_pixel', ['@camera_pixel' => $range[1]]);
          }
          elseif (strpos($raw_value, '-')) {
            $range = explode('-', $raw_value);
            $display_value = t('Từ @from đến @to', ['@from' => $range[0], '@to' => $range[1]]);
          }
          elseif (strpos($raw_value, '>=') === 0) {
            $range = explode('>=', $raw_value);
            $display_value = t('Lớn hơn @camera_pixel', ['@camera_pixel' => $range[1]]);
          }
          $result->setDisplayValue($display_value);
          $new_results[] = $result;
        }
        break;

      default:
        $new_results = $facet->getResults();
        break;
    }
    $facet->setResults($new_results);
  }

}
