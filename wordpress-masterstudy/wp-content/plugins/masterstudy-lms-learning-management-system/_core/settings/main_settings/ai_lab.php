<?php

use MasterStudy\Lms\Plugin\Addons;

function stm_lms_settings_ai_lab_section() {
	$model_class        = 'MasterStudy\\Lms\\Pro\\AddonsPlus\\AiLab\\Services\\OpenAi\\Model';
	$has_model_class    = class_exists( $model_class );
	$has_model_registry = $has_model_class
		&& method_exists( $model_class, 'get_text_providers' )
		&& method_exists( $model_class, 'get_image_providers' )
		&& method_exists( $model_class, 'get_text_models_by_provider' )
		&& method_exists( $model_class, 'get_image_models_by_provider' )
		&& method_exists( $model_class, 'get_default_text_model_by_provider' )
		&& method_exists( $model_class, 'get_default_image_model_by_provider' );
	$provider_openai    = 'openai';
	$provider_grok      = 'grok';
	$provider_gemini    = 'gemini';
	$provider_mistral   = 'mistral';
	$provider_claude    = 'claude';
	$is_ai_enabled      = is_ms_lms_addon_enabled( Addons::AI_LAB );
	$is_pro_plus        = STM_LMS_Helpers::is_pro_plus();
	$text_providers     = $has_model_registry
		? $model_class::get_text_providers()
		: array(
			$provider_openai => 'OpenAI',
		);
	$image_providers    = $has_model_registry
		? $model_class::get_image_providers()
		: array(
			$provider_openai => 'OpenAI',
		);
	$fallback_texts     = implode(
		'||',
		array(
			$provider_claude,
			$provider_mistral,
		)
	);
	$get_text_models    = static function( string $provider, array $fallback ) use ( $has_model_registry, $model_class ) {
		return $has_model_registry ? $model_class::get_text_models_by_provider( $provider ) : $fallback;
	};
	$get_image_models   = static function( string $provider, array $fallback ) use ( $has_model_registry, $model_class ) {
		return $has_model_registry ? $model_class::get_image_models_by_provider( $provider ) : $fallback;
	};
	$get_default_text_model = static function(
		string $provider,
		string $fallback
	) use ( $has_model_registry, $model_class ) {
		return $has_model_registry ? $model_class::get_default_text_model_by_provider( $provider ) : $fallback;
	};
	$get_default_image_model = static function(
		string $provider,
		string $fallback
	) use ( $has_model_registry, $model_class ) {
		return $has_model_registry ? $model_class::get_default_image_model_by_provider( $provider ) : $fallback;
	};

	$ai_settings_fields = array(
		'name'   => esc_html__( 'AI Lab', 'masterstudy-lms-learning-management-system' ),
		'label'  => esc_html__( 'AI Lab Settings', 'masterstudy-lms-learning-management-system' ),
		'icon'   => 'stmlms-wand-magic-sparkles',
		'fields' => array(
			'ai_text_provider'         => array(
				'group'       => 'started',
				'group_title' => esc_html__( 'AI Text', 'masterstudy-lms-learning-management-system' ),
				'type'        => 'select',
				'label'       => esc_html__( 'AI Lab Provider', 'masterstudy-lms-learning-management-system' ),
				'description' => esc_html__( 'Main provider for AI Lab. For Claude and Mistral, image generation will use an image partner provider.', 'masterstudy-lms-learning-management-system' ),
				'options'     => $text_providers,
				'value'       => $provider_openai,
			),
			'openai_api_key'           => array(
				'type'        => 'text',
				'label'       => esc_html__( 'OpenAI API Key', 'masterstudy-lms-learning-management-system' ),
				'description' => sprintf(
					// Translators: %1$s: Open Link for account api key, %2$s: Close Link for account api key
					esc_html__( 'You can obtain your API key from your %1$sOpenAI Account%2$s.', 'masterstudy-lms-learning-management-system' ),
					'<a href="https://platform.openai.com/api-keys/" target="_blank" rel="nofollow">',
					'</a>'
				),
				'placeholder' => 'Enter your OpenAI API key (starts with sk-...)',
				'dependency'  => array(
					'key'   => 'ai_text_provider',
					'value' => $provider_openai,
				),
			),
			'grok_api_key'             => array(
				'type'         => 'text',
				'label'        => esc_html__( 'Grok API Key', 'masterstudy-lms-learning-management-system' ),
				'placeholder'  => esc_html__( 'Enter your Grok API key', 'masterstudy-lms-learning-management-system' ),
				'dependency'   => array(
					'key'   => 'ai_text_provider',
					'value' => $provider_grok,
				),
			),
			'gemini_api_key'           => array(
				'type'         => 'text',
				'label'        => esc_html__( 'Gemini API Key', 'masterstudy-lms-learning-management-system' ),
				'placeholder'  => esc_html__( 'Enter your Gemini API key', 'masterstudy-lms-learning-management-system' ),
				'dependency'   => array(
					'key'   => 'ai_text_provider',
					'value' => $provider_gemini,
				),
			),
			'mistral_api_key'          => array(
				'type'         => 'text',
				'label'        => esc_html__( 'Mistral API Key', 'masterstudy-lms-learning-management-system' ),
				'placeholder'  => esc_html__( 'Enter your Mistral API key', 'masterstudy-lms-learning-management-system' ),
				'dependency'   => array(
					'key'   => 'ai_text_provider',
					'value' => $provider_mistral,
				),
			),
			'claude_api_key'           => array(
				'type'        => 'text',
				'label'       => esc_html__( 'Claude API Key', 'masterstudy-lms-learning-management-system' ),
				'placeholder' => esc_html__( 'Enter your Claude API key', 'masterstudy-lms-learning-management-system' ),
				'dependency'  => array(
					'key'   => 'ai_text_provider',
					'value' => $provider_claude,
				),
			),
			'openai_text_model'        => array(
				'type'        => 'select',
				'label'       => esc_html__( 'OpenAI Text Model', 'masterstudy-lms-learning-management-system' ),
				'description' => esc_html__( 'Choose the model for OpenAI text generation.', 'masterstudy-lms-learning-management-system' ),
				'options'     => $get_text_models(
					$provider_openai,
					array(
						'gpt-5'                   => 'GPT-5',
						'gpt-5-mini'              => 'GPT-5 Mini',
						'gpt-5-nano'              => 'GPT-5 Nano',
						'gpt-4o'                  => 'GPT-4o',
						'gpt-4o-mini'             => 'GPT-4o Mini',
						'gpt-4.1'                 => 'GPT-4.1',
						'gpt-4.1-mini'            => 'GPT-4.1 Mini',
						'gpt-4.1-nano'            => 'GPT-4.1 Nano',
						'gpt-4-turbo'             => 'GPT-4 Turbo',
						'gpt-4-turbo-latest'      => 'GPT-4 Turbo Latest',
						'gpt-4-turbo-latest-mini' => 'GPT-4 Turbo Latest Mini',
						'gpt-4'                   => 'GPT-4',
						'gpt-3.5-turbo'           => 'GPT-3.5 Turbo',
						'gpt-3.5-turbo-16k'       => 'GPT-3.5 Turbo 16K',
						'gpt-3.5-turbo-instruct'  => 'GPT-3.5 Turbo Instruct',
					)
				),
				'value'       => $get_default_text_model( $provider_openai, 'gpt-3.5-turbo' ),
				'dependency'  => array(
					'key'   => 'ai_text_provider',
					'value' => $provider_openai,
				),
			),
			'grok_text_model'          => array(
				'type'        => 'select',
				'label'       => esc_html__( 'Grok Text Model', 'masterstudy-lms-learning-management-system' ),
				'description' => esc_html__( 'Choose the model for Grok text generation.', 'masterstudy-lms-learning-management-system' ),
				'options'     => $get_text_models(
					$provider_grok,
					array(
						'grok-4.5' => 'Grok 4.5',
						'grok-4.3' => 'Grok 4.3',
					)
				),
				'value'       => $get_default_text_model( $provider_grok, 'grok-4.3' ),
				'dependency'  => array(
					'key'   => 'ai_text_provider',
					'value' => $provider_grok,
				),
			),
			'gemini_text_model'        => array(
				'type'        => 'select',
				'label'       => esc_html__( 'Gemini Text Model', 'masterstudy-lms-learning-management-system' ),
				'description' => esc_html__( 'Choose the model for Gemini text generation.', 'masterstudy-lms-learning-management-system' ),
				'options'     => $get_text_models(
					$provider_gemini,
					array(
						'gemini-3.5-flash'      => 'Gemini 3.5 Flash',
						'gemini-3.1-flash-lite' => 'Gemini 3.1 Flash-Lite',
					)
				),
				'value'       => $get_default_text_model( $provider_gemini, 'gemini-3.5-flash' ),
				'dependency'  => array(
					'key'   => 'ai_text_provider',
					'value' => $provider_gemini,
				),
			),
			'mistral_text_model'       => array(
				'type'        => 'select',
				'label'       => esc_html__( 'Mistral Text Model', 'masterstudy-lms-learning-management-system' ),
				'description' => esc_html__( 'Choose the model for Mistral text generation.', 'masterstudy-lms-learning-management-system' ),
				'options'     => $get_text_models(
					$provider_mistral,
					array(
						'mistral-small-latest' => 'Mistral Small Latest',
					)
				),
				'value'       => $get_default_text_model( $provider_mistral, 'mistral-small-latest' ),
				'dependency'  => array(
					'key'   => 'ai_text_provider',
					'value' => $provider_mistral,
				),
			),
			'claude_text_model'        => array(
				'type'        => 'select',
				'group'       => 'ended',
				'label'       => esc_html__( 'Claude Text Model', 'masterstudy-lms-learning-management-system' ),
				'description' => esc_html__( 'Choose the model for Claude text generation.', 'masterstudy-lms-learning-management-system' ),
				'options'     => $get_text_models(
					$provider_claude,
					array(
						'claude-sonnet-5'  => 'Claude Sonnet 5',
						'claude-opus-4-8'  => 'Claude Opus 4.8',
						'claude-haiku-4-5' => 'Claude Haiku 4.5',
					)
				),
				'value'       => $get_default_text_model( $provider_claude, 'claude-sonnet-5' ),
				'dependency'  => array(
					'key'   => 'ai_text_provider',
					'value' => $provider_claude,
				),
			),
			'ai_fallback_image_provider' => array(
				'group'       => 'started',
				'group_title' => esc_html__( 'AI Image', 'masterstudy-lms-learning-management-system' ),
				'type'        => 'select',
				'label'       => esc_html__( 'Image Partner Provider', 'masterstudy-lms-learning-management-system' ),
				'description' => esc_html__( 'Claude and Mistral are used for text. Select a second provider for image generation.', 'masterstudy-lms-learning-management-system' ),
				'options'     => $image_providers,
				'value'       => $provider_openai,
				'dependency'  => array(
					'key'   => 'ai_text_provider',
					'value' => $fallback_texts,
				),
			),
			'ai_fallback_image_api_key' => array(
				'type'        => 'text',
				'group'       => 'ended',
				'label'       => esc_html__( 'Image Partner API Key', 'masterstudy-lms-learning-management-system' ),
				'description' => esc_html__( 'Used for image generation only when AI Lab Provider is Claude or Mistral.', 'masterstudy-lms-learning-management-system' ),
				'placeholder' => esc_html__( 'Enter image partner API key', 'masterstudy-lms-learning-management-system' ),
				'dependency'  => array(
					'key'   => 'ai_text_provider',
					'value' => $fallback_texts,
				),
			),
			'openai_image_model'       => array(
				'type'         => 'select',
				'label'        => esc_html__( 'OpenAI Image Model', 'masterstudy-lms-learning-management-system' ),
				'options'      => $get_image_models(
					$provider_openai,
					array(
						'dall-e-3' => 'DALL-E 3',
					)
				),
				'value'        => $get_default_image_model( $provider_openai, 'dall-e-3' ),
				'dependency'   => array(
					'key'   => 'ai_text_provider',
					'value' => $provider_openai,
				),
			),
			'grok_image_model'         => array(
				'type'         => 'select',
				'label'        => esc_html__( 'Grok Image Model', 'masterstudy-lms-learning-management-system' ),
				'options'      => $get_image_models(
					$provider_grok,
					array(
						'grok-imagine-image-quality' => 'Grok Imagine Quality',
						'grok-imagine-image'         => 'Grok Imagine',
					)
				),
				'value'        => $get_default_image_model( $provider_grok, 'grok-imagine-image-quality' ),
				'dependency'   => array(
					'key'   => 'ai_text_provider',
					'value' => $provider_grok,
				),
			),
			'gemini_image_model'       => array(
				'type'         => 'select',
				'label'        => esc_html__( 'Gemini Image Model', 'masterstudy-lms-learning-management-system' ),
				'options'      => $get_image_models(
					$provider_gemini,
					array(
						'gemini-2.5-flash-image' => 'Gemini 2.5 Flash Image',
					)
				),
				'value'        => $get_default_image_model( $provider_gemini, 'gemini-2.5-flash-image' ),
				'dependency'   => array(
					'key'   => 'ai_text_provider',
					'value' => $provider_gemini,
				),
			),
			'mistral_image_model'      => array(
				'type'         => 'select',
				'label'        => esc_html__( 'Mistral Image Model', 'masterstudy-lms-learning-management-system' ),
				'options'      => $get_image_models(
					$provider_mistral,
					array(
						'mistral-image-generation' => 'Mistral Image Generation',
					)
				),
				'value'        => $get_default_image_model( $provider_mistral, 'mistral-image-generation' ),
				'dependency'   => array(
					'key'   => 'ai_text_provider',
					'value' => '__never__',
				),
			),
			'openai_text_suggestions'  => array(
				'group'       => 'started',
				'group_title' => esc_html__( 'AI Settings', 'masterstudy-lms-learning-management-system' ),
				'type'        => 'select',
				'label'       => esc_html__( 'Number of Text Suggestions', 'masterstudy-lms-learning-management-system' ),
				'description' => esc_html__( 'How many variations of text AI should generate.', 'masterstudy-lms-learning-management-system' ),
				'options'     => array(
					1  => 1,
					2  => 2,
					3  => 3,
					4  => 4,
					5  => 5,
					6  => 6,
					7  => 7,
					8  => 8,
					9  => 9,
					10 => 10,
				),
				'value'       => 3,
			),
			'openai_image_suggestions' => array(
				'type'        => 'select',
				'label'       => esc_html__( 'Number of Image Suggestions', 'masterstudy-lms-learning-management-system' ),
				'description' => esc_html__( 'Choose how many images you want to generate.', 'masterstudy-lms-learning-management-system' ),
				'options'     => array(
					1 => 1,
				),
				'value'       => 1,
			),
			'instructor_access'        => array(
				'type'  => 'instructor-access',
				'value' => $is_ai_enabled,
			),
			'openai_usage'             => array(
				'type'  => 'ai-usage',
				'group' => 'ended',
				'value' => $is_ai_enabled,
			),
		),
	);

	if ( ! $is_pro_plus || ! $is_ai_enabled ) {
		$ai_settings_fields = array(
			'name'   => esc_html__( 'AI Lab', 'masterstudy-lms-learning-management-system' ),
			'label'  => esc_html__( 'AI Lab Settings', 'masterstudy-lms-learning-management-system' ),
			'icon'   => 'stmlms-wand-magic-sparkles',
			'fields' => array(
				'pro_banner_ai_lab' => array(
					'type'        => 'pro_banner',
					'label'       => esc_html__( 'AI Lab', 'masterstudy-lms-learning-management-system' ),
					'img'         => STM_LMS_URL . 'assets/img/pro-features/addons/ai-lab.png',
					'desc'        => esc_html__( 'Generate your entire course in a click! AI instantly creates lessons, quizzes, and assignments based on your description—ready for you to edit and customize.', 'masterstudy-lms-learning-management-system' ),
					'hint'        => esc_html__( 'Unlock', 'masterstudy-lms-learning-management-system' ),
					'is_enable'   => $is_pro_plus && ! $is_ai_enabled,
					'is_pro_plus' => true,
					'search'      => esc_html__( 'AI Lab', 'masterstudy-lms-learning-management-system' ),
					'utm_url'     => 'https://stylemixthemes.com/wordpress-lms-plugin/pricing/?utm_source=mswpadmin&utm_medium=ai_lab&utm_campaign=masterstudy-plugin',
				),
			),
		);
	}

	return $ai_settings_fields;
}
