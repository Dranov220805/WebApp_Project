<?php

class BaseController {

    public function __construct() {}

    public function Views($content, $data = [])
    {
        include "./views/layout/index.php";
    }
}

?>