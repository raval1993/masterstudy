## How to Customize Texts and Links

The Support Page allows you to override texts and API links **directly from your project** without modifying the submodule itself.

---

## 1. Add the Support Page to the WordPress Admin Menu

To display the Support Page inside your project's WordPress admin panel, register a menu item that points to the Support Page template.

Example of adding a **submenu page**:

```php
add_action( 'admin_menu', function() {
	add_submenu_page(
		'stm-lms-settings', // Parent slug
		esc_html__( 'Help Center', 'textdomainname' ), // Page title
		'<span class="stm-lms-settings-menu-title">' . esc_html__( 'Help Center', 'textdomainname' ) . '</span>', // Menu title
		'manage_options', // Capability
		'stm-support-page', // Menu slug
		array( 'STM_Support_Page', 'render_support_page' ) // Callback function
	);
} );
```

🔹 Replace 'your-textdomain' with your actual project text domain.  
🔹 Adjust the 'stm-lms-settings' to your parent admin menu slug if necessary.

## 2. Override Texts and API URLs
To customize texts, promo banners, links, and news feeds, you can set data using the STM_Support_Page::set_data() method from your project.

Example:
```php
STM_Support_Page::set_data( array(
	'header'     => array(
		array(
			'title' => __( 'MasterStudy Help Center', 'masterstudy-lms-learning-management-system' ),
		),
	),
	'help_items' => array(
		'features' => array(
			'title'       => __( 'Get MasterStudy and Enjoy PRO Features', 'masterstudy-lms-learning-management-system' ),
			'description' => __( 'Upgrade now and access a world of pro features, advanced addons, and limitless possibilities for your journey. Turbocharge your courses and make your e-learning platform truly outstanding!', 'masterstudy-lms-learning-management-system' ),
			'buttons'     => array(
				array(
					'href' => 'https://stylemixthemes.com/wordpress-lms-plugin/pricing/',
				),
				array(
					'href' => 'https://stylemixthemes.com/wordpress-lms-plugin/',
				),
			),
			'image'       => 'feature-bg-ms.jpg',
			'has-pro'     => \STM_LMS_Helpers::is_pro_plus(),
		),
	),
	'news' => array(
		'blog_list' => array(
			'category_id' => '394',
		),
	),
) );
```

---

⭐ [Detailed documentation](https://stylemixthemes.atlassian.net/wiki/spaces/STAN/pages/2014511108/Help+Support)
