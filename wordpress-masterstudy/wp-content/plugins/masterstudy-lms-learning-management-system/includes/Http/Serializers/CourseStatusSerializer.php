<?php

namespace MasterStudy\Lms\Http\Serializers;

final class CourseStatusSerializer extends AbstractSerializer {
	public function collectionToArray( array $collection ): array {
		return array_map(
			array( $this, 'toArray' ),
			array_map(
				function ( $status, $id ) {
					return array(
						'id'    => $id,
						'label' => $status['label'],
					);
				},
				$collection,
				array_keys( $collection )
			)
		);
	}

	/**
	 * @param $data
	 *
	 * @return array
	 */
	public function toArray( $data ): array {
		return array(
			'id'    => $data['id'],
			'label' => $data['label'],
		);
	}
}
