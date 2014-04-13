<?php
/**
 * @file
 * Contains \Drupal\uuid_helper\Path\OutboundPathProcessor.
 */

namespace Drupal\uuid_helper\Path;

use Drupal\Core\Entity\EntityManagerInterface;
use Drupal\Core\PathProcessor\OutboundPathProcessorInterface;
use Drupal\Core\Routing\RouteProviderInterface;
use Drupal\uuid_helper\Routing\UuidEntityRoute;
use Symfony\Component\HttpFoundation\Request;

/**
 * Rewrites outbound paths for UuidEntityRoute paths with UUIDs.
 *
 * @package Drupal\uuid_helper\Path
 * @author Aaron McGowan <me@amcgowan.ca>
 */
class OutboundPathProcessor implements OutboundPathProcessorInterface {
  /**
   * The entity manager.
   *
   * @var \Drupal\Core\Entity\EntityManagerInterface
   */
  protected $entityManager;

  /**
   * The route provider.
   *
   * @var \Drupal\Core\Routing\RouteProviderInterface
   */
  protected $routeProvider;

  /**
   * Constructs a new OutboundPathProcessor.
   *
   * @param EntityManagerInterface $em
   *   The entity manager.
   *
   * @param RouteProviderInterface $route_provider
   *   The route provider.
   */
  public function __construct(EntityManagerInterface $em, RouteProviderInterface $route_provider) {
    $this->entityManager = $em;
    $this->routeProvider = $route_provider;
  }

  /**
   * {@inheritdoc}
   */
  public function processOutbound($path, &$options = array(), Request $request = NULL) {
    // @TODO: Determine performance of this routine.
    if ($routes = $this->routeProvider->getRoutesByPattern('/' . ltrim($path, '/'))) {
      // @TODO: Determine if an iterator is actually needed.
      foreach ($routes as $route) {
        if (!($route instanceof UuidEntityRoute)) {
          continue;
        }

        $outbound_parts = array();
        $outbound_parts_count = 0;
        $path_parts = explode('/', $path);
        $route_path_parts = explode('/', trim($route->getPath(), '/'));
        $route_path_parts_count = count($route_path_parts);
        foreach ($route_path_parts as $i => $route_part) {
          if (!isset($path_parts[$i])) {
            return $path;
          }

          if (0 === strpos($route_part, '{') && ((strlen($route_part) - 1) === strpos($route_part, '}'))) {
            $variable = substr($route_part, 1, strlen($route_part) - 2);
            if ($entity_type = $route->getEntityVariable($variable)) {
              if ($storage = $this->entityManager->getStorageController($entity_type)) {
                if (is_numeric($path_parts[$i]) && $entity = $storage->load($path_parts[$i])) {
                  $outbound_parts[] = $entity->uuid();
                  $outbound_parts_count++;
                }
              }
            }
          }
          else {
            $outbound_parts[] = $route_part;
            $outbound_parts_count++;
          }
        }

        if ($route_path_parts_count === $outbound_parts_count) {
          $path = implode('/', $outbound_parts);
        }
      }
    }
    return $path;
  }
}
