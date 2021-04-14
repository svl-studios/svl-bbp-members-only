<?php
/**
 * Page settings.
 *
 * @package   bbPress Members Only
 * @author    Tomas Zhu <support@bbp.design> & Kevin Provance <kevin.provance@gmail.com>
 * @license   GNU General Public License, version 3
 * @copyright Copyright 2016 - 2021  Tomas Zhu
 */

defined( 'ABSPATH' ) || exit;

/**
 * Optional Settings.
 */
function bbp_members_only_free_optional_setting() {
	global $wpdb;

	if ( isset( $_POST['bbpoptionsettinspanelsubmit'] ) ) {
		check_admin_referer( 'bbpoptionsettinspanelsubmit_free_nonce' );

		if ( isset( $_POST['bbprestrictsbbpresssection'] ) ) {
			$m_bbprestrictsbbpresssection = sanitize_text_field( wp_unslash( $_POST['bbprestrictsbbpresssection'] ) );

			update_option( 'bbprestrictsbbpresssection', $m_bbprestrictsbbpresssection );
		} else {
			delete_option( 'bbprestrictsbbpresssection' );
		}

		if ( isset( $_POST['bbpdisableallfeature'] ) ) {
			$bbpdisableallfeature = sanitize_text_field( wp_unslash( $_POST['bbpdisableallfeature'] ) );
			update_option( 'bbpdisableallfeature', $bbpdisableallfeature );
		} else {
			delete_option( 'bbpdisableallfeature' );
		}

		$bbpdisableallfeature = get_option( 'bbpdisableallfeature' );

		$bpmo_message_string = __( 'Your changes has been saved.', 'bbp-members-only' );

		bmo_tomas_bbp_members_only_message( $bpmo_message_string );
	}

	echo '<br />';

	?>
	<div style='margin:10px 5px;'>
		<div style='float:left;margin-right:10px;'>
			<img src='<?php echo esc_url( BBP_MEMBERSONLY_PLUGIN_URL ); ?>/images/new.png' style='width:30px;height:30px;'>
		</div>
		<div style='padding-top:5px; font-size:22px;'>bbPress Members Only Optional Settings:</div>
	</div>
	<div style='clear:both'></div>
	<div class="wrap">
		<div id="dashboard-widgets-wrap">
			<div id="dashboard-widgets" class="metabox-holder">
				<div id="post-body" style="width:60%;">
					<div id="dashboard-widgets-main-content">
						<div class="postbox-container" style="width:90%;">
							<div class="postbox">
								<h3 class='hndle' style='padding: 20px; !important'>
									<span>
									<?php
									echo esc_html__( 'Optional Settings Panel :', 'bbp-members-only' );
									?>
									</span>
								</h3>
								<div class="inside" style='padding-left:10px;'>
									<form id="bpmoform" name="bpmoform" action="" method="POST">
										<table id="bpmotable" style="width:100%;">
											<tr>
												<td style="vertical-align:top;width:30%;padding: 30px 20px 20px 20px;">
													<?php
													echo esc_html__( 'Only Protect My  bbPress Pages:', 'bbp-members-only' );
													?>
												</td>
												<td style="width:70%;padding: 20px;">
													<p>
														<?php
														$bbprestrictsbbpresssection = get_option( 'bbprestrictsbbpresssection' );
														if ( ! ( empty( $bbprestrictsbbpresssection ) ) ) {
															echo '<input type="checkbox" id="bbprestrictsbbpresssection" name="bbprestrictsbbpresssection"  style="" value="yes"  checked="checked"> All Other Sections On Your Site Will Be Opened to Guest ';

														} else {
															echo '<input type="checkbox" id="bbprestrictsbbpresssection" name="bbprestrictsbbpresssection"  style="" value="yes" > All Other Sections On Your Site Will Be Opened to Guest ';
														}
														?>
													</p>
													<p>
														<i style="color:gray;">
															<?php
															echo esc_html__( '# If you enabled this option, "opened Page URLs" setting in ', 'bbp-members-only' );
															echo "<a  style='color:#4e8c9e;' href='" . esc_url( get_option( 'siteurl' ) ) . "/wp-admin/admin.php?page=bbpmemberonlyfree' target='_blank'>Opened Pages Panel</a>";
															echo esc_html__( ' will be ignored', 'bbp-members-only' );
															?>
														</i>
													</p>
												</td>
											</tr>
											<tr>
												<td style="width:30%;vertical-align:top;padding: 30px 20px 20px 20px;">
													<?php
													echo esc_html__( 'Temporarily Turn Off All Featrures:', 'bbp-members-only' );
													?>
												</td>
												<td style="width:70%;padding: 20px;">
													<p>
														<?php
														$bbpdisableallfeature = get_option( 'bbpdisableallfeature' );
														if ( ! ( empty( $bbpdisableallfeature ) ) ) {
															echo '<input type="checkbox" id="bbpdisableallfeature" name="bbpdisableallfeature"  style="" value="yes"  checked="checked"> Temporarily Turn Off All Featrures Of bbPress Members Only ';

														} else {
															echo '<input type="checkbox" id="bbpdisableallfeature" name="bbpdisableallfeature"  style="" value="yes" > Temporarily Turn Off All Featrures Of bbPress Members Only ';
														}
														?>
													</p>
													<p>
														<i style="color:gray">
															<?php echo esc_html__( '# If you enabled this option, all features of bbPress Members Only will be disabled, you site will open to all users', 'bbp-members-only' ); ?>
														</i>
													</p>
												</td>
											</tr>
										</table>
										<br/>
										<?php wp_nonce_field( 'bbpoptionsettinspanelsubmit_free_nonce' ); ?>
										<input type="submit" id="bbpoptionsettinspanelsubmit" name="bbpoptionsettinspanelsubmit" value=" Submit " style="margin:1px 20px;">
									</form>
									<br/>
								</div>
							</div>
						</div>
					</div>
				</div>
				<?php

				tomas_bbpress_members_only_admin_sidebar_about();

				?>
			</div>
		</div>
	</div>
	<div style="clear:both"></div>
	<br/>
	<?php
}
