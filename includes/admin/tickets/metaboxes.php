<?php	
/**
 * Manage kbs_ticket post metaboxes.
 * 
 * @since		0.1
 * @package		KBS
 * @subpackage	Functions/Metaboxes
 */

// Exit if accessed directly.
if ( ! defined( 'ABSPATH' ) ) exit;

/**
 * Remove unwanted metaboxes to for the kbs_ticket post type.
 *
 * @since	1.0
 * @param
 * @return
 */
function kbs_ticket_remove_meta_boxes()	{
	$metaboxes = apply_filters( 'kbs_ticket_remove_metaboxes',
		array(
			array( 'submitdiv', 'kbs_ticket', 'side' ),
		)
	);
	
	foreach( $metaboxes as $metabox )	{
		remove_meta_box( $metabox[0], $metabox[1], $metabox[2] );
	}
} // kbs_ticket_remove_meta_boxes
add_action( 'admin_head', 'kbs_ticket_remove_meta_boxes' );

/**
 * Define and add the metaboxes for the kbs_ticket post type.
 *
 * @since	1.0
 * @param
 * @return
 */
function kbs_ticket_add_meta_boxes( $post )	{

	global $kbs_ticket, $kbs_ticket_update;

	$save              = __( 'Create', 'kb-support' );
	$kbs_ticket_update = false;
	$kbs_ticket        = new KBS_Ticket( $post->ID );
	
	if ( 'draft' != $post->post_status && 'auto-draft' != $post->post_status )	{
		$save              = __( 'Update', 'kb-support' );
		$kbs_ticket_update = true;
		remove_post_type_support( $post->post_type, 'editor' );
	}

	add_meta_box(
		'kbs-ticket-metabox-save',
		sprintf( '%1$s %2$s', $save, kbs_get_ticket_label_singular() ),
		'kbs_ticket_metabox_save_callback',
		'kbs_ticket',
		'side',
		'high',
		array()
	);
	
	if ( $kbs_ticket_update )	{
		add_meta_box(
			'kbs-ticket-metabox-ticket-details',
			sprintf( __( 'Original %1$s', 'kb-support' ), kbs_get_ticket_label_singular() ),
			'kbs_ticket_metabox_details_callback',
			'kbs_ticket',
			'normal',
			'high',
			array()
		);
		
		add_meta_box(
			'kbs-ticket-metabox-ticket-reply',
			sprintf( __( 'Reply to %1$s', 'kb-support' ), kbs_get_ticket_label_singular() ),
			'kbs_ticket_metabox_reply_callback',
			'kbs_ticket',
			'normal',
			'high',
			array()
		);
	}
	
} // kbs_ticket_add_meta_boxes
add_action( 'add_meta_boxes_kbs_ticket', 'kbs_ticket_add_meta_boxes' );

/**
 * The callback function for the save metabox.
 *
 * @since	1.0
 * @global	obj		$post				WP_Post object
 * @global	obj		$kbs_ticket			KBS_Ticket class object
 * @global	bool	$kbs_ticket_update	True if this ticket is being updated, false if new
 * @param
 * @return
 */
function kbs_ticket_metabox_save_callback()	{
	global $post, $kbs_ticket, $kbs_ticket_update;

	/*
	 * Output the items for the save metabox
	 * @since	1.0
	 * @param	int	$post_id	The Ticket post ID
	 */
	do_action( 'kbs_ticket_status_fields', $post->ID );
} // kbs_ticket_metabox_save_callback

/**
 * The callback function for the original ticket details metabox.
 *
 * @since	1.0
 * @global	obj		$post				WP_Post object
 * @global	obj		$kbs_ticket			KBS_Ticket class object
 * @global	bool	$kbs_ticket_update	True if this ticket is being updated, false if new
 * @param
 * @return
 */
function kbs_ticket_metabox_details_callback()	{
	global $post, $kbs_ticket, $kbs_ticket_update;

	/*
	 * Output the items for the details metabox
	 * @since	1.0
	 * @param	int	$post_id	The Ticket post ID
	 */
	do_action( 'kbs_ticket_detail_fields', $post->ID );
} // kbs_ticket_metabox_details_callback

/**
 * The callback function for the ticket reply metabox.
 *
 * @since	1.0
 * @global	obj		$post				WP_Post object
 * @global	obj		$kbs_ticket			KBS_Ticket class object
 * @global	bool	$kbs_ticket_update	True if this ticket is being updated, false if new
 * @param
 * @return
 */
function kbs_ticket_metabox_reply_callback()	{
	global $post, $kbs_ticket, $kbs_ticket_update;

	/*
	 * Output the items for the details metabox
	 * @since	1.0
	 * @param	int	$post_id	The Ticket post ID
	 */
	do_action( 'kbs_ticket_reply_fields', $post->ID );
} // kbs_ticket_metabox_reply_callback

/**
 * Display the save ticket metabox row.
 *
 * @since	1.0
 * @global	obj		$kbs_ticket			KBS_Ticket class object
 * @global	bool	$kbs_ticket_update	True if this ticket is being updated, false if new.
 * @param	int		$ticket_id			The ticket post ID.
 * @return	str
 */
