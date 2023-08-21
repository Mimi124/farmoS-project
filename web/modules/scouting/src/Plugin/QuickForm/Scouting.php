<?php

namespace Drupal\farm_quick_scouting\Plugin\QuickForm;

use Drupal\Core\Datetime\DrupalDateTime;
use Drupal\Core\Form\FormStateInterface;
use Drupal\Core\Entity\EntityInterface;
use Drupal\Core\Session\AccountInterface;
use Drupal\farm_quick\Plugin\QuickForm\QuickFormBase;
use Drupal\farm_quick\Traits\QuickFormElementsTrait;
use Drupal\farm_quick\Traits\QuickLogTrait;
use Drupal\farm_quick\Traits\QuickStringTrait;
use Drupal\farm_quick\Traits\QuickAssetTrait;
use Drupal\farm_location\AssetLocationInterface;
use Drupal\Core\Entity\EntityTypeManagerInterface;
use Drupal\Core\Messenger\MessengerInterface;
use Psr\Container\ContainerInterface;


/**
 * Scouting quick form.
 *
 * @QuickForm(
 *   id = "scouting",
 *   label = @Translation("Scouting"),
 *   description = @Translation("Record a field observation."),
 *   helpText = @Translation("Use this form to record field observations. A new observation log will be created tied to land asset records."),
 *   permissions = {
 *     "create observation log",
 *   }
 * )
 */
class Scouting extends QuickFormBase {

  use QuickAssetTrait;
  use QuickLogTrait;
  use QuickFormElementsTrait;
  use QuickStringTrait;

  /**
   * The entity type manager service.
   *
   * @var \Drupal\Core\Entity\EntityTypeManagerInterface
   */
  protected $entityTypeManager;


  /**
   * Asset location service.
   *
   * @var \Drupal\farm_location\AssetLocationInterface
   */
  protected $assetLocation;

  /**
   * Current user object.
   *
   * @var \Drupal\Core\Session\AccountInterface
   */
  protected $currentUser;

  /**
   * Constructs a QuickFormBase object.
   *
   * @param array $configuration
   *   A configuration array containing information about the plugin instance.
   * @param string $plugin_id
   *   The plugin_id for the plugin instance.
   * @param mixed $plugin_definition
   *   The plugin implementation definition.
   * @param \Drupal\Core\Messenger\MessengerInterface $messenger
   *   The messenger service.
   * @param \Drupal\Core\Entity\EntityTypeManagerInterface $entity_type_manager
   *   The entity type manager service.
   * @param \Drupal\farm_location\AssetLocationInterface $asset_location
   *   Asset location service.
   * @param \Drupal\Core\Session\AccountInterface $current_user
   *   Current user object.
   */


   public function __construct(array $configuration, $plugin_id, $plugin_definition, MessengerInterface $messenger, EntityTypeManagerInterface $entity_type_manager, AssetLocationInterface $asset_location, AccountInterface $current_user) {
    parent::__construct($configuration, $plugin_id, $plugin_definition, $messenger);
    $this->messenger = $messenger;
    $this->entityTypeManager = $entity_type_manager;
    $this->assetLocation = $asset_location;
    $this->currentUser = $current_user;
  }

  /**
   * {@inheritdoc}
   */

   public static function create(ContainerInterface $container, array $configuration, $plugin_id, $plugin_definition) {
    return new static(
      $configuration,
      $plugin_id,
      $plugin_definition,
      $container->get('messenger'),
      $container->get('entity_type.manager'),
      $container->get('asset.location'),
      $container->get('current_user'),
    );
  }

  /**
   * {@inheritdoc}
   */

