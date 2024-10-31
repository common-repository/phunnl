<?php


if($type == "plugin") : ?>
<div class="updated" style="padding: 0; margin: 0; border: none; background: none;">
    <div class="phunnl_activate">
        <div class=""></div>
        <div class="aa_button_container">
            <div class="aa_button_border">
                <a href="<?php echo esc_url( phunnl_Admin::get_page_url() ); ?>">
                    <input type="button" class="aa_button" value="<?php esc_attr_e( 'phunnl is ready to use after Setup!', 'phunnl' ); ?>" />
                </a>
            </div>
        </div>
        <div class="aa_description">
            <?php _e('<strong>Ready?</strong> - configure your phunnl and start experiencing customer service bliss', 'phunnl');?>
        </div>
    </div>

</div>

</div>
<?php elseif ( $type == 'notice' ) :?>
<div class="phunnl-alert phunnl-critical">
	<h3 class="phunnl-key-status failed"><?php echo $notice_header; ?></h3>
	<p class="phunnl-description">
		<?php echo $notice_text; ?>
	</p>
</div>
<?php elseif ( $type == 'missing-functions' ) :?>
<div class="phunnl-alert phunnl-critical">
	<h3 class="phunnl-key-status failed"><?php esc_html_e('Network functions are disabled.', 'phunnl'); ?></h3>
	<p class="phunnl-description"><?php printf( __('Your web host or server administrator has disabled PHP&#8217;s <code>gethostbynamel</code> function.  <strong>phunnl cannot work correctly until this is fixed.</strong>  Please contact your web host or firewall administrator and give them <a href="%s" target="_blank">this information about phunnl&#8217;s system requirements</a>.', 'phunnl'), 'https://blog.phunnl.com/phunnl-hosting-faq/'); ?></p>
</div>
<?php elseif ( $type == 'servers-be-down' ) :?>
<div class="phunnl-alert phunnl-critical">
	<h3 class="phunnl-key-status failed"><?php esc_html_e("Your site can&#8217;t connect to the phunnl servers.", 'phunnl'); ?></h3>
	<p class="phunnl-description"><?php printf( __('Your firewall may be blocking phunnl from connecting to its API. Please contact your host and refer to <a href="%s" target="_blank">our guide about firewalls</a>.', 'phunnl'), 'https://blog.phunnl.com/phunnl-hosting-faq/'); ?></p>
</div>
<?php elseif ( $type == 'active-dunning' ) :?>
<div class="phunnl-alert phunnl-critical">
	<h3 class="phunnl-key-status"><?php esc_html_e("Please update your payment information.", 'phunnl'); ?></h3>
	<p class="phunnl-description"><?php printf( __('We cannot process your payment. Please <a href="%s" target="_blank">update your payment details</a>.', 'phunnl'), 'https://phunnl.com/account/'); ?></p>
</div>
<?php elseif ( $type == 'cancelled' ) :?>
<div class="phunnl-alert phunnl-critical">
	<h3 class="phunnl-key-status"><?php esc_html_e("Your phunnl plan has been cancelled.", 'phunnl'); ?></h3>
	<p class="phunnl-description"><?php printf( __('Please visit your <a href="%s" target="_blank">phunnl account page</a> to reactivate your subscription.', 'phunnl'), 'https://phunnl.com/account/'); ?></p>
</div>
<?php elseif ( $type == 'suspended' ) :?>
<div class="phunnl-alert phunnl-critical">
	<h3 class="phunnl-key-status failed"><?php esc_html_e("Your phunnl subscription is suspended.", 'phunnl'); ?></h3>
	<p class="phunnl-description"><?php printf( __('Please contact <a href="%s" target="_blank">phunnl support</a> for assistance.', 'phunnl'), 'https://phunnl.com/contact/'); ?></p>
</div>
<?php elseif ( $type == 'missing' ) :?>
<div class="phunnl-alert phunnl-critical">
	<h3 class="phunnl-key-status failed"><?php esc_html_e( 'There is a problem with your Secret Key.', 'phunnl'); ?></h3>
	<p class="phunnl-description"><?php printf( __('Please contact <a href="%s" target="_blank">phunnl support</a> for assistance.', 'phunnl'), 'https://phunnl.com/contact/'); ?></p>
