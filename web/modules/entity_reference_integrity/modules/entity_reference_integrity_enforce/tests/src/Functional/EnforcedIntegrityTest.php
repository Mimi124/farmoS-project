<?php

namespace Drupal\Tests\entity_reference_integrity_enforce\Functional;

use Drupal\Core\Entity\EntityStorageException;
use Drupal\entity_reference_integrity_enforce\Exception\ProtectedEntityException;
use Drupal\Core\Entity\EntityInterface;
use Drupal\entity_test\Entity\EntityTest;
use Drupal\field\Entity\FieldConfig;
use Drupal\field\Entity\FieldStorageConfig;
use Drupal\Tests\BrowserTestBase;

/**
 * Test enforcing referential integrity.
 *
 * @group entity_reference_integrity_enforce
 */
class EnforcedIntegrityTest extends BrowserTestBase {

  /**
   * Modules to enable.
   *
   * @var array
   */
  protected static $modules = [
    'node',
    'entity_test',
    'block',
    'entity_reference_integrity_enforce',
  ];

  /**
   * {@inheritdoc}
   */
  protected $defaultTheme = 'stark';

  /**
   * {@inheritdoc}
   */
  protected function setUp(): void {
    parent::setUp();
    $this->drupalLogin($this->rootUser);
  }

  /**
   * Test a typical content entity.
   */
  public function testNodeIntegrity() {
    $node = $this->createNode(['title' => 'foo']);
    $this->assertEntityDeleteProtected($node, 'Cannot delete "node" of type "page" with label "foo" and ID "1" because other content is referencing it and the integrity of this entity type is enforced.');
  }

  /**
   * Test a typical config entity.
   */
  public function testBlockIntegrity() {
    $block = $this->placeBlock('system_branding_block', [
      'id' => 'foo',
      'label' => 'bar',
    ]);
    $this->assertEntityDeleteProtected($block, 'Cannot delete "block" of type "block" with label "bar" and ID "foo" because other content is referencing it and the integrity of this entity type is enforced.');
  }

  /**
   * Check that the delete form of an entity is protected.
   */
  protected function assertEntityDeleteProtected(EntityInterface $entity, $message) {
    $dependent = $this->createDependentEntity($entity);
    $this->drupalGet($entity->toUrl('delete-form'));

    // Initially, deleting an entity should be fine if it's not enabled.
    $this->assertSession()->elementsCount('css', '.form-submit', 1);
    $this->assertSession()->elementNotExists('css', '.form-submit[disabled]');
    $this->drupalGet('admin/config/content/entity-reference-integrity');

    // Enable protection of the entity type.
    $this->submitForm([
      sprintf('enabled_entity_type_ids[%s]', $entity->getEntityTypeId()) => TRUE,
    ], 'Save configuration');

    $this->drupalGet($entity->toUrl('delete-form'));

    // There should only be a single submit, which is also disabled.
    $this->assertSession()->elementNotExists('css', '.form-submit');

    $this->assertSession()->pageTextContains($dependent->getEntityType()->getLabel());
    $this->assertSession()->linkExists($dependent->label());

    // Attempt to delete the entity and see what happens.
    $this->assertDeleteThrowsException($entity, $message);

    // Make sure we can load the entity and it's still there.
    $reloaded_entity = \Drupal::entityTypeManager()->getStorage($entity->getEntityTypeId())->load($entity->id());
    $this->assertNotNull($reloaded_entity->label());
    $this->assertEquals($entity->label(), $reloaded_entity->label());

    // Ensure deletes work after references have been cleaned up.
    $dependent->delete();
    $entity->delete();
  }

  /**
   * Assert that deleting an entity throws an exception.
   */
  protected function assertDeleteThrowsException(EntityInterface $entity, $message) {
    try {
      $entity->delete();
    }
    catch (\Exception $e) {
      // SqlContentEntityStorage catches exceptions to rollback the transaction
      // and throws its own exception.
      if ($e instanceof EntityStorageException) {
        $this->assertInstanceOf(ProtectedEntityException::class, $e->getPrevious());
        $this->assertEquals($message, $e->getPrevious()->getMessage());
      }
      else {
        $this->assertInstanceOf(ProtectedEntityException::class, $e);
        $this->assertEquals($message, $e->getMessage());
      }
      return;
    }
    $this->fail('An exception was not thrown when attempting to delete a protected entity.');
  }

  /**
   * Create an entity that depends on the given entity.
   */
  protected function createDependentEntity(EntityInterface $entity) {
    // Create a bundle with the name name as the target entity type ID and an
    // entity reference field to link the two.
    entity_test_create_bundle($entity->getEntityTypeId());
    FieldStorageConfig::create([
      'field_name' => 'test_reference_field',
      'entity_type' => 'entity_test',
      'type' => 'entity_reference',
      'settings' => [
        'target_type' => $entity->getEntityTypeId(),
      ],
    ])->save();
    FieldConfig::create([
      'field_name' => 'test_reference_field',
      'entity_type' => 'entity_test',
      'bundle' => $entity->getEntityTypeId(),
      'label' => 'Reference Field',
    ])->save();

    $dependent = EntityTest::create([
      'name' => $this->randomMachineName(),
      'type' => $entity->getEntityTypeId(),
      'test_reference_field' => [
        'entity' => $entity,
      ],
    ]);
    $dependent->save();
    return $dependent;
  }

}
