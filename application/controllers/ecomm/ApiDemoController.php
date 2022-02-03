<?php
defined('BASEPATH') or exit('No direct script access allowed');

require APPPATH . 'libraries/Format.php';
require APPPATH . 'libraries/RestController.php';

use  chriskacerguis\RestServer\RestController;


class ApiDemoController extends RestController
{
    public function index_get()
    {
        echo "I am RESTful API";
    }
}
