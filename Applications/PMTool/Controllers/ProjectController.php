<?php

/**
 *
 * @package		Basic MVC framework test
 * @author		FWM DEV Team
 * @copyright	Copyright (c) 2014
 * @license		
 * @link		
 * @since		
 * @filesource
 */
// ------------------------------------------------------------------------

/**
 * Authenticate controller Class
 *
 * @package		Application/PMTool
 * @subpackage	Controllers
 * @category	ProjectController
 * @author		FWM DEV Team
 * @link		
 */

namespace Applications\PMTool\Controllers;

if (!defined('__EXECUTION_ACCESS_RESTRICTION__'))
  exit('No direct script access allowed');

class ProjectController extends \Library\BaseController {

  /**
   * Method that loads the main view for controller, being the list of project here?
   * 
   * It loads the page title, the logout_url and the resources to load in the placeholders
   * 
   * @param \Library\HttpRequest $rq: the request
   */
  public function executeIndex(\Library\HttpRequest $rq) {
    //Get list of projects and store in session
    $this->_GetAndStoreProjectsInSession($rq);
  }

  /**
   * Method that loads the add or edit view for controller
   * 
   * @param \Library\HttpRequest $rq: the request
   */
  public function executeShowForm(\Library\HttpRequest $rq) {
    //Load Modules for view
    $this->page->addVar(
        \Applications\PMTool\Resources\Enums\ViewVariablesKeys::form_modules, 
        $this->app()->router()->selectedRoute()->phpModules());
  }

  /**
   * Method that loads the list all project view for controller
   * 
   * @param \Library\HttpRequest $rq: the request
   */
  public function executeListAll(\Library\HttpRequest $rq) {
    //Get list of projects stored in session
    $this->_GetAndStoreProjectsInSession($rq);
    $this->page->addVar(
        \Applications\PMTool\Resources\Enums\ViewVariablesKeys::projects, 
        $this->app()->user->getAttribute(\Library\Enums\SessionKeys::UserProjects)
    );
  }

  /**
   * Method that adds a project and returns the result of operation
   * 
   * @param \Library\HttpRequest $rq
   * @return JSON
   */
  public function executeAdd(\Library\HttpRequest $rq) {
    // Init result
    $result = $this->ManageResponseWS();

    //Init PDO
    $pm = $this->app()->user->getAttribute(\Library\Enums\SessionKeys::UserConnected);
    $this->dataPost["pm_id"] = $pm === NULL ? NULL : $pm[0]->pm_id();
    $project = $this->_PrepareUserObject($this->dataPost());
    $result["dataIn"] = $project;

    //Load interface to query the database
    $manager = $this->managers->getManagerOf($this->module);
    $result["dataOut"] = $manager->add($project);

    //Clear the project and facility list from session for the connect PM
    $this->app()->user->unsetAttribute(\Library\Enums\SessionKeys::UserProjects);
    $this->app()->user->unsetAttribute(\Library\Enums\SessionKeys::UserProjectFacilityList);

    //Process DB result and send result
    $result = (intval($result["dataOut"])) > 0 ?
      $this->UpdateResponseWS($result, //success 
          array(
            "resx_file" => \Applications\PMTool\Resources\Enums\ResxFileNameKeys::Project, 
            "resx_key" => "_insert", "step" => "success")) :
      $this->UpdateResponseWS($result, //error
          array(
            "resx_file" => \Applications\PMTool\Resources\Enums\ResxFileNameKeys::Project, 
            "resx_key" => "_insert", "step" => "error"));
    
    //return the JSON data
    echo \Library\HttpResponse::encodeJson($result);
  }

  /**
   * Method that edits a project and returns the result of operation
   * 
   * @param \Library\HttpRequest $rq
   * @return JSON
   */
  public function executeEdit(\Library\HttpRequest $rq) {
    // Init result
    $result = $this->ManageResponseWS();

    //Init PDO
    $pm = $this->app()->user->getAttribute(\Library\Enums\SessionKeys::UserConnected);
    $this->dataPost["pm_id"] = $pm === NULL ? NULL : $pm[0]->pm_id();
    $project = $this->_PrepareUserObject($this->dataPost());
    $result["data"] = $project;

    $manager = $this->managers->getManagerOf('Project');
    $result_insert = $manager->edit($project);

    //Clear the project and facility list from session for the connect PM
    $this->app()->user->unsetAttribute(\Library\Enums\SessionKeys::UserProjects);
    $this->app()->user->unsetAttribute(\Library\Enums\SessionKeys::UserProjectFacilityList);

    //Process DB result and send result
    if ($result_insert)
      $result = $this->ManageResponseWS(array("resx_file" => "project", "resx_key" => "_edit", "step" => "success"));
    //return the JSON data
    echo \Library\HttpResponse::encodeJson($result);
  }

