<?php
/**
 * Plugin Name: bbPress Members Only
 * Description: Only registered users can view your site, non members can only see a login/home page and forums archive / forums homepage with no registration options
 * Version: 1.5.0
 * Author: Tomas Zhu, Kevin Provance
 * Author URI: https://github.com/svl-studios/svl-bbp-members-only
 * Plugin URI: https://github.com/svl-studios/svl-bbp-members-only
 * Text Domain: bbp-members-only
 *
 * Copyright 2016 - 2021  Tomas Zhu  (email : support@bbp.design)
 *
 * This program is free software; you can redistribute it and/or modify
 * it under the terms of the GNU General Public License as published by
 * the Free Software Foundation; either version 2 of the License, or
 * (at your option) any later version.
 *
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License
 * along with this program; if not, write to the Free Software
 * Foundation, Inc., 51 Franklin St, Fifth Floor, Boston, MA  02110-1301  USA
 *
 * @package   bbPress Members Only
 * @author    Tomas Zhu <support@bbp.design> & Kevin Provance <kevin.provance@gmail.com>
 * @license   GNU General Public License, version 3
 * @copyright Copyright 2016 - 2021  Tomas Zhu
 */

defined( 'ABSPATH' ) || exit;

/**
 * Plugin URL.
 */
define( 'BBP_MEMBERSONLY_PLUGIN_URL', plugin_dir_url( __FILE__ ) );

require_once 'bbpmembersonlypagesettings.php';
require_once 'rules/shortcoderestriction.php';

add_action( 'admin_menu', 'bmo_tomas_bbp_members_only_option_menu' );

// Localization.
add_action( 'plugins_loaded', 'bmo_tomas_bbp_members_only_load_textdomain' );

/**
 * Load textdomain.
 */
function bmo_tomas_bbp_members_only_load_textdomain() {
	load_plugin_textdomain( 'bbp-members-only', false, dirname( plugin_basename( __FILE__ ) ) . '/languages/' );
}

/**
 * Options menu.
 */
function bmo_tomas_bbp_members_only_option_menu() {

	add_menu_page( __( 'bbPress Members Only', 'bbp-members-only' ), __( 'bbPress Members Only', 'bbp-members-only' ), 'manage_options', 'bbpmemberonlyfree', 'bmo_tomas_bbp_members_only_free_setting' );
	add_submenu_page( 'bbpmemberonlyfree', __( 'bbPress Members Only', 'bbp-members-only' ), __( 'bbPress Members Only', 'bbp-members-only' ), 'manage_options', 'bbpmemberonlyfree', 'bmo_tomas_bbp_members_only_free_setting' );
	add_submenu_page( 'bbpmemberonlyfree', __( 'bbPress Members Only', 'bbp-members-only' ), __( 'Optional Settings', 'bbp-members-only' ), 'manage_options', 'bpmemberoptionalsettings', 'bbp_members_only_free_optional_setting' );
}

$bbpdisableallfeature = get_option( 'bbpdisableallfeature' );

if ( 'yes' === $bbpdisableallfeature ) {
	return;
}

/**
 * Settings.
 */
