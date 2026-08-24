<?php

use MasterStudy\Lms\Routing\Router;

/** @var Router $router */

/**
 * Comments routes
 */
$router->group(
	array(
		'middleware' => apply_filters(
			'masterstudy_lms_routes_middleware',
			array(
				\MasterStudy\Lms\Routing\Middleware\Authentication::class,
				\MasterStudy\Lms\Routing\Middleware\Instructor::class,
				\MasterStudy\Lms\Routing\Middleware\PostGuard::class,
				\MasterStudy\Lms\Routing\Middleware\CommentGuard::class,
			)
		),
		'prefix'     => '/comments',
	),
	function ( \MasterStudy\Lms\Routing\Router $router ) {
		$router->get(
			'/{post_id}',
			\MasterStudy\Lms\Http\Controllers\Comment\GetController::class,
			\MasterStudy\Lms\Routing\Swagger\Routes\Comment\Get::class,
		);

		$router->post(
			'/{post_id}',
			\MasterStudy\Lms\Http\Controllers\Comment\CreateController::class,
			\MasterStudy\Lms\Routing\Swagger\Routes\Comment\Create::class,
		);

		$router->post(
			'/{comment_id}/reply',
			\MasterStudy\Lms\Http\Controllers\Comment\ReplyController::class,
			\MasterStudy\Lms\Routing\Swagger\Routes\Comment\Reply::class,
		);

		$router->post(
			'/{comment_id}/approve',
			\MasterStudy\Lms\Http\Controllers\Comment\ApproveController::class,
			\MasterStudy\Lms\Routing\Swagger\Routes\Comment\Approve::class,
		);

		$router->post(
			'/{comment_id}/unapprove',
			\MasterStudy\Lms\Http\Controllers\Comment\UnapproveController::class,
			\MasterStudy\Lms\Routing\Swagger\Routes\Comment\Unapprove::class,
		);

		$router->post(
			'/{comment_id}/spam',
			\MasterStudy\Lms\Http\Controllers\Comment\SpamController::class,
			\MasterStudy\Lms\Routing\Swagger\Routes\Comment\Spam::class,
		);

		$router->post(
			'/{comment_id}/unspam',
			\MasterStudy\Lms\Http\Controllers\Comment\UnspamController::class,
			\MasterStudy\Lms\Routing\Swagger\Routes\Comment\Unspam::class,
		);

		$router->post(
			'/{comment_id}/trash',
			\MasterStudy\Lms\Http\Controllers\Comment\TrashController::class,
			\MasterStudy\Lms\Routing\Swagger\Routes\Comment\Trash::class,
		);

		$router->post(
			'/{comment_id}/untrash',
			\MasterStudy\Lms\Http\Controllers\Comment\UntrashController::class,
			\MasterStudy\Lms\Routing\Swagger\Routes\Comment\Untrash::class,
		);

		$router->post(
			'/{comment_id}/update',
			\MasterStudy\Lms\Http\Controllers\Comment\UpdateController::class,
			\MasterStudy\Lms\Routing\Swagger\Routes\Comment\Update::class,
		);
	}
);
