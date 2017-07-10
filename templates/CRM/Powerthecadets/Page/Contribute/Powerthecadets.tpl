<h3>Power the Cadets Configuration Preview</h3>

<div class="help">
  {ts 1=$readme_url}The <em>Power the Cadets</em> configuration for this contribution page, if any, is summarized below. For help modifying this configuration page, please see the <a href="%1">Power the Cadets extension README file</a>.{/ts}
</div>
{if $noconfig}
  <div class="messages status no-popup" data-options="null">
    <div class="icon inform-icon"></div>
    <span class="msg-text">{ts}No configuration exists for this contribution page.{/ts}</span>
  </div>  
{else}
  <table class="crm-info-panel">
    <tr class="{if $error_settings.calendar_url}crm-error{/if}">
      <td class="label">{ts}Calendar URL{/ts}</td>
      <td><a href="{$calendar_url}">{$calendar_url}</a></td>
    </tr>
    <tr>
      <td class="label">{ts}"Meal Selection" price field{/ts}</td>
      <td>{$meal_price_field}</td>
    </tr>
    <tr class="{if $error_settings.calendar_option_labels}crm-error{/if}">
      <td class="label">{ts}Calendar-only "meal selection" price field options{/ts}</td>
      <td>
        {if is_array($calendar_option_labels)}
          <ul>
          {foreach from=$calendar_option_labels item=calendar_option_label}
            <li>{$calendar_option_label}</li>
          {/foreach}
          </ul>
        {else}
          {$calendar_option_labels}
        {/if}
      </td>
    </tr>
    <tr class="{if $error_settings.meal_date_custom_field}crm-error{/if}">
      <td class="label">{ts}"Meal date" custom field{/ts}</td>
      <td>{$meal_date_custom_field}</td>
    </tr>
    <tr class="{if $error_settings.message_custom_field}crm-error{/if}">
      <td class="label">{ts}"Message" custom field{/ts}</td>
      <td>{$message_custom_field}</td>
    </tr>
    <tr class="{if $error_settings.donor_custom_field}crm-error{/if}">
      <td class="label">{ts}"Donor name" custom field{/ts}</td>
      <td>{$donor_custom_field}</td>
    </tr>
  </table>
{/if}