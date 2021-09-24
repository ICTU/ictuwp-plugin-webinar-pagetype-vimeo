<?php

if ( function_exists( 'acf_add_local_field_group' ) ):

	acf_add_local_field_group( array(
		'key'                   => 'group_6141dde284257',
		'title'                 => '01 - instellingen voor Vimeo-webinar',
		'fields'                => array(
			array(
				'key'               => 'field_6141ddff52807',
				'label'             => 'Vimeo embed code',
				'name'              => 'vimeo_embed',
				'type'              => 'textarea',
				'instructions'      => '',
				'required'          => 0,
				'conditional_logic' => 0,
				'wrapper'           => array(
					'width' => '',
					'class' => '',
					'id'    => '',
				),
				'default_value'     => '',
				'new_lines'         => '',
				'maxlength'         => '',
				'placeholder'       => '',
				'rows'              => '',
			),
			array(
				'key'               => 'field_614df2d997654',
				'label'             => 'Chat embed code',
				'name'              => 'chat_embed',
				'type'              => 'textarea',
				'instructions'      => '',
				'required'          => 0,
				'conditional_logic' => 0,
				'wrapper'           => array(
					'width' => '',
					'class' => '',
					'id'    => '',
				),
				'default_value'     => '',
				'placeholder'       => '',
				'maxlength'         => '',
				'rows'              => '',
				'new_lines'         => '',
			),
		),
		'location'              => array(
			array(
				array(
					'param'    => 'page_template',
					'operator' => '==',
					'value'    => 'page_webinar_genesis.php',
				),
			),
			array(
				array(
					'param'    => 'page_template',
					'operator' => '==',
					'value'    => 'page_webinar.php',
				),
			),
		),
		'menu_order'            => 0,
		'position'              => 'normal',
		'style'                 => 'default',
		'label_placement'       => 'top',
		'instruction_placement' => 'label',
		'hide_on_screen'        => '',
		'active'                => true,
		'description'           => '',
	) );

endif;
