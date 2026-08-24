<?php

defined( 'ABSPATH' ) || exit;

function masterstudy_lms_course_player_register_assets() {
	wp_register_style( 'masterstudy-course-player-video-plyr', STM_LMS_URL . 'assets/css/course-player/content/lesson/plyr.css', null, MS_LMS_VERSION );
	wp_register_style( 'masterstudy-course-player-fonts', STM_LMS_URL . 'assets/css/course-player/fonts.css', null, MS_LMS_VERSION );
	wp_register_style( 'masterstudy-course-player-main', STM_LMS_URL . 'assets/css/course-player/main.css', null, MS_LMS_VERSION );
	wp_register_style( 'masterstudy-course-player-header', STM_LMS_URL . 'assets/css/course-player/header.css', null, MS_LMS_VERSION );
	wp_register_style( 'masterstudy-course-player-curriculum', STM_LMS_URL . 'assets/css/course-player/curriculum.css', null, MS_LMS_VERSION );
	wp_register_style( 'masterstudy-course-player-discussions', STM_LMS_URL . 'assets/css/course-player/discussions.css', null, MS_LMS_VERSION );
	wp_register_style( 'masterstudy-course-player-navigation', STM_LMS_URL . 'assets/css/course-player/navigation.css', null, MS_LMS_VERSION );
	wp_register_style( 'masterstudy-course-player-lesson', STM_LMS_URL . 'assets/css/course-player/content/lesson/main.css', null, MS_LMS_VERSION );
	wp_register_style( 'masterstudy-course-player-lesson-materials', STM_LMS_URL . 'assets/css/course-player/content/lesson/materials.css', null, MS_LMS_VERSION );
	wp_register_style( 'masterstudy-course-player-lesson-video', STM_LMS_URL . 'assets/css/course-player/content/lesson/video.css', null, MS_LMS_VERSION );
	wp_register_style( 'masterstudy-course-player-lesson-type-audio', STM_LMS_URL . 'assets/css/course-player/content/lesson/audio-type.css', null, MS_LMS_VERSION );
	wp_register_style( 'masterstudy-course-player-quiz', STM_LMS_URL . 'assets/css/course-player/content/quiz.css', null, MS_LMS_VERSION );
	wp_register_style( 'masterstudy-course-player-quiz-history', STM_LMS_URL . 'assets/css/course-player/content/quiz-history.css', null, MS_LMS_VERSION );
	wp_register_style( 'masterstudy-course-player-question', STM_LMS_URL . 'assets/css/course-player/content/questions.css', null, MS_LMS_VERSION );
	wp_register_style( 'masterstudy-course-player-question-fonts', STM_LMS_URL . 'assets/css/course-player/content/questions-fonts.css', null, MS_LMS_VERSION );
	wp_register_style( 'masterstudy-course-player-locked', STM_LMS_URL . 'assets/css/course-player/locked.css', null, MS_LMS_VERSION );
	wp_register_style( 'masterstudy-course-player-pdf-lesson', STM_LMS_URL . 'assets/css/course-player/content/lesson/lesson-pdf.css', null, MS_LMS_VERSION );
	wp_register_style( 'masterstudy-course-player-pdf-lesson-fonts', STM_LMS_URL . 'assets/css/course-player/content/lesson/lesson-pdf-fonts.css', null, MS_LMS_VERSION );
	wp_register_style( 'pdfjs_viewer_styles', STM_LMS_URL . 'assets/vendors/pdf_viewer.css', null, MS_LMS_VERSION );

	wp_register_script( 'plyr', STM_LMS_URL . 'assets/vendors/plyr/plyr.js', array(), MS_LMS_VERSION, false );
	wp_register_script( 'youtube-iframe-api', 'https://www.youtube.com/iframe_api', array(), MS_LMS_VERSION, false );
	wp_register_script( 'vimeo-player-api', 'https://player.vimeo.com/api/player.js', array(), MS_LMS_VERSION, false );
	wp_register_script( 'pdfjs', STM_LMS_URL . 'assets/vendors/pdf.min.js', array(), MS_LMS_VERSION, true );
	wp_register_script( 'pdfjs_worker', STM_LMS_URL . 'assets/vendors/pdf.worker.min.js', array(), MS_LMS_VERSION, true );
	wp_register_script( 'pdfjs_viewer', STM_LMS_URL . 'assets/vendors/pdf_viewer.js', array(), MS_LMS_VERSION, true );
	wp_register_script( 'masterstudy-course-player-header', STM_LMS_URL . 'assets/js/course-player/header.js', array( 'jquery' ), MS_LMS_VERSION, true );
	wp_register_script( 'masterstudy-course-player-navigation', STM_LMS_URL . 'assets/js/course-player/navigation.js', array( 'jquery' ), MS_LMS_VERSION, true );
	wp_register_script( 'masterstudy-course-player-lesson', STM_LMS_URL . 'assets/js/course-player/content/lesson/lesson.js', array( 'jquery' ), MS_LMS_VERSION, true );
	wp_register_script( 'masterstudy-course-player-lesson-materials', STM_LMS_URL . 'assets/js/course-player/content/lesson/lesson-materials.js', array( 'jquery' ), MS_LMS_VERSION, true );
	wp_register_script( 'masterstudy-course-player-audio-lesson-type', STM_LMS_URL . 'assets/js/course-player/content/lesson/lesson-audio.js', array( 'jquery' ), MS_LMS_VERSION, true );
	wp_register_script( 'masterstudy-course-player-curriculum', STM_LMS_URL . 'assets/js/course-player/curriculum.js', array( 'jquery' ), MS_LMS_VERSION, true );
	wp_register_script( 'masterstudy-course-player-discussions', STM_LMS_URL . 'assets/js/course-player/discussions.js', array( 'jquery' ), MS_LMS_VERSION, true );
	wp_register_script( 'masterstudy-course-player-lesson-video', STM_LMS_URL . 'assets/js/course-player/content/lesson/lesson-video.js', array( 'jquery', 'plyr', 'youtube-iframe-api', 'vimeo-player-api' ), MS_LMS_VERSION, true );
	wp_register_script( 'masterstudy-course-player-quiz-touch', STM_LMS_URL . 'assets/js/jquery.ui.touch-punch.min.js', array( 'jquery-ui-sortable' ), MS_LMS_VERSION, true );
	wp_register_script( 'masterstudy-course-player-quiz', STM_LMS_URL . 'assets/js/course-player/content/quiz.js', array( 'jquery', 'jquery-ui-sortable' ), MS_LMS_VERSION, true );
	wp_register_script( 'masterstudy-course-player-quiz-history', STM_LMS_URL . 'assets/js/enrolled-quizzes.js', array( 'jquery' ), MS_LMS_VERSION, true );
	wp_register_script( 'masterstudy-course-player-quiz-attempt', STM_LMS_URL . 'assets/js/course-player/content/quiz-attempt.js', array( 'jquery' ), MS_LMS_VERSION, true );
	wp_register_script( 'masterstudy-course-player-question', STM_LMS_URL . 'assets/js/course-player/content/questions.js', array( 'jquery' ), MS_LMS_VERSION, true );
	wp_register_script( 'jspdf', STM_LMS_URL . 'assets/vendors/jspdf.umd.js', array(), MS_LMS_VERSION, false );
	wp_register_script( 'masterstudy-course-player-certificate', STM_LMS_URL . 'assets/js/course-player/generate-certificate.js', array( 'jspdf' ), MS_LMS_VERSION, false );
	wp_register_script( 'masterstudy-course-player-pdf-lesson', STM_LMS_URL . 'assets/js/course-player/content/lesson/lesson-pdf.js', array( 'jquery', 'pdfjs', 'pdfjs_worker', 'pdfjs_viewer' ), MS_LMS_VERSION, true );
}
add_action( 'masterstudy_lms_course_player_register_assets', 'masterstudy_lms_course_player_register_assets' );
