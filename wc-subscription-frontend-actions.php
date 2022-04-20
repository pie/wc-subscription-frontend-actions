<?php
/*
Plugin Name: WooCommerce Subscriptions: Modify Actions
Description: Adds a payment button for users on the frontend that allows them to make a payment for "on-hold" subscriptions and redirects the resubscribe button to the product URL
Version:     0.1
Author:      The team at PIE
Author URI:  http://pie.co.de
License:     GPL3
License URI: https://www.gnu.org/licenses/gpl-3.0.html
*/

/* PIE\WCSubscriptionFrontendActions is free software: you can redistribute it and/or modify it under the terms of the GNU General Public License as published by the Free Software Foundation, either version 2 of the License, or any later version.

PIE\WCSubscriptionFrontendActions is distributed in the hope that it will be useful, but WITHOUT ANY WARRANTY; without even the implied warranty of MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the GNU General Public License for more details.

You should have received a copy of the GNU General Public License along with PIE\WCSubscriptionFrontendActions. If not, see https://www.gnu.org/licenses/gpl-3.0.en.html */

namespace PIE\WCSubscriptionFrontendActions;

if ( function_exists( 'wc_get_endpoint_url' ) ) {
	/**
	 * Add "Make Payment" to the available actions for an on-hold subscription
	 *
	 * @param $actions
	 * @param $subscription
	 *
	 * @return mixed
	 */
	function add_renew_sub_action( $actions, $subscription ) {
		if ( is_user_logged_in() && $subscription->has_status( 'on-hold' ) ) {
			$last_order = $subscription->get_last_order( 'ids' );
			$renew      = array(
				'url'  => wc_get_endpoint_url( 'orders' ),
				'name' => _x( 'Make Payment', 'an action on a subscription', 'woocommerce-subscriptions' ),
			);
			array_unshift( $actions, $renew );
		}
		return $actions;
	}
	add_filter( 'wcs_view_subscription_actions', __NAMESPACE__ . '\add_renew_sub_action', 10, 2 );

	/**
	 * Add redirect resubscribe link to product URL
	 *
	 * @param $actions
	 * @param $subscription
	 *
	 * @return mixed
	 */
	function redirect_resub_action( $actions, $subscription ) {
		if ( isset( $actions['resubscribe'] ) ) {
			foreach ( $subscription->get_items() as $order_item ) {
				$actions['resubscribe']['url'] = get_permalink( $order_item->get_product_id() );
				break;
			}
		}
		return $actions;
	}
	add_filter( 'wcs_view_subscription_actions', __NAMESPACE__ . '\redirect_resub_sction', 10, 2 );
}
