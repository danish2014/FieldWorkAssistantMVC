<?php

namespace Applications\PMTool\Controllers;

if (!defined('__EXECUTION_ACCESS_RESTRICTION__'))
  exit('No direct script access allowed');

class TaskLocationController extends \Library\BaseController {

  public function executeUpdateItems(\Library\HttpRequest $rq) {
    $result = \Applications\PMTool\Helpers\LocationHelper::UpdateTaskLocations($this);
    $this->SendResponseWS(
            $result, array(
        "resx_file" => \Applications\PMTool\Resources\Enums\ResxFileNameKeys::Task,
        "resx_key" => $this->action(),
        "step" => ($result["rows_affected"] === count($result["location_ids"] )) ? "success" : "error"
    ));
  }

  public function executeManageLocations(\Library\HttpRequest $rq) {
    $sessionTask = \Applications\PMTool\Helpers\TaskHelper::GetCurrentSessionTask($this->user());
    if ($sessionTask[\Library\Enums\SessionKeys::TaskObj] === NULL) {
      $this->Redirect(\Library\Enums\ResourceKeys\UrlKeys::TaskRootUrl);
    }

    \Applications\PMTool\Helpers\TaskHelper::SetActiveTab($this->user(), \Applications\PMTool\Resources\Enums\TaskTabKeys::LocationsTab);
    $sessionProject = \Applications\PMTool\Helpers\ProjectHelper::GetCurrentSessionProject($this->user());
    $project_locations = \Applications\PMTool\Helpers\LocationHelper::GetProjectLocations($this, $sessionProject);
    $task_locations = \Applications\PMTool\Helpers\LocationHelper::GetAndStoreTaskLocations($this, $sessionTask);
    //filter the project locations after we retrieve the task locations
    $project_locations = \Applications\PMTool\Helpers\LocationHelper::FilterLocationsToExcludeTaskLocations($project_locations, $task_locations);

    $this->page->addVar(\Applications\PMTool\Resources\Enums\ViewVariablesKeys::currentProject, $sessionProject[\Library\Enums\SessionKeys::ProjectObject]);
    $this->page->addVar(\Applications\PMTool\Resources\Enums\ViewVariablesKeys::currentTask, $sessionTask[\Library\Enums\SessionKeys::TaskObj]);
    $this->page->addVar("HasItemsToDisplay", \Applications\PMTool\Helpers\ProjectHelper::DoesProjectHasActiveLocations($this->user()));
    $data = array(
        \Applications\PMTool\Resources\Enums\ViewVariablesKeys::module => strtolower($this->module()),
        \Applications\PMTool\Resources\Enums\ViewVariablesKeys::objects_list_right => $project_locations,
        \Applications\PMTool\Resources\Enums\ViewVariablesKeys::objects_list_left => $task_locations,
        \Applications\PMTool\Resources\Enums\ViewVariablesKeys::properties_right => \Applications\PMTool\Helpers\CommonHelper::SetPropertyNamesForDualList(strtolower("location")),
        \Applications\PMTool\Resources\Enums\ViewVariablesKeys::properties_left => \Applications\PMTool\Helpers\CommonHelper::SetPropertyNamesForDualList(strtolower("location"))
    );
    $this->page->addVar(\Applications\PMTool\Resources\Enums\ViewVariablesKeys::data, $data);

    //tab status
    $this->page->addVar(
            \Applications\PMTool\Resources\Enums\ViewVariablesKeys::tabStatus, \Applications\PMTool\Helpers\TaskHelper::GetTabsStatus($this->user()));
    //form modules
    $this->page->addVar(
            \Applications\PMTool\Resources\Enums\ViewVariablesKeys::form_modules, $this->app()->router()->selectedRoute()->phpModules());
  }

}
