<?php if (!defined('__EXECUTION_ACCESS_RESTRICTION__')) exit('No direct script access allowed'); ?>

<ol id="active-list" class="list-panel">
  <?php
  foreach ($data[\Applications\PMTool\Resources\Enums\ViewVariablesKeys::objects] as $object) {
    if ($object->active) {
      $dao_id_prop = $data[\Applications\PMTool\Resources\Enums\ViewVariablesKeys::module] . "_id";
      $dao_name_prop = $data[\Applications\PMTool\Resources\Enums\ViewVariablesKeys::module] . "_name";
      echo
      "<li data-project-id=\"" . $object->$dao_id_prop . "\" class=\"select_item ui-widget-content\">"
      . $object->$dao_name_prop
      . "</li>";
    }
  }
  ?>              
</ol>