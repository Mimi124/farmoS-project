<?php
namespace Drupal\farm_quick_plant_protection\Plugin\QuickForm;



use Drupal\Core\Form\FormStateInterface;
use Drupal\farm_quick\Plugin\QuickForm\QuickFormBase;
use Drupal\farm_quick\Traits\QuickLogTrait;
use Drupal\Core\Config\ConfigFactoryInterface;
use Drupal\Core\Entity\EntityTypeManagerInterface;
use Drupal\Core\Extension\ModuleHandlerInterface;
use Drupal\Core\Session\AccountInterface;
use Drupal\farm_group\GroupMembershipInterface;
use Drupal\Core\State\StateInterface;
use Drupal\Core\Messenger\MessengerInterface;
use Drupal\farm_quick\Traits\QuickAssetTrait;
use Drupal\farm_quick\Traits\QuickQuantityTrait;
use Drupal\farm_quick\Traits\QuickStringTrait;
use Drupal\farm_quick\Traits\QuickFormElementsTrait;
use Psr\Container\ContainerInterface;
use Drupal\farm_location\AssetLocationInterface;
use Drupal\Core\Datetime\DrupalDateTime;





/**
 * Plant Protection quick form.
 *
 * @QuickForm(
 *   id = "plant_protection",
 *   label = @Translation("Plant Protection"),
 *   description = @Translation("Record a Plant Protection."),
 *   helpText = @Translation("This form will create a plant protection asset"),
 *   permissions = {
 *     "create plant protection asset",
 *   }
 * )
 *
 * @internal
 */

 class PlantProtection extends QuickFormBase {

    use QuickAssetTrait;
    use QuickLogTrait;
    use QuickQuantityTrait;
    use QuickStringTrait;
    use QuickFormElementsTrait;
  
    /**
     * The entity type manager service.
     *
     * @var \Drupal\Core\Entity\EntityTypeManagerInterface
     */
    protected $entityTypeManager;
  
    /**
     * The module handler.
     *
     * @var \Drupal\Core\Extension\ModuleHandlerInterface
     */
    protected $moduleHandler;

     /**
     * Current user object.
     *
     * @var \Drupal\Core\Session\AccountInterface
     */
    protected $currentUser;

     /**
     * Asset location service.
     *
     * @var \Drupal\farm_location\AssetLocationInterface
     */
    protected $assetLocation;

     /**
   * The state service.
   *
   * @var \Drupal\Core\State\StateInterface
   */
  protected $state;


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
   * @param \Drupal\Core\Extension\ModuleHandlerInterface $module_handler
   *   The module handler.
   * @param \Drupal\farm_location\AssetLocationInterface $asset_location
   *   Asset location service.
   * @param \Drupal\Core\State\StateInterface $state
   *   The state service.
   * @param \Drupal\Core\Session\AccountInterface $current_user
   *   Current user object.
   */

   public function __construct(array $configuration, $plugin_id, $plugin_definition, MessengerInterface $messenger, EntityTypeManagerInterface $entity_type_manager, ModuleHandlerInterface $module_handler, StateInterface $state, AccountInterface $current_user) {
    parent::__construct($configuration, $plugin_id, $plugin_definition, $messenger);
    $this->messenger = $messenger;
    $this->entityTypeManager = $entity_type_manager;
    $this->moduleHandler = $module_handler;
    $this->state = $state;
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
      $container->get('module_handler'),
      $container->get('state'),
      $container->get('current_user'),
    );
  }

  
  public function buildForm(array $form, FormStateInterface $form_state) {

    // Date of plant protection activity.
    $form['date'] = [
      '#type' => 'datetime',
      '#title' => $this->t('Date of plant protection activity'),
      '#default_value' => new DrupalDateTime('midnight', $this->currentUser->getTimeZone()),
      '#required' => TRUE,
    ];

     // Name of the specific crops/plant species.
     $form['crops'] = [
        '#type' => 'container',
        '#tree' => TRUE,
        '#attributes' => ['id' => 'plant-crops'],
      ];

      //Identity the pest or disease target for production
      $form['pest'] = [
        '#type' => 'container',
        '#title' => $this->t('Pest or Disease Targeted for protection'),
        '#tree' => TRUE,

      ];

      // Create a set of checkboxes to enable log types, based on enabled modules,
      //Protection Method
      $log_type_module = [
        'crop_pesticide' => [
          'log_type' => 'pesticide',
          'label' => $this->t('Pesticide'),
          'default' => TRUE,
        ],
        'crop_herbicide' => [
          'log_type' => 'herbicide',
          'label' => $this->t('Herbicide'),
        ],
        'crop_fungicide' => [
          'log_type' => 'fungicide',
          'label' => $this->t('Fungicide'),
        ],
        'crop_natural_organic' => [
            'log_type' => 'natural_organic',
            'label' => $this->t('Natural / Organic '),
          ],
      ];

      
      $log_type_options = [];
      $log_type_defaults = [];
      foreach ($log_type_module as $module => $option) {
        if ($this->moduleHandler->moduleExists($module) && $this->currentUser->hasPermission('create ' . $option['log_type'] . ' log')) {
          $log_type_options[$option['log_type']] = $option['label'];
          if (!empty($option['default'])) {
            $log_type_defaults[$option['log_type']] = $option['log_type'];
          }
        }
      }
      if (!empty($log_type_options)) {
        $form['log_types'] = [
          '#type' => 'checkboxes',
          '#title' => $this->t('Type of protection method applied?'),
          '#options' => $log_type_options,
          '#default_value' => $log_type_defaults,
          '#ajax' => [
            'callback' => [$this, 'plantLogsCallback'],
            'wrapper' => 'plant-logs',
          ],
        ];
      }

      // Create a set of checkboxes to enable log types, based on enabled modules
      //Application Method
      $log_type_modules = [
        'method_spray' => [
          'log_type' => 'spray',
          'label' => $this->t('Spray'),
          'default' => TRUE,
        ],
        'method_drip' => [
          'log_type' => 'drip',
          'label' => $this->t('Drip'),
        ],
        'method_hand' => [
          'log_type' => 'hand',
          'label' => $this->t('Hand'),
        ],
      ];


      $log_type_options = [];
      $log_type_defaults = [];
      foreach ($log_type_modules as $module => $option) {
        if ($this->moduleHandler->moduleExists($module) && $this->currentUser->hasPermission('create ' . $option['log_type'] . ' log')) {
          $log_type_options[$option['log_type']] = $option['label'];
          if (!empty($option['default'])) {
            $log_type_defaults[$option['log_type']] = $option['log_type'];
          }
        }
      }
      if (!empty($log_type_options)) {
        $form['log_types'] = [
          '#type' => 'checkboxes',
          '#title' => $this->t('What protection method was applied?'),
          '#options' => $log_type_options,
          '#default_value' => $log_type_defaults,
          '#ajax' => [
            'callback' => [$this, 'plantLogsCallback'],
            'wrapper' => 'plant-logs',
          ],
        ];
      }

      // Create a wrapper for logs.
    $form['logs_wrapper'] = [
        '#type' => 'container',
        '#attributes' => ['id' => 'plant-logs'],
      ];
  
      // Create vertical tabs for logs.
      $form['logs_wrapper']['logs'] = [
        '#type' => 'vertical_tabs',
      ];

      // Add log forms that can be created for this plant asset.
    $enabled_logs = array_filter($form_state->getValue('log_types', $log_type_defaults));
    if (in_array('spray', $enabled_logs)) {
      $form['spray'] = [
        '#type' => 'details',
        '#title' => $this->t('Spray'),
        '#group' => 'logs',
        '#tree' => TRUE,
      ];
      $include_fields = ['date', 'location', 'weather', 'quantity', 'notes', 'done'];
      $quantity_applied = ['count', 'length', 'weight', 'area', 'volume', 'ratio'];
      $form['spray'] += $this->buildLogForm('spray', $include_fields, $quantity_applied);
    }
    if (in_array('drip', $enabled_logs)) {
      $form['drip'] = [
        '#type' => 'details',
        '#title' => $this->t('Drip'),
        '#group' => 'logs',
        '#tree' => TRUE,
      ];
      $include_fields = ['date', 'location', 'weather', 'quantity', 'notes', 'done'];
      $quantity_applied = ['count', 'length', 'weight', 'area', 'volume', 'ratio'];
      $form['drip'] += $this->buildLogForm('drip', $include_fields, $quantity_applied);
    }
    if (in_array('hand', $enabled_logs)) {
      $form['hand'] = [
        '#type' => 'details',
        '#title' => $this->t('hand'),
        '#group' => 'logs',
        '#tree' => TRUE,
      ];
      $include_fields = ['date', 'location', 'weather', 'quantity', 'notes', 'done'];
      $quantity_applied = ['count', 'length', 'weight', 'area', 'volume', 'ratio'];
      $form['hand'] += $this->buildLogForm('hand', $include_fields, $quantity_applied);
    }
  

    // Plant Protection asset name.
    // Provide a checkbox to allow customizing this. Otherwise it will be
    // automatically generated on submission.
    $form['custom_name'] = [
        '#type' => 'checkbox',
        '#title' => $this->t('Customize plant protection asset name'),
        '#description' => $this->t('The plant protection asset name will default to "[Location] [Crop]" but can be customized if desired.'),
        '#default_value' => FALSE,
        '#ajax' => [
          'callback' => [$this, 'plantNameCallback'],
          'wrapper' => 'plant-name',
        ],
      ];
      $form['name_wrapper'] = [
        '#type' => 'container',
        '#attributes' => ['id' => 'plant-name'],
      ];
      if ($form_state->getValue('custom_name', FALSE)) {
        $form['name_wrapper']['name'] = [
          '#type' => 'textfield',
          '#title' => $this->t('Plant Protectection asset name'),
          '#maxlength' => 255,
          '#default_value' => $this->generatePlantName($form_state),
          '#required' => TRUE,
        ];
      }
  
      return $form;

    }

  /**
   * Build a simplified log form.
   *
   * @param string $log_type
   *   The log type.
   * @param array $include_fields
   *   Array of fields to include.
   * @param array $quantity_measures
   *   Array of allowed quantity measures.
   *
   * @return array
   *   Returns a Form API array.
   */
  protected function buildLogForm(string $log_type, array $include_fields = [], array $quantity_applied = []) {
    $form = [];

    // Add a hidden value for the log type.
    $form['type'] = [
      '#type' => 'value',
      '#value' => $log_type,
    ];

    // Filter the available quantity measures, if desired.
    $quantity_applied_options = quantity_applied_options();
    $filtered_quantity_applied_options = $quantity_applied_options;
    if (!empty($quantity_applied)) {
      $filtered_quantity_applied_options = [];
      foreach ($quantity_applied as $applied) {
        if (!empty($quantity_applied_options[$applied])) {
          $filtered_quantity_applied_options[$measure] = $quantity_applied_options[$applied];
        }   
    }
 }

  // Create log fields.
  $field_info = [];
  $field_info['date'] = [
    '#type' => 'datetime',
    '#title' => $this->t('Date'),
    '#default_value' => new DrupalDateTime('midnight', $this->currentUser->getTimeZone()),
    '#required' => TRUE,
  ];
  $field_info['done'] = [
    '#type' => 'checkbox',
    '#title' => $this->t('Completed'),
  ];
  $field_info['location'] = [
    '#type' => 'entity_autocomplete',
    '#title' => $this->t('Location'),
    '#description' => $this->t('Where does this take place?'),
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
    '#required' => TRUE,
];
    $field_info['weather'] = [
        '#type' => 'entity_autocomplete',
        '#title' => $this->t('Weather'),
        '#description' => $this->t('Note the weather condition during the application'),
        '#target_type' => 'asset',
        '#selection_handler' => 'views',
        '#selection_settings' => [
          'view' => [
            'view_name' => 'farm_weather_reference',
            'display_name' => 'entity_reference',
            'arguments' => [],
          ],
          'match_operator' => 'CONTAINS',
        ],
        '#required' => TRUE,
  ];
  $field_info['quantity'] = $this->buildInlineContainer();
  $field_info['quantity']['value'] = [
    '#type' => 'textfield',
    '#title' => $this->t('Quantity'),
    '#description' => $this->t('Amount or dosage of the protection used'),
    '#size' => 16,
  ];
  $field_info['quantity']['units'] = [
    '#type' => 'entity_autocomplete',
    '#title' => $this->t('Units'),
    '#target_type' => 'taxonomy_term',
    '#selection_settings' => [
      'target_bundles' => ['unit'],
    ],
    '#autocreate' => [
      'bundle' => 'unit',
    ],
    '#size' => 16,
  ];
  $field_info['quantity']['measure'] = [
    '#type' => 'select',
    '#title' => $this->t('Measure'),
    '#options' => $filtered_quantity_applied_options,
    '#default_value' => 'weight',
  ];
  $field_info['notes'] = [
    '#type' => 'text_format',
    '#title' => $this->t('Notes'),
    '#format' => 'default',
  ];
  foreach ($include_fields as $field) {
    if (array_key_exists($field, $field_info)) {
      $form[$field] = $field_info[$field];
    }
  }

  return $form;
 }

 
