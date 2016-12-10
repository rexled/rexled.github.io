<?php
/**
 * Login form
 *
 * @author 		WooThemes
 * @package 	WooCommerce/Templates
 * @version     2.1.0
 */

if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly

if ( is_user_logged_in() ) 
	return;
?>
<div class="row">
    <div class="col-md-12">
        <form method="post" class="login" <?php if ( $hidden ) echo 'style="display:none;"'; ?>>
            <div class="row">
                <div class="col-sm-6 col-sm-offset-3">

                    <?php do_action( 'woocommerce_login_form_start' ); ?>

                    <?php if ( $message ) echo wpautop( wptexturize( $message ) ); ?>

                    <p class="form-row">
                        <label for="username"><?php _e( 'Username or email', 'woocommerce' ); ?> <span class="required">*</span></label>
                        <input type="text" class="input-text" name="username" id="username" />
                    </p>
                    <p class="form-row">
                        <label for="password"><?php _e( 'Password', 'woocommerce' ); ?> <span class="required">*</span></label>
                        <input class="input-text" type="password" name=W"password" id="password" />
                    </p>
                    <div class="clear"></div>

                    <?php do_action( 'woocommerce_login_form' ); ?>

                    <p class="form-row">
                        <?php wp_nonce_field( 'woocommerce-login' ); ?>
                        <label for="rememberme">
                            <input name="rememberme" type="checkbox" id="rememberme" value="forever" /> <?php _e( 'Remember me', 'woocommerce' ); ?>
                        </label>
                        <br>
                        <input type="submit" class="button alt btn-block" name="login" value="<?php _e( 'Login', 'woocommerce' ); ?>" />
                        <input type="hidden" name="redirect" value="<?php echo esc_url( $redirect ) ?>" />
                    </p>
                    <p class="lost_password">
                        <a href="<?php echo esc_url( wc_lostpassword_url() ); ?>"><?php _e( 'Lost your password?', 'woocommerce' ); ?></a>
                    </p>

                    <div class="clear"></div>

                    <?php do_action( 'woocommerce_login_form_end' ); ?>
                </div>
            </div>


        </form>
    </div>
</div>
