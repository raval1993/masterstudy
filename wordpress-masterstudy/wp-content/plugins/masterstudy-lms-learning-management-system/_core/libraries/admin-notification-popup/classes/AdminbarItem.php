<?php

namespace ANP;

use ANP\Popup\NotificationPopupHtml;

class AdminbarItem {
	public static function init() {
		update_option( NotificationEnqueueControl::$optionName, array( 'main' => array(), 'second' => array() ) );
		add_action( 'admin_bar_menu', array( self::class, 'addAdminbarItem' ), 300, 1 );
	}

	public static function addAdminbarItem( $admin_bar ) {
		$product_name = get_product_name();
		$plugin_name  = isset( $product_name['plugin_name'] ) ? $product_name['plugin_name'] : '';

		$admin_bar->add_group(
			array(
				'id' => 'admin-notification',
			)
		);

		$hasNew = ( NotificationEnqueueControl::getNotificationsQty() > 0 ) ? ' has_new' : '';

		$admin_bar->add_menu(
			array(
				'id'     => 'admin-notification-popup',
				'parent' => 'admin-notification',
				'title'  => '<span><i class="ab-icon dashicons dashicons-bell' . $hasNew . '"></i>' . esc_html__( 'Notifications', $plugin_name ) . '</span>',
				'meta'   => array(
					'class' => 'menupop',
					'html'  => NotificationPopupHtml::popup_html(),
				),
			)
		);
	}
}
