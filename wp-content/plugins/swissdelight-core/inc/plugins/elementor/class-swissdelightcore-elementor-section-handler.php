<?php

class SwissDelightCore_Elementor_Section_Handler {
	private static $instance;
	public $sections = array();

	public function __construct() {
		add_action( 'elementor/element/section/_section_responsive/after_section_end', array( $this, 'render_parallax_options' ), 10, 2 );
		add_action( 'elementor/element/section/_section_responsive/after_section_end', array( $this, 'render_offset_options' ), 10, 2 );
		add_action( 'elementor/element/section/_section_responsive/after_section_end', array( $this, 'render_grid_options' ), 10, 2 );
		add_action( 'elementor/frontend/section/before_render', array( $this, 'section_before_render' ) );
		add_action( 'elementor/frontend/element/before_render', array( $this, 'section_before_render' ) );
		add_action( 'elementor/frontend/before_enqueue_styles', array( $this, 'enqueue_styles' ), 9 );
		add_action( 'elementor/frontend/before_enqueue_scripts', array( $this, 'enqueue_scripts' ), 9 );
	}

	/**
	 * @return SwissDelightCore_Elementor_Section_Handler
	 */
	public static function get_instance() {
		if ( is_null( self::$instance ) ) {
			self::$instance = new self();
		}

		return self::$instance;
	}

	public function render_parallax_options( $section, $args ) {
		$section->start_controls_section(
			'qodef_parallax',
			[
				'label' => esc_html__( 'SwissDelight Parallax', 'swissdelight-core' ),
				'tab'   => \Elementor\Controls_Manager::TAB_ADVANCED,
			]
		);

		$section->add_control(
			'qodef_parallax_type',
			[
				'label'       => esc_html__( 'Enable Parallax', 'swissdelight-core' ),
				'type'        => \Elementor\Controls_Manager::SELECT,
				'default'     => 'no',
				'options'     => [
					'no'       => esc_html__( 'No', 'swissdelight-core' ),
					'parallax' => esc_html__( 'Yes', 'swissdelight-core' ),
				],
				'render_type' => 'template',
			]
		);

		$section->add_control(
			'qodef_parallax_image',
			[
				'label'       => esc_html__( 'Parallax Background Image', 'swissdelight-core' ),
				'type'        => \Elementor\Controls_Manager::MEDIA,
				'condition'   => [
					'qodef_parallax_type' => 'parallax',
				],
				'render_type' => 'template',
			]
		);

		$section->end_controls_section();
	}

	public function render_offset_options( $section, $args ) {
		$section->start_controls_section(
			'qodef_offset',
			[
				'label' => esc_html__( 'SwissDelight Offset Image', 'swissdelight-core' ),
				'tab'   => \Elementor\Controls_Manager::TAB_ADVANCED,
			]
		);

		$section->add_control(
			'qodef_offset_type',
			[
				'label'       => esc_html__( 'Enable Offset Image', 'swissdelight-core' ),
				'type'        => \Elementor\Controls_Manager::SELECT,
				'default'     => 'no',
				'options'     => [
					'no'     => esc_html__( 'No', 'swissdelight-core' ),
					'offset' => esc_html__( 'Yes', 'swissdelight-core' ),
				],
				'render_type' => 'template',
			]
		);

		$section->add_control(
			'qodef_offset_image',
			[
				'label'       => esc_html__( 'Offset Image', 'swissdelight-core' ),
				'type'        => \Elementor\Controls_Manager::MEDIA,
				'condition'   => [
					'qodef_offset_type' => 'offset',
				],
				'render_type' => 'template',
			]
		);

		$section->add_control(
			'qodef_offset_top',
			[
				'label'       => esc_html__( 'Offset Image Top Position', 'swissdelight-core' ),
				'type'        => \Elementor\Controls_Manager::TEXT,
				'default'     => '50%',
				'condition'   => [
					'qodef_offset_type' => 'offset',
				],
				'render_type' => 'template',
			]
		);

		$section->add_control(
			'qodef_offset_left',
			[
				'label'       => esc_html__( 'Offset Image Left Position', 'swissdelight-core' ),
				'type'        => \Elementor\Controls_Manager::TEXT,
				'default'     => '50%',
				'condition'   => [
					'qodef_offset_type' => 'offset',
				],
				'render_type' => 'template',
			]
		);

        $section->add_control(
            'qodef_offset_appear',
            [
                'label'       => esc_html__( 'Enable Offset Image Appear Animation', 'swissdelight-core' ),
                'type'        => \Elementor\Controls_Manager::SELECT,
                'default'     => 'no',
                'options'     => [
                    'no'  => esc_html__( 'No', 'swissdelight-core' ),
                    'yes' => esc_html__( 'Yes', 'swissdelight-core' ),
                ],
                'render_type' => 'template',
            ]
        );

        $section->add_control(
            'qodef_offset_appear_direction',
            [
                'label'       => esc_html__( 'Offset Image Appear Direction', 'swissdelight-core' ),
                'type'        => \Elementor\Controls_Manager::SELECT,
                'default'     => 'left',
                'options'     => [
                    'left'   => esc_html__( 'Left', 'swissdelight-core' ),
                    'right'  => esc_html__( 'Right', 'swissdelight-core' ),
                    'bottom' => esc_html__( 'Bottom', 'swissdelight-core' )
                ],
                'condition'   => [
                    'qodef_offset_appear' => 'yes',
                ],
                'render_type' => 'template',
            ]
        );

        $section->add_control(
            'qodef_offset_float',
            [
                'label'       => esc_html__( 'Enable Offset Image Float Animation', 'swissdelight-core' ),
                'type'        => \Elementor\Controls_Manager::SELECT,
                'default'     => 'no',
                'options'     => [
                    'no'  => esc_html__( 'No', 'swissdelight-core' ),
                    'yes' => esc_html__( 'Yes', 'swissdelight-core' ),
                ],
                'render_type' => 'template',
            ]
        );

		$section->end_controls_section();
	}

