{combine_css path=$RELAY_PATH|cat:'admin/template/style.css'}
{combine_script id='common' load='footer' path='admin/themes/default/js/common.js'}

{combine_css path='themes/default/js/plugins/chosen.css'}
{combine_script id='jquery.chosen' load='footer' path='themes/default/js/plugins/chosen.jquery.min.js'}

{footer_script require='jquery'}
  $('form#relay .albumSelect').chosen();
{/footer_script}

<noscript>
  <div class="errors"><ul><li>JavaScript required!</li></ul></div>
</noscript>

<div id="batchManagerGlobal">
<form action="{$F_ACTION}" method="POST" id="relay">

  <fieldset id="RelayAlbum_children_options" style="margin-top:1em;">
    <legend>{'Album To Relay'|translate}</legend>
    
    <select name="relay[children][]" class="albumSelect" multiple="multiple" data-placeholder="{'Select albums...'|translate}">
      {html_options options=$all_albums selected=$relay_children_selected}
    </select>

  </fieldset>

  <fieldset id="RelayAlbum_parents_options" style="margin-top:1em;">
    <legend>{'Relayed By Album'|translate}</legend>
    
    <select name="relay[parents][]" class="albumSelect" multiple="multiple" data-placeholder="{'Select albums...'|translate}">
      {html_options options=$all_albums selected=$relay_parents_selected}
    </select>

  </fieldset>

  <p class="actionButtons" id="applyRelayBlock">
    <input class="submit" type="submit" value="{'Save'|translate}" name="submitRelay"/>
  </p>

</form>
</div>