function kbs_ticket_metabox_save_row( $ticket_id )	{

	global $kbs_ticket, $kbs_ticket_update;
	
	?>
	<div class="submitbox" id="submitpost">
		<div id="minor-publishing">
        	<div id="minor-publishing-actions">
            
            </div><!-- #minor-publishing-actions -->
            <div id="kbs-ticket-actions">
                <p class="dashicons-before dashicons-post-status">&nbsp;&nbsp;<label for="post_status"><?php _e( 'Status:' ); ?></label>
                    <?php echo KBS()->html->ticket_status_dropdown( 'post_status', $kbs_ticket->post_status ); ?></p>

                    <p class="dashicons-before dashicons-admin-users"></span>&nbsp;&nbsp;<label for="post_author"><?php _e( 'Customer:', 'kb-support' ); ?></label>
                    <?php echo KBS()->html->customer_dropdown( 'post_author', $kbs_ticket->post_author ); ?></p>
                    
                    <p class="dashicons-before dashicons-businessman"></span>&nbsp;&nbsp;<label for="kbs_agent"><?php _e( 'Agent:', 'kb-support' ); ?></label>
                    <?php echo KBS()->html->agent_dropdown( 'kbs_agent', ( ! empty( $kbs_ticket->agent ) ? $kbs_ticket->agent : get_current_user_id() ) ); ?></p>
                    
                <?php do_action( 'kbs_ticket_metabox_after_agent', $ticket_id ); ?>
    
                <p><a class="kbs-delete"><?php printf( __( 'Delete %s', 'kb-support' ), kbs_get_ticket_label_singular() ); ?></a>
                <?php
                
                submit_button( 
                    sprintf( '%s %s',
                        empty( $kbs_ticket_update ) ? __( 'Add', 'kb-support' ) : __( 'Update', 'kb-support' ),
                        kbs_get_ticket_label_singular()
                    ),
                    'primary',
                    'save',
                    false,
                    array( 'id' => 'save-post' )
                ); ?>
                </p>
            </div><!-- #kbs-ticket-actions -->
        </div><!-- #minor-publishing -->
    </div><!-- #submitpost -->
    <?php
	
} // kbs_ticket_metabox_save_row
add_action( 'kbs_ticket_status_fields', 'kbs_ticket_metabox_save_row', 10 );

/**
 * Output the SLA data for the ticket.
 *
 * @since	1.0
 * @global	obj		$kbs_ticket			KBS_Ticket class object
 * @global	bool	$kbs_ticket_update	True if this ticket is being updated, false if new.
 * @param	int		$ticket_id			The ticket post ID.
 * @return	str
 */
function kbs_ticket_metabox_sla_row( $ticket_id )	{
	
	global $kbs_ticket, $kbs_ticket_update;
	
	if ( ! kbs_get_option( 'sla_tracking' ) || ! $kbs_ticket_update )	{
		return;
	}
	
	$sla_respond_class  = '';
	$sla_resolve_class  = '';
	$sla_respond_remain = '';
	$sla_resolve_remain = '';
	
	if ( $kbs_ticket_update )	{
		$respond            = $kbs_ticket->get_target_respond();
		$resolve            = $kbs_ticket->get_target_resolve();
		$sla_respond_class  = kbs_sla_has_passed( $kbs_ticket->ID ) ? '_over' : '';
		$sla_resolve_class  = kbs_sla_has_passed( $kbs_ticket->ID, 'resolve' ) ? '_over' : '';
		$sla_respond_remain = ' ' . $kbs_ticket->get_sla_remain();
		$sla_resolve_remain = ' ' . $kbs_ticket->get_sla_remain( 'resolve' );
	} else	{
		$respond = date_i18n( get_option( 'date_format' ) . ' ' . get_option( 'time_format' ), strtotime( kbs_calculate_sla_target_response() ) );
		$resolve = date_i18n( get_option( 'date_format' ) . ' ' . get_option( 'time_format' ), strtotime( kbs_calculate_sla_target_resolution() ) );
	}
	
	?>
    <p><strong><?php _e( 'SLA Status', 'kb-support' ); ?></strong></p>
    <p>&nbsp;<i class="fa fa-dot-circle-o fa-lg" aria-hidden="true"></i>&nbsp;&nbsp;<label><?php _e( 'Respond:', 'kb-support' ); ?></label>
    	&nbsp;<span class="dashicons dashicons-flag kbs_sla_status<?php echo $sla_respond_class; ?>" title="<?php echo $respond; ?>"></span><?php echo $sla_respond_remain; ?></p>
        
    <p>&nbsp;<i class="fa fa-clock-o fa-lg" aria-hidden="true"></i>&nbsp;&nbsp;<label><?php _e( 'Resolve:', 'kb-support' ); ?></label>
    	&nbsp;<span class="dashicons dashicons-flag kbs_sla_status<?php echo $sla_resolve_class; ?>" title="<?php echo $resolve; ?>"></span><?php echo $sla_resolve_remain; ?></p>
    
    <?php
} // kbs_ticket_metabox_sla_row
add_action( 'kbs_ticket_metabox_after_agent', 'kbs_ticket_metabox_sla_row', 10 );