  public function buildForm(array $form, FormStateInterface $form_state, string $id = NULL) {

    // Date of observation.
    $form['date'] = [
      '#type' => 'datetime',
      '#title' => $this->t('Date of observation'),
      '#default_value' => new DrupalDateTime('midnight', $this->currentUser->getTimeZone()),
      '#required' => TRUE,
    ];

    // Assets.
    $form['asset'] = [
      '#type' => 'entity_autocomplete',
      '#title' => $this->t('Assets'),
      '#description' => $this->t('Which assets are being observed?'),
      '#target_type' => 'asset',
      '#selection_settings' => [
        'sort' => [
          'field' => 'status',
          'direction' => 'ASC',
        ],
      ],
      '#maxlength' => 1024,
      '#tags' => TRUE,
      '#required' => TRUE,
      '#ajax' => [
        'callback' => [$this, 'assetGeometryCallback'],
        'wrapper' => 'asset-geometry',
        'event' => 'autocompleteclose change',
      ],
    ];

            
    // Locations.
    $form['location'] = [
        '#type' => 'entity_autocomplete',
        '#title' => $this->t('Locations'),
        '#description' => $this->t('Where is the assets located ?'),
        '#target_type' => 'asset',
        '#selection_handler' => 'views',
        '#selection_settings' => [
        'view' => [
            'view_name' => 'farm_location_reference',
            'display_name' => 'entity_reference',
            'arguments' => [],
        ],
        'match_operator' => 'CONTAINS',
        ],
        '#maxlength' => 1024,
        '#tags' => TRUE,
        '#ajax' => [
        'callback' => [$this, 'locationGeometryCallback'],
        'wrapper' => 'location-geometry',
        'event' => 'autocompleteclose change',
        ],
    ];



    // Geometry.
    $form['geometry'] = [
        '#type' => 'farm_map_input',
        '#title' => $this->t('Geometry'),
        '#description' => $this->t('The current geometry of the assets is blue. The new geometry is orange. It is copied from the locations selected above, and can be modified to give the assets a more specific geometry.'),
        '#behaviors' => [
        'quick_movement',
        ],
        '#display_raw_geometry' => TRUE,
    ];
      

       // Hidden fields to store asset and location geometry.
       $form['asset_geometry_wrapper'] = [
        '#type' => 'container',
        '#attributes' => [
          'id' => 'asset-geometry',
          'data-movement-geometry' => 'asset-geometry',
        ],
        'asset_geometry' => [
          '#type' => 'hidden',
          '#value' => $this->combinedAssetGeometries($this->loadEntityAutocompleteAssets($form_state->getValue('asset'))),
        ],
      ];
      $form['location_geometry_wrapper'] = [
        '#type' => 'container',
        '#attributes' => [
          'id' => 'location-geometry',
          'data-movement-geometry' => 'location-geometry',
        ],
        'location_geometry' => [
          '#type' => 'hidden',
          '#value' => $this->combinedAssetGeometries($this->loadEntityAutocompleteAssets($form_state->getValue('location'))),
        ],
      ];

    // Notes.
    $form['notes'] = [
      '#type' => 'details',
      '#title' => $this->t('Notes'),
    ];
    $form['notes']['notes'] = [
      '#type' => 'text_format',
      '#title' => $this->t('Notes'),
      '#title_display' => 'invisible',
      '#format' => 'default',
    ];

    // Done.
    $form['done'] = [
      '#type' => 'checkbox',
      '#title' => $this->t('Completed'),
      '#default_value' => TRUE,
    ];

    

    return $form;
  }
/**
   * Ajax callback for asset geometry field.
   */
  public function assetGeometryCallback(array $form, FormStateInterface $form_state) {
    return $form['asset_geometry_wrapper'];
  }
/**
   * Ajax callback for location geometry field.
   */
  public function locationGeometryCallback(array $form, FormStateInterface $form_state) {
    return $form['location_geometry_wrapper'];
  }
   

  
  /**
   * Load assets from entity_autocomplete values.
   *
   * @param array|null $values
   *   The value from $form_state->getValue().
   *
   * @return \Drupal\asset\Entity\AssetInterface[]
   *   Returns an array of assets.
   */
  protected function loadEntityAutocompleteAssets($values) {
    $entities = [];
    if (empty($values)) {
      return $entities;
    }
    foreach ($values as $value) {
      if ($value instanceof EntityInterface) {
        $entities[] = $value;
      }
      elseif (!empty($value['target_id'])) {
        $entities[] = $this->entityTypeManager->getStorage('asset')->load($value['target_id']);
      }
    }
    return $entities;
  }

  /**
   * Load combined WKT geometry of assets.
   *
   * @param array $assets
   *   An array of assets.
   *
   * @return string
   *   Returns a WKT geometry string.
   */
  protected function combinedAssetGeometries(array $assets) {
    if (empty($assets)) {
      return '';
    }
    $geometries = [];
    foreach ($assets as $asset) {
      $geometries[] = $this->assetLocation->getGeometry($asset);
    }
    return $this->combineWkt($geometries);
  }

  /**
   * {@inheritdoc}
   */
  public function validateForm(array &$form, FormStateInterface $form_state) {

    // Validate that a geometry is only present if a location is specified.
    if (empty($form_state->getValue('location')) && !empty($form_state->getValue('geometry'))) {
        $form_state->setError($form['geometry'], $this->t('A geometry cannot be set if there is no location.'));
    }
    
  }

  /**
   * {@inheritdoc}
   */
  public function submitForm(array &$form, FormStateInterface $form_state) {

    // Get the observationdate.
    /** @var \Drupal\Core\Datetime\DrupalDateTime $observationdate */
    $observationdate = $form_state->getValue('date');

       // Draft an observation log from the user-submitted data.
       $timestamp = $form_state->getValue('date')->getTimestamp();
       $status = $form_state->getValue('done') ? 'done' : 'pending';
       $log = [
         'type' => 'observation',
         'timestamp' => $timestamp,
         'asset' => $form_state->getValue('asset'),
         'location' => $form_state->getValue('location'),
         'geometry' => $form_state->getValue('geometry'),
         'notes' => $form_state->getValue('notes'),
         'status' => $status,
         'is_movement' => FALSE,
       ];
   
       // Load assets and locations.
       $assets = $this->loadEntityAutocompleteAssets($form_state->getValue('asset'));
       $locations = $this->loadEntityAutocompleteAssets($form_state->getValue('location'));
   
       // Generate a name for the log.
       $asset_names = $this->entityLabelsSummary($assets);
       $location_names = $this->entityLabelsSummary($locations);
       $log['name'] = $this->t('Clear location of @assets', ['@assets' => $asset_names]);
       if (!empty($location_names)) {
         $log['name'] = $this->t('Move @assets to @locations', ['@assets' => $asset_names, '@locations' => $location_names]);
       }
   
     
   // Create the log.
   $this->createLog($log);

    }

  
  

}
