<?php

namespace Drupal\nine_mobile_product\Plugin\Field\FieldFormatter;

use Drupal\Core\Field\FieldItemListInterface;
use Drupal\image\Plugin\Field\FieldFormatter\ImageFormatterBase;
use Drupal\Core\Cache\Cache;
use Drupal\image\Entity\ImageStyle;

/**
 * Plugin implementation of the 'nine_mobile_image_responsive' formatter.
 *
 * @FieldFormatter(
 *   id = "nine_mobile_image_responsive",
 *   label = @Translation("Nine Mobile: Custom image responsive with picture tag"),
 *   field_types = {
 *     "image"
 *   }
 * )
 */
class NineMobileImageResponsive extends ImageFormatterBase {

  /**
   * {@inheritdoc}
   */
  public function viewElements(FieldItemListInterface $items, $langcode) {
    $elements = [];
    $files = $this->getEntitiesToView($items, $langcode);

    // Early opt-out if the field is empty.
    if (empty($files)) {
      return $elements;
    }

    //Get break points
    $break_points = [
      '(min-width: 567px)'
    ];
    $sources = [];
    foreach ($files as $delta => $file) {
      $cache_contexts = [];
      #$cache_tags = Cache::mergeTags($base_cache_tags, $file->getCacheTags());
      // Extract field item attributes for the theme function, and unset them
      // from the $item so that the field template does not re-render them.
      $image_uri = $file->getFileUri();
      $url_file = file_create_url($image_uri);
      $item = $file->_referringItem;
      $item_attributes = $item->_attributes;
      unset($item->_attributes);
      $sources[] = [
        '#type' => 'html_tag',
        '#tag' => 'source',
        '#attributes' => [
          'media' => isset($break_points[$delta]) ? $break_points[$delta] : '',
          'srcset' => $url_file,
        ]
      ];

      //Build fallback image = last file
      if (($delta + 1) == count($files)) {
        $item_attributes['data-title'] = $item->get('title')->getValue();
        $item_attributes['data-thumb'] = ImageStyle::load('thumbnail')->buildUrl($image_uri);
        $image_fallback = [
          '#theme' => 'image_formatter',
          '#item' => $item,
          '#item_attributes' => $item_attributes,
          '#image_style' => ''
        ];
        unset($sources[$delta]);
      }
    }

    $elements[0] = [
      '#type' => 'html_tag',
      '#tag' => 'picture',
      'sources' => $sources,
      'image_fallback' => $image_fallback
    ];
    return $elements;
  }
}
