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
 * TaskTabKeys Class
 *
 * @package		Applications/PMTool
 * @subpackage	Resources/Enum
 * @category	TaskTabKeys
 * @author		FWM DEV Team
 * @link		
 */


namespace Applications\PMTool\Resources\Enums;
if ( ! defined('__EXECUTION_ACCESS_RESTRICTION__')) exit('No direct script access allowed');

class TaskTabKeys {
  const InfoTab = "ttk_it";
  const TechniciansTab = "ttk_tt";
  const LocationsTab = "ttk_lt";
  const InspFormsTab = "ttk_itft";
  const FieldAnalytesTab = "ttk_fat";
  const LabAnalytesTab = "ttk_lat";
  const FieldSampleMatrixTab = "ttk_fsmt";
  const LabSampleMatrixTab = "ttk_lsmt";
  const CocTab = "ttk_ct";
  const ServicesTab = "ttk_st";
}
