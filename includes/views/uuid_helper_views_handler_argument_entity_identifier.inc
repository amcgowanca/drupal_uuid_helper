<?php
/**
 * @file
 * Definition of uuid_helper_views_handler_argument_entity_identifer.
 */

/**
 * Basic argument handler for entity identifiers where id type may be either a
 * serial identifier or a universal unique identifier. Incorporates
 * break_phrase.
 */
class uuid_helper_views_handler_argument_entity_identifier extends views_handler_argument {
  /**
   * The entity's serial identifier field name (e.g. uid).
   * @var string
   */
  private $serial_id_field = NULL;

  /**
   * The entity's universal identifier field name (e.g. uuid).
   * @var string
   */
  private $universal_id_field = NULL;

  /**
   * {@inheritDoc}
   */
  public function init(&$view, &$options) {
    parent::init($view, $options);
    if (empty($this->definition['entity serial identifier']) || empty($this->definition['entity universal identifier'])) {
      throw new Exception(t("Missing arguments for `@class` definition.", array('@class' => get_called_class())));
    }

    $this->serial_id_field = $this->definition['entity serial identifier'];
    $this->universal_id_field = $this->definition['entity universal identifier'];
  }

  /**
   * {@inheritDoc}
   */
  public function query($group_by = FALSE) {
    $this->ensure_my_table();
    if (!empty($this->options['break_phrase'])) {
      views_break_phrase($this->argument, $this);
    }
    else {
      $this->value = array($this->argument);
    }

    $placeholder = $this->placeholder();
    $group_id = $this->query->set_where_group('OR', 'entity_identifier_group');
    if (1 < count($this->value)) {
      $operator = empty($this->options['not']) ? 'IN' : 'NOT IN';
      $this->query->add_where_expression($group_id, "$this->table_alias.$this->universal_id_field $operator($placeholder)", array($placeholder => $this->argument));
      $this->query->add_where_expression($group_id, "$this->table_alias.$this->serial_id_field $operator($placeholder)", array($placeholder => $this->argument));
    }
    else {
      $operator = empty($this->options['not']) ? '=' : '!=';
      $this->query->add_where_expression($group_id, "$this->table_alias.$this->universal_id_field $operator $placeholder", array($placeholder => $this->argument));
      $this->query->add_where_expression($group_id, "$this->table_alias.$this->serial_id_field $operator $placeholder", array($placeholder => $this->argument));
    }
  }
}

