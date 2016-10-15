<?php
/**
 * This template is used to display the form for submitting a ticket [kbs_form]
 */
global $kbs_form;
?>

<div id="kbs_ticket_wrap">
	<?php do_action( 'kbs_notices' ); ?>
	<div id="kbs_ticket_form_wrap" class="kbs_clearfix">
        <?php do_action( 'kbs_before_ticket_form' ); ?>
        <form<?php kbs_maybe_set_enctype(); ?> id="kbs_ticket_form" class="kbs_form" action="" method="post">
    		<div class="kbs_alert kbs_alert_error kbs_hidden"></div>
            <?php do_action( 'kbs_ticket_form_top' ); ?>

            <fieldset id="kbs_ticket_form_fields">
                <legend><?php esc_attr_e( get_the_title( $kbs_form->ID ) ); ?></legend>

				<?php if( is_ssl() ) : ?>
                    <div id="kbs_secure_site_wrapper">
                        <span class="padlock"></span>
                        <span><?php _e( 'This form is secured and encrypted via SSL', 'kb-support' ); ?></span>
                    </div>
                <?php endif; ?>

                <?php foreach( $kbs_form->fields as $field ) : ?>

					<?php $label_class = ''; ?>
                    <?php $settings    = $kbs_form->get_field_settings( $field->ID ); ?>

                        <p class="kbs-<?php echo $field->post_name; ?>">
                            <?php if ( empty( $settings['hide_label'] ) && 'recaptcha' != $settings['type'] ) : ?>
                            	<?php if ( ! empty( $settings['label_class'] ) ) : ?>
                                	<?php $label_class = ' class="' . sanitize_html_class( $settings['label_class'] ) . '"'; ?>
                                <?php endif; ?>

                                <label for="<?php echo $field->post_name; ?>"<?php echo $label_class; ?>>

									<?php esc_attr_e( get_the_title( $field->ID ) ); ?>

                                    <?php if ( $settings['required'] ) : ?>
                                        <span class="kbs-required-indicator">*</span>
                                    <?php endif; ?>

                                </label>

                                <?php if ( ! empty( $settings['description'] ) && 'label' == $settings['description_pos'] ) : ?>
                                	<?php kbs_display_form_field_description( $field, $settings ); ?>
                                <?php endif; ?>

                            <?php endif; ?>

                            <?php $kbs_form->display_field( $field, $settings ); ?>

                            <?php if ( ! empty( $settings['description'] ) && 'field' == $settings['description_pos'] ) : ?>
								<?php kbs_display_form_field_description( $field, $settings ); ?>
                            <?php endif; ?>

							<?php if ( ! empty( $settings['kb_search'] ) ) : ?>
                            	<div id="kbs-loading" class="kbs-loader kbs-hidden"></div>
                            	<div class="kbs_alert kbs_alert_warn kbs-article-search-results kbs_hidden">
                                    <span class="right">
                                    	<a id="close-search"><?php _e( 'Close', 'kb-support' ); ?></a>
                                    </span>
                                    <strong><?php printf( __( 'Could any of the following %s help resolve your query?', 'kb-support' ), kbs_get_kb_label_plural() ); ?></strong>
                                    <span id="kbs-article-list"></span>
                                </div>
                            <?php endif; ?>

                        </p>

                <?php endforeach; ?>

        		<?php do_action( 'kbs_ticket_form_after_fields' ); ?>
            </fieldset>

            <fieldset id="kbs_ticket_form_submit">

            	<?php do_action( 'kbs_ticket_form_before_submit' ); ?>
            	<?php kbs_render_hidden_form_fields( $kbs_form->ID ); ?>

                <input class="button" name="kbs_ticket_submit" id="kbs_ticket_submit" type="submit" value="<?php esc_attr_e( kbs_get_form_submit_label() ); ?>" />

                <?php do_action( 'kbs_ticket_form_after_submit' ); ?>

            </fieldset>

        	<?php do_action( 'kbs_ticket_form_bottom' ); ?>

        </form>

        <?php do_action( 'kbs_after_ticket_form' ); ?>

    </div><!--end #kbs_ticket_form_wrap-->
</div><!-- end of #kbs_ticket_wrap -->
