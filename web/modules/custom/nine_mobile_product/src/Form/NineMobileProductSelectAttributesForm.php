<?php

namespace Drupal\nine_mobile_product\Form;

use Drupal\Core\Form\FormBase;
use Drupal\Core\Form\FormStateInterface;
use Drupal\Core\Ajax\AjaxResponse;
use Drupal\Core\Ajax\AlertCommand;
use Drupal\Core\Ajax\HtmlCommand;
use Drupal\Core\Ajax\ReplaceCommand;
use Drupal\Core\Ajax\AppendCommand;
use Drupal\Core\Ajax\InvokeCommand;
use Drupal\Core\Field\FieldItemListInterface;
use CommerceGuys\Intl\Formatter\NumberFormatterInterface;
use Drupal\commerce_price\NumberFormatterFactoryInterface;
use Drupal\Core\Entity\EntityTypeManagerInterface;
use Symfony\Component\DependencyInjection\ContainerInterface;

/**
 * Class NineMobileProductSelectAttributesForm.
 *
 * @package Drupal\nine_mobile_product\Form
 */
class NineMobileProductSelectAttributesForm extends FormBase {
  /**
   * The number formatter.
   *
   * @var \CommerceGuys\Intl\Formatter\NumberFormatterInterface
   */
  protected $numberFormatter;

  /**
   * Class constructor.
   */
  public function __construct(EntityTypeManagerInterface $entity_type_manager, NumberFormatterFactoryInterface $number_formatter_factory) {
    $this->currencyStorage = $entity_type_manager->getStorage('commerce_currency');
    $this->numberFormatter = $number_formatter_factory->createInstance();
    $this->numberFormatter->setMaximumFractionDigits(6);
    $this->numberFormatter->setMinimumFractionDigits(0);
    //$this->numberFormatter->setCurrencyDisplay(NumberFormatterInterface::CURRENCY_DISPLAY_CODE);
  }

  /**
   * {@inheritdoc}
   */
  public static function create(ContainerInterface $container) {
    return new static(
      $container->get('entity_type.manager'),
      $container->get('commerce_price.number_formatter_factory')
    );
  }

  /**
   * {@inheritdoc}
   */
  public function getFormId() {
    return 'nine_mobile_product_select_attributes_form';
  }

  /**
   * {@inheritdoc}
   */
  public function buildForm(array $form, FormStateInterface $form_state, FieldItemListInterface $items = NULL) {
    $form['#attached']['library'][] = 'nine_mobile_product/mobile_product_select_attributes';
    $product_variation_ids = array();
    //Build array product variation with attributes
    $product_variations = array();
    foreach ($items as $delta => $item) {
      $product_variation = $item->entity;
      $currency_codes[] = $product_variation->getPrice()->getCurrencyCode();
    }
    $currencies = $this->currencyStorage->loadMultiple($currency_codes);

    foreach ($items as $key => $item) {
      $product_variation = $item->entity;
      $currency_code = $product_variation->getPrice()->getCurrencyCode();
      $currency = $currencies[$currency_code];
      $color = $product_variation->getAttributeValue('attribute_color')->get('field_color')->first()->getValue();
      $number = $product_variation->getPrice()->getNumber();
      $product_variations[] = array(
        'product_variant_id' => $product_variation->Id(),
        'attribute_color' => array(
          'name' => $product_variation->getAttributeValue('attribute_color')->label(),
          'id' => $product_variation->getAttributeValue('attribute_color')->Id(),
          'hex' => $color['color'],
        ),
        'attribute_memory' => array(
          'name' => $product_variation->getAttributeValue('attribute_memory')->label(),
          'id' => $product_variation->getAttributeValueId('attribute_memory'),
        ),
        'price' => $this->numberFormatter->formatCurrency($number, $currency),
      );
    }
    //Set product variation in form stata
    $form_state->set('product_variations', $product_variations);
    //Get color by product variant id
    $product_variation_default = $product_variations[0];
    $options_color = array();
    $list_color = array();
    foreach ($product_variations as $key => $value) {
      $options_color[$value['attribute_color']['id']] = $value['attribute_color']['name'];
      $list_color[$value['attribute_color']['id']] = array(
        '#wrapper_attributes' => array(
          'style' => 'background-color: '.$value['attribute_color']['hex'].'; width: 37px; height: 37px;',
          'data-color-id' => $value['attribute_color']['id'],
        ),
        '#markup' => ''
      );
    }
    $color_ids = array_keys($list_color);
    if (!empty($color_ids)) {
      $list_color[$color_ids[0]]['#wrapper_attributes']['class'] = 'active';
    }
    $form['attribute_color_list'] = array(
      '#theme' => 'item_list',
      '#items' => $list_color,
      '#attributes' => array(
        'class' => array('product__attributes-colors')
      ),
    );
    $form['attribute_color'] = array(
      '#title' => 'Color',
      '#type' => 'select',
      '#options' => $options_color,
      '#default_value' => $product_variation_default['attribute_color']['id'],
      '#ajax' => array(
        'callback' => '::ajaxCallback'
      ),
      '#prefix' => '<div class="hidden">',
      '#suffix' => '</div>',
    );

    //We need get list memory base on color
    $memory_options = array();
    $list_memory = array();
    foreach ($product_variations as $key => $value) {
      if ($product_variation_default['attribute_color']['id'] == $value['attribute_color']['id']) {
        $memory_options[$value['attribute_memory']['id']] = $value['attribute_memory']['name'];
        $list_memory[$value['attribute_memory']['id']] = array(
          '#wrapper_attributes' => array(
            'data-memory-id' => $value['attribute_memory']['id'],
          ),
          '#markup' => $value['attribute_memory']['name']
        );
      }
    }
    $memory_ids = array_keys($list_memory);
    if (!empty($memory_ids)) {
      $list_memory[$memory_ids[0]]['#wrapper_attributes']['class'] = 'active';
    }
    $form['attribute_memory_list'] = array(
      '#theme' => 'item_list',
      '#items' => $list_memory,
      '#attributes' => array(
        'class' => array('product__attributes-memory')
      ),
      //'#title' => t('Memory'),
    );
    $form['attribute_memory'] = array(
      '#type' => 'select',
      '#options' => $memory_options,
      '#default_value' => $product_variation_default['attribute_memory']['id'],
      '#ajax' => array(
        'callback' => '::ajaxCallback'
      ),
      '#validated' => TRUE,
      '#prefix' => '<div class="hidden">',
      '#suffix' => '</div>',
    );
    return $form;
  }

