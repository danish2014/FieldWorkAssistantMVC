<?php
/**
 *
 * @package		Basic MVC framework
 * @author		FWM DEV Team
 * @copyright	Copyright (c) 2014
 * @license		
 * @link		
 * @since		
 * @filesource
 */
// ------------------------------------------------------------------------

/**
 * ResxFileNameKeys Class
 *
 * @package		Applications/PMTool
 * @subpackage	Resources/Enum
 * @category	ResxFileNameKeys
 * @author		FWM DEV Team
 * @link		
 */

namespace Applications\PMTool\Resources\Enums;
if ( ! defined('__EXECUTION_ACCESS_RESTRICTION__')) exit('No direct script access allowed');

abstract class ResxFileNameKeys {
  /**
   * Local file names
   */
  const Project = "project";
  const Facility = "facility";
  const Client = "client";
  const Location = "location";
  const Technician = "technician";
  const Service = "service";
  const Task = "task";
//  const FieldForm = "field_form";
  const Pm = "pm";
  const Analyte = "analyte";
  const FieldAnalyte = "field_analyte";
  const LabAnalyte = "lab_analyte";
}

?>
