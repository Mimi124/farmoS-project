<?php

namespace Drupal\Tests\taxonomy\Functional;

use Drupal\Component\Render\FormattableMarkup;
use Drupal\Core\Field\FieldStorageDefinitionInterface;
use Drupal\Core\Render\BubbleableMetadata;

/**
 * Tests taxonomy token replacement.
 *
 * @group taxonomy
 */
class TokenReplaceTest extends TaxonomyTestBase {

  /**
   * The vocabulary used for creating terms.
   *
   * @var \Drupal\taxonomy\VocabularyInterface
   */
  protected $vocabulary;

  /**
   * {@inheritdoc}
   */
  protected static $modules = ['node'];

  /**
   * Name of the taxonomy term reference field.
   *
   * @var string
   */
  protected $fieldName;

  /**
   * {@inheritdoc}
   */
  protected $defaultTheme = 'stark';

  /**
   * {@inheritdoc}
   */
  protected function setUp(): void {
    parent::setUp();
    $this->drupalLogin($this->drupalCreateUser([
      'administer taxonomy',
      'bypass node access',
    ]));
    $this->vocabulary = $this->createVocabulary();
    $this->fieldName = 'taxonomy_' . $this->vocabulary->id();

    $handler_settings = [
      'target_bundles' => [
        $this->vocabulary->id() => $this->vocabulary->id(),
      ],
      'auto_create' => TRUE,
    ];
    $this->createEntityReferenceField('node', 'article', $this->fieldName, NULL, 'taxonomy_term', 'default', $handler_settings, FieldStorageDefinitionInterface::CARDINALITY_UNLIMITED);

    /** @var \Drupal\Core\Entity\EntityDisplayRepositoryInterface $display_repository */
    $display_repository = \Drupal::service('entity_display.repository');
    $display_repository->getFormDisplay('node', 'article')
      ->setComponent($this->fieldName, [
        'type' => 'options_select',
      ])
      ->save();
    $display_repository->getViewDisplay('node', 'article')
      ->setComponent($this->fieldName, [
        'type' => 'entity_reference_label',
      ])
      ->save();
  }

  /**
   * Creates some terms and a node, then tests the tokens generated from them.
   */
  public function testTaxonomyTokenReplacement() {
    $token_service = \Drupal::token();
    $language_interface = \Drupal::languageManager()->getCurrentLanguage();

    // Create two taxonomy terms.
    $term1 = $this->createTerm($this->vocabulary);
    $term2 = $this->createTerm($this->vocabulary);

    // Edit $term2, setting $term1 as parent.
    $edit = [];
    $edit['name[0][value]'] = '<blink>Blinking Text</blink>';
    $edit['parent[]'] = [$term1->id()];
    $this->drupalGet('taxonomy/term/' . $term2->id() . '/edit');
    $this->submitForm($edit, 'Save');

    // Create node with term2.
    $edit = [];
    $node = $this->drupalCreateNode(['type' => 'article']);
    $edit[$this->fieldName . '[]'] = $term2->id();
    $this->drupalGet('node/' . $node->id() . '/edit');
    $this->submitForm($edit, 'Save');

    // Generate and test sanitized tokens for term1.
    $tests = [];
    $tests['[term:tid]'] = $term1->id();
    $tests['[term:name]'] = $term1->getName();
    $tests['[term:description]'] = $term1->description->processed;
    $tests['[term:url]'] = $term1->toUrl('canonical', ['absolute' => TRUE])->toString();
    $tests['[term:node-count]'] = 0;
    $tests['[term:parent:name]'] = '[term:parent:name]';
    $tests['[term:vocabulary:name]'] = $this->vocabulary->label();
    $tests['[term:vocabulary]'] = $this->vocabulary->label();

    $base_bubbleable_metadata = BubbleableMetadata::createFromObject($term1);

    $metadata_tests = [];
    $metadata_tests['[term:tid]'] = $base_bubbleable_metadata;
    $metadata_tests['[term:name]'] = $base_bubbleable_metadata;
    $metadata_tests['[term:description]'] = $base_bubbleable_metadata;
    $metadata_tests['[term:url]'] = $base_bubbleable_metadata;
    $metadata_tests['[term:node-count]'] = $base_bubbleable_metadata;
    $metadata_tests['[term:parent:name]'] = $base_bubbleable_metadata;
    $bubbleable_metadata = clone $base_bubbleable_metadata;
    $metadata_tests['[term:vocabulary:name]'] = $bubbleable_metadata->addCacheTags($this->vocabulary->getCacheTags());
    $metadata_tests['[term:vocabulary]'] = $bubbleable_metadata->addCacheTags($this->vocabulary->getCacheTags());

    foreach ($tests as $input => $expected) {
      $bubbleable_metadata = new BubbleableMetadata();
      $output = $token_service->replace($input, ['term' => $term1], ['langcode' => $language_interface->getId()], $bubbleable_metadata);
      $this->assertEquals($expected, $output, new FormattableMarkup('Sanitized taxonomy term token %token replaced.', ['%token' => $input]));
      $this->assertEquals($metadata_tests[$input], $bubbleable_metadata);
    }

    // Generate and test sanitized tokens for term2.
    $tests = [];
    $tests['[term:tid]'] = $term2->id();
    $tests['[term:name]'] = $term2->getName();
    $tests['[term:description]'] = $term2->description->processed;
    $tests['[term:url]'] = $term2->toUrl('canonical', ['absolute' => TRUE])->toString();
    $tests['[term:node-count]'] = 1;
    $tests['[term:parent:name]'] = $term1->getName();
    $tests['[term:parent:url]'] = $term1->toUrl('canonical', ['absolute' => TRUE])->toString();
    $tests['[term:parent:parent:name]'] = '[term:parent:parent:name]';
    $tests['[term:vocabulary:name]'] = $this->vocabulary->label();

    // Test to make sure that we generated something for each token.
    $this->assertNotContains(0, array_map('strlen', $tests), 'No empty tokens generated.');

    foreach ($tests as $input => $expected) {
      $output = $token_service->replace($input, ['term' => $term2], ['langcode' => $language_interface->getId()]);
      $this->assertEquals($expected, $output, new FormattableMarkup('Sanitized taxonomy term token %token replaced.', ['%token' => $input]));
    }

    // Generate and test sanitized tokens.
    $tests = [];
    $tests['[vocabulary:vid]'] = $this->vocabulary->id();
    $tests['[vocabulary:name]'] = $this->vocabulary->label();
    $tests['[vocabulary:description]'] = $this->vocabulary->getDescription();
    $tests['[vocabulary:node-count]'] = 1;
    $tests['[vocabulary:term-count]'] = 2;

    // Test to make sure that we generated something for each token.
    $this->assertNotContains(0, array_map('strlen', $tests), 'No empty tokens generated.');

    foreach ($tests as $input => $expected) {
      $output = $token_service->replace($input, ['vocabulary' => $this->vocabulary], ['langcode' => $language_interface->getId()]);
      $this->assertEquals($expected, $output, new FormattableMarkup('Sanitized taxonomy vocabulary token %token replaced.', ['%token' => $input]));
    }
  }

}
