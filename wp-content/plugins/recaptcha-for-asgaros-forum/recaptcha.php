<div class="wrap">
	<?php if(isset($_REQUEST['nonce_error'])):?>
		<div id="message" class="error notice notice-success is-dismissible">
			<p>Something went wrong!</p>
			<button type="button" class="notice-dismiss">
				<span class="screen-reader-text">Dismiss this notice.</span>
			</button>
		</div>
	<?php elseif(isset($_POST['rfaf_recaptcha_submit'])):?>
		<div id="message" class="updated notice notice-success is-dismissible">
			<p>Updated</p>
			<button type="button" class="notice-dismiss">
				<span class="screen-reader-text">Dismiss this notice.</span>
			</button>
		</div>
	<?php endif;?>
	<div id="poststuff">
		<div id="post-body" class="metabox-holder">
			<div class="postbox-container">
				<form method="post">
					<?php wp_nonce_field( 'rfaf_recaptcha_submit_nonce');?>
					<div class="postbox">
						<h2 class="hndle">Goolge reCAPTCHA</h2>
						<div class="inside">
							<p><a href="https://www.google.com/recaptcha/admin" target="_blank" tabindex="-1">Don't have keys? Get reCAPTCHA v2 Api Keys</a></p>
							<p>
								<label for="forum_title">reCAPTCHA Site Key:</label><br>
								<input class="regular-text" type="text" name="rfaf_recaptcha_site_key" value="<?php echo esc_attr(get_option('rfaf_recaptcha_site_key'));?>" placeholder="**************">
							</p>
							<p>
								<label for="forum_title">reCAPTCHA Server Key:</label><br>
								<input class="regular-text" type="text" name="rfaf_recaptcha_server_key" value="<?php echo esc_attr(get_option('rfaf_recaptcha_server_key'));?>" placeholder="**************">
							</p>
							<p>
								<input type="checkbox" name="rfaf_recaptcha_registerd_user" id="require_login" value="1" <?php checked(esc_attr(get_option('rfaf_recaptcha_registerd_user',false)));?>>
								<label for="require_login">Enable for logged-in users?</label>

							</p>
							<p>
								<hr>
								<input type="submit" name="rfaf_recaptcha_submit" class="button button-primary" value="Save">
							</p>
						</div>
					</div>
					
				</form>
			</div>
		</div>
	</div>
</div>