  /**
   * Method that delete a project and returns the result of operation
   * 
   * @param \Library\HttpRequest $rq
   * @return JSON
   */
  public function executeDelete(\Library\HttpRequest $rq) {
    // Init result
    $result = $this->ManageResponseWS();
    $db_result = FALSE;
    $project_id = intval($this->dataPost["project_id"]);

    //Check if the project to be deleted if the Project manager's
    $project_selected = $this->_GetProjectFromSession($project_id);
    //Load interface to query the database
    if ($project_selected !== NULL) {
      $manager = $this->managers->getManagerOf($this->module());
      $db_result = $manager->delete($project_id);
    }
    //Clear the project and facility list from session for the connect PM
    $this->app()->user->unsetAttribute(\Library\Enums\SessionKeys::UserProjects);
    $this->app()->user->unsetAttribute(\Library\Enums\SessionKeys::UserProjectFacilityList);

    $result = $db_result !== FALSE ?
        $this->ManageResponseWS(array("resx_file" => \Applications\PMTool\Resources\Enums\ResxFileNameKeys::Project, "resx_key" => "_delete", "step" => "success")) :
        $this->ManageResponseWS(array("resx_file" => \Applications\PMTool\Resources\Enums\ResxFileNameKeys::Project, "resx_key" => "_delete", "step" => "error"));
    //return the JSON data
    echo \Library\HttpResponse::encodeJson($result);
  }

  /**
   * Method that gets a list of projects and returns the result of operation with the list
   * 
   * @param \Library\HttpRequest $rq
   * @return JSON
   */
  public function executeGetList(\Library\HttpRequest $rq, $isNotAjaxCall = FALSE) {
    // Init result
    $result = $this->ManageResponseWS();

    //Init PDO
    $pm = $this->app()->user->getAttribute(\Library\Enums\SessionKeys::UserConnected);
    $this->dataPost["pm_id"] = $pm === NULL ? NULL : $pm[0]->pm_id();
    $project = $this->_PrepareUserObject($this->dataPost());
    $result["data"] = $project;

    //Load interface to query the database for projects
    $manager = $this->managers->getManagerOf($this->module);
    $list[\Library\Enums\SessionKeys::UserProjects] = $manager->selectMany($project);

    //Load interface to query the database for facilities
    $manager = $this->managers->getManagerOf('Facility');
    $list[\Library\Enums\SessionKeys::UserProjectFacilityList] = $manager->selectMany($project);

    //Process DB result and send result
    $result = $this->ManageResponseWS(
        array(
          "resx_file" => \Applications\PMTool\Resources\Enums\ResxFileNameKeys::Project, 
          "resx_key" => "_getlist", "step" => "success"));
    
    $result["lists"] = $list;
    //return the JSON data
    if ($isNotAjaxCall) {
      return $list;
    } else {
      echo \Library\HttpResponse::encodeJson($result);
    }
  }

  /**
   * Method that get a project and returns the result of operation
   * 
   * @param \Library\HttpRequest $rq
   * @return JSON
   */
  public function executeGetItem(\Library\HttpRequest $rq) {
    // Init result
    $result = $this->ManageResponseWS();
    $project_id = intval($this->dataPost["project_id"]);
    
    $project_selected = $this->_GetProjectFromSession($project_id);
    $facility_selected = $this->_GetFacilityProjectFromSession($project_id);

    $result = ($project_selected !== NULL && $facility_selected !== NULL) ?//condition
      $this->ManageResponseWS(//true
          array(
            "resx_file" => \Applications\PMTool\Resources\Enums\ResxFileNameKeys::Project, 
            "resx_key" => "_getItem", "step" => "success")) :
      $this->ManageResponseWS(//false
          array(
            "resx_file" => \Applications\PMTool\Resources\Enums\ResxFileNameKeys::Project, 
            "resx_key" => "_getItem", "step" => "error"));
    
    $result["project"] = $project_selected;
    $result["facility"] = $facility_selected;
    //return the JSON data
    return $result;
  }

  /**
   * Method that get a project and returns the result of operation
   * 
   * @param \Library\HttpRequest $rq
   * @return JSON
   */
  public function executeUpdateItems(\Library\HttpRequest $rq) {
    $result = $this->ManageResponseWS(); // Init result

    $rows_affected = 0;
    //Get the project objects from ids received
    $project_ids = str_getcsv($this->dataPost["project_ids"], ',');
    $projects = $this->app()->user->getAttribute(\Library\Enums\SessionKeys::UserProjects);
    $matchedElements = $this->FindObjectsFromIds(
        array(
          "filter" => "project_id",
          "ids" => $project_ids,
          "objects" => $projects)
    );

    //Update the project objects in DB and get result (number of rows affected)
    //$this->app()->user->unsetAttribute(\Library\Enums\SessionKeys::UserProjects);
    foreach ($matchedElements as $project) {
      $project->setActive($this->dataPost["action"] === "active" ? TRUE : FALSE);
      $manager = $this->managers->getManagerOf($this->module);
      $rows_affected += $manager->edit($project) ? 1 : 0;
    }

    $result = ($rows_affected === count($project_ids)) ? //condition
       $this->UpdateResponseWS($result, //true
           array(
             "resx_file" => \Applications\PMTool\Resources\Enums\ResxFileNameKeys::Project, 
             "resx_key" => "_getItem", "step" => "success")) :
       $this->UpdateResponseWS($result, //false
           array(
             "resx_file" => \Applications\PMTool\Resources\Enums\ResxFileNameKeys::Project, 
             "resx_key" => "_getItem", "step" => "error"));
    
    //return the JSON data
    return $result;
  }

