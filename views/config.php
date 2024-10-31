<div id="phunnl-plugin-container">
	<div class="phunnl-masthead">
		<div class="phunnl-masthead__inside-container">
			<div class="phunnl-masthead__logo-container">
                <img class="phunnl-masthead__logo" src="<?php echo esc_url( plugins_url( '../_inc/img/phunnl-logo-blue.png', __FILE__ ) ); ?>" alt="phunnl" />
			</div>
		</div>
	</div>
	<div class="phunnl-lower">
		<?php if ( phunnl::get_api_key() ) { ?>
			<?php phunnl_Admin::display_status(); ?>
		<?php } ?>
		<?php if ( ! empty( $notices ) ) { ?>
			<?php foreach ( $notices as $notice ) { ?>
				<?php phunnl::view( 'notice', $notice ); ?>
			<?php } ?>
		<?php } ?>
		

		<?php if ( $phunnl_user ):?>


        <div class="phunnl-card">
            <div class="phunnl-section-header">
                <div class="phunnl-section-header__label">
                    <span>
                        <?php esc_html_e( 'Your phunnl Customer Service Phone Number' , 'phunnl'); ?>
                    </span>
                </div>
            </div>

            <div class="inside">
                
                    <table cellspacing="0" class="phunnl-settings">
                        <tbody>
                            <?php if ( ! phunnl::predefined_api_key() ) { ?>
                            <tr>
                                <th class="phunnl-api-key" width="10%" align="left" scope="row">
                                  <h2> <?php echo  $phunnl_user->did ; ?></h2>
                                </th>
                                <tr>
                                    <td colspan="2" width="5%"> </td>
                                </tr>
                        
                           
                            <tr>
                                <td colspan="2" width="5%">You should put this number prominently on your site's Help Page. </td>
                            </tr>
                            <?php } ?>
                        </tbody>
                    </table>
                  
                   
            </div>
        </div>




			<div class="phunnl-card">
				<div class="phunnl-section-header">
					<div class="phunnl-section-header__label">
						<span><?php esc_html_e( 'Settings' , 'phunnl'); ?></span>
					</div>
				</div>

				<div class="inside">
					<form action="<?php echo esc_url( phunnl_Admin::get_page_url() ); ?>" method="POST">
						<table cellspacing="0" class="phunnl-settings">
							<tbody>
								<?php if ( ! phunnl::predefined_api_key() ) { ?>
								<tr>
									<th class="phunnl-api-key" width="10%" align="left" scope="row"><?php esc_html_e('Secret Key', 'phunnl');?></th>
									<td width="5%"/>
									<td align="left">
										<span class="api-key"><input id="key" name="key" type="text" size="15" value="<?php echo esc_attr( get_option('wordpress_phunnl_api_key') ); ?>" class="<?php echo esc_attr( 'regular-text code ' . $phunnl_user->status ); ?>"></span>
									</td>
								</tr>
								<?php } ?>
								<?php if ( isset( $_GET['ssl_status'] ) ) { ?>
									<tr>
										<th align="left" scope="row"><?php esc_html_e( 'SSL Status', 'phunnl' ); ?></th>
										<td></td>
										<td align="left">
											<p>
												<?php

												if ( ! wp_http_supports( array( 'ssl' ) ) ) {
													?><b><?php esc_html_e( 'Disabled.', 'phunnl' ); ?></b> <?php esc_html_e( 'Your Web server cannot make SSL requests; contact your Web host and ask them to add support for SSL requests.', 'phunnl' ); ?><?php
												}
												else {
													$ssl_disabled = get_option( 'phunnl_ssl_disabled' );

													if ( $ssl_disabled ) {
														?><b><?php esc_html_e( 'Temporarily disabled.', 'phunnl' ); ?></b> <?php esc_html_e( 'phunnl encountered a problem with a previous SSL request and disabled it temporarily. It will begin using SSL for requests again shortly.', 'phunnl' ); ?><?php
													}
													else {
														?><b><?php esc_html_e( 'Enabled.', 'phunnl' ); ?></b> <?php esc_html_e( 'All systems functional.', 'phunnl' ); ?><?php
													}
												}

												?>
											</p>
										</td>
									</tr>
								<?php } ?>
                                <tr>

                                    <th class="phunnl-api-key" width="10%" align="left" scope="row">
                                        <p>
                                            &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
                                    </th>
                                    <td width="5%"></td>
                                    <td align="left">
                                            &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
                                    </td>
                                </tr>
                               
							</tbody>
						</table>
						<div class="phunnl-card-actions">
							<?php if ( ! phunnl::predefined_api_key() ) { ?>
							<div id="delete-action">
								<a class="submitdelete deletion" href="<?php echo esc_url( phunnl_Admin::get_page_url( 'delete_key' ) ); ?>"><?php esc_html_e('Disconnect this account', 'phunnl'); ?></a>
							</div>
							<?php } ?>
							<?php wp_nonce_field(phunnl_Admin::NONCE) ?>
							<!--<div id="publishing-action">
								<input type="hidden" name="action" value="enter-key">
								<input type="submit" name="submit" id="submit" class="phunnl-button phunnl-is-primary" value="<?php esc_attr_e('Save Changes', 'phunnl');?>">
							</div>-->
							<div class="clear"></div>
						</div>
					</form>
				</div>
			</div>
			
			<?php if ( ! phunnl::predefined_api_key() ) { ?>
				<div class="phunnl-card">
					<div class="phunnl-section-header">
						<div class="phunnl-section-header__label">
							<span><?php esc_html_e( 'Account' , 'phunnl'); ?></span>
						</div>
					</div>
				
					<div class="inside">
						<table cellspacing="0" border="0" class="phunnl-settings">
							<tbody>

                                <tr>
									<th scope="row" align="left"><?php esc_html_e( 'Subscription Type' , 'phunnl');?></th>
									<td width="5%"/>
									<td align="left">
										<p>phunnl <?php echo esc_html( $phunnl_user->account_name ); ?> plan</p>
                                        <p>
                                            Monthly Fee: <?php echo esc_html( $phunnl_user->monthly_fee ); ?>
                                        </p>
                                        <p>
                                           Fee Per Minute:  <?php echo esc_html( $phunnl_user->fee_per_minute ); ?>
                                        </p>
									</td>
								</tr>
								<tr>
									<th scope="row" align="left"><?php esc_html_e( 'Status' , 'phunnl');?></th>
									<td width="5%"/>
									<td align="left">
										<p><?php 
											if ( 'cancelled' == $phunnl_user->status ) :
												esc_html_e( 'Cancelled', 'phunnl' ); 
											elseif ( 'suspended' == $phunnl_user->status ) :
												esc_html_e( 'Suspended', 'phunnl' );
											elseif ( 'missing' == $phunnl_user->status ) :
												esc_html_e( 'Missing', 'phunnl' ); 
											elseif ( 'no-sub' == $phunnl_user->status ) :
												esc_html_e( 'No Subscription Found', 'phunnl' );
											else :
												esc_html_e( 'Active', 'phunnl' );  
											endif; ?></p>
                                            Free Minutes Remaining: <?php echo esc_html( $phunnl_user->free_minutes_remaining ); ?>
									</td>
								</tr>
								<?php if ( $phunnl_user->next_billing_date ) : ?>
								<tr>
									<th scope="row" align="left"><?php esc_html_e( 'Next Billing Date' , 'phunnl');?></th>
									<td width="5%"/>
									<td align="left">
										<p><?php echo $phunnl_user->next_billing_date; ?></p>
									</td>
								</tr>
								<?php endif; ?>
							</tbody>
						</table>
						<div class="phunnl-card-actions">
							<!--<div id="publishing-action">
								<?php phunnl::view( 'get', array( 'text' => ( $phunnl_user->account_type == 'free-api-key' && $phunnl_user->status == 'active' ? __( 'Upgrade' , 'phunnl') : __( 'Change' , 'phunnl') ), 'redirect' => 'upgrade' ) ); ?>
							</div>-->
							<div class="clear"></div>
						</div>
					</div>
				</div>
			<?php } ?>
		<?php endif;?>
	</div>
</div>