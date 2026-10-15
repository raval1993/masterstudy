<?php
defined( 'ABSPATH' ) || exit;

function mls_validate_form_schema( array $schema ): array {
	if ( ! array_is_list( $schema ) || count( $schema ) > 15 ) { throw new InvalidArgumentException( 'Use up to 15 learner fields.' ); }
	$clean = array(); $names = array();
	foreach ( $schema as $field ) {
		if ( ! is_array( $field ) ) { throw new InvalidArgumentException( 'Invalid learner field.' ); }
		$name = $field['name'] ?? ''; $label = $field['label'] ?? ''; $type = $field['type'] ?? 'textarea';
		if ( ! is_string( $name ) || ! preg_match( '/^[a-z][a-z0-9_]{0,39}$/D', $name ) || isset( $names[ $name ] ) || in_array( $name, array( 'schema', 'answers', 'action' ), true ) ) { throw new InvalidArgumentException( 'Field keys must be unique lowercase names, up to 40 characters.' ); }
		if ( ! is_string( $label ) || ! trim( $label ) || strlen( $label ) > 160 || ! in_array( $type, array( 'text', 'textarea', 'select', 'checkbox', 'date', 'number' ), true ) ) { throw new InvalidArgumentException( 'Invalid field label or type.' ); }
		$row = array( 'name' => $name, 'label' => sanitize_text_field( $label ), 'type' => $type, 'required' => ! empty( $field['required'] ) );
		if ( ! $row['label'] ) { throw new InvalidArgumentException( 'Field labels cannot be empty.' ); }
		if ( $type === 'select' ) {
			$options = $field['options'] ?? array();
			if ( ! is_array( $options ) || ! array_is_list( $options ) || count( $options ) < 2 || count( $options ) > 20 ) { throw new InvalidArgumentException( 'Select fields need 2-20 unique choices.' ); }
			foreach ( $options as $option ) { if ( ! is_string( $option ) || ! trim( $option ) || strlen( $option ) > 100 ) { throw new InvalidArgumentException( 'Invalid select choice.' ); } }
			$row['options'] = array_map( 'sanitize_text_field', $options );
			if ( in_array( '', $row['options'], true ) || count( array_unique( $row['options'] ) ) !== count( $options ) ) { throw new InvalidArgumentException( 'Select choices must be unique and nonempty.' ); }
		}
		if ( $type === 'number' ) {
			foreach ( array( 'min' => -1000000, 'max' => 1000000 ) as $key => $default ) {
				$value = $field[ $key ] ?? $default;
				if ( ! is_int( $value ) || $value < -1000000 || $value > 1000000 ) { throw new InvalidArgumentException( 'Number limits must be whole numbers between -1000000 and 1000000.' ); }
				$row[ $key ] = $value;
			}
			if ( $row['min'] > $row['max'] ) { throw new InvalidArgumentException( 'Minimum exceeds maximum.' ); }
		}
		$names[ $name ] = true; $clean[] = $row;
	}
	return $clean;
}

function mls_form_fingerprint( array $schema ): string { return hash( 'sha256', wp_json_encode( mls_validate_form_schema( $schema ) ) ); }

function mls_validate_form_answers( array $schema, array $input ): array {
	$clean = array(); $allowed = array_column( $schema, 'name' );
	if ( array_diff( array_keys( $input ), $allowed ) ) { throw new InvalidArgumentException( 'The form fields changed. Reload the lesson.' ); }
	foreach ( $schema as $field ) {
		$value = $input[ $field['name'] ] ?? ( $field['type'] === 'checkbox' ? false : '' );
		if ( $field['type'] === 'checkbox' ) {
			if ( ! is_bool( $value ) || ( $field['required'] && ! $value ) ) { throw new InvalidArgumentException( 'Check the required confirmation: ' . $field['label'] ); }
		} else {
			if ( ! is_string( $value ) || strlen( $value ) > ( $field['type'] === 'textarea' ? 1000 : 200 ) ) { throw new InvalidArgumentException( 'Invalid value: ' . $field['label'] ); }
			$value = trim( $value );
			if ( $field['required'] && $value === '' ) { throw new InvalidArgumentException( 'Complete: ' . $field['label'] ); }
			if ( $value !== '' ) {
				if ( $field['type'] === 'select' && ! in_array( $value, $field['options'], true ) ) { throw new InvalidArgumentException( 'Choose a listed option: ' . $field['label'] ); }
				if ( $field['type'] === 'date' && ( ! preg_match( '/^(\d{4})-(\d{2})-(\d{2})$/D', $value, $parts ) || ! checkdate( (int) $parts[2], (int) $parts[3], (int) $parts[1] ) ) ) { throw new InvalidArgumentException( 'Enter a valid date: ' . $field['label'] ); }
				if ( $field['type'] === 'number' && ( ! preg_match( '/^-?(0|[1-9]\d{0,6})$/D', $value ) || (int) $value < $field['min'] || (int) $value > $field['max'] ) ) { throw new InvalidArgumentException( 'Enter a whole number within the limits: ' . $field['label'] ); }
			}
			$value = $field['type'] === 'textarea' ? sanitize_textarea_field( $value ) : sanitize_text_field( $value );
			if ( $field['required'] && $value === '' ) { throw new InvalidArgumentException( 'Complete: ' . $field['label'] ); }
		}
		$clean[ $field['name'] ] = $value;
	}
	return $clean;
}

