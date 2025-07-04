<?php

/**
 * Storewide Sale Options.
 *
 * @package Merchant
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}

Merchant_Admin_Options::create( array(
	'title'  => esc_html__( 'Discounts', 'merchant' ),
	'module' => Merchant_Storewide_Sale::MODULE_ID,
	'fields' => array(
		array(
			'id'           => 'offers',
			'type'         => 'flexible_content',
			'button_label' => esc_html__( 'Add New Campaign', 'merchant' ),
			'style'        => Merchant_Storewide_Sale::MODULE_ID . '-style default',
			'sorting'      => true,
			'accordion'    => true,
			'duplicate'    => true,
			'layouts'      => array(
				'offer-details' => array(
					'title'       => esc_html__( 'Storewide Discount Campaign', 'merchant' ),
					'title-field' => 'offer-title', // text field ID to use as title for the layout
					'fields'      => array(
						array(
							'id'      => 'campaign_status',
							'type'    => 'select',
							'title'   => esc_html__( 'Status', 'merchant' ),
							'options' => array(
								'active'   => esc_html__( 'Active', 'merchant' ),
								'inactive' => esc_html__( 'Inactive', 'merchant' ),
							),
							'default' => 'active',
						),
						array(
							'id'      => 'offer-title',
							'type'    => 'text',
							'title'   => esc_html__( 'Offer name', 'merchant' ),
							'default' => esc_html__( 'Storewide Discount Campaign', 'merchant' ),
							'desc'    => esc_html__( 'Internal campaign name. This is not visible to customers.', 'merchant' ),
						),
						array(
							'id'      => 'rules_to_display',
							'type'    => 'select',
							'title'   => esc_html__( 'Trigger', 'merchant' ),
							'options' => array(
								'all'        => esc_html__( 'All Products', 'merchant' ),
								'products'   => esc_html__( 'Specific Products', 'merchant' ),
								'categories' => esc_html__( 'Specific Categories', 'merchant' ),
								'tags'       => esc_html__( 'Specific Tags', 'merchant' ),
							),
							'default' => 'all',
						),
						array(
							'id'        => 'products_to_display',
							'type'      => 'products_selector',
							//'title'    => esc_html__( 'Select a product', 'merchant' ),
							'multiple'  => true,
							'desc'      => esc_html__( 'Select the products included in this discount campaign.', 'merchant' ),
							'condition' => array( 'rules_to_display', '==', 'products' ),
						),
						array(
							'id'          => 'category_slugs',
							'type'        => 'select_ajax',
							'title'       => esc_html__( 'Categories', 'merchant' ),
							'source'      => 'options',
							'multiple'    => true,
							'options'     => Merchant_Admin_Options::get_category_select2_choices(),
							'placeholder' => esc_html__( 'Select categories', 'merchant' ),
							'desc'        => esc_html__( 'Select the product categories that will show the offer.', 'merchant' ),
							'condition'   => array( 'rules_to_display', '==', 'categories' ),
						),

						array(
							'id'          => 'tag_slugs',
							'type'        => 'select_ajax',
							'title'       => esc_html__( 'Tags', 'merchant' ),
							'source'      => 'options',
							'multiple'    => true,
							'options'     => Merchant_Admin_Options::get_tag_select2_choices(),
							'placeholder' => esc_html__( 'Select tags', 'merchant' ),
							'desc'        => esc_html__( 'Select the product tags that will show the offer.', 'merchant' ),
							'condition'   => array( 'rules_to_display', '==', 'tags' ),
						),

						array(
							'id'         => 'exclude_products_toggle',
							'type'       => 'switcher',
							'title'      => esc_html__( 'Exclude products', 'merchant' ),
							'default'    => 0,
							'conditions' => array(
								'relation' => 'AND',
								'terms'    => array(
									array(
										'field'    => 'rules_to_display',
										'operator' => 'in',
										'value'    => array( 'all', 'categories', 'tags' ),
									),
								),
							),
						),

						array(
							'id'         => 'excluded_products',
							'type'       => 'products_selector',
							'title'      => esc_html__( 'Exclude Products', 'merchant' ),
							'multiple'   => true,
							'desc'       => esc_html__( 'Exclude products from this campaign.', 'merchant' ),
							'conditions' => array(
								'relation' => 'AND',
								'terms'    => array(
									array(
										'field'    => 'rules_to_display',
										'operator' => 'in',
										'value'    => array( 'all', 'categories', 'tags' ),
									),
									array(
										'field'    => 'exclude_products_toggle',
										'operator' => '===',
										'value'    => true,
									),
								),
							),
						),

						array(
							'id'         => 'exclude_categories_toggle',
							'type'       => 'switcher',
							'title'      => esc_html__( 'Exclude categories', 'merchant' ),
							'default'    => 0,
							'conditions' => array(
								'relation' => 'AND',
								'terms'    => array(
									array(
										'field'    => 'rules_to_display',
										'operator' => 'in',
										'value'    => array( 'all', 'categories', 'tags' ),
									),
								),
							),
						),

						array(
							'id'          => 'excluded_categories',
							'type'        => 'select_ajax',
							'title'       => esc_html__( 'Excluded Categories List', 'merchant' ),
							'source'      => 'options',
							'multiple'    => true,
							'options'     => Merchant_Admin_Options::get_category_select2_choices(),
							'placeholder' => esc_html__( 'Select categories', 'merchant' ),
							'desc'        => esc_html__( 'Exclude categories from this campaign.', 'merchant' ),
							'conditions'  => array(
								'relation' => 'AND',
								'terms'    => array(
									array(
										'field'    => 'rules_to_display',
										'operator' => 'in',
										'value'    => array( 'all', 'categories', 'tags' ),
									),
									array(
										'field'    => 'exclude_categories_toggle',
										'operator' => '===',
										'value'    => true,
									),
								),
							),
						),

						array(
							'id'         => 'exclude_tags_toggle',
							'type'       => 'switcher',
							'title'      => esc_html__( 'Exclude product tags', 'merchant' ),
							'default'    => 0,
							'conditions' => array(
								'relation' => 'AND',
								'terms'    => array(
									array(
										'field'    => 'rules_to_display',
										'operator' => 'in',
										'value'    => array( 'all', 'categories', 'tags' ),
									),
								),
							),
						),

						array(
							'id'          => 'excluded_tags',
							'type'        => 'select_ajax',
							'title'       => esc_html__( 'Excluded Tags List', 'merchant' ),
							'source'      => 'options',
							'multiple'    => true,
							'options'     => Merchant_Admin_Options::get_tag_select2_choices(),
							'placeholder' => esc_html__( 'Select tags', 'merchant' ),
							'desc'        => esc_html__( 'Exclude tags from this campaign.', 'merchant' ),
							'conditions'  => array(
								'relation' => 'AND',
								'terms'    => array(
									array(
										'field'    => 'rules_to_display',
										'operator' => 'in',
										'value'    => array( 'all', 'categories', 'tags' ),
									),
									array(
										'field'    => 'exclude_tags_toggle',
										'operator' => '===',
										'value'    => true,
									),
								),
							),
						),

						array(
							'id'         => 'exclude_onsale_products_toggle',
							'type'       => 'switcher',
							'title'      => esc_html__( 'Exclude On-Sale products', 'merchant' ),
							'default'    => 0,
							'conditions' => array(
								'relation' => 'AND',
								'terms'    => array(
									array(
										'field'    => 'rules_to_display',
										'operator' => 'in',
										'value'    => array( 'all', 'categories', 'tags' ),
									),
								),
							),
						),

						array(
							'id'      => 'discount_type',
							'type'    => 'radio',
							'title'   => esc_html__( 'Discount', 'merchant' ),
							'options' => array(
								'percentage_discount' => esc_html__( 'Percentage', 'merchant' ),
								'fixed_discount'      => esc_html__( 'Fixed', 'merchant' ),
							),
							'default' => 'percentage_discount',
						),
						array(
							'id'      => 'discount_value',
							'type'    => 'number',
							'default' => 10,
							'min'     => 0,
							'step'    => 0.01,
						),

						array(
							'id'      => 'discount_target',
							'type'    => 'select',
							'title'   => __( 'Apply discount to', 'merchant' ),
							'options' => array(
								'regular' => __( 'Regular Price', 'merchant' ),
								'sale'    => __( 'Sale Price', 'merchant' ),
							),
							'default' => 'regular',
						),

						array(
							'id'      => 'user_condition',
							'type'    => 'select',
							'title'   => esc_html__( 'User Condition', 'merchant' ),
							'options' => array(
								'all'       => esc_html__( 'All Users', 'merchant' ),
								'customers' => esc_html__( 'Selected Users', 'merchant' ),
								'roles'     => esc_html__( 'Selected Roles', 'merchant' ),
							),
							'default' => 'all',
						),

						array(
							'id'        => 'user_condition_roles',
							'type'      => 'select_ajax',
							'title'     => esc_html__( 'User Roles', 'merchant' ),
							'desc'      => esc_html__( 'This will limit the offer to users with these roles.', 'merchant' ),
							'source'    => 'options',
							'multiple'  => true,
							'classes'   => array( 'flex-grow' ),
							'options'   => Merchant_Admin_Options::get_user_roles_select2_choices(),
							'condition' => array( 'user_condition', '==', 'roles' ),
						),

						array(
							'id'        => 'user_condition_users',
							'type'      => 'select_ajax',
							'title'     => esc_html__( 'Users', 'merchant' ),
							'desc'      => esc_html__( 'This will limit the offer to the selected customers.', 'merchant' ),
							'source'    => 'user',
							'multiple'  => true,
							'classes'   => array( 'flex-grow' ),
							'condition' => array( 'user_condition', '==', 'customers' ),
						),

						array(
							'id'         => 'user_exclusion_enabled',
							'type'       => 'switcher',
							'title'      => esc_html__( 'Exclusion List', 'merchant' ),
							'desc'       => esc_html__( 'Select the users that will not show the offer.', 'merchant' ),
							'default'    => 0,
							'conditions' => array(
								'relation' => 'AND',
								'terms'    => array(
									array(
										'field'    => 'user_condition',
										'operator' => 'in',
										'value'    => array( 'all', 'roles' ),
									),
								),
							),
						),

						array(
							'id'         => 'exclude_roles',
							'type'       => 'select_ajax',
							'title'      => esc_html__( 'Exclude Roles', 'merchant' ),
							'desc'       => esc_html__( 'This will exclude the offer for users with these roles.', 'merchant' ),
							'source'     => 'options',
							'multiple'   => true,
							'classes'    => array( 'flex-grow' ),
							'options'    => Merchant_Admin_Options::get_user_roles_select2_choices(),
							'conditions' => array(
								'relation' => 'AND',
								'terms'    => array(
									array(
										'field'    => 'user_condition',
										'operator' => 'in',
										'value'    => array( 'all' ),
									),
									array(
										'field'    => 'user_exclusion_enabled',
										'operator' => '===',
										'value'    => true,
									),
								),
							),
						),

						array(
							'id'         => 'exclude_users',
							'type'       => 'select_ajax',
							'title'      => esc_html__( 'Exclude Users', 'merchant' ),
							'desc'       => esc_html__( 'This will exclude the offer for the selected customers.', 'merchant' ),
							'source'     => 'user',
							'multiple'   => true,
							'classes'    => array( 'flex-grow' ),
							'conditions' => array(
								'relation' => 'AND',
								'terms'    => array(
									array(
										'field'    => 'user_condition',
										'operator' => 'in',
										'value'    => array( 'all', 'roles' ),
									),
									array(
										'field'    => 'user_exclusion_enabled',
										'operator' => '===',
										'value'    => true,
									),
								),
							),
						),

						array(
							'id'      => 'availability',
							'type'    => 'radio',
							'title'   => esc_html__( 'Availability', 'merchant' ),
							'options' => array(
								'always'   => esc_html__( 'Always available', 'merchant' ),
								'specific' => esc_html__( 'Specific dates', 'merchant' ),
							),
							'default' => 'always',
						),
						array(
							'id'          => 'start_date',
							'type'        => 'date_time',
							'title'       => esc_html__( 'Start at', 'merchant' ),
							'condition'   => array( 'availability', '==', 'specific' ),
							'placeholder' => esc_html__( 'mm/dd/yy, --:-- --', 'merchant' ),
						),
						array(
							'id'          => 'end_date',
							'type'        => 'date_time',
							'title'       => esc_html__( 'Ends at', 'merchant' ),
							'condition'   => array( 'availability', '==', 'specific' ),
							'placeholder' => esc_html__( 'mm/dd/yy, --:-- --', 'merchant' ),
							'desc'        => sprintf(
							/*
							 * translators:
							 * %1$s: time zone
							 * %2$s WordPress setting link
							*/
								esc_html__( 'Leave it empty if you don’t want to have an end date. The times set above are in the %1$s timezone, according to your settings from %2$s.',
									'merchant' ),
								'<strong>' . wp_timezone_string() . '</strong>',
								'<a href="' . esc_url( admin_url( 'options-general.php' ) ) . '" target="_blank">' . esc_html__( 'WordPress Settings', 'merchant' ) . '</a>'
							),
						),
					),
				),
			),
			'default'      => array(
				array(
					'layout'         => 'offer-details',
					'min_quantity'   => 2,
					'discount'       => 10,
					'discount_type'  => 'percentage_discount',
					'availability'   => 'always',
					'user_condition' => 'all',
				),
			),
		),
		array(
			'id'          => 'helping_instructions',
			'type'        => 'info_block',
			'description' => esc_html__( 'You can display a sale badge on products eligible for this discount by using Merchant’s Product Labels module.', 'merchant' ),
			'button_text' => esc_html__( 'View Product Labels', 'merchant' ),
			'button_link' => esc_url( admin_url( 'admin.php?page=merchant&module=product-labels' ) ),
		),
	),
) );