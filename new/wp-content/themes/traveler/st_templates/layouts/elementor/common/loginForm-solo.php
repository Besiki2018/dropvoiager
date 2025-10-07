<div class="modal fade form-login--solo" id="st-login-form" tabindex="-1" role="dialog">
    <div class="modal-dialog modal-dialog--width" role="document" style="max-width: 450px;">
        <div class="modal-content relative">
            <?php echo st()->load_template('layouts/modern/common/loader'); ?>
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <?php echo TravelHelper::getNewIcon('Ico_close') ?>
                </button>
                <div class="modal-title"><?php echo esc_html__('Log In', 'traveler') ?></div>
                <span class="modal-sub-title"><?php echo esc_html__('Log in to add new things to your wishlist and access your any device', 'traveler') ?></span>
            </div>
            <?php
				if (
					is_plugin_active('traveler-social-login/traveler-social-login.php') &&
					(
						st_social_channel_status('facebook') ||
						st_social_channel_status('google') ||
						st_social_channel_status('twitter')
					)
				):
			?>
                <div class="advanced">
                    <div class="row">
                        <div class="col-xs-12 col-sm-12">
                            <?php if (st_social_channel_status('facebook')): ?>
                                <a onclick="return false" href="#"
                                class="btn_login_fb_link st_login_social_link" data-channel="facebook">
                                    <div class="st-login-facebook">
                                        <div
                                            onlogin="startLoginWithFacebook()"
                                            class="fb-login-button"
                                            data-width="100%"
                                            data-height="48px"
                                            data-max-rows="1"
                                            data-size="large"
                                            login_text="<?php echo esc_html__('Continue with Facebook', 'traveler') ?>"
                                            data-scope="public_profile, email">
                                        </div>

                                    </div>
                                </a>
                            <?php endif; ?>
                        </div>
                        <div class="col-12 col-sm-12">
                            <?php if (st_social_channel_status('google')):
                                echo do_shortcode('[st-google-login type="login"]');
                                ?>
                            <?php endif; ?>
                        </div>
                        <div class="col-xs-12 col-sm-12">
                            <?php if (st_social_channel_status('twitter')): ?>
                                <a href="<?php echo site_url() ?>/social-login/twitter"
                                onclick="return false"
                                class="btn_login_tw_link st_login_social_link" data-channel="twitter">
                                    <span id="button-twitter">
                                        <span class="icon">
											<svg style="color: white" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 512 512">
												<path fill="white" d="M389.2 48h70.6L305.6 224.2 487 464H345L233.7 318.6 106.5 464H35.8L200.7 275.5 26.8 48H172.4L272.9 180.9 389.2 48zM364.4 421.8h39.1L151.1 88h-42L364.4 421.8z"/>
											</svg>
										</span>
										<span class="text"><?php echo esc_html__('Log in with X.com', 'traveler') ?></span>
                                	</span>
                                </a>
                            <?php endif; ?>
                        </div>
                    </div>
                </div>
            <?php endif; ?>
            <div class="modal-body relative">
                <form action="#" class="form" method="post">
                    <input type="hidden" name="st_theme_style" value="modern"/>
                    <input type="hidden" name="action" value="st_login_popup">
                    <input type="hidden" name="post_id" value="<?php echo get_the_ID();?>">
                    <?php if (st_social_channel_status('facebook') || st_social_channel_status('google') || st_social_channel_status('twitter') ): ?>
                        <div class="form-group form-padding">
                            <label class="title-form"><?php echo esc_html__('Or','traveler') ?></label>
                        </div>
                    <?php endif; ?>
                    <div class="form-group">
                        <input type="text" class="form-control" name="username" autocomplete="off"
                               placeholder="<?php echo esc_html__('Email or Username', 'traveler') ?>">

                    </div>
                    <div class="form-group">
                        <input type="password" class="form-control" name="password" autocomplete="off"
                               placeholder="<?php echo esc_html__('Password', 'traveler') ?>">

                    </div>
                    <div class="mt20 st-flex space-between st-icheck">
                        <div class="st-icheck-item">
                            <label for="remember-me" class="c-grey">
                                <input type="checkbox" name="remember" id="remember-me"
                                       value="1"> <?php echo esc_html__('Remember me', 'traveler') ?>
                                <span class="checkmark fcheckbox"></span>
                            </label>
                        </div>
                        <a href="#" class="st-link open-loss-password"
                           data-toggle="modal"><?php echo esc_html__('Forgot Password?', 'traveler') ?></a>
                    </div>
                    <div class="form-group">
                        <input type="submit" name="submit" class="form-submit"
                               value="<?php echo esc_html__('Log In', 'traveler') ?>">
                    </div>
                    <div class="message-wrapper mt20"></div>


                    <div class="mt20 c-grey font-medium f14 text-center">
                        <?php echo esc_html__('New here?', 'traveler') ?>
                        <a href="#"
                           class="st-link open-signup"
                           data-toggle="modal"><?php echo esc_html__('Create an Account', 'traveler') ?></a>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
