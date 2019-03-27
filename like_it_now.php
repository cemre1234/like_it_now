<?php
/*
Plugin Name: Like It Now
Plugin URI: https://github.com/cemre1234/like_it_now/
Description: A simple wordpress like/dislike plugin
Version: 1.0
Author: Cemre Tellioğlu
Author URI: http://www.frizzythemes.com
License: GPL2
*/
/*
Copyright 2019  Cemre Tellioğlu  (email : cemreislev@gmail.com)

This program is free software; you can redistribute it and/or modify
it under the terms of the GNU General Public License, version 2, as
published by the Free Software Foundation.

This program is distributed in the hope that it will be useful,
but WITHOUT ANY WARRANTY; without even the implied warranty of
MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
GNU General Public License for more details.

You should have received a copy of the GNU General Public License
along with this program; if not, write to the Free Software
Foundation, Inc., 51 Franklin St, Fifth Floor, Boston, MA  02110-1301  USA
*/
defined( 'ABSPATH' ) or die( 'Nope!' );

if(!class_exists('Like_It_Now'))
{
	class Like_It_Now
	{
		/**
		 * Construct the plugin object
		 */
		public function __construct()
		{
			// Initialize Settings
			require_once(sprintf("%s/settings.php", dirname(__FILE__)));
 			$likeit = new Like_it();

                        
		} // END public function __construct

		/**
		 * Activate the plugin
		 */
		public static function activate()
		{
			// Do nothing
		} // END public static function activate

		/**
		 * Deactivate the plugin
		 */
		public static function deactivate()
		{
			// Do nothing
		} // END public static function deactivate



	} // END class WP_Plugin_Template
} // END if(!class_exists('WP_Plugin_Template'))

if(class_exists('Like_It_Now'))
{
	// Installation and uninstallation hooks
	register_activation_hook(__FILE__, array('Like_It_Now', 'activate'));
	register_deactivation_hook(__FILE__, array('Like_It_Now', 'deactivate'));

	// instantiate the plugin class
	$wp_plugin_template = new Like_It_Now();

}