  public function ajaxCallback(array $form, FormStateInterface $form_state, $items) {
    $response = new AjaxResponse();
    $memory_options = array();
    $product_variations = $form_state->get('product_variations');
    $product_variation_default = $product_variations[0];
    $trigger_element = $form_state->getTriggeringElement();
    $i = 0;
    $color_id = $form_state->getValue('attribute_color');
    $memory_id = $form_state->getValue('attribute_memory');
    if ($trigger_element['#name'] == 'attribute_color') {
      foreach ($product_variations as $key => $value) {
        if ($value['attribute_color']['id'] == $color_id) {
          if ($i == 0) {
            $product_variation_default = $product_variations[$key];
            $selected = 'selected="selected"';
            $active = 'class="active"';
          }else {
            $selected = '';
            $active = '';
          }
          $i++;
          $memory_options[$value['attribute_memory']['id']] = '<option '.$selected.' value="'.$value['attribute_memory']['id'].'">'.$value['attribute_memory']['name'].'</option>';
          $memory_list[$value['attribute_memory']['id']] = '<li '.$active.' data-memory-id="'.$value['attribute_memory']['id'].'">'.$value['attribute_memory']['name'].'</li>';
        }
      }
      $price = $product_variation_default['price'];
      $memory_options = implode('', $memory_options);
      $memory_list = implode('', $memory_list);
      $response->addCommand(new InvokeCommand('#edit-attribute-memory', 'html', array($memory_options)));
      $response->addCommand(new InvokeCommand('.product__attributes-memory', 'html', array($memory_list)));
    }else {
      //Get Price
      foreach ($product_variations as $key => $value) {
        if ($value['attribute_color']['id'] == $color_id && $value['attribute_memory']['id'] == $memory_id) {
          $product_variation_default = $product_variations[$key];
          $price = $product_variation_default['price'];
          break;
        }
      }
    }
    $response->addCommand(new InvokeCommand('.field--name-price .field__item', 'html', array($price)));
    return $response;
  }

  /**
    * {@inheritdoc}
    */
  public function validateForm(array &$form, FormStateInterface $form_state) {
    parent::validateForm($form, $form_state);
  }

  /**
   * {@inheritdoc}
   */
  public function submitForm(array &$form, FormStateInterface $form_state) {
    // Display result.
    foreach ($form_state->getValues() as $key => $value) {
        drupal_set_message($key . ': ' . $value);
    }

  }

}
