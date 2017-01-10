<?php

namespace Drupal\block_Content_render\Controller;

use Drupal\block_content\BlockContentInterface;
use Drupal\Core\Controller\ControllerBase;
use Drupal\Core\Render\Element;

/**
 * Endpoints for the Block Content Render module.
 */
class BlockContentRender extends ControllerBase {

  /**
   * Renders a block in two contexts to outline a silly core quirk.
   *
   * @param \Drupal\block_content\BlockContentInterface $block_content
   *   A given content block.
   * @return array
   *   The render array for the example.
   */
  function example(BlockContentInterface $block_content) {
    $build = [];
    $view_builder = \Drupal::entityTypeManager()->getViewBuilder('block_content');
    $build[] = [
      '#type' => 'details',
      '#title' => 'This block_content entity is rendered using the Block Content view builder.',
      '#open' => TRUE,
      $view_builder->view($block_content, 'full'),
    ];
    /** @var \Drupal\Component\Plugin\PluginManagerInterface $block_manager */
    $block_manager = \Drupal::service('plugin.manager.block');
    /** @var \Drupal\block_content\Plugin\Block\BlockContentBlock $block */
    $block = $block_manager->createInstance('block_content:' . $block_content->uuid());

    $block_render_array = [
      '#theme' => 'block',
      '#attributes' => [],
      '#contextual_links' => [],
      '#weight' => 0,
      '#configuration' => $block->getConfiguration(),
      '#plugin_id' => $block->getPluginId(),
      '#base_plugin_id' => $block->getBaseId(),
      '#derivative_plugin_id' => $block->getDerivativeId(),
      '#content' => '',
    ];

    // See \Drupal\block\BlockViewBuilder::preRender() for reference.
    $content = $block->build();
    if ($content !== NULL && !Element::isEmpty($content)) {
      foreach (['#attributes', '#contextual_links'] as $property) {
        if (isset($content[$property])) {
          $block_render_array[$property] += $content[$property];
          unset($content[$property]);
        }
      }
    }

    $block_render_array['content'] = $content;

    $build[] = [
      '#type' => 'details',
      '#title' => 'This block_content entity is rendered using a render array a temporary block entity instance',
      '#open' => TRUE,
      $block_render_array
    ];
    return $build;
  }

}
