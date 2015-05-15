<div style="float:right">
	<?php printf( __( 'View %1$sRegistrations%4$s /  %2$sTransactions%4$s for this %3$sevent%4$s.', 'event_espresso' ), '<a href="' . $filtered_registrations_link . '">', '<a href="' . $filtered_transactions_link . '">', '<a href="' . $event_link . '">', '</a>' ); ?>
</div>
<h3 id="reg-admin-reg-details-reg-nmbr-hdr"><?php echo __( 'Registration # ', 'event_espresso' ) . $reg_nmbr['value'];?></h3>
<h2 id="reg-admin-reg-details-reg-date-hdr"><?php echo $reg_datetime['value'];?></h2>
<h2 id="reg-admin-reg-details-reg-status-hdr">
	<?php echo __( 'Registration Status : ', 'event_espresso' );?>
	<span class="<?php echo $reg_status['class'];?>"><?php echo $reg_status['value'];?></span>
</h2>
<h3>
	<?php _e('Change status to: ', 'event_espresso'); ?>
	<span id="reg-admin-approve-decline-reg-status-spn">
		<?php echo $approve_decline_reg_status_buttons;?>
	</span>
</h3>
<?php if ( $registration->group_size() > 1 ) : ?>
	<a id="scroll-to-other-attendees" class="scroll-to" href="#other-attendees"><?php echo __( 'Scroll to Other Registrations in the Same Transaction', 'event_espresso' );?></a>
<?php endif; ?>