/**
 * Display the customer details row.
 *
 * @since	1.0
 * @global	obj		$kbs_ticket			KBS_Ticket class object
 * @global	bool	$kbs_ticket_update	True if this ticket is being updated, false if new.
 * @param	int		$ticket_id			The ticket post ID.
 * @return	str
 */
function kbs_ticket_metabox_customer_row( $ticket_id )	{

	global $kbs_ticket, $kbs_ticket_update;
	
	?>
	<div id="kbs-original-ticket-wrap" class="kbs_ticket_wrap">
        <p><?php echo $kbs_ticket->get_content(); ?></p>
    </div>
    <?php
		
} // kbs_ticket_metabox_customer_row
add_action( 'kbs_ticket_detail_fields', 'kbs_ticket_metabox_customer_row', 10 );

/**
 * Display the original ticket details row.
 *
 * @since	1.0
 * @global	obj		$kbs_ticket			KBS_Ticket class object
 * @global	bool	$kbs_ticket_update	True if this ticket is being updated, false if new.
 * @param	int		$ticket_id			The ticket post ID.
 * @return	str
 */
function kbs_ticket_metabox_details_row( $ticket_id )	{

	global $kbs_ticket, $kbs_ticket_update;
	
	?>
	<div id="kbs-original-ticket-wrap" class="kbs_ticket_wrap">
        <p><?php echo $kbs_ticket->get_content(); ?></p>
    </div>
    <?php
		
} // kbs_ticket_metabox_details_row
add_action( 'kbs_ticket_detail_fields', 'kbs_ticket_metabox_details_row', 20 );

/**
 * Display the ticket files row.
 *
 * @since	1.0
 * @global	obj		$kbs_ticket			KBS_Ticket class object
 * @global	bool	$kbs_ticket_update	True if this ticket is being updated, false if new.
 * @param	int		$ticket_id			The ticket post ID.
 * @return	str
 */
function kbs_ticket_metabox_files_row( $ticket_id )	{

	global $kbs_ticket, $kbs_ticket_update;
	
	if ( ! kbs_get_option( 'file_uploads' ) )	{
		return;
	}

	if ( $kbs_ticket_update )	{

		$files = $kbs_ticket->get_files();
		
		if ( ! $files )	{
			return;
		}

		?>
		<div id="kbs-original-ticket-files-wrap" class="kbs_ticket_files_wrap">
        	<p><strong><?php _e( 'Attached Files', 'kb-support' ); ?></strong></p>
			<?php foreach( $files as $file ) : ?>
                <p><a href="<?php echo wp_get_attachment_url( $file->ID ); ?>" target="_blank"><?php echo basename( get_attached_file( $file->ID ) ); ?></a></p>
            <?php endforeach; ?>
		</div>
	
		<?php

	}
		
} // kbs_ticket_metabox_details_row
add_action( 'kbs_ticket_detail_fields', 'kbs_ticket_metabox_files_row', 30 );

/**
 * Display the ticket reply row.
 *
 * @since	1.0
 * @global	obj		$kbs_ticket			KBS_Ticket class object
 * @global	bool	$kbs_ticket_update	True if this ticket is being updated, false if new.
 * @param	int		$ticket_id			The ticket post ID.
 * @return	str
 */
function kbs_ticket_metabox_reply_row( $ticket_id )	{

	global $kbs_ticket, $kbs_ticket_update;

	if ( 'closed' == $kbs_ticket->post_status )	{
		printf( __( 'This %1$s is currently closed. <a href="%2$s">Re-open %1$s.</a>', 'kb-support' ),
			kbs_get_ticket_label_singular(),
			wp_nonce_url( add_query_arg( 'kbs-action', 're-open-ticket', get_edit_post_link( $ticket_id ) ), 'kbs-reopen-ticket', 'kbs-ticket-nonce' )
		);
	} else	{
		$settings = apply_filters( 'kbs_ticket_reply_mce_settings', array(
			'textarea_rows'    => 5,
			'drag_drop_upload' => true,
			'quicktags'        => false
		) );

		wp_editor( '', 'kbs_ticket_reply', $settings );

		/*
		 * Fires immediately before the reply buttons are output
		 * @since	1.0
		 * @param	int	$post_id	The Ticket post ID
		 */
		do_action( 'kbs_ticket_before_reply_buttons', $ticket_id );

		?>
        <div id="kbs-ticket-reply-container">
            <div class="kbs-reply"><a id="kbs-reply-close" class="button button-primary"><?php _e( 'Reply and Close', 'kb-support' ); ?></a></div>
            <div class="kbs-reply"><a id="kbs-reply-update" class="button button-secondary"><?php _e( 'Reply', 'kb-support' ); ?></a></div>
        </div>
        <span id="kbs-loading" class="kbs-loader kbs-hidden"><img src="<?php echo KBS_PLUGIN_URL . 'assets/images/loading.gif'; ?>" /></span>
        <?php
	}
		
} // kbs_ticket_metabox_details_row
add_action( 'kbs_ticket_reply_fields', 'kbs_ticket_metabox_reply_row', 10 );
