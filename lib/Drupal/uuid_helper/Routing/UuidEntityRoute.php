<?php
/**
 * @file
 * Contains \Drupal\uuid_helper\Routing\UuidEntityRoute.
 */

namespace Drupal\uuid_helper\Routing;

use Symfony\Component\Routing\Route as SymfonyRoute;

/**
 * Represents a uuid enabled entity's route.
 *
 * @package Drupal\uuid_helper\Routing
 * @author Aaron McGowan <me@amcgowan.ca>
 */
class UuidEntityRoute extends SymfonyRoute {
  /**
   * An array of entity tokens within the route.
   *
   * @var array
   */
  protected $entityVariables = array();

  /**
   * Creates a new UuidEntityRoute instance from a SymfonyRoute.
   *
   * @access public
   *
   * @param SymfonyRoute $route
   *   The SymfonyRoute instance to derive from.
   *
   * @return UuidEntityRoute
   *   The UuidEntityRoute instance.
   */
  public static function fromSymfonyRoute(SymfonyRoute $route) {
    // @TODO: Implement a more appropriate implementation.
    $entity_route = new UuidEntityRoute(
      $route->getPath(),
      $route->getDefaults(),
      $route->getRequirements(),
      $route->getOptions(),
      $route->getHost(),
      $route->getSchemes(),
      $route->getMethods(),
      $route->getCondition()
    );
    return $entity_route;
  }

  /**
   * Adds a new entity variable to this route.
   *
   * @access public
   *
   * @param string $variable
   *   The name of the variable within the route to upcast.
   * @param string $entity_type
   *   The entity type id to upcast the variable for.
   *
   * @return UuidEntityRoute $this
   *   Returns this instance.
   */
  public function addEntityVariable($variable, $entity_type) {
    if (!isset($this->entityVariables[$variable])) {
      $this->entityVariables[$variable] = $entity_type;
    }
    return $this;
  }

  /**
   * Retrieves a single entity variable.
   *
   * @access public
   *
   * @param string $variable
   *   The name of the route variable to retrieve.
   *
   * @return mixed
   *   Returns the entity type id for the variable if it exists, otherwise NULL.
   */
  public function getEntityVariable($variable) {
    return isset($this->entityVariables[$variable]) ? $this->entityVariables[$variable] : NULL;
  }

  /**
   * Returns all entity variables.
   *
   * @access public
   *
   * @return array
   *   An array of all entity variables.
   */
  public function getEntityVariables() {
    return $this->entityVariables;
  }

  /**
   * Sets an array of all entity variables for this route.
   *
   * @access public
   *
   * @param array $variables
   *   An array of entity variables.
   *
   * @return UuidEntityRoute $this
   *   Returns this instance.
   */
  public function setEntityVariables(array $variables) {
    $this->entityVariables = $variables;
    return $this;
  }

  /**
   * Checks if this UuidEntityRoute has any route vars for entity upcasting.
   *
   * @access public
   *
   * @return bool
   *   Returns TRUE if this route has entity variables, otherwise FALSE.
   */
  public function hasEntityVariables() {
    return 0 < count($this->entityVariables) ? TRUE : FALSE;
  }

  /**
   * {@inheritdoc}
   */
  public function serialize() {
    return serialize(array(
      'path' => $this->getPath(),
      'host' => $this->getHost(),
      'defaults' => $this->getDefaults(),
      'requirements' => $this->getRequirements(),
      'options' => $this->getOptions(),
      'schemes' => $this->getSchemes(),
      'methods' => $this->getMethods(),
      'condition' => $this->getCondition(),
      'entity_variables' => $this->entityVariables
    ));
  }

  /**
   * {@inheritdoc}
   */
  public function unserialize($data) {
    $data = unserialize($data);
    $this->setPath($data['path']);
    $this->setHost($data['host']);
    $this->setDefaults($data['defaults']);
    $this->setRequirements($data['requirements']);
    $this->setOptions($data['options']);
    $this->setSchemes($data['schemes']);
    $this->setMethods($data['methods']);
    $this->setCondition($data['condition']);
    $this->entityVariables = $data['entity_variables'];
  }
}
