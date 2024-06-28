<?php
/**
 * Theme Options 
 * 
 * @package Mag_Lite
 */
$default = mag_lite_get_default_theme_options();

/****************  Add Pannel   ***********************/
$wp_customize->add_panel( 'theme_option_panel',
	array(
	'title'      => esc_html__( 'Theme Options', 'mag-lite' ),
	'priority'   => 100,
	'capability' => 'edit_theme_options',
	)
);

/****************  Header Setting Section starts ************/
$wp_customize->add_section('section_header', 
	array(    
	'title'       => esc_html__('Header Setting', 'mag-lite'),
	'panel'       => 'theme_option_panel'    
	)
);

/************************  Site Identity  ******************/
$wp_customize->add_setting('theme_options[site_identity]', 
	array(
	'default' 			=> $default['site_identity'],
	'sanitize_callback' => 'mag_lite_sanitize_select'
	)
);

$wp_customize->add_control('theme_options[site_identity]', 
	array(		
	'label' 	=> esc_html__('Choose Option', 'mag-lite'),
	'section' 	=> 'title_tagline',
	'settings'  => 'theme_options[site_identity]',
	'type' 		=> 'radio',
	'choices' 	=>  array(
			'logo-only' 	=> esc_html__('Logo Only', 'mag-lite'),
			'logo-text' 	=> esc_html__('Logo + Tagline', 'mag-lite'),
			'title-only' 	=> esc_html__('Title Only', 'mag-lite'),
			'title-text' 	=> esc_html__('Title + Tagline', 'mag-lite')
		)
	)
);

/********************* Enable Top Header ****************************/
$wp_customize->add_setting( 'theme_options[enable_top_header]',
	array(
		'default'           => $default['enable_top_header'],
		'capability'        => 'edit_theme_options',
		'sanitize_callback' => 'mag_lite_sanitize_checkbox',
	)
);
$wp_customize->add_control( 'theme_options[enable_top_header]',
	array(
		'label'    => esc_html__( 'Enable Top Header', 'mag-lite' ),
		'section'  => 'section_header',
		'type'     => 'checkbox',		
	)
);

/************************  Top Header Left Part  ******************/
$wp_customize->add_setting('theme_options[top_header_left]', 
	array(
	'default' 			=> $default['top_header_left'],
	'sanitize_callback' => 'mag_lite_sanitize_select'
	)
);

$wp_customize->add_control('theme_options[top_header_left]', 
	array(		
	'label' 	=> esc_html__('Top Left Header Option', 'mag-lite'),
	'section' 	=> 'section_header',
	'settings'  => 'theme_options[top_header_left]',
	'type' 		=> 'select',
	'choices' 	=>  array(
			'menu' 	=> esc_html__('Menu', 'mag-lite'),
			'address' 	=> esc_html__('Address', 'mag-lite'),
			'current-date' 	=> esc_html__('Current Date', 'mag-lite'),
			'social-media' 	=> esc_html__('Social Media', 'mag-lite')
		)
	)
);

/************************  Top Header Right Part  ******************/
$wp_customize->add_setting('theme_options[top_header_right]', 
	array(
	'default' 			=> $default['top_header_right'],
	'sanitize_callback' => 'mag_lite_sanitize_select'
	)
);

$wp_customize->add_control('theme_options[top_header_right]', 
	array(		
	'label' 	=> esc_html__('Top Right Header Option', 'mag-lite'),
	'section' 	=> 'section_header',
	'settings'  => 'theme_options[top_header_right]',
	'type' 		=> 'select',
	'choices' 	=>  array(
			'menu' 	=> esc_html__('Menu', 'mag-lite'),
			'address' 	=> esc_html__('Address', 'mag-lite'),
			'current-date' 	=> esc_html__('Current Date', 'mag-lite'),
			'social-media' 	=> esc_html__('Social Media', 'mag-lite')
		)
	)
);

