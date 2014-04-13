<?php
/**
 * @file
 * Contains \Drupal\uuid_helper\Routing\RouteSubscriber.
 */

namespace Drupal\uuid_helper\Routing;

use Drupal\Core\Entity\EntityManagerInterface;
use Drupal\Core\Routing\RouteSubscriberBase;
use Symfony\Component\Routing\RouteCollection;

/**
 * Alters routes with entity variables to be represented as UuidEntityRoutes.
 *
 * @package Drupal\uuid_helper\Routing
 * @author Aaron McGowan <me@amcgowan.ca>
 */
class RouteSubscriber extends RouteSubscriberBase {
  /**
   * An array of entity types to re-write.
   *
   * @var array
   */
  protected $entityTypes = array();

  /**
   * Constructs a new RouteSubscriber instance.
   *
   * @param EntityManagerInterface $em
   *   The entity manager to retrieve entity information from.
   */
  public function __construct(EntityManagerInterface $em) {
    foreach ($em->getDefinitions() as $entity_type) {
      if ($entity_type->hasKey('uuid')) {
        $this->entityTypes[$entity_type->id()] = $entity_type;
      }
    }
  }

  /**
   * {@inheritdoc}
   */
  public function alterRoutes(RouteCollection $collection, $provider) {
    foreach ($collection as $route_name => $route) {
      $entity_variables = array();
      foreach ($route->compile()->getVariables() as $variable) {
        if (isset($this->entityTypes[$variable])) {
          $entity_variables[$variable] = $this->entityTypes[$variable]->id();
        }
      }

      if (!empty($entity_variables)) {
        $entity_route = UuidEntityRoute::fromSymfonyRoute($route);
        $entity_route->setEntityVariables($entity_variables);
        $collection->add($route_name, $entity_route);
      }
    }
  }
}
