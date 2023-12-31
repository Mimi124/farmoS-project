<?php

namespace Drupal\Tests\entity_reference_validators\Functional;

use Drupal\field\Entity\FieldConfig;
use Drupal\Tests\field\Traits\EntityReferenceTestTrait;
use Drupal\Tests\BrowserTestBase;
use Drupal\Tests\node\Traits\ContentTypeCreationTrait;

/**
 * Tests circular entity reference validation.
 *
 * @group entity_reference
 */
class CircularEntityReferenceTest extends BrowserTestBase {

  use ContentTypeCreationTrait;
  use EntityReferenceTestTrait;

  /**
   * The entity reference field under test.
   *
   * @var \Drupal\field\Entity\FieldConfig
   */
  protected $field;

  /**
   * Modules to install.
   *
   * @var array
   */
  protected static $modules = ['entity_reference_validators', 'node', 'field_ui'];

  /**
   * {@inheritdoc}
   */
  protected $defaultTheme = 'stark';

  /**
   * Sets up the test.
   */
  protected function setUp() {
    parent::setUp();

    $this->createContentType(['type' => 'test']);
    $this->createEntityReferenceField('node', 'test', 'field_entity_ref_test', 'Test circular entity reference', 'node');
    $this->createEntityReferenceField('node', 'test', 'field_entity_ref_other_test', 'Test other entity type reference', 'node_type');
    $admin_user = $this->drupalCreateUser(['administer node fields']);
    $this->drupalLogin($admin_user);
  }

  /**
   * Tests circular references in field UI.
   */
  public function testCircularReference() {
    // Ensure that entity reference fields that reference the same entity type
    // can configure the circular reference validator.
    $this->drupalGet('admin/structure/types/manage/test/fields/node.test.field_entity_ref_test');
    $this->assertSession()->pageTextContains(t('Reference validators'));
    $this->assertSession()->pageTextContains(t('Prevent circular references'));
    $this->assertSession()->pageTextContains(t('Recursively check circular references'));

    $edit = [
      'settings[handler_settings][target_bundles][test]' => TRUE,
      'third_party_settings[entity_reference_validators][circular_reference]' => TRUE,
    ];
    $this->submitForm($edit, t('Save settings'), 'field-config-edit-form');
    $this->assertTrue(FieldConfig::loadByName('node', 'test', 'field_entity_ref_test')->getThirdPartySetting('entity_reference_validators', 'circular_reference', FALSE));
    $this->assertFalse(FieldConfig::loadByName('node', 'test', 'field_entity_ref_test')->getThirdPartySetting('entity_reference_validators', 'circular_reference_deep', FALSE));

    $this->drupalGet('admin/structure/types/manage/test/fields/node.test.field_entity_ref_test');
    $this->assertSession()->pageTextContains(t('Recursively check circular references'));
    $edit = [
      'third_party_settings[entity_reference_validators][circular_reference_deep]' => TRUE,
    ];
    $this->submitForm($edit, t('Save settings'), 'field-config-edit-form');
    $this->assertTrue(FieldConfig::loadByName('node', 'test', 'field_entity_ref_test')->getThirdPartySetting('entity_reference_validators', 'circular_reference_deep', FALSE));

    // Ensure that setting the circular_reference setting to FALSE also sets the
    // circular_reference_deep setting to FALSE.
    $this->drupalGet('admin/structure/types/manage/test/fields/node.test.field_entity_ref_test');
    $this->assertSession()->pageTextContains(t('Recursively check circular references'));
    $edit = [
      'third_party_settings[entity_reference_validators][circular_reference]' => FALSE,
    ];
    $this->submitForm($edit, t('Save settings'), 'field-config-edit-form');
    $this->assertFalse(FieldConfig::loadByName('node', 'test', 'field_entity_ref_test')->getThirdPartySetting('entity_reference_validators', 'circular_reference', FALSE));
    $this->assertFalse(FieldConfig::loadByName('node', 'test', 'field_entity_ref_test')->getThirdPartySetting('entity_reference_validators', 'circular_reference_deep', FALSE));

    // Ensure that entity reference fields that reference other entity types
    // cannot configure the circular reference validator.
    $this->drupalGet('admin/structure/types/manage/test/fields/node.test.field_entity_ref_other_test');
    $this->assertSession()->pageTextNotContains(t('Prevent circular references'));
    // Just check something positive so we know we're on the proper page.
    $this->assertSession()->pageTextContains(t('Reference type'));
  }

}