function bmo_tomas_bbp_members_only_free_setting() {
	global $wpdb;

	$m_bbpmoregisterpageurl = get_option( 'bbpmoregisterpageurl' );

	if ( isset( $_POST['bbpmosubmitnew'] ) ) {
		check_admin_referer( 'bmo_tomas_bbp_members_only_nonce' );
		if ( isset( $_POST['bbpmoregisterpageurl'] ) ) {
			$m_bbpmoregisterpageurl = esc_url( sanitize_text_field( wp_unslash( $_POST['bbpmoregisterpageurl'] ) ) );
		}

		update_option( 'bbpmoregisterpageurl', $m_bbpmoregisterpageurl );

		if ( isset( $_POST['bbpopenedpageurl'] ) ) {
			$bbpopenedpagechecktextarea = sanitize_text_field( wp_unslash( $_POST['bbpopenedpageurl'] ) );
			$bbpopenedpagecheckarray    = explode( "\n", $bbpopenedpagechecktextarea );

			$bbpopenedpagefilteredarray = array();
			$bbpopenedpageurl           = '';

			if ( ( is_array( $bbpopenedpagecheckarray ) ) && ( count( $bbpopenedpagecheckarray ) > 0 ) ) {
				foreach ( $bbpopenedpagecheckarray as $bbpopenedpagechecksingle ) {
					$bbpopenedpagechecksingle = esc_url( $bbpopenedpagechecksingle );
					if ( strlen( $bbpopenedpagechecksingle ) > 0 ) {
						$bbpopenedpagefilteredarray[] = $bbpopenedpagechecksingle;
					}
				}
			}

			if ( ( is_array( $bbpopenedpagefilteredarray ) ) && ( count( $bbpopenedpagefilteredarray ) > 0 ) ) {
				$bbpopenedpageurl = implode( "\n", $bbpopenedpagefilteredarray );
			}

			if ( strlen( $bbpopenedpageurl ) === 0 ) {
				delete_option( 'bbp_members_only_saved_open_page_url', $bbpopenedpageurl );
			} else {
				update_option( 'bbp_members_only_saved_open_page_url', $bbpopenedpageurl );
			}
		}

		$bpmo_message_string = esc_html__( 'Your changes has been saved.', 'bbp-members-only' );

		bmo_tomas_bbp_members_only_message( $bpmo_message_string );
	}

	echo '<br />';

	$saved_register_page_url = get_option( 'bbpmoregisterpageurl' );
	?>

	<div style='margin:10px 5px;'>
		<div style='float:left;margin-right:10px;'>
			<img src='<?php echo esc_url( plugins_url( '/images/new.png', __FILE__ ) ); ?>' style='width:30px;height:30px;'>
		</div>
		<div style='padding-top:5px; font-size:22px;'>bbPress Members Only Setting:</div>
	</div>
	<div style='clear:both'></div>
	<div class="wrap">
		<div id="dashboard-widgets-wrap">
			<div id="dashboard-widgets" class="metabox-holder">
				<div id="post-body" style="width:100%;">
					<div id="dashboard-widgets-main-content">
						<div class="postbox-container" style="width:98%;">
							<div class="postbox">
								<h3 class='hndle' style='padding: 20px; !important'>
											<span>
											<?php echo esc_html__( 'Opened Pages Panel:', 'bbp-members-only' ); ?>
											</span>
								</h3>
								<div class="inside" style='padding-left:10px;'>
									<form id="bpmoform" name="bpmoform" action="" method="POST">
										<?php
										wp_nonce_field( 'bmo_tomas_bbp_members_only_nonce' );
										?>
										<table id="bpmotable" style="width:100%;">
											<tr>
												<td style="width:30%;padding: 20px;">
													<?php
													echo esc_html__( 'Register Page URL:', 'bbp-members-only' );
													echo '<div style="color:#888 !important;"><i>';
													echo esc_html__( '(or redirect url)', 'bbp-members-only' );
													echo '</i></div>';
													?>
												</td>
												<td style="width:70%;padding: 20px;">
													<input type="text" id="bbpmoregisterpageurl" name="bbpmoregisterpageurl" style="width:500px;" size="70" value="<?php echo esc_url( $saved_register_page_url ); ?>">
												</td>
											</tr>
											<tr style="margin-top:30px;">
												<td style="width:30%;padding: 20px;vertical-align:top;">
													<?php echo esc_html__( 'Opened Page URLs:', 'bbp-members-only' ); ?>
												</td>
												<td style="width:70%;padding: 20px;">
													<?php $urlsarray = get_option( 'bbp_members_only_saved_open_page_url' ); ?>
													<textarea name="bbpopenedpageurl" id="bbpopenedpageurl" cols="70" rows="10" style="width:500px;"><?php echo esc_textarea( $urlsarray ); ?></textarea>
													<p>
														<i style="color:gray;">
															<?php echo esc_html__( 'Enter one URL per line please.', 'bbp-members-only' ); ?>
														</i>
													</p>
													<p>
														<i style='color:gray;'>
															<?php echo esc_html__( 'These pages will opened for guest and guest will not be directed to register page.', 'bbp-members-only' ); ?>
														</i>
													</p>
												</td>
											</tr>
										</table>
										<br/>
										<input type="submit" id="bbpmosubmitnew" name="bbpmosubmitnew" value=" Submit " style="margin:1px 20px;">
									</form>
									<br/>
								</div>
							</div>
						</div>
					</div>
				</div>
			</div>
		</div>
	</div>
	<div style="clear:both"></div>
	<br/>
	<?php
}