/************************  Header Address  ******************/
$wp_customize->add_setting( 'theme_options[header_address]',
	array(
	'default'           => $default['header_address'],
	'capability'        => 'edit_theme_options',
	'sanitize_callback' => 'sanitize_textarea_field',	
	)
);
$wp_customize->add_control( 'theme_options[header_address]',
	array(
	'label'    => esc_html__( 'Top Header Address', 'mag-lite' ),
	'section'  => 'section_header',
	'type'     => 'text',
	
	)
);

/************************  Top Header Phone Number  ******************/
$wp_customize->add_setting( 'theme_options[header_number]',
	array(
	'default'           => $default['header_number'],
	'capability'        => 'edit_theme_options',
	'sanitize_callback' => 'sanitize_text_field',	
	)
);
$wp_customize->add_control( 'theme_options[header_number]',
	array(
	'label'    => esc_html__( 'Phone Number', 'mag-lite' ),
	'section'  => 'section_header',
	'type'     => 'text',
	
	)
);

/************************  Top Header Email  ******************/
$wp_customize->add_setting('theme_options[header_email]',  
	array(
	'default'           => $default['header_email'],
	'type'              => 'theme_mod',
	'capability'        => 'edit_theme_options',	
	'sanitize_callback' => 'sanitize_email',
	
	)
);

$wp_customize->add_control('theme_options[header_email]', 
	array(
	'label'       => esc_html__('Contact Email', 'mag-lite'),
	'section'     => 'section_header',   
	'settings'    => 'theme_options[header_email]',		
	'type'        => 'text'
	)
);
/****************  Archive Page Setting ************/
$wp_customize->add_section('section_archive', 
	array(    
	'title'       => esc_html__('Archive Setting', 'mag-lite'),
	'panel'       => 'theme_option_panel'    
	)
);

/************************  Archive Page Layout ******************/
$wp_customize->add_setting('theme_options[archive_layout]', 
	array(
	'default' 			=> $default['archive_layout'],
	'sanitize_callback' => 'mag_lite_sanitize_select'
	)
);

$wp_customize->add_control('theme_options[archive_layout]', 
	array(		
	'label' 	=> __('Choose Option', 'mag-lite'),
	'section' 	=> 'section_archive',
	'settings'  => 'theme_options[archive_layout]',
	'type' 		=> 'select',
	'choices' 	=>  array(
			'first-design' 		=> esc_html__('Layout 1', 'mag-lite'),
			'second-design' 	=> esc_html__('Layout 2', 'mag-lite'),			
		)
	)
);

/****************  General Setting Section starts ************/
$wp_customize->add_section('section_general', 
	array(    
	'title'       => esc_html__('General Setting', 'mag-lite'),
	'panel'       => 'theme_option_panel'    
	)
);

/**********************  Layout Options ***************************/
$wp_customize->add_setting('theme_options[layout_options]', 
	array(
	'default' 			=> $default['layout_options'],
	'sanitize_callback' => 'mag_lite_sanitize_select',
	)
);

$wp_customize->add_control(new Mag_lite_Image_Radio_Control($wp_customize, 'theme_options[layout_options]', 
	array(		
	'label' 	=> esc_html__('Layout Options', 'mag-lite'),
	'section' 	=> 'section_general',
	'settings'  => 'theme_options[layout_options]',
	'type' 		=> 'radio-image',
	'choices' 	=> array(		
		'left' 			=> get_template_directory_uri() . '/assest/img/left-sidebar.png',							
		'right' 		=> get_template_directory_uri() . '/assest/img/right-sidebar.png',
		'no-sidebar' 	=> get_template_directory_uri() . '/assest/img/no-sidebar.png',
		),	
	))
);

/************************** Breadcrumb Section  **************************/
$wp_customize->add_section('section_breadcrumb', 
	array(    
	'title'       => esc_html__('Breadcrumb Setting', 'mag-lite'),
	'panel'       => 'theme_option_panel'    
	)
);
/****************************** Enable Breadcrumb *************************/
$wp_customize->add_setting('theme_options[enable_breadcrumb]', 
	array(
	'default' 			=> $default['enable_breadcrumb'],
	'type'              => 'theme_mod',
	'capability'        => 'edit_theme_options',
	'sanitize_callback' => 'mag_lite_sanitize_checkbox'
	)
);

