<?php 

namespace Drupal\farm_quick_plant_protection\Plugin\QuickForm;

use Drupal\Core\Datetime\DrupalDateTime;
use Drupal\Core\Form\FormStateInterface;
use Drupal\Core\Entity\EntityInterface;
use Drupal\Core\Session\AccountInterface;
use Drupal\farm_quick\Plugin\QuickForm\QuickFormBase;
use Drupal\farm_quick\Traits\QuickFormElementsTrait;
use Drupal\farm_quick\Traits\QuickLogTrait;
use Drupal\farm_quick\Traits\QuickStringTrait;
use Drupal\farm_location\AssetLocationInterface;
use Drupal\Core\Entity\EntityTypeManagerInterface;
use Drupal\Core\Messenger\MessengerInterface;
use Psr\Container\ContainerInterface;
// use Drupal\geolocation\Plugin\Field\FieldType\GeolocationFieldItemList;
// use Drupal\commerce_physical\Plugin\Field\FieldType\MeasurementItem;




/**
 * Plant Protection quick form.
 *
 * @QuickForm(
 *   id = "plant_protection",
 *   label = @Translation("Plant Protection"),
 *   description = @Translation("Record plant protection activities."),
 *   helpText = @Translation("This form will create a plant protection asset."),
 *   permissions = {
 *     "create input log",
 *   }
 * )
 */

class PlantProtection extends QuickFormBase {

    use QuickLogTrait;
    use QuickFormElementsTrait;
    use QuickStringTrait;



  


      /**
     * {@inheritdoc}
     */

    
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


    public function buildForm(array $form, FormStateInterface $form_state, string $id = NULL) {

          // Date of plant protection activity.
        $form['date'] = [
            '#type' => 'datetime',
            '#title' => $this->t('Date'),
            '#default_value' => new DrupalDateTime('midnight', $this->currentUser->getTimeZone()),
            '#required' => TRUE,
        ];


      //Plant/Crop
        $form['crop'] = [
            '#type' => 'textfield',
            '#title' => t('Crop/Plant Species'),
            '#description' => $this->t('Type of Crop or Plant Species?'),
            '#required' => TRUE,
          ];

    //Identity the pest or disease target for production
      $form['pest'] = [
        '#type' => 'textfield',
        '#title' => $this->t('Pest or Disease Targeted for protection'),
        '#description' => $this->t('Identify the pest or disease targeted for protection?'),
        '#required' => TRUE,
        
      ];
          

        // Locations.
        $form['location'] = [
            '#type' => 'entity_autocomplete',
            '#title' => $this->t('Locations'),
            '#description' => $this->t('Where is the assets loacted ?'),
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

    

        $form['products_used']= array(
            '#type' => 'checkboxes',
            '#options' => array('pesticide' => $this->t('Pesticide'), 'herbicide' => $this->t('Herbicide'), 
                               'fungicide' => $this->t('Fungicide'), 'natural_organic' => $this->t('Natural / Organic ')),
            '#title' => $this->t('Type of protection method applied?'),
            
          );

        $form['application_method'] = array(
            '#type' => 'checkboxes',
            '#options' => array('spray' => $this->t('Spray'), 'drip' => $this->t('Drip'), 
                               'hand' => $this->t('Hand')),
            '#title' => $this->t('Type of protection method applied?'),
            
          );
      
      
          $form['quantities'] = [
            '#type' => 'tel',
            '#title' => t('Quantities'),
            '#required' => TRUE,
        ];

   
   
    
        return $form;
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
     * {@inheritdoc}
     */

      public function submitForm(array &$form, FormStateInterface $form_state) {

          // Get the submitted values from the form state.
            $timestamp = $form_state->getValue('date')->getTimestamp();
            $log = [
                'type' => 'input',
                'timestamp' => $timestamp,              
                'crop' => $form_state->getValue('crop'),
                'pest' => $form_state->getValue('pest'),
                'location' => $form_state->getValue('location'),
                'application_method' => $form_state->getValue('application_method'),
                'products_used' => $form_state->getValue('products_used'),
                'quantities' => $form_state->getValue('quantities'),
            ];
           
            // Load locations.
            // $crops = $this->loadEntityAutocompleteAssets($form_state->getValue('crop'));
            // $locations = $this->loadEntityAutocompleteAssets($form_state->getValue('location'));
       
            // // Generate a name for the log.
            // $crop_names = $this->entityLabelsSummary($crops);
            // $location_names = $this->entityLabelsSummary($locations);
            // $log['name'] = $this->t('Protected Plants @crops', ['@crops' => $crop_names]);
            // if (!empty($location_names)) {
            // $log['name'] = $this->t('Protected Plant @crops to @locations', ['@crops' => $crop_names, '@locations' => $location_names]);
            // }

              // Display a message to the user after data is saved.
                // drupal_set_message(t('Form submission successful'));

            // Create the log.
             $this->createLog($log);
    }
    

}
