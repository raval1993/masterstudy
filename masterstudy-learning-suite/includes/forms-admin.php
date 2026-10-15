<?php
defined( 'ABSPATH' ) || exit;
add_action( 'admin_menu', function () { add_submenu_page( 'mls-suite', 'Learner forms', 'Learner forms', 'read', 'mls-forms', 'mls_forms_admin' ); }, 20 );
add_action( 'admin_post_mls_form_schema', 'mls_form_schema_post' );
add_action( 'admin_enqueue_scripts', function () { if ( ( $_GET['page'] ?? '' ) === 'mls-forms' ) { wp_enqueue_script( 'mls-form-builder', plugins_url( 'assets/forms-admin.js', dirname( __DIR__ ) . '/masterstudy-learning-suite.php' ), array(), MLS_VERSION, true ); } } );

function mls_form_builder_row( $index, array $field = array() ): string {
	$prefix = 'fields[' . $index . ']'; ob_start(); ?>
	<tr class="mls-form-row"><td><label>Field key<input class="regular-text" name="<?php echo esc_attr( $prefix ); ?>[name]" value="<?php echo esc_attr( $field['name'] ?? '' ); ?>" pattern="[a-z][a-z0-9_]{0,39}" maxlength="40"></label><br><label>Label<input class="regular-text" name="<?php echo esc_attr( $prefix ); ?>[label]" value="<?php echo esc_attr( $field['label'] ?? '' ); ?>" maxlength="160"></label></td>
	<td><label>Type<select name="<?php echo esc_attr( $prefix ); ?>[type]" class="mls-field-type"><?php foreach ( array( 'text' => 'Text', 'textarea' => 'Long text', 'select' => 'Select', 'checkbox' => 'Confirmation', 'date' => 'Date', 'number' => 'Whole number' ) as $key => $label ) : ?><option value="<?php echo esc_attr( $key ); ?>" <?php selected( $field['type'] ?? 'textarea', $key ); ?>><?php echo esc_html( $label ); ?></option><?php endforeach; ?></select></label><br><label><input type="checkbox" name="<?php echo esc_attr( $prefix ); ?>[required]" value="1" <?php checked( ! empty( $field['required'] ) ); ?>>Required</label></td>
	<td><label class="mls-choice-settings">Choices<textarea name="<?php echo esc_attr( $prefix ); ?>[options]" rows="4" maxlength="2200"><?php echo esc_textarea( implode( "\n", $field['options'] ?? array() ) ); ?></textarea></label><span class="mls-number-settings"><label>Minimum<input type="number" name="<?php echo esc_attr( $prefix ); ?>[min]" value="<?php echo (int) ( $field['min'] ?? 0 ); ?>" min="-1000000" max="1000000"></label><br><label>Maximum<input type="number" name="<?php echo esc_attr( $prefix ); ?>[max]" value="<?php echo (int) ( $field['max'] ?? 100 ); ?>" min="-1000000" max="1000000"></label></span></td>
	<td><button type="button" class="button mls-remove-field" title="Remove field" aria-label="Remove field"><span class="dashicons dashicons-trash" aria-hidden="true"></span></button></td></tr>
	<?php return ob_get_clean();
}

