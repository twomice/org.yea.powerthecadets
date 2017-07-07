<?php

require_once 'powerthecadets.civix.php';

/**
 * Implements hook_civicrm_buildForm().
 *
 * @link http://wiki.civicrm.org/confluence/display/CRMDOC/hook_civicrm_buildForm
 */
function powerthecadets_civicrm_buildForm($formName, $form) {
  if ($formName == 'CRM_Contribute_Form_Contribution_Main') {
    // Add a hidden field to store the value of any nocalendar parameter; this
    // hidden field value will persist in the form. We have to define the field 
    // on every buildForm run, but we only set the value when we want to (see
    // below).
    $form->addElement('hidden', 'nocalendar');
    $config = _powerthecadets_get_setting('config');
    // Only if we have powerthecadets config for this contribution page.
    if (!empty($contribution_page_config = $config['contribution_pages'][$form->_id])) {
      // Only if we're at the first form opening.
      if (empty($form->_submitValues)) {
        // Set the value of the "nocalendar" hidden field. We only do this on
        // the very first opening of the form, and then that value will persist
        // for the life of the form.
        $form->setDefaults(array('nocalendar' => !empty($_GET['nocalendar'])));
        // Only if we don't have the "nocalendar" query parameter, determine 
        // whether we need to redirect.
        if (empty($_GET['nocalendar'])) {
          $date_custom_field_id = CRM_Utils_Array::value('date_custom_field_id', $contribution_page_config);
          $meal_price_field_id = CRM_Utils_Array::value('meal_price_field_id', $contribution_page_config);

          // Skip this whole thing if the correct config is not set.
          if(
            // custom date field ID is undefined.
            empty($date_custom_field_id) 
            // meal price field ID is undefined.
            || empty($meal_price_field_id) 
            // defined meal price field ID doesn't match a price field in the form.
            || empty($form->_priceSet['fields'][$meal_price_field_id])
            // defined custom date field ID doesn't match a field in the form.
            || empty($form->_fields['custom_'. $date_custom_field_id])
          ) {
            return;
          }
          
          $default_date = CRM_Utils_Array::value('custom_' . $date_custom_field_id, $form->_defaultValues);
          $default_meal = CRM_Utils_Array::value('price_' . $meal_price_field_id, $form->_defaultValues);
          $valid_meal_option_ids = array_keys($form->_priceSet['fields'][$meal_price_field_id]['options']);
          if (!in_array($default_meal, $valid_meal_option_ids)) {
            // If the preset meal value isn't one of the available options, 
            // it doesn't count, so unset it.
            $default_meal = NULL;
          }
          if (empty($default_date) || empty($default_meal) && !empty($contribution_page_config['calendar_url'])) {
            CRM_Utils_System::redirect($contribution_page_config['calendar_url']);
          }
        }
      }
    }
  }
}

/**
 * Implements hook_civicrm_buildAmount().
 */
function powerthecadets_civicrm_buildAmount($pageType, &$form, &$amount) {
  $config = _powerthecadets_get_setting('config');
  // Only if we have powerthecadets config for this contribution page, and the nocalendar param has been set for this form.
  if (!empty($contribution_page_config = $config['contribution_pages'][$form->_id]) && _powerthecadets_is_nocalendar($form)) {
    // Since we have the nocalendar param, remove all "nocalendar_blocked" 
    // options from the meal price field.
    $nocalendar_blocked_options = CRM_Utils_Array::value('nocalendar_blocked_options', $contribution_page_config, array());
    foreach ($amount[$contribution_page_config['meal_price_field_id']]['options'] as $option_id => $option) {
      if (in_array($option_id, $nocalendar_blocked_options)) {
        unset($amount[$contribution_page_config['meal_price_field_id']]['options'][$option_id]);
      }
    }
  }
}

function _powerthecadets_is_nocalendar($form) {
  return (!empty($_GET['nocalendar']) || !empty($form->_submitValues['nocalendar']));
}

/**
 * Implements hook_civicrm_config().
 *
 * @link http://wiki.civicrm.org/confluence/display/CRMDOC/hook_civicrm_config
 */
function powerthecadets_civicrm_config(&$config) {
  _powerthecadets_civix_civicrm_config($config);
}

/**
 * Implements hook_civicrm_xmlMenu().
 *
 * @link http://wiki.civicrm.org/confluence/display/CRMDOC/hook_civicrm_xmlMenu
 */
function powerthecadets_civicrm_xmlMenu(&$files) {
  _powerthecadets_civix_civicrm_xmlMenu($files);
}

/**
 * Implements hook_civicrm_install().
 *
 * @link http://wiki.civicrm.org/confluence/display/CRMDOC/hook_civicrm_install
 */
function powerthecadets_civicrm_install() {
  _powerthecadets_civix_civicrm_install();
}

/**
 * Implements hook_civicrm_postInstall().
 *
 * @link http://wiki.civicrm.org/confluence/display/CRMDOC/hook_civicrm_postInstall
 */