$wp_customize->add_control('theme_options[enable_breadcrumb]', 
	array(		
	'label' 	=> esc_html__('Enable Breadcrumb:', 'mag-lite'),
	'section' 	=> 'section_breadcrumb',
	'settings'  => 'theme_options[enable_breadcrumb]',
	'type' 		=> 'checkbox',	
	)
);
/****************  Categories Color ************/
$wp_customize->add_section('section_categories_color', 
	array(    
	'title'       => esc_html__('Categories Color Setting', 'mag-lite'),
	'panel'       => 'theme_option_panel'    
	)
);

	$priority = 3;
	$categories = get_terms( 'category' ); // Get all Categories
	$wp_category_list = array();

	foreach ( $categories as $category_list ) {

		$wp_customize->add_setting('theme_options[mag_lite_category_color_'.esc_html( strtolower($category_list->name) ).']',
			array(
				'default'              => $default['mag_lite_category_color_'.esc_html( strtolower($category_list->name) ).''],
				'capability'           => 'edit_theme_options',
				'sanitize_callback'    => 'sanitize_hex_color'
			)
		);

		$wp_customize->add_control(
			new WP_Customize_Color_Control($wp_customize,'theme_options[mag_lite_category_color_'.esc_html( strtolower($category_list->name) ).']',
				array(
					/* translators: %s: category namet */
					'label'    => sprintf( esc_html__( ' %s', 'mag-lite' ), esc_html( $category_list->name ) ),
					'section'  => 'section_categories_color',
					'priority' => absint($priority)
				)
			)
		);
		$priority++;
	}


/****************  Footer Setting Section starts ************/
$wp_customize->add_section('section_footer', 
	array(    
	'title'       => esc_html__('Footer Setting', 'mag-lite'),
	'panel'       => 'theme_option_panel'    
	)
);

/********************** Subscription Page *****************************/
$wp_customize->add_setting('theme_options[subscription_page]', 
	array(
	'default'           => $default['subscription_page'],
	'type'              => 'theme_mod',
	'capability'        => 'edit_theme_options',	
	'sanitize_callback' => 'mag_lite_sanitize_dropdown_pages'
	)
);

$wp_customize->add_control('theme_options[subscription_page]', 
	array(
	'label'       => esc_html__('Select Subscription Page', 'mag-lite'),
    'description' => esc_html__( 'Select page from dropdown or leave blank if you want to hide this section.', 'mag-lite' ), 
	'section'     => 'section_footer',   
	'settings'    => 'theme_options[subscription_page]',		
	'type'        => 'dropdown-pages'
	)
);

/************************  Footer Copyright  ******************/
$wp_customize->add_setting( 'theme_options[copyright_text]',
	array(
	'default'           => $default['copyright_text'],
	'capability'        => 'edit_theme_options',
	'sanitize_callback' => 'sanitize_textarea_field',	
	)
);
$wp_customize->add_control( 'theme_options[copyright_text]',
	array(
	'label'    => esc_html__( 'Footer Copyright', 'mag-lite' ),
	'section'  => 'section_footer',
	'type'     => 'text',
	
	)
);

/********************* Enable Social ****************************/
$wp_customize->add_setting( 'theme_options[enable_footer_menu]',
	array(
		'default'           => $default['enable_footer_menu'],
		'capability'        => 'edit_theme_options',
		'sanitize_callback' => 'mag_lite_sanitize_checkbox',
	)
);
$wp_customize->add_control( 'theme_options[enable_footer_menu]',
	array(
		'label'    => esc_html__( 'Enable Socia Icon', 'mag-lite' ),
		'section'  => 'section_footer',
		'type'     => 'checkbox',		
	)
);
