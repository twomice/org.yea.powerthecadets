<?php

use CRM_Powerthecadets_ExtensionUtil as E;

class CRM_Powerthecadets_Page_Contribute_Powerthecadets extends CRM_Core_Page {

  public function run() {
    // Example: Set the page-title dynamically; alternatively, declare a static title in xml/Menu/*.xml
    // CRM_Utils_System::setTitle(E::ts('Contribute_Powerthecadets'));
    $config = _powerthecadets_get_setting('config');
    // Only if we have powerthecadets config for this contribution page.
    $contribution_page_id = CRM_Utils_Array::value('id', $_REQUEST);
    $error_settings = array();
    if ($contribution_page_config = CRM_Utils_Array::value($contribution_page_id, $config['contribution_pages'])) {
      if ($calendar_url = CRM_Utils_Array::value('calendar_url', $contribution_page_config)) {
        $this->assign('calendar_url', $calendar_url);
      }
      else {
        $this->assign('calendar_url', E::ts('Error: Not defined. Redirection will not be performed.'));
        $error_settings['calendar_url'] = TRUE;
      }

      if ($meal_price_field_id = CRM_Utils_Array::value('meal_price_field_id', $contribution_page_config)) {
        $result = civicrm_api3('PriceField', 'get', array(
          'sequential' => 1,
          'id' => $meal_price_field_id,
          'api.PriceSet.get' => array(
            'return' => 'title',
          ),
        ));
        if (empty($result['values'][0])) {
          $meal_price_field = E::ts(
            'Error: Set to value %1, which is not a valid price field. Neither redirection nor automatic update will be performed.',
            array(
              1 => $meal_price_field_id,
            )
          );
          $error_settings['meal_price_field'] = TRUE;
        }
        else {
          $meal_price_field = $result['values'][0]['api.PriceSet.get']['values'][0]['title'] . '::' . $result['values'][0]['label'];
        }
      }
      else {
        $meal_price_field = E::ts('Error: Not defined. Neither redirection nor automatic update will be performed.');
        $error_settings['meal_price_field'] = TRUE;
      }
      $this->assign('meal_price_field', $meal_price_field);

      if ($calendar_options = CRM_Utils_Array::value('calendar_options', $contribution_page_config, array())) {
        $calendar_option_labels = array();
        foreach ($calendar_options as $calendar_option_id => $calendar_option_label) {
          $result = civicrm_api3('PriceFieldValue', 'get', array(
            'sequential' => 1,
            'id' => $calendar_option_id,
          ));
          $calendar_option_labels[] = "$calendar_option_id: \"{$result['values'][0]['label']}\" (auto-updates as \"$calendar_option_label\")";
        }
      }
      else {
        $calendar_option_labels = E::ts('Error: Not defined. All options will be hidden with the <em>nocalendar</em> parameter; automatic update will not be performed.');
        $error_settings['calendar_option_labels'] = TRUE;
      }
      $this->assign('calendar_option_labels', $calendar_option_labels);

      if ($date_custom_field_id = CRM_Utils_Array::value('date_custom_field_id', $contribution_page_config)) {
        $result = civicrm_api3('CustomField', 'get', array(
          'sequential' => 1,
          'id' => $date_custom_field_id,
          'api.CustomGroup.get' => array(
            'return' => 'title',
          ),
        ));
        if (empty($result['values'][0])) {
          $meal_date_custom_field = E::ts(
            'Error: Set to value %1, which is not a valid custom field. Neither redirection nor automatic update will be performed.',
            array(
              1 => $date_custom_field_id,
            )
          );
          $error_settings['meal_date_custom_field'] = TRUE;
        }
        else {
          $meal_date_custom_field = $result['values'][0]['api.CustomGroup.get']['values'][0]['title'] . '::' . $result['values'][0]['label'];
        }
      }
      else {
        $meal_date_custom_field = E::ts('Error: Not defined. Neither redirection nor automatic update will be performed.');
        $error_settings['meal_date_custom_field'] = TRUE;
      }
      $this->assign('meal_date_custom_field', $meal_date_custom_field);

      if ($message_custom_field_id = CRM_Utils_Array::value('message_custom_field_id', $contribution_page_config)) {
        $result = civicrm_api3('CustomField', 'get', array(
          'sequential' => 1,
          'id' => $message_custom_field_id,
          'api.CustomGroup.get' => array(
            'return' => 'title',
          ),
        ));
        if (empty($result['values'][0])) {
          $message_custom_field = E::ts(
            'Error: Set to value %1, which is not a valid custom field. Message will not be saved in automatic update.',
            array(
              1 => $message_custom_field_id,
            )
          );
          $error_settings['message_custom_field'] = TRUE;
        }
        else {
          $message_custom_field = $result['values'][0]['api.CustomGroup.get']['values'][0]['title'] . '::' . $result['values'][0]['label'];
        }
      }
      else {
        $message_custom_field = E::ts('Error: Not defined. Message will not be saved in automatic update.');
        $error_settings['message_custom_field'] = TRUE;
      }
      $this->assign('message_custom_field', $message_custom_field);

      if ($donor_custom_field_id = CRM_Utils_Array::value('donor_custom_field_id', $contribution_page_config)) {
        $result = civicrm_api3('CustomField', 'get', array(
          'sequential' => 1,
          'id' => $donor_custom_field_id,
          'api.CustomGroup.get' => array(
            'return' => 'title',
          ),
        ));
        if (empty($result['values'][0])) {
          $donor_custom_field = E::ts(
            'Error: Set to value %1, which is not a valid custom field. Donor will not be saved in automatic update.',
            array(
              1 => $donor_custom_field_id,
            )
          );
          $error_settings['donor_custom_field'] = TRUE;
        }
        else {
          $donor_custom_field = $result['values'][0]['api.CustomGroup.get']['values'][0]['title'] . '::' . $result['values'][0]['label'];
        }
      }
      else {
        $donor_custom_field = E::ts('Error: Not defined. Donor will not be saved in automatic update.');
        $error_settings['donor_custom_field'] = TRUE;
      }
      $this->assign('donor_custom_field', $donor_custom_field);
    }
    else {
      $this->assign('noconfig', 1);
    }

    $this->assign('error_settings', $error_settings);

    // Assign README.html URL for access to docs.
    $resource = CRM_Core_Resources::singleton();
    $this->assign('readme_url', $resource->getUrl('org.yea.powerthecadets', 'README.html'));

    parent::run();
  }

}
