<?php
/**
 * @file
 * Contains view hook implementations.
 */

/**
 * Implements hook_views_data_alter().
 */
function uuid_helper_views_data_alter(&$data) {
  foreach (\Drupal::entityManager()->getDefinitions() as $entity_type) {
    if (isset($data[$entity_type->id()]) && $entity_type->hasKey('uuid')) {
      $data[$entity_type->id()]['entity_identifier'] = array(
        'title' => t('@type entity identifier', array('@type' => $entity_type->getLabel())),
        'help' => t("@type's entity identifier, serial or uuid.", array('@type' => $entity_type->getLabel())),
        'argument' => array(
          'id' => 'entity_identifier',
          'entity id field' => $entity_type->getKey('id'),
          'entity uuid field' => $entity_type->getKey('uuid'),
          'validate_options' => array(
            'entity_type' => $entity_type->id(),
          ),
        ),
      );
    }
  }
}
