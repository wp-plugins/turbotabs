<?php 
/**
  * Plugin Name: TurboTabs LIGHT
  * Plugin URI: http://turbotabs.themeflection.com
  * Version: 1.0
  * Author: Aleksej Vukomanovic
  * Author URI: http://themeflection.com
  * Description: Responsive Tabs Wordpress plugin with plenty of customization options that allows you to have all-in-one tabs package.
  * Text Domain: turbotabs
  * Domain Path: /languages
  * License: GPL
  */
/*==============================================
  Prevent Direct Access of this file
==============================================*/
if ( ! defined( 'ABSPATH' ) ) exit; // Exit if this file is accessed directly

load_plugin_textdomain( 'turbotabs', false, 'inc/languages' );

/*============================================
      Installing tabs
=============================================*/
  require_once 'inc/font-awesome.php';
  require_once 'inc/setup.php';
  require_once 'inc/register.php';
  require_once 'inc/shortcode.php';
  TurboTabs_Setup::initialize();
  TurboTabs_Shortcode::initialize();      
?>