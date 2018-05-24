<?php

/**
 * @file
 * Paragraphs item translation handler for the entity translation module.
 */


/**
 * Paragraphs item translation handler.
 *
 * Overrides default behaviours for Paragraphs item properties.
 */
class EntityTranslationParagraphsItemHandler extends EntityTranslationDefaultHandler {

  public $hostEntity = NULL;
  public $hostEntityHandler = NULL;

  public function __construct($entity_type, $entity_info, $entity) {
    if (method_exists($entity, 'hostEntity')) {
      $this->hostEntity = $entity->hostEntity();
      $host_entity_type = $entity->hostEntityType();
      if (empty($host_entity_type)) {
        $host_entity_type = 'paragraphs_item';
      }

      // A nested paragraphs field.
      if ($host_entity_type == 'paragraphs_item') {
        // Look for the parent host entity.
        while (method_exists($this->hostEntity, 'hostEntity')) {
          $this->hostEntity->hostEntity();
          $host_entity_type = $this->hostEntity->hostEntityType();
          $this->hostEntity = $this->hostEntity->hostEntity();
        }
      }

      $this->hostEntityHandler = entity_translation_get_handler($host_entity_type, $this->hostEntity);
    }
    parent::__construct('paragraphs_item', $entity_info, $entity);
  }

  public function getLanguage() {
    return $this->hostEntityHandler ? $this->hostEntityHandler->getLanguage() : parent::getLanguage();
  }

  public function getFormLanguage() {
    return $this->hostEntityHandler ? $this->hostEntityHandler->getFormLanguage() : parent::getFormLanguage();
  }

  public function getSourceLanguage() {
    return $this->hostEntityHandler ? $this->hostEntityHandler->getSourceLanguage() : parent::getSourceLanguage();
  }

  public function getTranslations() {
    return $this->hostEntityHandler ? $this->hostEntityHandler->getTranslations() : parent::getTranslations();
  }

  /**
   * @see EntityTranslationDefaultHandler::entityFormLanguageWidgetSubmit()
   */
  function entityFormLanguageWidgetSubmit($form, &$form_state) {
    $this->updateFormLanguage($form_state);
  }
}
