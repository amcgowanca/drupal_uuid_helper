<?php
/**
 * @file
 * Contains \Drupal\uuid_helper\Plugin\views|argument_validator\Entity.
 */

namespace Drupal\uuid_helper\Plugin\views\argument_validator;

use Drupal\views\Plugin\views\argument\ArgumentPluginBase;
use Drupal\views\Plugin\views\argument_validator\Entity as ViewsEntityValidator;

/**
 * Validate an argument to determine if it is an acceptable entity identifier.
 *
 * @ViewsArgumentValidator(
 *    id = "entity_identifier",
 *    title = @Translation("Entity Identifier")
 * )
 *
 * @package Drupal\uuid_helper\Plugin\views\argument_validator
 * @author Aaron McGowan <me@amcgowan.ca>
 */
class Entity extends ViewsEntityValidator {
  /**
   * {@inheritdoc}
   */
  public function setArgument(ArgumentPluginBase $argument) {
    parent::setArgument($argument);
    // TODO: Determine best way to set `entity_type`.
    $this->definition['entity_type'] = $argument->getEntityType();
  }

  /**
   * {@inheritdoc}
   */
  public function validateArgument($argument) {
    if ($this->options['multiple']) {
      $ids = array_filter(preg_split('/[,+ ]/', $argument));
    }
    elseif ($argument) {
      $ids = array($argument);
    }
    else {
      $ids = array();
      return FALSE;
    }

    $entity_type = $this->definition['entity_type'];
    if ($storage = $this->entityManager->getStorageController($entity_type)) {
      foreach ($ids as $id) {
        if (is_numeric($id)) {
          $entity = $storage->load($id);
        }
        else {
          $entity = $storage->loadByProperties(array('uuid' => $id));
        }
        if (!$this->validateEntity($entity)) {
          return FALSE;
        }
      }
      return TRUE;
    }
    return FALSE;
  }
}
