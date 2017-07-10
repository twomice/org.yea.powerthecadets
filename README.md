# Power The Cadets

CiviCRM customizations to support _Power The Cadets_.

## Use case
YEA maintains a _Power The Cadets_ program to sponsor meals for cadets on their summer tour. This program relies on a custom-built calendar to display the meals that have not yet been sponsored, with links to a CiviCRM contribution page with which donors can submit a contribution to sponsor a given meal on a given date.

## Functionality
This extension provides the following functionality, for any contribution page which is appropriately configured (see _Configuration_, below).

### Upon opening the contribution page

Upon opening the contribution page, this extension checks for certain conditions and reacts accordingly, in order to decrease the chance that the contribution page can be used to sponsor a meal that's already sponsored. 

* If the special query parameter `nocalendar=1` is found in the URL, disable meal-related options that should be handled through the calendar. For example, if _Breakfast_, _Lunch_, and _Full Day_ options are configured as calendar options, those options will be hidden, and only the other options (e.g., Cereal, Peanut Butter) will remain available.
* If the query parameters for meal and date are found in the URL, display the form as usual.
* If neither of the above are true, redirect to the calendar at the appropriate URL.

### Upon submitting the contribution

Upon submitting the contribution, this extension automatically makes appropriate updates to the calendar data, in order to immeidately note which meals still remain available for sponsorship.

* If the selected meal is not one of the calendar options, do nothing. For example, _Breakfast_, _Lunch_, and _Full Day_ options may be configured as calendar options, and the contribution form may additionally provide additional options (e.g., Cereal, Peanut Butter); in this example, if the contrubution page is submitted for the _Cereal_ option, no automatic update is performed.
* Otherwise, make the following updates:
  * Locate the record in the table 'powerthecadets' which corresponds to the selected meal and date.
  * Update field 'available' to 0 (not available)
  * Update field 'donor' to the value submitted in the _Name on whiteboard_ profile field (if such field is configured)
  * Update field 'message' to the value submitted in the _Message to members_ profile field (if such field is configured)

## Configuration
Configuration is achieved by adding lines like these to civicrm.settings.php:

```php
global $civicrm_setting;
$civicrm_setting['org.yea.powerthecadets']['config'] = array(
  'contribution_pages' => array(
    N => array(
      'calendar_url' => 'CALENDAR_URL',
      'meal_price_field_id' => MEAL_PRICE_FIELD_ID,
      'date_custom_field_id' => DATE_CUSTOM_FIELD_ID,
      'message_custom_field_id' => MESSAGE_CUSTOM_FIELD_ID,
      'donor_custom_field_id' => DONOR_CUSTOM_FIELD_ID,
      'calendar_options' => OPTIONS_ARRAY,
    ),
  ),
);

```

* `N`: _Integer._ The CiviCRM system ID of the contribution page.
* `CALENDAR_URL`: _String._ The URL of the calendar. Any eedirection to the calendar will send the user to this URL.
* `MEAL_PRICE_FIELD_ID`: _Integer._ The CiviCRM system ID of the price field with which the donor will select the meal (Lunch, Dinner, etc.)
* `DATE_CUSTOM_FIELD_ID`: _Integer._ The CiviCRM system ID of the custom data field with which the donor will indicate the date of the meal.
* `MESSAGE_CUSTOM_FIELD_ID`: _Integer._ The CiviCRM system ID of the custom data field with which the donor will provide their desired custom message to the cadets.
* `DONOR_CUSTOM_FIELD_ID`: _Integer._ The CiviCRM system ID of the custom data field with which the donor will provide a name to be displayed in any public thanks for the meal sponsorship.
* `OPTIONS_ARRAY`: _Array_. An associateive array of meal options which should be handled through the calendar only. Array keys are _Integer_ CiviCRM system IDs of the relevant price options in the MEAL_PRICE_FIELD_ID price field. Array values are _String_ labels which are exactly equal to the corresponding meal as it appears in the calendar. See example below

### Example configuration

Note the comments in the PHP code below, for explanation of the given configuration options.

```php
global $civicrm_setting;
$civicrm_setting['org.yea.powerthecadets']['config'] = array(
  'contribution_pages' => array(
    '5' => array( // The contribution page with id=5 is the one 
    			  // where donations are accepted for this campaign.
                  
      // Any valid URL can be used here. Obviously, check to ensure 
      // that this URL presents a calendar where people can sponsor
      // meals for the campaign.
      'calendar_url' => 'http://example.com/ptc-calendar',
      
      
      // FINDING SYSTEM IDs
      // --- Price Fields ---
      // The system ID of a price field can be determined within 
      // the Price Set configuration by observing the URL of the 
      // "Edit Price Field" link, specifically the `fid` query parameter. 
      // For example, if that URL is 
      // http://example.com/administrator/?option=com_civicrm&task=civicrm/admin/price/field&action=update&reset=1&sid=201&fid=36
      // then the price field ID is 36.
      // 
      // --- Custom Data Fields ---
      // The system ID of a custom data field can be determined within 
      // the Custom Field Group configuration by observing the URL of the 
      // "Edit Field" link, specifically the `id` query parameter. 
      // For example, if that URL is 
      // http://example.com/administrator/?option=com_civicrm&task=civicrm/admin/custom/group/field/update&action=update&reset=1&gid=14&id=20      
      // then the custom field ID is 20.
      // 
      // --- Price Optinos ---
      // The system ID of a price option can be determined within 
      // the Edit Price Options configuration for a given price field,
      // by observing the URL of the relevant "Edit Option" link, 
      // specifically the `oid` query parameter. For example, if that URL is 
      // http://example.com/administrator/?option=com_civicrm&task=civicrm/admin/price/field/option&reset=1&action=update&oid=123&fid=355&sid=201
      // then the price option ID is 123.
      
      // The contribution page for this campaign uses a priceset
      // which includes a field labled "The meal to sponsor", with
      // options like Breakfast, Lunch, etc. That price field has
      // the system ID of 36.
      'meal_price_field_id' => '36',
      
      // The contribution page for this campaign uses a profile which
      // includes a custom data field labeled "The date of the meal".
      // This custom field has a system ID of 20.
      // then the field ID is 20.
      'date_custom_field_id' => '20',
      
      // The contribution page for this campaign uses a profile which
      // includes a custom data field labeled "Message to the cadets".
      // This custom field has a system ID of 20.
      'message_custom_field_id' => '21',

	  // The contribution page for this campaign uses a profile which
      // includes a custom data field labeled "Donor name on whiteboard".
      // This custom field has a system ID of 22.
      'donor_custom_field_id' => '22',      
      

      // The contribution page for this campaign uses a priceset
      // which includes a field labled "The meal to sponsor", with
      // options including labeled "Cereal", "Peanut Butter", "Great 
      // Breakfast", "Super Lunch", and "Yummy Dinner". Only the options
      // "Great Breakfast" (id=123), "Super Lunch" (id=124), and "Yummy
      // Dinner" (id=125) are managed through the calendar. Also note that
      // the calendar uses the simple labels "Breakfast", "Lunch", and 
      // "Dinner", so we specify those calendar labels here, for each
      // corresponding price option ID.
	'calendar_options' => array(
        123 => 'Breakfast',
        124 => 'Lunch',
        125 => 'Dinner',
      ),
    ),
  ),
);
```
