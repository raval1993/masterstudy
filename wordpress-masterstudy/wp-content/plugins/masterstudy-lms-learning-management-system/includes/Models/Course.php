<?php

namespace MasterStudy\Lms\Models;

use WP_User;

class Course {
	public string $access_status;
	public array $category = array();
	/** @var string|int|null */
	public $certificate_id;
	public ?WP_User $co_instructor;
	public ?string $content;
	public ?int $current_students;
	public ?string $duration_info;
	public ?string $basic_info;
	public ?string $requirements;
	public ?string $course_page_style;
	public ?string $intended_audience;
	public ?string $excerpt;

	/**
	 * Number of days for time limit
	 * @var int
	 */
	public int $end_time = 0;

	/**
	 * Has time limit
	 * @var bool
	 */
	public bool $expiration   = false;
	public array $files       = array();
	public array $attachments = array();
	public int $id;
	/**
	 * @var array{url: string, width: int, height: int, id: int}|null
	 */
	public ?array $image;
	public bool $is_featured;
	public bool $is_lock_lesson;
	public ?string $level;
	public WP_User $owner;
	/**
	 * @var array{passing_level: int, courses: array<int>}
	 */
	public array $prerequisites = array(
		'courses'       => array(),
		'passing_level' => 0,
	);

	/**
	 * Has trial
	 * @var bool
	 */
	public bool $shareware = false;
	public string $slug;
	public string $url;
	public ?string $status;
	public ?int $status_date_start;
	public ?int $status_date_end;
	public string $title;
	public bool $single_sale;
	public bool $not_in_membership;
	public ?string $video_duration;
	public ?int $video_poster;
	public ?int $views;
	public ?string $access_duration;
	public ?string $access_devices;
	public ?string $certificate_info;
	public bool $coming_soon_show;
	public bool $coming_soon_preorder;
	public ?string $announcement;
	public ?array $reviews;
	public ?array $marks;
	public ?array $rate;
	public bool $is_udemy_course;
	public ?string $udemy_headline;
	public ?int $udemy_marks;
	public ?float $udemy_rate;
	public ?array $udemy_languages;
	public ?array $udemy_instructor;
	public ?array $udemy_objectives;
	public ?string $udemy_video;
	public ?string $udemy_assets;
	public ?string $udemy_articles;
	public ?string $udemy_certificate;
	public ?string $single_sale_price_info;
	public ?string $free_price_info;
	public ?string $price_info;
	public ?string $enterprise_price_info;
	public ?string $points_price_info;
	public ?string $subscription_price_info;
	public ?string $pricing_mode;
	public ?float $price;
	public ?float $sale_price;
	public ?string $coming_soon_date;
	public ?bool $coming_soon_details;
	public ?bool $coming_soon_price;
	public ?bool $is_sale_active;
	public ?array $thumbnail;
	public ?array $full_image;
}
