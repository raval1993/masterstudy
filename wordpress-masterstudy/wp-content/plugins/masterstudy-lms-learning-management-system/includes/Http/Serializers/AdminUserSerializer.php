<?php

namespace MasterStudy\Lms\Http\Serializers;

final class AdminUserSerializer extends AbstractSerializer {

	/**
	 * @param \WP_User $user
	 */
	public function toArray( $user ): array {
		$name          = trim( (string) $user->display_name );
		$login         = (string) $user->user_login;
		$email         = sanitize_email( (string) $user->user_email );
		$name_or_login = $login;

		if ( '' !== $name ) {
			$name_or_login = $name;
		}

		$parts = array_values(
			array_unique(
				array_filter(
					array(
						$name_or_login,
						$login,
						$email,
					)
				)
			)
		);

		return array(
			'id'    => (int) $user->ID,
			'name'  => $name_or_login,
			'login' => $login,
			'email' => $email,
			'label' => implode( ' / ', $parts ),
		);
	}
}
