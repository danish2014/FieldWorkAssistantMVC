<?php

/**
 *
 * @package		Basic MVC framework
 * @author		Jeremie Litzler
 * @copyright	Copyright (c) 2014
 * @license		
 * @link		
 * @since		
 * @filesource
 */
// ------------------------------------------------------------------------

/**
 * ResourcesController controller Class
 *
 * @package		Library
 * @subpackage	Controllers
 * @category	ResourcesController
 * @author		FWM DEV Team
 * @link		
 */
namespace Libray\Controllers;
if ( ! defined('__EXECUTION_ACCESS_RESTRICTION__')) exit('No direct script access allowed');

class ResourcesController extends \Library\BaseController {

  public function executeGetResx(\Library\HttpRequest $rq) {
    $response = GetResource();
    echo \Library\HttpResponse::encodeJson($response);
  }
  
  protected function GetResource() {
//    $this->dataPost();
  }

}