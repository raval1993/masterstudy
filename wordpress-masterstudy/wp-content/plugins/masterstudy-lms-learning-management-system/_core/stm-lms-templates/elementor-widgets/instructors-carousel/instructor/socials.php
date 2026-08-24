<div class="ms_lms_instructors_carousel__item_socials <?php echo ( ! empty( $socials_presets ) ) ? esc_attr( $socials_presets ) : 'style_1'; ?>">
	<?php if ( ! empty( $user['meta']['facebook'] ) ) { ?>
		<a href="<?php echo esc_url( $user['meta']['facebook'] ); ?>" class="ms_lms_instructors_carousel__item_socials_link">
			<i class="stmlms-facebook-f"></i>
		</a>
	<?php } ?>
	<?php if ( ! empty( $user['meta']['instagram'] ) ) { ?>
		<a href="<?php echo esc_url( $user['meta']['instagram'] ); ?>" class="ms_lms_instructors_carousel__item_socials_link">
			<i class="stmlms-instagram"></i>
		</a>
	<?php } ?>
	<?php if ( ! empty( $user['meta']['twitter'] ) ) { ?>
		<a href="<?php echo esc_url( $user['meta']['twitter'] ); ?>" class="ms_lms_instructors_carousel__item_socials_link">
			<i class="stmlms-twitter-2"></i>
		</a>
	<?php } ?>
	<?php if ( ! empty( $user['meta']['linkedin'] ) ) { ?>
		<a href="<?php echo esc_url( $user['meta']['linkedin'] ); ?>" class="ms_lms_instructors_carousel__item_socials_link">
			<i class="stmlms-linkedin-2"></i>
		</a>
	<?php } ?>
</div>
