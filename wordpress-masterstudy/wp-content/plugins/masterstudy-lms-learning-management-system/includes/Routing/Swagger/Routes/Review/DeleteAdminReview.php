<?php

namespace MasterStudy\Lms\Routing\Swagger\Routes\Review;

use MasterStudy\Lms\Routing\Swagger\Route;

class DeleteAdminReview extends Route {
	public function get_summary(): string {
		return 'Delete Admin Review';
	}

	public function get_description(): string {
		return 'Deletes a review by ID.';
	}
}
