<?php

namespace Drupal\nine_mobile_product\Plugin\facets\widget;

use Drupal\facets\FacetInterface;
use Drupal\Core\Form\FormStateInterface;
use Drupal\facets\Plugin\facets\widget\LinksWidget;
use Drupal\facets\Result\Result;

/**
 * The checkbox ajax widget.
 *
 * @FacetsWidget(
 *   id = "ajax_checkboxes",
 *   label = @Translation("List of checkboxes with ajax update"),
 *   description = @Translation("A configurable widget that shows a list of checkboxes"),
 * )
 */
class AjaxCheckboxWidget extends LinksWidget {

  /**
   * {@inheritdoc}
   */
  public function build(FacetInterface $facet) {
    $build = parent::build($facet);
    $config = $this->getConfiguration();
    $ranges = $config['numeric_value'];
    $items = array_map(function (Result $result) use ($facet) {
      if (empty($result->getUrl())) {
        return $this->buildResultItem($result);
      }
      else {
        return $this->buildListItems($facet, $result);
      }
    }, $facet->getResults());
    $build['tan'] = ['#markup' => 'asdd'];

    return $build;
  }

  /**
   * {@inheritdoc}
   */
  public function buildConfigurationForm(array $form, FormStateInterface $form_state, FacetInterface $facet) {
    $config = $this->getConfiguration();
    $form = parent::buildConfigurationForm($form, $form_state, $facet);
    $form['numeric_value'] = [
      '#type' => 'textarea',
      '#title' => $this->t('Values'),
      '#required' => TRUE,
      '#default_value' => $config['numeric_value'],
    ];
    return $form;
  }

}
