<?php
/**
 * @file
 * Contains \Drupal\uuid_helper\Plugin\views\argument\EntityIdentifier.
 */

namespace Drupal\uuid_helper\Plugin\views\argument;

use Drupal\views\Plugin\views\argument\Standard;

/**
 * Argument handler to accept serial id or uuid of entities.
 *
 * @PluginID("entity_identifier")
 *
 * @package Drupal\uuid_helper\Plugin\views\argument
 * @author Aaron McGowan <me@amcgowan.ca>
 */
class EntityIdentifier extends Standard {
  /**
   * {@inheritdoc}
   */
  public function __construct(array $configuration, $plugin_id, array $plugin_definition) {
    parent::__construct($configuration, $plugin_id, $plugin_definition);
    // Ensure that the entity id and uuid field configuration is set.
    if (!isset($this->configuration['entity id field']) || !isset($this->configuration['entity uuid field'])) {
      throw new \InvalidArgumentException(t('EntityIdentifier Views Argument is missing entity serial or universal field name.'));
    }
  }

  /**
   * {@inheritdoc}
   */
  protected function defineOptions() {
    $options = parent::defineOptions();
    $options['validate'] = array(
      'default' => array(
        'type' => 'entity_identifier',
        'fail' => 'not found'
      )
    );
    $options['validate_options'] = array(
      'default' => array(
        'entity_type' => $this->getEntityType(),
      ),
    );
    return $options;
  }

  /**
   * {@inheritdoc}
   */
  public function query($group_by = FALSE) {
    $this->ensureMyTable();
    if (!empty($this->options['break_phrase'])) {
      $this->breakPhrase($this->argument, $this);
    }
    else {
      $this->value = array($this->argument);
    }

    $placeholder = $this->placeholder();
    $query_group_id = $this->query->setWhereGroup('OR', 'entity_identifier_group');
    if (1 < count($this->value)) {
      $operator = empty($this->option['not']) ? 'IN' : 'NOT IN';
      $this->query->addWhereExpression($query_group_id, "$this->tableAlias.{$this->configuration['entity id field']} $operator($placeholder)", array($placeholder => $this->argument));
      $this->query->addWhereExpression($query_group_id, "$this->tableAlias.{$this->configuration['entity uuid field']} $operator($placeholder)", array($placeholder => $this->argument));
    }
    else {
      $operator = empty($this->options['not']) ? '=' : '!=';
      $this->query->addWhereExpression($query_group_id, "$this->tableAlias.{$this->configuration['entity id field']} $operator $placeholder", array($placeholder => $this->argument));
      $this->query->addWhereExpression($query_group_id, "$this->tableAlias.{$this->configuration['entity uuid field']} $operator $placeholder", array($placeholder => $this->argument));
    }
  }

}
