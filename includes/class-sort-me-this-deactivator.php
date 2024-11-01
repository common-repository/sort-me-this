<?php

/**
 * Fired during plugin deactivation
 * @since             1.0.0
 * 
 * @package    Sort_Me_This
 * @subpackage Sort_Me_This/includes
 */


/**
 * Fired during plugin deactivation.
 *
 * This class defines all code necessary to run during the plugin's deactivation.
 *
 * @since      1.0.0
 * @package    Sort_Me_This
 * @subpackage Sort_Me_This/includes
 * @author     Algaweb
 */
class Sort_Me_This_Deactivator {

	/**
	 * Short Description. (use period)
	 *
	 * @since    1.0.0
	 */
	public static function deactivate() {
		delete_option('smt-media-categories');
		delete_option('cmc-fv');
	}

}