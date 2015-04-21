<?php
/**
 * @file
 * Contains \Drupal\paragraphs\ParagraphsTranslationTest.
 */

namespace Drupal\paragraphs\Tests;

use Drupal\Core\Entity\Entity;
use Drupal\language\Entity\ConfigurableLanguage;
use Drupal\simpletest\WebTestBase;

/**
 * Tests the configuration of paragraphs.
 *
 * @group paragraphs
 */
class ParagraphsTranslationTest extends WebTestBase {

  /**
   * Disabled config schema checking temporarily until all errors are resolved.
   */
  protected $strictConfigSchema = FALSE;

  /**
   * Modules to enable.
   *
   * @var array
   */
  public static $modules = array(
    'node',
    'paragraphs_demo',
    'content_translation',
  );

  /**
   * {@inheritdoc}
   */
  protected function setUp() {
    parent::setUp();
    $language = ConfigurableLanguage::createFromLangcode('de');
    $language->save();
    $language = ConfigurableLanguage::createFromLangcode('fr');
    $language->save();
  }

  /**
   * Tests the paragraph translation.
   */
  public function testParagraphTranslation() {
    $admin_user = $this->drupalCreateUser(array(
      'administer site configuration',
      'administer nodes',
      'create paragraphed_content_demo content',
      'edit any paragraphed_content_demo content',
      'delete any paragraphed_content_demo content',
      'administer content translation',
      'translate any entity',
      'create content translations',
      'administer languages',
    ));

    $this->drupalLogin($admin_user);

    $this->drupalGet('admin/config/regional/content-language');

    // Enable translation for paragraphs and it's bundles.
    $edit = array(
      'entity_types[node]' => TRUE,
      'entity_types[paragraph]' => TRUE,
      'settings[paragraph][images][translatable]' => TRUE,
      'settings[paragraph][image_text][translatable]' => TRUE,
      'settings[node][paragraphed_content_demo][settings][language][language_alterable]' => TRUE,
      'settings[paragraph][user][translatable]' => TRUE,
      'settings[paragraph][text_image][translatable]' => TRUE,
      'settings[paragraph][text_image][fields][field_text_demo]' => TRUE,
      'settings[node][paragraphed_content_demo][translatable]' => TRUE,
      'settings[node][paragraphed_content_demo][fields][title]' => TRUE,
      'settings[node][paragraphed_content_demo][fields][uid]' => TRUE,
      'settings[node][paragraphed_content_demo][fields][status]' => TRUE,
      'settings[node][paragraphed_content_demo][fields][created]' => TRUE,
      'settings[node][paragraphed_content_demo][fields][changed]' => TRUE,
      'settings[node][paragraphed_content_demo][fields][promote]' => TRUE,
      'settings[node][paragraphed_content_demo][fields][sticky]' => TRUE,
      'settings[node][paragraphed_content_demo][fields][revision_log]' => TRUE,
    );
    $this->drupalPostForm(NULL, $edit, t('Save configuration'));

    // Clear cached bundles refer 2450251.
    \Drupal::entityManager()->clearCachedBundles();

    // Check the settings are saved correctly.
    // @todo Uncomment these lines once core is fixed https://www.drupal.org/node/2449457
    // https://www.drupal.org/node/2473721
    // $this->assertFieldChecked('edit-entity-types-paragraph');
    $this->assertFieldChecked('edit-settings-node-paragraphed-content-demo-translatable');
    // $this->assertFieldChecked('edit-settings-paragraph-text-image-translatable');

    // Add paragraphed content.
    $this->drupalGet('node/add/paragraphed_content_demo');
    $this->drupalPostForm(NULL, NULL, t('Add Text + Image'));
    $edit = array(
      'title[0][value]' => 'Title in english',
      'field_paragraphs_demo[0][subform][field_text_demo][0][value]' => 'Text in english',
    );
    $this->drupalPostForm(NULL, $edit, t('Save and publish'));
    $node = $this->drupalGetNodeByTitle('Title in english');

    $this->clickLink(t('Edit'));
    $this->assertText('Title in english');
    $this->assertText('Text in english');

    // Add french translation.
    $this->clickLink(t('Translate'));
    $this->clickLink(t('Add'));
    $edit = array(
      'title[0][value]' => 'Title in french',
      'field_paragraphs_demo[0][subform][field_text_demo][0][value]' => 'Text in french',
    );
    $this->drupalPostForm(NULL, $edit, t('Save and keep published (this translation)'));

    // Check the english translation.
    $this->drupalGet('node/' . $node->id());
    $this->assertText('Title in english');
    $this->assertText('Text in english');
    $this->assertNoText('Title in french');
    $this->assertNoText('Text in french');
    // Check the french translation.
    $this->drupalGet('fr/node/' . $node->id());
    $this->assertText('Title in french');
    $this->assertText('Text in french');
    $this->assertNoText('Title in english');

    $this->clickLink(t('Edit'));
    $this->assertText('Title in french');
    $this->assertText('Text in french');
    $edit = array(
      'field_paragraphs_demo[0][subform][field_text_demo][0][value]' => 'New text in french',
    );
    $this->drupalPostForm(NULL, $edit, t('Save and keep published (this translation)'));
    $this->assertText('Title in french');
    $this->assertText('New text in french');

    $this->drupalGet('node/' . $node->id());
    $this->clickLink(t('Edit'));
    $this->assertText('Title in english');
    $this->assertText('Text in english');
  }
}
