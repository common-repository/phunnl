<div id="phunnl-plugin-container">
	<div class="phunnl-masthead">
		<div class="phunnl-masthead__inside-container">
			<div class="phunnl-masthead__logo-container">
                <img class="phunnl-masthead__logo" src="<?php echo esc_url( plugins_url( '../_inc/img/phunnl-logo-blue.png', __FILE__ ) ); ?>" alt="phunnl" />
			</div>
		</div>
	</div>
	<div class="phunnl-lower">
		<?php phunnl_Admin::display_status(); //gets status of phunnl server remvoed for now ?>
		
		<div class="phunnl-box">

			<h2><?php esc_html_e( 'Save Money and Increase Customer Service ROI with phunnl', 'phunnl' ); ?></h2>
			<p><?php esc_html_e( 'Not big enough for to hire a Customer Service department? No problem. Our virtual system will help answer basic questions about your business when customers call.', 'phunnl' ); ?></p>
            <p> <?php esc_html_e( 'You wouldn\'t leave your door unanswered when a customer knocks, don\'t leave a call unanswered.', 'phunnl' ); ?></p>

        
        </div>
		<div class="phunnl-boxes">
           
			<?php if ( ! phunnl::predefined_api_key() ) { ?>
				
            
            <?php if ( $phunnl_user && in_array( $phunnl_user->status, array( 'active', 'active-dunning', 'no-sub', 'missing', 'cancelled', 'suspended' ) ) ) { ?>
					<?php if ( in_array( $phunnl_user->status, array( 'no-sub', 'missing' ) ) ) { ?>
						



					<?php } elseif ( $phunnl_user->status == 'cancelled' ) { ?>
						<div class="phunnl-box">
							<h3><?php esc_html_e( 'Connect via phunnl', 'phunnl' ); ?></h3>
							<form name="phunnl_activate" id="phunnl_activate" action="https://phunnl.com/get/" method="post" class="phunnl-right" target="_blank">
								<input type="hidden" name="passback_url" value="<?php echo esc_url( phunnl_Admin::get_page_url() ); ?>"/>
								<input type="hidden" name="blog" value="<?php echo esc_url( get_option( 'home' ) ); ?>"/>
								<input type="hidden" name="user_id" value="<?php echo esc_attr( $phunnl_user->ID ); ?>"/>
								<input type="hidden" name="redirect" value="upgrade"/>
								<input type="submit" class="phunnl-button phunnl-is-primary" value="<?php esc_attr_e( 'Reactivate phunnl' , 'phunnl' ); ?>"/>
							</form>
							<p><?php echo esc_html( sprintf( __( 'Your subscription for %s is cancelled.' , 'phunnl' ), $phunnl_user->user_email ) ); ?></p>
						</div>



					<?php } elseif ( $phunnl_user->status == 'suspended' ) { ?>
						<div class="centered phunnl-box">
							<h3><?php esc_html_e( 'Connected via phunnl' , 'phunnl' ); ?></h3>
							<p class="phunnl-alert-text"><?php echo esc_html( sprintf( __( 'Your subscription for %s is suspended.' , 'phunnl' ), $phunnl_user->user_email ) ); ?></p>
							<p><?php esc_html_e( 'No worries! Get in touch and we&#8217;ll sort this out.', 'phunnl' ); ?></p>
							<p><a href="https://phunnl.com/contact" class="phunnl-button phunnl-is-primary"><?php esc_html_e( 'Contact phunnl support' , 'phunnl' ); ?></a></p>
						</div>
					<?php } ?>
                     
				<?php } else { ?>

					<div class="phunnl-box">
						<h3><?php esc_html_e( 'Activate phunnl' , 'phunnl' );?></h3>
						<div class="phunnl-right">
						</div>
						<p><?php esc_html_e( 'Complete our quick and easy signup process to activate phunnel!', 'phunnl' ); ?></p>
					</div>
				<?php } ?>


				 <div class="phunnl-box">
					<h3><?php esc_html_e( 'Or enter a Secret Key', 'phunnl' ); ?></h3>
					<p><?php esc_html_e( 'Already have your key? Enter it here.', 'phunnl' ); ?> <a href="https://phunnl.com/about.html" target="_blank"><?php esc_html_e( '(What is a Secret Key?)', 'phunnl' ); ?></a></p>
					
                     <form action="<?php echo esc_url( phunnl_Admin::get_page_url() ); ?>" method="post">
						<?php wp_nonce_field( phunnl_Admin::NONCE ) ?>
						<input type="hidden" name="action" value="enter-key">
						<p style="width: 100%; display: flex; flex-wrap: nowrap; box-sizing: border-box;">
							<input id="key" name="key" type="text" size="60" value="" class="regular-text code" style="flex-grow: 1; margin-right: 1rem;">
							<input type="submit" name="submit" id="submit" class="phunnl-button" value="<?php esc_attr_e( 'Connect with Secret Key', 'phunnl' );?>">
						</p>
					</form>



				</div> 
			<?php } else { ?>
				<div class="phunnl-box">
					<h2><?php esc_html_e( 'Manual Configuration', 'phunnl' ); ?></h2>
					<p><?php echo sprintf( esc_html__( 'A phunnl Secret Key has been defined in the %s file for this site.', 'phunnl' ), '<code>wp-config.php</code>' ); ?></p>
				</div>
			<?php } ?>
		</div>
	</div>
</div>