function powerthecadets_civicrm_postInstall() {
  _powerthecadets_civix_civicrm_postInstall();
}

/**
 * Implements hook_civicrm_uninstall().
 *
 * @link http://wiki.civicrm.org/confluence/display/CRMDOC/hook_civicrm_uninstall
 */
function powerthecadets_civicrm_uninstall() {
  _powerthecadets_civix_civicrm_uninstall();
}

/**
 * Implements hook_civicrm_enable().
 *
 * @link http://wiki.civicrm.org/confluence/display/CRMDOC/hook_civicrm_enable
 */
function powerthecadets_civicrm_enable() {
  _powerthecadets_civix_civicrm_enable();
}

/**
 * Implements hook_civicrm_disable().
 *
 * @link http://wiki.civicrm.org/confluence/display/CRMDOC/hook_civicrm_disable
 */
function powerthecadets_civicrm_disable() {
  _powerthecadets_civix_civicrm_disable();
}

/**
 * Implements hook_civicrm_upgrade().
 *
 * @link http://wiki.civicrm.org/confluence/display/CRMDOC/hook_civicrm_upgrade
 */
function powerthecadets_civicrm_upgrade($op, CRM_Queue_Queue $queue = NULL) {
  return _powerthecadets_civix_civicrm_upgrade($op, $queue);
}

/**
 * Implements hook_civicrm_managed().
 *
 * Generate a list of entities to create/deactivate/delete when this module
 * is installed, disabled, uninstalled.
 *
 * @link http://wiki.civicrm.org/confluence/display/CRMDOC/hook_civicrm_managed
 */
function powerthecadets_civicrm_managed(&$entities) {
  _powerthecadets_civix_civicrm_managed($entities);
}

/**
 * Implements hook_civicrm_caseTypes().
 *
 * Generate a list of case-types.
 *
 * Note: This hook only runs in CiviCRM 4.4+.
 *
 * @link http://wiki.civicrm.org/confluence/display/CRMDOC/hook_civicrm_caseTypes
 */
function powerthecadets_civicrm_caseTypes(&$caseTypes) {
  _powerthecadets_civix_civicrm_caseTypes($caseTypes);
}

/**
 * Implements hook_civicrm_angularModules().
 *
 * Generate a list of Angular modules.
 *
 * Note: This hook only runs in CiviCRM 4.5+. It may
 * use features only available in v4.6+.
 *
 * @link http://wiki.civicrm.org/confluence/display/CRMDOC/hook_civicrm_angularModules
 */
function powerthecadets_civicrm_angularModules(&$angularModules) {
  _powerthecadets_civix_civicrm_angularModules($angularModules);
}

/**
 * Implements hook_civicrm_alterSettingsFolders().
 *
 * @link http://wiki.civicrm.org/confluence/display/CRMDOC/hook_civicrm_alterSettingsFolders
 */
function powerthecadets_civicrm_alterSettingsFolders(&$metaDataFolders = NULL) {
  _powerthecadets_civix_civicrm_alterSettingsFolders($metaDataFolders);
}

// --- Functions below this ship commented out. Uncomment as required. ---

/**
 * Implements hook_civicrm_preProcess().
 *
 * @link http://wiki.civicrm.org/confluence/display/CRMDOC/hook_civicrm_preProcess
 *
function powerthecadets_civicrm_preProcess($formName, &$form) {

} // */

/**
 * Implements hook_civicrm_navigationMenu().
 *
 * @link http://wiki.civicrm.org/confluence/display/CRMDOC/hook_civicrm_navigationMenu
 *
function powerthecadets_civicrm_navigationMenu(&$menu) {
  _powerthecadets_civix_insert_navigation_menu($menu, NULL, array(
    'label' => ts('The Page', array('domain' => 'org.yea.powerthecadets')),
    'name' => 'the_page',
    'url' => 'civicrm/the-page',
    'permission' => 'access CiviReport,access CiviContribute',
    'operator' => 'OR',
    'separator' => 0,
  ));
  _powerthecadets_civix_navigationMenu($menu);
} // */

/**
 * Get the value of the given config setting.
 */
function _powerthecadets_get_setting($name) {
  // If this is the  first time, prime a $settings array with the default values,
  // overridden with any values found by CRM_Core_BAO_Setting::getItem().
  static $settings = array();
  if (empty($settings)) {
    $defaults = array(); // No defaults yet; add them here later if needed.

    foreach ($defaults as $key => $value) {
      $config_value = CRM_Core_BAO_Setting::getItem('org.yea.powerthecadets', $key);
      if (!is_null($config_value)) {
        $settings[$key] = $config_value;
      }
    }
    $settings = array_replace_recursive($defaults, $settings);

    // If the setting is still unset, set it from CRM_Core_BAO_Setting::getItem().
    if (!array_key_exists($name, $settings)) {
      $settings[$name] = CRM_Core_BAO_Setting::getItem('org.yea.powerthecadets', $name);
    }
  }

  return $settings[$name];
}