	public function render_grid_options( $section, $args ) {
		$section->start_controls_section(
			'qodef_grid_row',
			[
				'label' => esc_html__( 'SwissDelight Grid', 'swissdelight-core' ),
				'tab'   => \Elementor\Controls_Manager::TAB_ADVANCED,
			]
		);

		$section->add_control(
			'qodef_enable_grid_row',
			[
				'label'        => esc_html__( 'Make this row "In Grid"', 'swissdelight-core' ),
				'type'         => \Elementor\Controls_Manager::SELECT,
				'default'      => 'no',
				'options'      => [
					'no'   => esc_html__( 'No', 'swissdelight-core' ),
					'grid' => esc_html__( 'Yes', 'swissdelight-core' ),
				],
				'prefix_class' => 'qodef-elementor-content-',
			]
		);

		$section->add_control(
			'qodef_grid_row_behavior',
			[
				'label'        => esc_html__( 'Grid Row Behavior', 'swissdelight-core' ),
				'type'         => \Elementor\Controls_Manager::SELECT,
				'default'      => '',
				'options'      => [
					''      => esc_html__( 'Default', 'swissdelight-core' ),
					'right' => esc_html__( 'Extend Grid Right', 'swissdelight-core' ),
					'left'  => esc_html__( 'Extend Grid Left', 'swissdelight-core' ),
				],
				'condition'    => [
					'qodef_enable_grid_row' => 'grid',
				],
				'prefix_class' => 'qodef-extended-grid qodef-extended-grid--',
			]
		);

		$section->end_controls_section();
	}

	public function section_before_render( $widget ) {
		$data     = $widget->get_data();
		$type     = isset( $data['elType'] ) ? $data['elType'] : 'section';
		$settings = $data['settings'];

		if ( 'section' === $type ) {
			if ( isset( $settings['qodef_parallax_type'] ) && 'parallax' === $settings['qodef_parallax_type'] ) {
				$parallax_type  = $widget->get_settings_for_display( 'qodef_parallax_type' );
				$parallax_image = $widget->get_settings_for_display( 'qodef_parallax_image' );

				if ( ! in_array( $data['id'], $this->sections, true ) ) {
					$this->sections[ $data['id'] ][] = array(
						'parallax_type'  => $parallax_type,
						'parallax_image' => $parallax_image,
					);
				}
			}

			if ( isset( $settings['qodef_offset_type'] ) && 'offset' === $settings['qodef_offset_type'] ) {
				$offset_type      = $widget->get_settings_for_display( 'qodef_offset_type' );
				$offset_image     = $widget->get_settings_for_display( 'qodef_offset_image' );
				$offset_top       = $widget->get_settings_for_display( 'qodef_offset_top' );
				$offset_left      = $widget->get_settings_for_display( 'qodef_offset_left' );
				$offset_appear    = $widget->get_settings_for_display( 'qodef_offset_appear' );
                $offset_direction = $widget->get_settings_for_display( 'qodef_offset_appear_direction' );
                $offset_float     = $widget->get_settings_for_display( 'qodef_offset_float' );

				if ( ! in_array( $data['id'], $this->sections, true ) ) {
					$this->sections[ $data['id'] ][] = array(
						'offset_type'       => $offset_type,
						'offset_image'      => $offset_image,
						'offset_top'        => $offset_top,
						'offset_left'       => $offset_left,
                        'offset_appear'     => $offset_appear,
                        'offset_direction' => $offset_direction,
                        'offset_float'      => $offset_float
					);
				}
			}
		}
	}

	public function enqueue_styles() {
		wp_enqueue_style( 'swissdelight-core-elementor', SWISSDELIGHT_CORE_PLUGINS_URL_PATH . '/elementor/assets/css/elementor.min.css' );
	}

	public function enqueue_scripts() {
		wp_enqueue_script( 'swissdelight-core-elementor', SWISSDELIGHT_CORE_PLUGINS_URL_PATH . '/elementor/assets/js/elementor.js', array( 'jquery', 'elementor-frontend' ) );

		$elementor_global_vars = array(
			'elementorSectionHandler' => $this->sections,
		);

		wp_localize_script(
			'swissdelight-core-elementor',
			'qodefElementorGlobal',
			array(
				'vars' => $elementor_global_vars,
			)
		);
	}
}

if ( ! function_exists( 'swissdelight_core_init_elementor_section_handler' ) ) {
	/**
	 * Function that initialize main page builder handler
	 */
	function swissdelight_core_init_elementor_section_handler() {
		SwissDelightCore_Elementor_Section_Handler::get_instance();
	}

	add_action( 'init', 'swissdelight_core_init_elementor_section_handler', 1 );
}