/**
 * Init, basically.
 */
function bmo_tomas_bbpress_only_for_members() {
	global $user_ID, $post;

	if ( is_front_page() ) {
		return;
	}

	$current_page_id = get_the_ID();

	if ( function_exists( 'bp_is_register_page' ) && function_exists( 'bp_is_activation_page' ) ) {
		if ( bp_is_register_page() || bp_is_activation_page() ) {
			return;
		}
	}

	if ( function_exists( 'bbp_is_forum_archive' ) ) {
		$bbp_is_forum_archive = bbp_is_forum_archive();
		if ( $bbp_is_forum_archive ) {
			return;
		}
	}

	$current_url = sanitize_text_field( wp_unslash( $_SERVER['HTTP_HOST'] ?? '' ) ) . sanitize_text_field( wp_unslash( $_SERVER['REQUEST_URI'] ?? '' ) );
	$current_url = str_ireplace( 'http://', '', $current_url );
	$current_url = str_ireplace( 'https://', '', $current_url );
	$current_url = str_ireplace( 'ws://', '', $current_url );
	$current_url = str_ireplace( 'www.', '', $current_url );
	$current_url = strtolower( $current_url );

	$saved_register_page_url = get_option( 'bbpmoregisterpageurl' );
	$saved_register_page_url = str_ireplace( 'http://', '', $saved_register_page_url );
	$saved_register_page_url = str_ireplace( 'https://', '', $saved_register_page_url );
	$saved_register_page_url = str_ireplace( 'ws://', '', $saved_register_page_url );
	$saved_register_page_url = str_ireplace( 'www.', '', $saved_register_page_url );
	$saved_register_page_url = strtolower( $saved_register_page_url );

	if ( function_exists( 'is_bbpress' ) ) {
		if ( is_bbpress() ) {
			$is_bbp_current_forum = bbp_get_forum_id();
		} else {
			$is_bbp_current_forum = '';
		}
	} else {
		$is_bbp_current_forum = '';
	}

	if ( function_exists( 'is_bbpress' ) ) {
		$bbprestrictsbbpresssection = get_option( 'bbprestrictsbbpresssection' );
		if ( ! ( empty( $bbprestrictsbbpresssection ) ) ) {
			if ( ! is_bbpress() ) {
				return;
			}
		}
	}

	if ( $saved_register_page_url === $current_url ) {
		return;
	}

	$saved_open_page_option = get_option( 'bbp_members_only_saved_open_page_url' );

	$bbp_members_only_saved_open_page_url = explode( "\n", trim( $saved_open_page_option ) );

	if ( ( is_array( $bbp_members_only_saved_open_page_url ) ) && ( count( $bbp_members_only_saved_open_page_url ) > 0 ) ) {
		$root_domain = get_option( 'siteurl' );

		foreach ( $bbp_members_only_saved_open_page_url as $bbp_members_only_saved_open_page_url_single ) {
			$bbp_members_only_saved_open_page_url_single = trim( $bbp_members_only_saved_open_page_url_single );

			if ( bmo_tomas_bbp_members_only_reserved_url( $bbp_members_only_saved_open_page_url_single ) === true ) {
				continue;
			}

			$bbp_members_only_saved_open_page_url_single = bmo_tomas_bbp_members_only_pure_url( $bbp_members_only_saved_open_page_url_single );
			$bbp_members_only_saved_open_page_url_single = strtolower( $bbp_members_only_saved_open_page_url_single );

			if ( $current_url === $bbp_members_only_saved_open_page_url_single ) {
				return;
			}
		}
	}

	if ( is_user_logged_in() === false ) {
		if ( empty( $saved_register_page_url ) ) {
			$current_url  = sanitize_text_field( wp_unslash( $_SERVER['REQUEST_URI'] ?? '' ) );
			$redirect_url = wp_login_url();
			header( 'Location: ' . $redirect_url );
		} else {
			$protocol = 'http://';
			if ( array_key_exists( 'HTTPS', $_SERVER ) && 'on' === $_SERVER['HTTPS'] ) {
				$protocol = 'https://';
			}

			$saved_register_page_url = $protocol . $saved_register_page_url;
			header( 'Location: ' . $saved_register_page_url );
		}

		die();
	}
}

