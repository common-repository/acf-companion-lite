<?php
/*
Plugin Name: ACF Companion Lite
Plugin URI: https://acfcompanion.com
Description: ACF Companion Lite brings Advanced Custom Fields (ACF) to life and offers site editors a stunning content editing experience.
Version: 1.0.0
Author: Going Bold
Author URI: https://goingbold.co.uk/
Text Domain: acf-c-lite
License: GPL2
*/

// Exit if accessed directly
if( ! defined( 'ABSPATH' ) ) exit;

if( ! class_exists('acf_c_lite') ) :

	class acf_c_lite {

		/**
		 * Adds register hooks.
		 */
		public function __construct() {

			/**
			 * Check we're in the admin area.
			 */
			if ( is_admin() ) {

				// vars
				$this->settings = array(
					'plugin'			=> 'ACF Companion Lite',
					'this_acf_version'	=> 0,
					'min_acf_version'	=> '5.6.3',
					'version'			=> '1.0.0',
					'url'				=> plugin_dir_url( __FILE__ ),
					'path'				=> plugin_dir_path( __FILE__ ),
				);

				//Get the absolute path of the directory that contains the file.
				define('ACF_C_LITE_PLUGIN_PATH', __FILE__ ); 

				// Set text domain
				load_plugin_textdomain( 'acf-c-lite', false, dirname( plugin_basename(__FILE__) ) . '/languages/' );

				// Add the 'acf_or_die' function (defined below) that checks if ACF is installed
				add_action( 'admin_init', array($this, 'acf_or_die'), 11);

				// Adds some CSS to the admin pages that ACF is on
				add_action('acf/input/admin_enqueue_scripts', array( $this, 'acf_c_lite_admin_scripts_styles' ) );

			}

		}




		/**
		 * Let's make sure ACF Pro is installed & activated
		 * If not, we give notice and kill the activation of ACF Companion Lite.
		 * Also works if ACF Pro is deactivated.
		 *
		 * @since 1.0.0
		 * @version 1.0.0
		 */
		function acf_or_die() {

			// If the 'acf' class (defined by ACF) DOES NOT exist or if the class 'acf_c' DOES exist then do this.
			if ( (!class_exists('acf')) || (class_exists('acf_c')) ) {
				$this->kill_plugin();
			// Otherwise check the version of ACF.
			} else {
				$this->settings['this_acf_version'] = acf()->settings['version'];
				if ( version_compare( $this->settings['this_acf_version'], $this->settings['min_acf_version'], '<' ) ) {
					$this->kill_plugin();
				}
			}
		}


		/**
		 * Deactivate plugin if ACF isn't installed (or ver too old).
		 *
		 * @since 1.0.0
		 * @version 1.0.0
		 */
		function kill_plugin() {
			deactivate_plugins( plugin_basename( __FILE__ ) );   
				if ( isset( $_GET['activate'] ) ) {
					unset( $_GET['activate'] );
				}
			add_action( 'admin_notices', array($this, 'acf_dependent_plugin_notice') );
		}

		/**
		 * Display notice.
		 *
		 * @since 1.0.0
		 * @version 1.0.0
		 */
		function acf_dependent_plugin_notice() {

			// If the 'acf' class (defined by ACF) DOES NOT exist othen display this message.
			if (!class_exists('acf')) {

				echo sprintf( __('%s requires ACF %s or higher to be installed and activated. %sDismiss This Notice%s', 'acf-c-lite'), '<div class="notice notice-error is-dismissible"><p>' . $this->settings['plugin'], $this->settings['min_acf_version'], '</p><button type="button" class="notice-dismiss"><span class="screen-reader-text">', '</span></button></div>');

				// echo '<div class="error"><p>' . sprintf( __('%1$s requires ACF v%2$s or higher to be installed and activated.', 'acf-c-lite'), $this->settings['plugin'],  . '</p></div>';

			// If the 'acf_c' class DOES exist then display this notice.
			} elseif (class_exists('acf_c')) {

				echo sprintf( __('%sLooks like you\'ve got %sACF Companion%s installed and active - go you! %2$s%s%3$s isn\'t required to be installed and activate. %sDismiss This Notice%s', 'acf-c-lite'), '<div class="notice notice-info is-dismissible"><p>', '<strong>', '</strong>', $this->settings['plugin'], '</p><button type="button" class="notice-dismiss"><span class="screen-reader-text">', '</span></button></div>');

			}

		}




		/**
		 * Puts CSS/JS in the head of ACF admin pages.
		 *
		 * @link https://wordpress.stackexchange.com/a/94952/115004
		 * @link https://www.advancedcustomfields.com/resources/acf-input-admin_enqueue_scripts/
		 *
		 * @since 1.0.0
		 * @version 1.0.0
		 */
		function acf_c_lite_admin_scripts_styles() {

			// ACF Companion Lite core styles (used to style post edit screen)
			wp_register_style( 'acf-c-lite-styles', plugins_url( '/assets/css/admin/acf-c-lite-styles.css', __FILE__ ), array(), '1.0.0' );
			wp_enqueue_style( 'acf-c-lite-styles' );

		}




	}

	// initialize
	$acf_c_lite = new acf_c_lite;

endif;