protected function generatePlantName(FormStateInterface $form_state) {

  // Get the crop/variety names.
  /** @var \Drupal\taxonomy\TermInterface[] $crops */
  $crops = $form_state->getValue('crops', []);
  $crop_names = [];
  foreach ($crops as $crop) {
    if (is_numeric($crop)) {
      $crop = $this->entityTypeManager->getStorage('taxonomy_term')->load($crop);
    }
    elseif (!empty($crop['entity'])) {
      $crop = $crop['entity'];
    }
    if ($crop instanceof TermInterface) {
      $crop_names[] = $crop->label();
    }
  }

  // Get the location name.
  // The "final" location of the plant is assumed to be the transplanting
  // location (if the transplanting module is enabled). If a transplanting is
  // not being created, but a seeding is, then use the seeding location.
  $location_keys = [
    ['spray', 'location'],
    ['drip', 'location'],
    ['hand', 'location'],
  ];
  $location_name = '';
  foreach ($location_keys as $key) {
    if ($form_state->hasValue($key)) {
      $location_id = $form_state->getValue($key);
      if (!empty($location_id)) {
        $location = $this->entityTypeManager->getStorage('asset')->load($location_id);
        if (!empty($location)) {
          $location_name = $location->label();
        }
      }
    }
  }

    // Generate the plant name, giving priority to the seasons and crops.
    $name_parts = [
        'crops' => implode(', ', $crop_names),
      ];
      $priority_keys = ['crops'];
      return $this->prioritizedString($name_parts, $priority_keys);
    }
 

    public function submitForm(array &$form, FormStateInterface $form_state) {

        // If a custom plant name was provided, use that. Otherwise generate one.
        $plant_name = $this->generatePlantName($form_state);
        if (!empty($form_state->getValue('custom_name', FALSE)) && $form_state->hasValue('name')) {
          $plant_name = $form_state->getValue('name');
        }
    
        // Create a new planting asset.
        $plant_protection_asset = $this->createAsset([
          'type' => 'plant',
          'name' => $plant_name,
          'plant_type' => $form_state->getValue('crops'),
         
        ]);
    
        // Generate logs.
        $log_types = [
          'spray',
          'drip',
          'hand',
        ];
        foreach ($log_types as $log_type) {
    
          // If there are no values for this log type, skip it.
          if (!$form_state->hasValue($log_type)) {
            continue;
          }
    
          // Get the log values.
          $log_values = $form_state->getValue($log_type);
    
          // Name the log based on the type and asset.
          switch ($log_type) {
            case 'spray':
              $log_name = $this->t('Spray @asset', ['@asset' => $plant_protection_asset->label()]);
              break;
    
            case 'drip':
              $log_name = $this->t('Drip @asset', ['@asset' => $plant_protection_asset->label()]);
              break;
    
            case 'hand':
              $log_name = $this->t('Hand @asset', ['@asset' => $plant_protection_asset->label()]);
              break;
          }
    
       
          // Set the log status.
          $status = 'pending';
          if (!empty($log_values['done'])) {
            $status = 'done';
          }
    
          // Create the log.
          $this->createLog([
            'type' => $log_type,
            'name' => $log_name,
            'timestamp' => $log_values['date']->getTimestamp(),
            'asset' => $plant_protection_asset,
            'quantity' => [$this->prepareQuantity($log_values['quantity'])],
            'location' => $log_values['location'] ?? NULL,
            'weather' => $log_values['weather'] ?? NULL,
            'notes' => $log_values['notes'] ?? NULL,
            'status' => $status,
          ]);
        }
      }

      
  /**
   * Prepare quantity values for use with createLog() or createQuantity().
   *
   * @param array $values
   *   Quantity field values from the form.
   *
   * @return array
   *   Returns an array to pass into createLog() or createQuantity().
   */
  protected function prepareQuantity(array $values) {

    // If there is no value, return an empty array.
    if (empty($values['value'])) {
      return [];
    }

    // If units is specified, then we need to convert it to units_id, which
    // is expected by createLog() and createQuantity().
    if (!empty($values['units'])) {

      // If units is a numeric value, assume that it is already a term ID.
      // This will be the case when the form value is set programatically
      // (eg: via automated tests).
      if (is_numeric($values['units'])) {
        $values['units_id'] = $values['units'];
        unset($values['units']);
      }

      // Or, if units is an array, and it has either a target_id or entity,
      // translate it to units_id. This will be the case when a term is selected
      // via the UI, when referencing an existing term or creating a new one,
      // respectively.
      elseif (is_array($values['units'])) {

        // If an existing term is selected, target_id will be set.
        if (!empty($values['units']['target_id'])) {
          $values['units_id'] = $values['units']['target_id'];
          unset($values['units']);
        }

        // Or, if a new term is being created, the full entity is available.
        elseif (!empty($values['units']['entity']) && $values['units']['entity'] instanceof TermInterface) {
          $values['units'] = $values['units']['entity'];
        }
      }
    }

    // Return the prepared values.
    return $values;
  }

 /**
   * Ajax callback for crop/variety fields.
   */
  public function plantCropsCallback(array $form, FormStateInterface $form_state) {
    return $form['crops'];
  }

  /**
   * Ajax callback for logs fields.
   */
  public function plantLogsCallback(array $form, FormStateInterface $form_state) {
    return $form['logs_wrapper'];
  }

  /**
   * Ajax callback for plant name field.
   */
  public function plantNameCallback(array $form, FormStateInterface $form_state) {
    return $form['name_wrapper'];
  }





  }






