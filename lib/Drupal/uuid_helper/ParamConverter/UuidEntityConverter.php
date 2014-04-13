<?php
/**
 * @file
 * Contains \Drupal\uuid_helper\ParamConverter\UuidEntityConverter.
 */

namespace Drupal\uuid_helper\ParamConverter;

use Drupal\Core\ParamConverter\ParamConverterInterface;
use Drupal\Core\Entity\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Route;

/**
 * Parameter converter for upcasting entity uuid OR ids to full objects.
 *
 * @package Drupal\uuid_helper\ParamConverter
 * @author Aaron McGowan <me@amcgowan.ca>
 */
class UuidEntityConverter implements ParamConverterInterface {
  /**
   * Entity manager.
   *
   * @var \Drupal\Core\Entity\EntityManagerInterface
   */
  protected $entityManager;

  /**
   * Constructs a new EntityUuidParamConverter.
   *
   * @param EntityManagerInterface $em
   *   The entity manager.
   */
  public function __construct(EntityManagerInterface $em) {
    $this->entityManager = $em;
  }

  /**
   * {@inheritdoc}
   */
  public function convert($value, $definition, $name, array $defaults, Request $request) {
    $entity_type = substr($definition['type'], strlen('entity:'));
    $entity = entity_load_by_uuid($entity_type, $value);
    if (FALSE === $entity) {
      $entity = entity_load($entity_type, $value);
    }
    return $entity;
  }

  /**
   * {@inheritdoc}
   */
  public function applies($definition, $name, Route $route) {
    if (!empty($definition['type']) && 0 === strpos($definition['type'], 'entity:')) {
      $entity_type_id = substr($definition['type'], strlen('entity:'));
      if ($entity_type = $this->entityManager->getDefinition($entity_type_id)) {
        return $entity_type->hasKey('uuid');
      }
    }
    return FALSE;
  }
}
