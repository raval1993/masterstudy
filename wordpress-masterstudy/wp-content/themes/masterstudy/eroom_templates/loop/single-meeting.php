<?php
$post_id = get_the_ID();
$post_type = get_post_type();
if( $post_type === 'product' ) {
    $post_id = get_post_meta( $post_id, '_meeting_id', true );
}
if( !empty( $post_id ) ) {

    $host_id = get_post_meta( $post_id, 'stm_host', true );
    $timezone = get_post_meta( $post_id, 'stm_timezone', true );
    $user_email = '';
    $first_name = '';
    $last_name = '';
    if( empty( $timezone ) ) {
        $timezone = 'UTC';
    }

    $timezones = stm_zoom_get_timezone_options();
    if( !empty( $timezones[ $timezone ] ) ) {
        $timezone = $timezones[ $timezone ];
    }

    if( !empty( $host_id ) && !empty( $host_id ) ) {
        $zoom_users = array();
        if( !empty( $users ) ) {
            foreach( $users as $user ) {
                $zoom_users[ $user[ 'id' ] ] = array(
                    'email' => $user[ 'email' ],
                    'first_name' => $user[ 'first_name' ],
                    'last_name' => $user[ 'last_name' ],
                );
            }
        }
        if( !empty( $zoom_users[ $host_id ] ) ) {
            if( !empty( $zoom_users[ $host_id ][ 'email' ] ) ) {
                $user_email = $zoom_users[ $host_id ][ 'email' ];
            }
            if( !empty( $zoom_users[ $host_id ][ 'first_name' ] ) ) {
                $first_name = $zoom_users[ $host_id ][ 'first_name' ];
            }
            if( !empty( $zoom_users[ $host_id ][ 'last_name' ] ) ) {
                $last_name = $zoom_users[ $host_id ][ 'last_name' ];
            }
        }
    }

    ?>
    <div class="stm_zoom_grid__item">
        <div class="zoom-single_meeting">
            <?php if( has_post_thumbnail() ): ?>
                <div class="image ">
                    <a href="<?php the_permalink(); ?>">
                        <?php the_post_thumbnail( 'large' ); ?>
                    </a>
                </div>
            <?php endif; ?>
            <div class="info">
                <div class="title">
                    <a href="<?php the_permalink(); ?>"><h3><?php the_title(); ?></h3></a>
                </div>
                <div class="zoom_date">
                    <?php
                    $start_date = get_post_meta( $post_id, 'stm_date', true );
                    $start_time = get_post_meta( $post_id, 'stm_time', true );
                    if ( !empty( $start_date) ) {
                        $meeting_start = strtotime( 'today', ( apply_filters('eroom_sanitize_stm_date', $start_date )  / 1000 ) );
                        if( !empty( $start_time ) ) {
                            $time = explode( ':', $start_time );
                            if( is_array( $time ) and count( $time ) === 2 ) {
                                $meeting_start = strtotime( "+{$time[0]} hours +{$time[1]} minutes", $meeting_start );
                            }
                        }
                        $meeting_start = date( 'Y-m-d H:i:s', $meeting_start );
                        $date_format = get_option( 'date_format', 'd M Y H:i' );
                        $time_format = get_option( 'time_format', 'H:i' );
                        $format = $date_format . ' ' . $time_format;
                        $date = strtotime( $meeting_start );
                        $date = date_i18n( $format, $date );
                        echo esc_html( $date );
                    }
                    ?>
                    <?php
                    $price = '';
                    if($post_type === 'product' && class_exists('WooCommerce')){
                        global $product;
                        $price = $product->get_price_html();
                    } ?>
                </div>

	            <div class="zoom_grid_bottom_section">
		            <div class="zoom_host">
			            <div class="host_info">
				            <div class="host_title">
                                <?php if( $post_type === 'product' ): ?>
					            <a href="<?php echo get_home_url( '/' ) . '/zoom-users/' . esc_attr( $host_id ); ?>">
                                    <?php endif; ?>
                                    <?php echo esc_html( $first_name . ' ' . $last_name ); ?>
                                    <?php if( $post_type === 'product' ): ?>
					            </a>
                            <?php endif; ?>
				            </div>
			            </div>
		            </div>
		            <div class="zoom_product_price">
			            <?php if(!empty($price)): ?>
				            <span class="price"><?php echo wp_kses_post($price); ?></span>
                        <?php endif; ?>
		            </div>
	            </div>

            </div>
        </div>
    </div>
<?php } ?>