</div>
<?php elseif ( $type == 'no-sub' ) :?>
<div class="phunnl-alert phunnl-critical">
	<h3 class="phunnl-key-status failed"><?php esc_html_e( 'You don&#8217;t have an phunnl plan.', 'phunnl'); ?></h3>
	<p class="phunnl-description">
		<?php printf( __( ' A plan has not been assigned to your account, and we&#8217;d appreciate it if you&#8217;d <a href="%s" target="_blank">sign into your account</a> and choose one.', 'phunnl'), 'https://phunnl.com/account/upgrade/' ); ?>
		<br /><br />
		<?php printf( __( 'Please <a href="%s" target="_blank">contact our support team</a> with any questions.', 'phunnl' ), 'https://phunnl.com/contact/' ); ?>
	</p>
</div>

<?php elseif ( $type == 'new-key-valid' ) :
	global $wpdb;
	
	$check_pending_link = false; //todo use this in the case that their account does not yet have a did or they are not completed with step2
 
	?>
<div class="phunnl-alert phunnl-active">
	<h3 class="phunnl-key-status"><?php esc_html_e( 'phunnl is now activated! Your subscription is current.', 'phunnl' ); ?></h3>
	<?php if ( $check_pending_link ) { ?>
		<p class="phunnl-description"><?php printf( __( 'Would you like to <a href="%s">check pending comments</a>?', 'phunnl' ), esc_url( $check_pending_link ) ); ?></p>
	<?php } ?>
</div>
<?php elseif ( $type == 'new-key-invalid' ) :?>
<div class="phunnl-alert phunnl-critical">
	<h3 class="phunnl-key-status"><?php esc_html_e( 'The key you entered is invalid. Please double-check it.' , 'phunnl'); ?></h3>
</div>
<?php elseif ( $type == 'existing-key-invalid' ) :?>
<div class="phunnl-alert phunnl-critical">
	<h3 class="phunnl-key-status"><?php esc_html_e( 'Your Secret Key is no longer valid. Please enter a new key or contact support@phunnl.com.' , 'phunnl'); ?></h3>
</div>
<?php elseif ( $type == 'new-key-failed' ) :?>
<div class="phunnl-alert phunnl-critical">
	<h3 class="phunnl-key-status"><?php esc_html_e( 'The Secret Key you entered could not be verified.' , 'phunnl'); ?></h3>
	<p class="phunnl-description"><?php printf( __('The connection to phunnl.com could not be established. Please refer to <a href="%s" target="_blank">our guide about firewalls</a> and check your server configuration.', 'phunnl'), 'https://blog.phunnl.com/phunnl-hosting-faq/'); ?></p>
</div>
<?php elseif ( $type == 'limit-reached' && in_array( $level, array( 'yellow', 'red' ) ) ) :?>
<div class="phunnl-alert phunnl-critical">
	<?php if ( $level == 'yellow' ): ?>
	<h3 class="phunnl-key-status failed"><?php esc_html_e( 'You&#8217;re using your phunnl key on more sites than your Pro subscription allows.', 'phunnl' ); ?></h3>
	<p class="phunnl-description">
		<?php printf( __( 'Your Pro subscription allows the use of phunnl on only one site. Please <a href="%s" target="_blank">purchase additional Pro subscriptions</a> or upgrade to an Enterprise subscription that allows the use of phunnl on unlimited sites.', 'phunnl' ), 'https://docs.phunnl.com/billing/add-more-sites/' ); ?>
		<br /><br />
		<?php printf( __( 'Please <a href="%s" target="_blank">contact our support team</a> with any questions.', 'phunnl' ), 'https://phunnl.com/contact/'); ?>
	</p>
	<?php elseif ( $level == 'red' ): ?>
	<h3 class="phunnl-key-status failed"><?php esc_html_e( 'You&#8217;re using phunnl on far too many sites for your Pro subscription.', 'phunnl' ); ?></h3>
	<p class="phunnl-description">
		<?php printf( __( 'To continue your service, <a href="%s" target="_blank">upgrade to an Enterprise subscription</a>, which covers an unlimited number of sites.', 'phunnl'), 'https://phunnl.com/account/upgrade/' ); ?>
		<br /><br />
		<?php printf( __( 'Please <a href="%s" target="_blank">contact our support team</a> with any questions.', 'phunnl' ), 'https://phunnl.com/contact/'); ?>
	</p>
	<?php endif; ?>
</div>
<?php endif;?>