  /**
   * Find a project from an id
   * 
   * @param int $project_id : the id of the project to find
   * @return \Applications\PMTool\Models\Dao\Project $projectMatch : the match
   */
  private function _GetProjectFromSession($project_id) {
    $projects = array();
    $projectMatch = NULL;
    if ($this->app()->user->keyExistInSession(\Library\Enums\SessionKeys::UserProjects)) {
      $projects = $this->app()->user->getAttribute(\Library\Enums\SessionKeys::UserProjects);
    }
    foreach ($projects as $project) {
      if (intval($project->project_id()) === $project_id) {
        $projectMatch = $project;
        break;
      }
    }
    return $projectMatch;
  }
  
  /**
   * Find a facility from an  project id
   * 
   * @param int $project_id : the id of the project to find
   * @return \Applications\PMTool\Models\Dao\Facility $facilityMatch : the match
   */
  private function _GetFacilityProjectFromSession($project_id) {
    $facilities = array();
    $facilityMatch = NULL;
    if ($this->app()->user->keyExistInSession(\Library\Enums\SessionKeys::UserProjectFacilityList)) {
      $facilities = $this->app()->user->getAttribute(\Library\Enums\SessionKeys::UserProjectFacilityList);
    }
    foreach ($facilities as $facility) {
      if (intval($facility->project_id()) === $project_id) {
        $facilityMatch = $facility;
        break;
      }
    }
    return $facilityMatch;
  }

  /**
   * Check if the current pm has projects to decide where to send him: stay on the project list or asking him to add a project
   * 
   * @param \Applications\PMTool\Models\Dao\Project_manager $pm
   * @return boolean
   */
  private function _CheckIfPmHasProjects(\Applications\PMTool\Models\Dao\Project_manager $pm) {
    
    if ($this->app()->user->keyExistInSession(\Library\Enums\SessionKeys::UserProjects)) {
      $projects = $this->app()->user->getAttribute(\Library\Enums\SessionKeys::UserProjects);
      return count($projects) > 0 ? TRUE : FALSE;
    }
    $manager = $this->managers->getManagerOf($this->module);
    $count = $manager->countById($pm->pm_id());
    return $count > 0 ? TRUE : FALSE;
  }

  /**
   * Prepare the Project Object before calling the DB.
   * 
   * @param array $data_sent from POST request
   * @return \Applications\PMTool\Models\Dao\Project_manager
   */
  private function _PrepareUserObject($data_sent) {
    $project = new \Applications\PMTool\Models\Dao\Project();
    $project->setPm_id($data_sent["pm_id"]);
    $project->setProject_id(!array_key_exists('project_id', $data_sent) ? NULL : $data_sent["project_id"]);
    $project->setProject_name(!array_key_exists('project_name', $data_sent) ? NULL : $data_sent["project_name"]);
    $project->setProject_number(!array_key_exists('project_num', $data_sent) ? "" : $data_sent["project_num"]);
    $project->setProject_desc(!array_key_exists('project_desc', $data_sent) ? "" : $data_sent["project_desc"]);
    $project->setActive(!array_key_exists('active', $data_sent) ? 0 : ($data_sent["active"] === "1"));
    $project->setVisible(!array_key_exists('visible', $data_sent) ? 0 : ($data_sent["visible"] === "1"));

    return $project;
  }

  /**
   * Checks if the user projects and facilities are not stored in Session.
   * Stores the projects and facilities after call to WS to retrieve them
   * Set the data into the session for later use.
   * 
   * @param /Library/HttpRequest $rq
   * @return array $lists : the lists of objects if any 
   */
  private function _GetAndStoreProjectsInSession($rq) {
    $lists = array();
    if (!$this->app()->user->keyExistInSession(\Library\Enums\SessionKeys::UserProjects) &&
        !$this->app()->user->keyExistInSession(\Library\Enums\SessionKeys::UserProjectFacilityList)) {
      
      $lists = $this->executeGetList($rq, TRUE);
      
      $this->app()->user->setAttribute(
          \Library\Enums\SessionKeys::UserProjects, $lists[\Library\Enums\SessionKeys::UserProjects]
      );
      
      $this->app()->user->setAttribute(
          \Library\Enums\SessionKeys::UserProjectFacilityList, $lists[\Library\Enums\SessionKeys::UserProjectFacilityList]
      );
    } else {
      $lists[\Library\Enums\SessionKeys::UserProjects] = $this->app()->user->getAttribute(\Library\Enums\SessionKeys::UserProjects);
      $lists[\Library\Enums\SessionKeys::UserProjectFacilityList] = $this->app()->user->getAttribute(\Library\Enums\SessionKeys::UserProjectFacilityList);
    }
    return $lists;
  }

}