function mls_forms_admin(): void {
	$course = absint( $_GET['course'] ?? 9838 );
	if ( ! mls_manage( $course ) || get_post_type( $course ) !== 'stm-courses' ) { wp_die( 'Select an assigned course.', '', array( 'response' => 403 ) ); }
	echo '<div class="wrap"><h1>Learner forms</h1><form method="get"><input type="hidden" name="page" value="mls-forms"><label>Course ID <input type="number" name="course" value="' . $course . '" min="1"></label> <button class="button">Open course</button></form><h2>' . esc_html( get_the_title( $course ) ) . '</h2>';
	$schema = get_post_meta( $course, '_mls_form', true ); $schema = is_array( $schema ) ? $schema : array();
	if ( current_user_can( 'manage_options' ) ) {
		try { $schema = mls_validate_form_schema( $schema ); } catch ( InvalidArgumentException $error ) { echo '<p>' . esc_html( $error->getMessage() ) . '</p></div>'; return; }
		echo '<form method="post" action="' . esc_url( admin_url( 'admin-post.php' ) ) . '" id="mls-form-builder">'; wp_nonce_field( 'mls_form_schema' );
		echo '<input type="hidden" name="action" value="mls_form_schema"><input type="hidden" name="course" value="' . $course . '"><input type="hidden" name="schema" value="' . esc_attr( mls_form_fingerprint( $schema ) ) . '"><table class="widefat striped"><thead><tr><th>Field</th><th>Input</th><th>Settings</th><th>Remove</th></tr></thead><tbody>';
		foreach ( $schema as $index => $field ) { echo mls_form_builder_row( $index, $field ); }
		echo '</tbody></table><p><button type="button" class="button" id="mls-add-field"><span class="dashicons dashicons-plus-alt2" aria-hidden="true"></span> Add field</button> <button class="button button-primary">Save form</button></p><p id="mls-builder-status" role="status"></p><template id="mls-field-template">' . mls_form_builder_row( '__index__' ) . '</template></form>';
	}
	$page = max( 1, absint( $_GET['pg'] ?? 1 ) ); $query = new WP_Query( array( 'post_type' => 'mls_form_entry', 'post_status' => 'private', 'posts_per_page' => 25, 'paged' => $page, 'meta_key' => '_mls_course', 'meta_value' => $course ) );
	echo '<h2>Private responses</h2><table class="widefat striped"><thead><tr><th>Learner</th><th>Recorded UTC</th><th>Response</th></tr></thead><tbody>';
	foreach ( $query->posts as $entry ) {
		$record = json_decode( $entry->post_content, true ); $user = get_userdata( $entry->post_author );
		echo '<tr><td>' . esc_html( $user ? $user->display_name : 'Deleted account' ) . '</td><td>' . esc_html( $entry->post_date_gmt ) . '</td><td><details><summary>View saved response</summary><dl>';
		foreach ( $record['schema'] ?? array() as $field ) { $answer = $record['answers'][ $field['name'] ] ?? ''; echo '<dt><strong>' . esc_html( $field['label'] ) . '</strong></dt><dd>' . nl2br( esc_html( is_bool( $answer ) ? ( $answer ? 'Yes' : 'No' ) : $answer ) ) . '</dd>'; }
		echo '</dl></details></td></tr>';
	}
	echo '</tbody></table><p>' . wp_kses_post( paginate_links( array( 'base' => add_query_arg( array( 'page' => 'mls-forms', 'course' => $course, 'pg' => '%#%' ), admin_url( 'admin.php' ) ), 'current' => $page, 'total' => $query->max_num_pages ) ) ) . '</p></div>';
}

function mls_form_schema_post(): void {
	check_admin_referer( 'mls_form_schema' ); $course = absint( $_POST['course'] ?? 0 );
	if ( ! current_user_can( 'manage_options' ) || ! mls_manage( $course ) ) { wp_die( 'Administrator required.', '', array( 'response' => 403 ) ); }
	try {
		$fields = wp_unslash( $_POST['fields'] ?? array() ); if ( ! is_array( $fields ) || count( $fields ) > 15 ) { throw new InvalidArgumentException( 'Maximum 15 fields.' ); }
		$schema = array();
		foreach ( $fields as $field ) {
			if ( ! is_array( $field ) || ! is_string( $field['name'] ?? null ) || ! is_string( $field['label'] ?? null ) ) { throw new InvalidArgumentException( 'Invalid field.' ); }
			if ( ! trim( $field['name'] ) && ! trim( $field['label'] ) ) { continue; }
			if ( ( $field['type'] ?? '' ) === 'select' ) { if ( ! is_string( $field['options'] ?? null ) ) { throw new InvalidArgumentException( 'Invalid choices.' ); } $field['options'] = preg_split( '/\R/', trim( $field['options'] ) ); }
			if ( ( $field['type'] ?? '' ) === 'number' ) { foreach ( array( 'min', 'max' ) as $key ) { $value = filter_var( $field[ $key ] ?? null, FILTER_VALIDATE_INT ); if ( $value === false || $value === null ) { throw new InvalidArgumentException( 'Invalid number limits.' ); } $field[ $key ] = $value; } }
			$schema[] = $field;
		}
		$result = mls_save_form_schema( $course, $schema, sanitize_text_field( $_POST['schema'] ?? '' ) );
		if ( is_wp_error( $result ) ) { wp_die( esc_html( $result->get_error_message() ), '', array( 'response' => $result->get_error_data()['status'] ) ); }
		wp_safe_redirect( admin_url( 'admin.php?page=mls-forms&course=' . $course ) ); exit;
	} catch ( Throwable $error ) { wp_die( esc_html( $error->getMessage() ), '', array( 'response' => 400 ) ); }
}
