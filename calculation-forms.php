<?php
/**
* Plugin Name: Cost Calculator & Cost Estimation Builder Pro
* Description:  WordPress Calculator helps you to build any type of estimation forms on a few easy steps
* Author: add-ons.org
* Version: 1.8.6
* Text Domain: calculation-forms
* Domain Path: /languages/
* Author URI: https://add-ons.org/
*/
if ( !function_exists( 'add_action' ) ) {
    echo 'Hi there!  I\'m just a plugin, not much I can do when called directly.';
    exit;
}
define( 'CALCULATION_FORMS_PLUGIN_URL', plugin_dir_url( __FILE__ ) );
define( 'CALCULATION_FORMS_PLUGIN_PATH', plugin_dir_path( __FILE__ ) );
define( 'CALCULATION_FORMS_PREMIUM', true );
include CALCULATION_FORMS_PLUGIN_PATH."vendor/autoload.php";
foreach (glob(CALCULATION_FORMS_PLUGIN_PATH."backend/*.php") as $filename){
    include $filename;
}
foreach (glob(CALCULATION_FORMS_PLUGIN_PATH."backend/block_editor/*.php") as $filename){
    include $filename;
}
foreach (glob(CALCULATION_FORMS_PLUGIN_PATH."fields/*.php") as $filename){
    include $filename;
}
foreach (glob(CALCULATION_FORMS_PLUGIN_PATH."frontend/*.php") as $filename){
    include $filename;
}
$dir = new RecursiveDirectoryIterator(CALCULATION_FORMS_PLUGIN_PATH."modules");
$ite = new RecursiveIteratorIterator($dir);
$files = new RegexIterator($ite, "/\.php/", RegexIterator::MATCH);
foreach ($files as $file) {
    if (!$file->isDir()){
        include $file->getPathname();
    }
}
if(!class_exists('Superaddons_List_Addons')) {  
    include CALCULATION_FORMS_PLUGIN_PATH."add-ons.php"; 
}