<?php

namespace Drupal\farm_quick_plant_protection\Plugin\Action;

use Drupal\farm_quick\Plugin\Action\QuickFormActionBase;

/**
 * Action for recording plant protection activity.
 *
 * @Action(
 *   id = "plant_protect",
 *   label = @Translation("Record Plant Protection Activities"),
 *   type = "asset",
 *   confirm_form_route_name = "farm.quick.plant_protection"
 * )
 */
class PlantProtect extends QuickFormActionBase {

  /**
   * {@inheritdoc}
   */
  public function getQuckFormId(): string {
    return 'plant_protection';
  }

}