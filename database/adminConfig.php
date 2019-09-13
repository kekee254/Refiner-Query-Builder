<?php
 class adminConfig {

     public  static  function configs($key)
     {
         $config = [
             'default_controller' => 'Admin',
             'default_action' => 'index',
             'controller_path' => realpath(dirname(__FILE__).'/../') . '/admin/controller/',
             'path_view'=>realpath(dirname(__FILE__).'/../') . '/admin/view/',
             'upload_view'=> realpath(dirname(__FILE__).'/../') . '/uploads/',
             'base_url' => 'http://' . $_SERVER['HTTP_HOST'] . str_replace('ext', '', dirname($_SERVER['SCRIPT_NAME'])).'/',
             'ext_url' => 'http://' . $_SERVER['HTTP_HOST'] .'/Fcart/ext/',
             'f_db' => [
                 'db_driver'=> 'pdo',
                 'db_host'=> 'localhost',
                 'db_type'=> 'mysql',
                 'db_name'=> 'fcart',
                 'db_account'=> ['username'=>'root', 'password'=>''],
                 'db_charset'=> 'UTF8',
                 'db_port'=> '3306',
                 'pdo_driver_options' => [PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_OBJ, PDO::ATTR_ERRMODE => PDO::ERRMODE_WARNING],
             ],
             'permissions' => [
                 'user_group1' => 'global',
                 'user_group2' => 'modules',
                 'user_group3' => ['editor','not-global'],
             ],
             'path_for_uploads' =>dirname($_SERVER['DOCUMENT_ROOT']) .'/www/extfcart/catalog/'

         ];

         return $config[$key];
     }
}