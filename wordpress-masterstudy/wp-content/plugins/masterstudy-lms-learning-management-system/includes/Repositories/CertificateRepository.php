<?php

namespace MasterStudy\Lms\Repositories;

use MasterStudy\Lms\Plugin\PostType;

final class CertificateRepository {
	public function get_all(): array {
		$args = array(
			'post_status' => 'publish',
			'post_type'   => PostType::CERTIFICATE,
			'numberposts' => -1,
		);

		if ( ! current_user_can( 'manage_options' ) ) {
			$super_admins = get_super_admins();
			$author_ids   = array( get_current_user_id() );

			if ( ! empty( $super_admins ) ) {
				$super_admin_ids = get_users(
					array(
						'login__in' => $super_admins,
						'fields'    => 'ID',
					)
				);

				$author_ids = array_merge( $author_ids, $super_admin_ids );
			}

			$args['author__in'] = $author_ids;
		}

		return get_posts( $args );
	}
}