/**
 * Purify URL.
 *
 * @param string $current_url Current URL.
 *
 * @return false|string
 */
function bmo_tomas_bbp_members_only_pure_url( string $current_url ) {
	if ( empty( $current_url ) ) {
		return false;
	}

	$current_url_array = wp_parse_url( $current_url );

	$current_url = str_ireplace( 'http://', '', $current_url );
	$current_url = str_ireplace( 'https://', '', $current_url );
	$current_url = str_ireplace( 'ws://', '', $current_url );
	$current_url = str_ireplace( 'www.', '', $current_url );

	return trim( $current_url );
}

/**
 * Is reserved URL.
 *
 * @param string $url URL.
 *
 * @return bool
 */
function bmo_tomas_bbp_members_only_reserved_url( string $url ): bool {
	$home_page = get_option( 'siteurl' );

	$home_page = bmo_tomas_bbp_members_only_pure_url( $home_page );
	$url       = bmo_tomas_bbp_members_only_pure_url( $url );

	if ( $home_page === $url ) {
		return true;
	} else {
		return false;
	}
}

add_action( 'wp', 'bmo_tomas_bbpress_only_for_members' );

/**
 * Members only message.
 *
 * @param string $p_message The message.
 */
function bmo_tomas_bbp_members_only_message( string $p_message ) {

	echo "<div id='message' class='updated fade' style='line-height: 30px;margin-left: 0px;margin-top:10px; margin-bottom:10px;'>";

	echo wp_kses_post( $p_message );

	echo '</div>';
}

add_filter( 'plugin_action_links', 'tomas_bbpress_members_only_settings_link', 10, 2 );

/**
 * Plugin meta settings link.
 *
 * @param array  $links Links.
 * @param string $file  File.
 *
 * @return array
 */
function tomas_bbpress_members_only_settings_link( array $links, string $file ): array {
	$tomas_bbpress_members_only_file = plugin_basename( __FILE__ );

	if ( $file === $tomas_bbpress_members_only_file ) {
		$settings_link = '<a href="' . esc_url( admin_url( 'admin.php?page=bbpmemberonlyfree' ) ) . '">' . esc_html__( 'Settings', 'bbp-members-only' ) . '</a>';

		array_unshift( $links, $settings_link );
	}

	return $links;
}

/**
 * First run guide bar.
 */
function bbpmof_user_first_run_guide_bar() {
	$is_bbpmof_user_first_run_guide_bar = get_option( 'bbpmof_user_first_run_guide_bar' );

	if ( empty( $is_bbpmof_user_first_run_guide_bar ) ) {
		echo "<div class='notice bbpmof-notice notice-info'><p>Thanks for installing <strong>bbPress Members Only</strong>! Please check <a href='" . esc_url( admin_url() ) . "admin.php?page=bbpmemberonlyfree' target='_blank'>Global Settings Panel</a> and <a href='" . esc_url( admin_url() ) . "admin.php?page=bpmemberoptionalsettings' target='_blank'>Optional Panel</a>, to set up the plugin. Any question or feature request please contact <a href='https://github.com/svl-studios/svl-bbp-members-only/issues'  target='_blank'>Support</a> :)</p></div>";

		update_option( 'bbpmof_user_first_run_guide_bar', 'yes' );
	}
}

add_action( 'admin_notices', 'bbpmof_user_first_run_guide_bar' );
