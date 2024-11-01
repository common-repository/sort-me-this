<?php

/**
 * Fired during plugin activation
 * @since             1.0.0
 * 
 * @package    Sort_Me_This
 * @subpackage Sort_Me_This/includes
 */

/**
 * Fired during plugin activation.
 *
 * This class defines all code necessary to run during the plugin's activation.
 *
 * @since      1.0.0
 * @package    Sort_Me_This
 * @subpackage Sort_Me_This/includes
 * @author     Algaweb
 */
class Sort_Me_This_Activator {

	/**
	 * Short Description. (use period)
	 *
	 * @since    1.0.0
	 */
	public static function activate() {
		add_option('smt-media-categories', array(
			'0' => 'My Media Category',
		));
		add_option('cmc-fv', 1);
	}

}