function mls_save_form_schema( int $course, array $schema, string $expected ) {
	if ( ! current_user_can( 'manage_options' ) || get_post_type( $course ) !== 'stm-courses' ) { return mls_error( 'Administrator required.', 403 ); }
	global $wpdb; $lock = 'mls-form-schema-' . $course;
	if ( (int) $wpdb->get_var( $wpdb->prepare( 'SELECT GET_LOCK(%s,2)', $lock ) ) !== 1 ) { return mls_error( 'Please retry shortly.', 409 ); }
	try {
		clean_post_cache( $course ); $current = get_post_meta( $course, '_mls_form', true ); $current = is_array( $current ) ? $current : array();
		if ( ! hash_equals( mls_form_fingerprint( $current ), $expected ) ) { return mls_error( 'Another administrator changed this form. Reload before saving.', 409 ); }
		$clean = mls_validate_form_schema( $schema ); update_post_meta( $course, '_mls_form', $clean );
		mls_event( $course, 0, 'learner_form_configured', array( 'schema' => mls_form_fingerprint( $clean ) ) ); return true;
	} catch ( InvalidArgumentException $error ) { return mls_error( $error->getMessage() ); }
	finally { $wpdb->get_var( $wpdb->prepare( 'SELECT RELEASE_LOCK(%s)', $lock ) ); }
}

function mls_form_latest( int $user, array $context, string $hash ): ?WP_Post {
	$posts = get_posts( array( 'post_type' => 'mls_form_entry', 'post_status' => 'private', 'author' => $user, 'posts_per_page' => 1, 'orderby' => 'ID', 'order' => 'DESC', 'meta_query' => array( array( 'key' => '_mls_course', 'value' => $context['course_id'] ), array( 'key' => '_mls_release', 'value' => $context['release'] ), array( 'key' => '_mls_form_hash', 'value' => $hash ) ) ) );
	return $posts[0] ?? null;
}

function mls_form_render( array $context ): string {
	$raw = get_post_meta( $context['course_id'], '_mls_form', true ); if ( ! is_array( $raw ) || ! $raw ) { return ''; }
	try { $schema = mls_validate_form_schema( $raw ); } catch ( InvalidArgumentException $error ) { return ''; }
	$hash = mls_form_fingerprint( $schema ); $latest = mls_form_latest( get_current_user_id(), $context, $hash );
	$record = $latest ? json_decode( $latest->post_content, true ) : array();
	// Keep legacy responses intact; reuse them only for an unchanged legacy text form.
	if ( ! $latest && ! array_filter( $raw, fn( $row ) => isset( $row['type'] ) ) ) { $legacy = get_user_meta( get_current_user_id(), '_mls_form_' . $context['course_id'], true ); if ( ( $legacy['release'] ?? '' ) === $context['release'] ) { $record = $legacy; } }
	ob_start(); ?><details><summary>My learning goals</summary><form class="mls-learner-form" data-schema="<?php echo esc_attr( $hash ); ?>">
	<?php foreach ( $schema as $field ) : $value = $record['answers'][ $field['name'] ] ?? ''; $id = 'mls-field-' . $context['lesson_id'] . '-' . $field['name']; ?>
	<p><label for="<?php echo esc_attr( $id ); ?>"><?php echo esc_html( $field['label'] ); ?><?php echo $field['required'] ? ' *' : ''; ?></label>
	<?php if ( $field['type'] === 'textarea' ) : ?><textarea id="<?php echo esc_attr( $id ); ?>" name="<?php echo esc_attr( $field['name'] ); ?>" rows="3" maxlength="1000" <?php echo $field['required'] ? 'required' : ''; ?>><?php echo esc_textarea( is_string( $value ) ? $value : '' ); ?></textarea>
	<?php elseif ( $field['type'] === 'select' ) : ?><select id="<?php echo esc_attr( $id ); ?>" name="<?php echo esc_attr( $field['name'] ); ?>" <?php echo $field['required'] ? 'required' : ''; ?>><option value="">Select</option><?php foreach ( $field['options'] as $option ) : ?><option <?php selected( $value, $option ); ?>><?php echo esc_html( $option ); ?></option><?php endforeach; ?></select>
	<?php else : ?><input id="<?php echo esc_attr( $id ); ?>" name="<?php echo esc_attr( $field['name'] ); ?>" type="<?php echo esc_attr( $field['type'] ); ?>" <?php if ( $field['type'] === 'checkbox' ) { checked( $value, true ); } else { echo 'value="' . esc_attr( is_string( $value ) ? $value : '' ) . '"'; } ?> <?php echo $field['required'] ? 'required' : ''; ?> <?php echo $field['type'] === 'number' ? 'step="1" min="' . $field['min'] . '" max="' . $field['max'] . '"' : 'maxlength="200"'; ?>><?php endif; ?></p>
	<?php endforeach; ?><button type="submit">Save goals</button><p class="mls-form-status" role="status"></p></form></details><?php return ob_get_clean();
}

require_once __DIR__ . '/forms-admin.php';
