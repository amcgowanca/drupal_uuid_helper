<?php
/**
 * @file
 */

/**
 * Implements hook_views_data_alter().
 */
function uuid_helper_views_data_alter(&$data) {
  foreach (entity_get_info() as $entity_info) {
    if (isset($entity_info['uuid']) && TRUE == $entity_info['uuid'] && !empty($entity_info['entity keys']['uuid'])) {
      $table = $entity_info['base table'];
      $data[$table]['identifier'] = array(
        'title' => t('@type identifier', array('@type' => $entity_info['label'])),
        'help' => t('The entity identifier (serial or universal unique id) of the @type.', array('@type' => $entity_info['label'])),
        'argument' => array(
          'handler' => 'uuid_helper_views_handler_argument_entity_identifier',
          // Handler specific definition elements required.
          'entity serial identifier' => $entity_info['entity keys']['id'],
          'entity universal identifier' => $entity_info['entity keys']['uuid'],
        ),
      );
    }
  }
}
