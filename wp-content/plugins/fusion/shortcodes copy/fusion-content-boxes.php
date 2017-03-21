<?php

/**
 * Shortcode class.
 *
 * @package fusion-builder
 * @since 1.0
 */
class FusionSC_ContentBoxes {

	/**
	 * Content box counter.
	 *
	 * @access private
	 * @since 1.0
	 * @var int
	 */
	private $content_box_counter = 1;

	/**
	 * Columns counter.
	 *
	 * @access private
	 * @since 1.0
	 * @var int
	 */
	private $column_counter = 1;

	/**
	 * Number of columns.
	 *
	 * @access private
	 * @since 1.0
	 * @var int
	 */
	private $num_of_columns = 1;

	/**
	 * Total number of columns.
	 *
	 * @access private
	 * @since 1.0
	 * @var int
	 */
	private $total_num_of_columns = 1;

	/**
	 * Rows counter.
	 *
	 * @access private
	 * @since 1.0
	 * @var int
	 */
	private $row_counter = 1;

	/**
	 * Parent SC arguments.
	 *
	 * @static
	 * @access public
	 * @since 1.0
	 * @var array
	 */
	public static $parent_args;

	/**
	 * Child SC arguments.
	 *
	 * @static
	 * @access public
	 * @since 1.0
	 * @var array
	 */
	public static $child_args;

	/**
	 * Constructor.
	 *
	 * @access public
	 * @since 1.0
	 * @since 1.0
	 */
	public function __construct() {

		add_filter( 'fusion_attr_content-box-shortcode', array( $this, 'child_attr' ) );
		add_filter( 'fusion_attr_content-box-shortcode-content-wrapper', array( $this, 'content_wrapper_attr' ) );
		add_filter( 'fusion_attr_content-box-shortcode-heading-wrapper', array( $this, 'heading_wrapper_attr' ) );
		add_filter( 'fusion_attr_content-box-shortcode-content-container', array( $this, 'content_container_attr' ) );

		add_filter( 'fusion_attr_content-box-shortcode-link', array( $this, 'link_attr' ) );
		add_filter( 'fusion_attr_content-box-shortcode-icon-parent', array( $this, 'icon_parent_attr' ) );
		add_filter( 'fusion_attr_content-box-shortcode-icon-wrapper', array( $this, 'icon_wrapper_attr' ) );
		add_filter( 'fusion_attr_content-box-shortcode-icon', array( $this, 'icon_attr' ) );
		add_filter( 'fusion_attr_content-box-shortcode-timeline', array( $this, 'timeline_attr' ) );
		add_filter( 'fusion_attr_content-box-heading', array( $this, 'content_box_heading_attr' ) );
		add_shortcode( 'fusion_content_box', array( $this, 'render_child' ) );

		add_filter( 'fusion_attr_content-boxes-shortcode', array( $this, 'parent_attr' ) );
		add_shortcode( 'fusion_content_boxes', array( $this, 'render_parent' ) );

	}

	/**
	 * Render the shortcode.
	 *
	 * @access public
	 * @since 1.0
	 * @param  array  $args    Shortcode parameters.
	 * @param  string $content Content between shortcode.
	 * @return string          HTML output.
	 */
	public function render_parent( $args, $content = '' ) {

		$defaults = FusionBuilder::set_shortcode_defaults(
			array(
				'hide_on_mobile'         => fusion_builder_default_visibility( 'string' ),
				'class'                  => '',
				'id'                     => '',
				'backgroundcolor'        => FusionBuilder::get_theme_option( 'content_box_bg_color' ),
				'columns'                => '',
				'circle'                 => '',
				'iconcolor'              => ( class_exists( 'Avada_Sanitize' ) ) ? Avada_Sanitize::color( FusionBuilder::get_theme_option( 'content_box_icon_color' ) ) : '',
				'circlecolor'            => ( class_exists( 'Avada_Sanitize' ) ) ? Avada_Sanitize::color( FusionBuilder::get_theme_option( 'content_box_icon_bg_color' ) ) : '',
				'circlebordercolor'      => ( class_exists( 'Avada_Sanitize' ) ) ? Avada_Sanitize::color( FusionBuilder::get_theme_option( 'content_box_icon_bg_inner_border_color' ) ) : '',
				'circlebordersize'       => intval( FusionBuilder::get_theme_option( 'content_box_icon_bg_inner_border_size' ) ) . 'px',
				'outercirclebordercolor' => ( class_exists( 'Avada_Sanitize' ) ) ? Avada_Sanitize::color( FusionBuilder::get_theme_option( 'content_box_icon_bg_outer_border_color' ) ) : '',
				'outercirclebordersize'  => intval( FusionBuilder::get_theme_option( 'content_box_icon_bg_outer_border_size' ) ) . 'px',
				'icon_circle'            => FusionBuilder::get_theme_option( 'content_box_icon_circle' ),
				'icon_circle_radius'     => FusionBuilder::get_theme_option( 'content_box_icon_circle_radius' ),
				'icon_size'              => ( class_exists( 'Avada_Sanitize' ) ) ? Avada_Sanitize::size( FusionBuilder::get_theme_option( 'content_box_icon_size' ) ) : '',
				'icon_align'             => '',
				'icon_hover_type'        => FusionBuilder::get_theme_option( 'content_box_icon_hover_type' ),
				'hover_accent_color'     => ( class_exists( 'Avada_Sanitize' ) ) ? ( '' !== FusionBuilder::get_theme_option( 'content_box_hover_animation_accent_color' ) ) ? Avada_Sanitize::color( FusionBuilder::get_theme_option( 'content_box_hover_animation_accent_color' ) ) : Avada_Sanitize::color( FusionBuilder::get_theme_option( 'primary_color' ) ) : '',
				'layout'                 => 'icon-with-title',
				'margin_top'             => FusionBuilder::get_theme_option( 'content_box_margin', 'top' ),
				'margin_bottom'          => FusionBuilder::get_theme_option( 'content_box_margin', 'bottom' ),
				'title_size'             => ( class_exists( 'Avada_Sanitize' ) ) ? Avada_Sanitize::size( FusionBuilder::get_theme_option( 'content_box_title_size' ) ) : '',
				'title_color'            => ( class_exists( 'Avada_Sanitize' ) ) ? Avada_Sanitize::color( FusionBuilder::get_theme_option( 'content_box_title_color' ) ) : '',
				'body_color'             => ( class_exists( 'Avada_Sanitize' ) ) ? Avada_Sanitize::color( FusionBuilder::get_theme_option( 'content_box_body_color' ) ) : '',
				'link_type'              => FusionBuilder::get_theme_option( 'content_box_link_type' ),
				'link_area'              => FusionBuilder::get_theme_option( 'content_box_link_area' ),
				'link_target'            => FusionBuilder::get_theme_option( 'content_box_link_target' ),
				'animation_type'         => '',
				'animation_delay'        => '',
				'animation_direction'    => 'left',
				'animation_speed'        => '0.1',
				'animation_offset'       => ( class_exists( 'Avada' ) ) ? Avada()->settings->get( 'animation_offset' ) : '',
				'settings_lvl'           => 'child',
				'linktarget'             => '', // Deprecated.
			), $args
		);

		$defaults['title_size']            = FusionBuilder::validate_shortcode_attr_value( $defaults['title_size'], 'px' );
		$defaults['icon_circle_radius']    = FusionBuilder::validate_shortcode_attr_value( $defaults['icon_circle_radius'], 'px' );
		$defaults['icon_size']             = FusionBuilder::validate_shortcode_attr_value( $defaults['icon_size'], 'px' );
		$defaults['margin_top']            = FusionBuilder::validate_shortcode_attr_value( $defaults['margin_top'], 'px' );
		$defaults['margin_bottom']         = FusionBuilder::validate_shortcode_attr_value( $defaults['margin_bottom'], 'px' );
		$defaults['margin_bottom']         = FusionBuilder::validate_shortcode_attr_value( $defaults['margin_bottom'], 'px' );
		$defaults['circlebordersize']      = FusionBuilder::validate_shortcode_attr_value( $defaults['circlebordersize'], 'px' );
		$defaults['outercirclebordersize'] = FusionBuilder::validate_shortcode_attr_value( $defaults['outercirclebordersize'], 'px' );

		if ( $defaults['linktarget'] ) {
			$defaults['link_target'] = $defaults['linktarget'];
		}

		if ( 'timeline-vertical' === $defaults['layout'] ) {
			$defaults['columns'] = 1;
		}

		if ( 'timeline-vertical' === $defaults['layout'] || 'timeline-horizontal' === $defaults['layout'] ) { // See #1362.
			$defaults['animation_delay']     = 350;
			$defaults['animation_speed']     = 0.25;
			$defaults['animation_type']      = 'fade';
			$defaults['animation_direction'] = '';
		}

		extract( $defaults );

		self::$parent_args = $defaults;

		$this->column_counter = 1;
		$this->row_counter = 1;

		preg_match_all( '/(\[fusion_content_box (.*?)\](.*?)\[\/fusion_content_box\])/s', $content, $matches );
		$this->total_num_of_columns = count( $matches[0] );

		$this->num_of_columns = $columns;
		if ( ! $columns || empty( $columns ) ) {
			$this->num_of_columns = 1;
			if ( is_array( $matches ) && ! empty( $matches ) ) {
				$this->num_of_columns = count( $matches[0] );
				$this->num_of_columns = max( 6, $this->num_of_columns );
			}
		} elseif ( $columns > 6 ) {
			$this->num_of_columns = 6;
		}

		$styles = '<style type="text/css" scoped="scoped">';

		if ( $title_color ) {
			$styles .= ".fusion-content-boxes-{$this->content_box_counter} .heading h2{color:{$title_color};}";
		}

		$styles .= "
			.fusion-content-boxes-{$this->content_box_counter} .fusion-content-box-hover .link-area-link-icon-hover .heading h2,
			.fusion-content-boxes-{$this->content_box_counter} .fusion-content-box-hover .link-area-link-icon-hover .heading .heading-link h2,
			.fusion-content-boxes-{$this->content_box_counter} .fusion-content-box-hover .link-area-box-hover .heading h2,
			.fusion-content-boxes-{$this->content_box_counter} .fusion-content-box-hover .link-area-box-hover .heading .heading-link h2,
			.fusion-content-boxes-{$this->content_box_counter} .fusion-content-box-hover .link-area-link-icon-hover.link-area-box .fusion-read-more,
			.fusion-content-boxes-{$this->content_box_counter} .fusion-content-box-hover .link-area-link-icon-hover.link-area-box .fusion-read-more::after,
			.fusion-content-boxes-{$this->content_box_counter} .fusion-content-box-hover .link-area-link-icon-hover.link-area-box .fusion-read-more::before,
			.fusion-content-boxes-{$this->content_box_counter} .fusion-content-box-hover .fusion-read-more:hover:after,
			.fusion-content-boxes-{$this->content_box_counter} .fusion-content-box-hover .fusion-read-more:hover:before,
			.fusion-content-boxes-{$this->content_box_counter} .fusion-content-box-hover .fusion-read-more:hover,
			.fusion-content-boxes-{$this->content_box_counter} .fusion-content-box-hover .link-area-box-hover.link-area-box .fusion-read-more,
			.fusion-content-boxes-{$this->content_box_counter} .fusion-content-box-hover .link-area-box-hover.link-area-box .fusion-read-more::after,
			.fusion-content-boxes-{$this->content_box_counter} .fusion-content-box-hover .link-area-box-hover.link-area-box .fusion-read-more::before,
			.fusion-content-boxes-{$this->content_box_counter} .fusion-content-box-hover .link-area-link-icon-hover .icon .circle-no,
			.fusion-content-boxes-{$this->content_box_counter} .fusion-content-box-hover .link-area-box-hover .icon .circle-no {
				color: {$hover_accent_color} !important;
			}";

		$circle_hover_accent_color = $hover_accent_color;
		if ( 'transparent' === $circlecolor || '0' == Avada_Color::new_color( $circlecolor )->alpha ) {
			$circle_hover_accent_color = 'transparent';
		}
		$styles .= "
			.fusion-content-boxes-{$this->content_box_counter} .fusion-content-box-hover .heading-link:hover .icon i.circle-yes,
			.fusion-content-boxes-{$this->content_box_counter} .fusion-content-box-hover .link-area-box:hover .heading-link .icon i.circle-yes,
			.fusion-content-boxes-{$this->content_box_counter} .fusion-content-box-hover .link-area-link-icon-hover .heading .icon i.circle-yes,
			.fusion-content-boxes-{$this->content_box_counter} .fusion-content-box-hover .link-area-box-hover .heading .icon i.circle-yes {
				background-color: {$circle_hover_accent_color} !important;
				border-color: {$hover_accent_color} !important;
			}";

		if ( 'pulsate' === $icon_hover_type && $hover_accent_color ) {

			$styles .= "
				.fusion-content-boxes-{$this->content_box_counter} .fusion-content-box-hover .link-area-link-icon-hover.icon-hover-animation-pulsate .fontawesome-icon:after,
				.fusion-content-boxes-{$this->content_box_counter} .fusion-content-box-hover .link-area-box-hover.icon-hover-animation-pulsate .fontawesome-icon:after,
				.fusion-content-boxes-{$this->content_box_counter} .fusion-content-box-hover .link-area-link-icon-hover.icon-wrapper-hover-animation-pulsate .icon span:after,
				.fusion-content-boxes-{$this->content_box_counter} .fusion-content-box-hover .link-area-box-hover.icon-wrapper-hover-animation-pulsate .icon span:after {
					-webkit-box-shadow:0 0 0 2px rgba(255,255,255,0.1), 0 0 10px 10px {$hover_accent_color}, 0 0 0 10px rgba(255,255,255,0.5);
					-moz-box-shadow:0 0 0 2px rgba(255,255,255,0.1), 0 0 10px 10px {$hover_accent_color}, 0 0 0 10px rgba(255,255,255,0.5);
					box-shadow: 0 0 0 2px rgba(255,255,255,0.1), 0 0 10px 10px {$hover_accent_color}, 0 0 0 10px rgba(255,255,255,0.5);
				}
			";
		}

		$styles .= '</style>';

		$html  = '<div ' . FusionBuilder::attributes( 'content-boxes-shortcode' ) . '>';
		$html .= $styles . do_shortcode( $content );
		$html .= '<div class="fusion-clearfix"></div></div>';

		$this->content_box_counter++;

		return $html;

	}

	/**
	 * Builds the attributes array.
	 *
	 * @access public
	 * @since 1.0
	 * @return array
	 */
	public function parent_attr() {

		$attr = array(
			'class' => '',
		);

		$attr['class']  = 'fusion-content-boxes content-boxes columns row';
		$attr['class'] .= ' fusion-columns-' . $this->num_of_columns;
		$attr['class'] .= ' fusion-columns-total-' . $this->total_num_of_columns;
		$attr['class'] .= ' fusion-content-boxes-' . $this->content_box_counter;
		$attr['class'] .= ' content-boxes-' . self::$parent_args['layout'];
		$attr['class'] .= ' content-' . self::$parent_args['icon_align'];

		$attr = fusion_builder_visibility_atts( self::$parent_args['hide_on_mobile'], $attr );

		if ( 'timeline-horizontal' === self::$parent_args['layout'] || 'clean-vertical' === self::$parent_args['layout'] ) {
			$attr['class'] .= ' content-boxes-icon-on-top';
		}

		if ( 'timeline-vertical' === self::$parent_args['layout'] ) {
			$attr['class'] .= ' content-boxes-icon-with-title';
		}

		if ( 'clean-horizontal' === self::$parent_args['layout'] ) {
			$attr['class'] .= ' content-boxes-icon-on-side';
		}

		if ( self::$parent_args['class'] ) {
			$attr['class'] .= ' ' . self::$parent_args['class'];
		}

		if ( self::$parent_args['id'] ) {
			$attr['id'] = self::$parent_args['id'];
		}

		if ( self::$parent_args['animation_delay'] ) {
			$attr['data-animation-delay'] = self::$parent_args['animation_delay'];
			$attr['class'] .= ' fusion-delayed-animation';
		}

		if ( self::$parent_args['animation_offset'] ) {
			$animations = FusionBuilder::animations( array(
				'offset'     => self::$parent_args['animation_offset'],
			) );

			$attr = array_merge( $attr, $animations );
		}

		$attr['style'] = 'margin-top:' . self::$parent_args['margin_top'] . ';margin-bottom:' . self::$parent_args['margin_bottom'] . ';';

		return $attr;

	}

	/**
	 * Render the child shortcode.
	 *
	 * @access public
	 * @since 1.0
	 * @param  array  $args    Shortcode parameters.
	 * @param  string $content Content between shortcode.
	 * @return string          HTML output.
	 */
	public function render_child( $args, $content = '' ) {

		$defaults = FusionBuilder::set_shortcode_defaults(
			array(
				'class'                  => '',
				'id'                     => '',
				'backgroundcolor'        => self::$parent_args['backgroundcolor'],
				'circle'                 => '',
				'circlecolor'            => self::$parent_args['circlecolor'],
				'circlebordercolor'      => self::$parent_args['circlebordercolor'],
				'circlebordersize'       => self::$parent_args['circlebordersize'],
				'outercirclebordercolor' => self::$parent_args['outercirclebordercolor'],
				'outercirclebordersize'  => self::$parent_args['outercirclebordersize'],
				'icon'                   => '',
				'iconcolor'              => self::$parent_args['iconcolor'],
				'iconrotate'             => '',
				'iconspin'               => '',
				'image'                  => '',
				'image_height'           => '35',
				'image_width'            => '35',
				'link'                   => '',
				'link_target'            => self::$parent_args['link_target'],
				'linktext'               => '',
				'textcolor'              => '',
				'title'                  => '',
				'animation_type'         => self::$parent_args['animation_type'],
				'animation_direction'    => self::$parent_args['animation_direction'],
				'animation_speed'        => self::$parent_args['animation_speed'],
				'animation_offset'       => self::$parent_args['animation_offset'],
				'linktarget'             => '', // Deprecated.
			), $args
		);

		$defaults['image_width'] = FusionBuilder::validate_shortcode_attr_value( $defaults['image_width'], '' );
		$defaults['image_height'] = FusionBuilder::validate_shortcode_attr_value( $defaults['image_height'], '' );

		if ( $defaults['linktarget'] ) {
			$defaults['link_target'] = $defaults['linktarget'];
		}

		if ( 'parent' === self::$parent_args['settings_lvl'] ) {
			$defaults['backgroundcolor']        = self::$parent_args['backgroundcolor'];
			$defaults['circlecolor']            = self::$parent_args['circlecolor'];
			$defaults['circlebordercolor']      = self::$parent_args['circlebordercolor'];
			$defaults['circlebordersize']       = self::$parent_args['circlebordersize'];
			$defaults['outercirclebordercolor'] = self::$parent_args['outercirclebordercolor'];
			$defaults['outercirclebordersize']  = self::$parent_args['outercirclebordersize'];
			$defaults['iconcolor']              = self::$parent_args['iconcolor'];
			$defaults['animation_type']         = self::$parent_args['animation_type'];
			$defaults['animation_direction']    = self::$parent_args['animation_direction'];
			$defaults['animation_speed']        = self::$parent_args['animation_speed'];
			$defaults['link_target']            = self::$parent_args['link_target'];
		}

		if ( 'timeline-vertical' === self::$parent_args['layout'] || 'timeline-horizontal' === self::$parent_args['layout'] ) {
			$defaults['animation_speed']     = 0.25;
			$defaults['animation_type']      = 'fade';
			$defaults['animation_direction'] = '';
		}

		extract( $defaults );

		self::$child_args = $defaults;

		$output         = '';
		$icon_output    = '';
		$title_output   = '';
		$content_output = '';
		$link_output    = '';
		$alt            = '';
		$heading        = '';

		if ( $image && $image_width && $image_height ) {
			$image_id = FusionBuilder::get_attachment_id_from_url( $image );
			if ( $image_id ) {
				$alt = get_post_meta( $image_id, '_wp_attachment_image_alt', true );
			}
			$icon_output  = '<div ' . FusionBuilder::attributes( 'content-box-shortcode-icon' ) . '>';
			$icon_output .= '<img src="' . $image . '" width="' . $image_width . '" height="' . $image_height . '" alt="' . $alt . '" />';
			$icon_output .= '</div>';
		} elseif ( $icon ) {
			$icon_output  = '<div ' . FusionBuilder::attributes( 'content-box-shortcode-icon-parent' ) . '>';
			$icon_output .= '<i ' . FusionBuilder::attributes( 'content-box-shortcode-icon' ) . '></i>';
			$icon_output .= '</div>';
			if ( $outercirclebordercolor && $outercirclebordersize && intval( $outercirclebordersize ) ) {
				$icon_output  = '<div ' . FusionBuilder::attributes( 'content-box-shortcode-icon-parent' ) . '>';
				$icon_output .= '<span ' . FusionBuilder::attributes( 'content-box-shortcode-icon-wrapper' ) . '>';
				$icon_output .= '<i ' . FusionBuilder::attributes( 'content-box-shortcode-icon' ) . '></i>';
				$icon_output .= '</span></div>';
			}
		}

		if ( $title ) {
			$title_output = '<h2 ' . FusionBuilder::attributes( 'content-box-heading' ) . '>' . $title . '</h2>';
		}

		if ( 'right' === self::$parent_args['icon_align'] && in_array( self::$parent_args['layout'], array( 'icon-on-side', 'icon-with-title', 'timeline-vertical', 'clean-horizontal' ), true ) ) {
			$heading_content = $title_output . $icon_output;
		} else {
			$heading_content = $icon_output . $title_output;
		}

		if ( $link ) {
			$heading_content = '<a ' . FusionBuilder::attributes( 'heading-link' ) . ' ' . FusionBuilder::attributes( 'content-box-shortcode-link' ) . '>' . $heading_content . '</a>';
		}

		if ( $heading_content ) {
			$heading = '<div ' . FusionBuilder::attributes( 'content-box-shortcode-heading-wrapper' ) . '>' . $heading_content . '</div>';
		}

		if ( $link && $linktext ) {
			if ( 'text' === self::$parent_args['link_type'] || 'button-bar' === self::$parent_args['link_type'] ) {
				$link_output  = '<div class="fusion-clearfix"></div>';
				$link_output .= '<a ' . FusionBuilder::attributes( 'fusion-read-more' ) . ' ' . FusionBuilder::attributes( 'content-box-shortcode-link', array( 'readmore' => true ) ) . '>' . $linktext . '</a>';
				$link_output .= '<div class="fusion-clearfix"></div>';
			} elseif ( 'button' === self::$parent_args['link_type'] ) {
				$link_output  = '<div class="fusion-clearfix"></div>';
				$link_output .= '<a ' . FusionBuilder::attributes( 'content-box-shortcode-link' ) . '>' . $linktext . '</a>';
				$link_output .= '<div class="fusion-clearfix"></div>';
			}
		}

		$content_output  = '<div class="fusion-clearfix"></div>';
		$content_output .= '<div ' . FusionBuilder::attributes( 'content-box-shortcode-content-container' ) . '>' . do_shortcode( $content ) . '</div>' . $link_output;
		$output          = $heading . $content_output;
		$timeline        = '';

		if ( $icon && 'yes' === self::$parent_args['icon_circle'] && 'timeline-horizontal' === self::$parent_args['layout'] && '1' != self::$parent_args['columns'] ) {
			$timeline = '<div ' . FusionBuilder::attributes( 'content-box-shortcode-timeline' ) . '></div>';
		}

		if ( $icon && 'yes' === self::$parent_args['icon_circle'] && 'timeline-vertical' === self::$parent_args['layout'] ) {
			$timeline = '<div ' . FusionBuilder::attributes( 'content-box-shortcode-timeline' ) . '></div>';
		}

		$html  = '<div ' . FusionBuilder::attributes( 'content-box-shortcode' ) . '>';
		$html .= '<div ' . FusionBuilder::attributes( 'content-box-shortcode-content-wrapper' ) . '>' . $output . $timeline . '</div>';
		$html .= '</div>';

		$clearfix_test = $this->column_counter / $this->num_of_columns;

		if ( is_int( $clearfix_test ) ) {
			$html .= '<div class="fusion-clearfix"></div>';
		}

		$this->column_counter++;

		return $html;

	}

	/**
	 * Builds the attributes array.
	 *
	 * @access public
	 * @since 1.0
	 * @return array
	 */
	public function child_attr() {

		$columns = 12 / $this->num_of_columns;

		if ( $this->row_counter > intval( $this->num_of_columns ) ) {
			$this->row_counter = 1;
		}

		$attr = array(
			'style' => '',
			'class' => 'fusion-column content-box-column',
		);
		$attr['class'] .= ' content-box-column content-box-column-' . $this->column_counter . ' col-lg-' . $columns . ' col-md-' . $columns . ' col-sm-' . $columns;

		if ( '5' == $this->num_of_columns ) {
			$attr['class'] = 'fusion-column content-box-column content-box-column-' . $this->column_counter . ' col-lg-2 col-md-2 col-sm-2';
		}

		$attr['class'] .= ' fusion-content-box-hover ';

		$border_color = '';

		if ( self::$child_args['circlebordercolor'] ) {
			$border_color = self::$child_args['circlebordercolor'];
		}

		if ( self::$child_args['outercirclebordercolor'] ) {
			$border_color = self::$child_args['outercirclebordercolor'];
		}

		if ( ! self::$child_args['circlebordercolor'] && ! self::$child_args['outercirclebordercolor'] ) {
			$border_color = '#f6f6f6';
		}

		if ( intval( $this->column_counter ) % intval( $this->num_of_columns ) == 1 ) {
			$attr['class'] .= ' content-box-column-first-in-row';
		}

		if ( intval( $this->column_counter ) == intval( $this->total_num_of_columns ) ) {
			$attr['class'] .= ' content-box-column-last';
		}

		if ( intval( $this->num_of_columns ) == $this->row_counter ) {
			$attr['class'] .= ' content-box-column-last-in-row';
		}

		if ( $border_color && in_array( self::$parent_args['layout'], array( 'clean-vertical', 'clean-horizontal' ), true ) ) {
			$attr['style'] .= 'border-color:' . $border_color . ';';
		}

		if ( self::$child_args['class'] ) {
			$attr['class'] .= ' ' . self::$child_args['class'];
		}

		if ( self::$child_args['id'] ) {
			$attr['id'] = self::$child_args['id'];
		}

		$this->row_counter++;

		return $attr;

	}

	/**
	 * Builds the attributes array.
	 *
	 * @access public
	 * @since 1.0
	 * @return array
	 */
	public function content_wrapper_attr() {

		$attr = array(
			'class' => 'col content-wrapper',
		);

		// Set parent values if child values are unset to get downwards compatibility.
		if ( ! self::$child_args['backgroundcolor'] ) {
			self::$child_args['backgroundcolor'] = self::$parent_args['backgroundcolor'];
		}

		if ( self::$child_args['backgroundcolor'] ) {
			$attr['style'] = 'background-color:' . self::$child_args['backgroundcolor'] . ';';

			if ( 'transparent' !== self::$child_args['backgroundcolor'] && '0' != Avada_Color::new_color( self::$child_args['backgroundcolor'] )->alpha ) {
				$attr['class'] .= '-background';
			}
		}

		if ( 'icon-boxed' === self::$parent_args['layout'] ) {
			$attr['class'] .= ' content-wrapper-boxed';
		}

		if ( self::$child_args['link'] && 'box' === self::$parent_args['link_area'] ) {
			$attr['data-link'] = self::$child_args['link'];

			$attr['data-link-target'] = self::$child_args['link_target'];
		}

		$attr['class'] .= ' link-area-' . self::$parent_args['link_area'];

		if ( self::$child_args['link'] && self::$parent_args['link_type'] ) {
			$attr['class'] .= ' link-type-' . self::$parent_args['link_type'];
		}

		if (  self::$child_args['outercirclebordercolor'] && self::$child_args['outercirclebordersize'] && intval( self::$child_args['outercirclebordersize'] ) ) {
			$attr['class'] .= ' content-icon-wrapper-yes';
		}
		if ( self::$child_args['outercirclebordercolor'] && self::$child_args['outercirclebordersize'] && intval( self::$child_args['outercirclebordersize'] ) && 'pulsate' === self::$parent_args['icon_hover_type'] ) {
			$attr['class'] .= ' icon-wrapper-hover-animation-' . self::$parent_args['icon_hover_type'];
		} else {
			$attr['class'] .= ' icon-hover-animation-' . self::$parent_args['icon_hover_type'];
		}

		if ( self::$child_args['textcolor'] ) {
			$attr['style'] .= 'color:' . self::$child_args['textcolor'] . ';';
		}

		if ( self::$child_args['animation_type'] ) {
			$animations = FusionBuilder::animations( array(
				'type'      => self::$child_args['animation_type'],
				'direction' => self::$child_args['animation_direction'],
				'speed'     => self::$child_args['animation_speed'],
				'offset'    => self::$child_args['animation_offset'],
			) );

			$attr = array_merge( $attr, $animations );

			$attr['class'] .= ' ' . $attr['animation_class'];
			unset( $attr['animation_class'] );
		}

		return $attr;
	}


	/**
	 * Builds the attributes array.
	 *
	 * @access public
	 * @since 1.0
	 * @param array $args The arguments array.
	 * @return array
	 */
	public function link_attr( $args ) {

		$attr = array(
			'class' => '',
		);

		if ( self::$child_args['link'] ) {
			$attr['href'] = self::$child_args['link'];
		}

		if ( self::$child_args['link_target'] ) {
			$attr['target'] = self::$child_args['link_target'];
		}
		if ( '_blank' === self::$child_args['link_target'] ) {
			$attr['rel'] = 'noopener noreferrer';
		}
		if ( 'button' === self::$parent_args['link_type'] ) {
			$attr['class'] .= 'fusion-read-more-button fusion-button fusion-button-default fusion-button-' . strtolower( FusionBuilder::get_theme_option( 'button_size' ) ) . ' fusion-button-' . strtolower( FusionBuilder::get_theme_option( 'button_shape' ) ) . ' fusion-button-' . strtolower( FusionBuilder::get_theme_option( 'button_type' ) );
		}

		if ( 'button-bar' === self::$parent_args['link_type'] && 'timeline-vertical' === self::$parent_args['layout'] && isset( $args['readmore'] ) ) {
			$attr['style'] = '';

			$addition_margin = 20 + 15;
			if ( self::$child_args['backgroundcolor'] && 'transparent' !== self::$child_args['backgroundcolor'] && '0' != Avada_Color::new_color( self::$child_args['backgroundcolor'] )->alpha ) {
				$addition_margin += 35;
			}

			if ( self::$child_args['image'] && self::$child_args['image_width'] && self::$child_args['image_height'] ) {
				$full_icon_size = self::$child_args['image_width'];
			} elseif ( self::$child_args['icon'] ) {
				if ( 'yes' === self::$parent_args['icon_circle'] ) {
					$full_icon_size = ( self::$parent_args['icon_size'] + intval( self::$child_args['circlebordersize'] ) + intval( self::$child_args['outercirclebordersize'] ) ) * 2;
				} else {
					$full_icon_size = self::$parent_args['icon_size'];
				}
			}

			if ( 'right' === self::$parent_args['icon_align'] ) {
				$attr['style'] .= 'margin-right:' . ( $full_icon_size + $addition_margin ) . 'px;';
			} else {
				$attr['style'] .= 'margin-left:' . ( $full_icon_size + $addition_margin ) . 'px;';
			}

			$attr['style'] .= 'width:calc(100% - ' . ( $full_icon_size + $addition_margin + 15 ) . 'px);';
		}

		return $attr;

	}

	/**
	 * Builds the attributes array.
	 *
	 * @access public
	 * @since 1.0
	 * @return array
	 */
	public function heading_wrapper_attr() {

		$attr = array(
			'class' => 'heading',
			'style' => '',
		);

		if ( self::$child_args['icon'] || self::$child_args['image'] ) {
			$attr['class'] .= ' heading-with-icon';
		}

		if ( self::$parent_args['icon_align'] ) {
			$attr['class'] .= ' icon-' . self::$parent_args['icon_align'];
		}

		return $attr;

	}

	/**
	 * Builds the attributes array.
	 *
	 * @access public
	 * @since 1.0
	 * @return array
	 */
	public function icon_parent_attr() {
		$attr = array(
			'class' => 'icon',
			'style' => '',
		);

		if (  'yes' !== self::$parent_args['icon_circle'] && 'icon-boxed' === self::$parent_args['layout'] ) {
			$attr['style'] .= 'position:absolute;width: 100%;top:-' . ( 50 + ( intval( self::$parent_args['icon_size'] ) / 2 ) ) . 'px;';
		}

		if ( 'timeline-vertical' === self::$parent_args['layout'] && 'right' === self::$parent_args['icon_align'] && ( ! self::$child_args['outercirclebordercolor'] || ! self::$child_args['circlebordersize'] ) ) {
			$attr['style'] .= 'padding-left:20px;';
		}

		if ( self::$parent_args['animation_delay'] ) {
			$animation_delay = self::$parent_args['animation_delay'];
			$attr['style'] .= '-webkit-animation-duration: ' . $animation_delay . 'ms;';
			$attr['style'] .= 'animation-duration: ' . $animation_delay . 'ms;';
		}

		return $attr;
	}

	/**
	 * Builds the attributes array.
	 *
	 * @access public
	 * @since 1.0
	 * @return array
	 */
	public function icon_wrapper_attr() {

		$attr = array(
			'style' => '',
		);

		if ( self::$child_args['icon'] ) {

			$attr['class'] = '';

			if ( 'yes' === self::$parent_args['icon_circle'] ) {
				$attr['style'] .= 'height:' . ( ( self::$parent_args['icon_size'] + intval( self::$child_args['circlebordersize'] ) ) * 2 ) . 'px;width:' . ( ( self::$parent_args['icon_size'] + intval( self::$child_args['circlebordersize'] ) ) * 2 ) . 'px;line-height:' . ( self::$parent_args['icon_size'] + intval( self::$child_args['circlebordersize'] ) * 2 ) . 'px;';

				if ( self::$child_args['outercirclebordercolor'] ) {
					$attr['style'] .= 'border-color:' . self::$child_args['outercirclebordercolor'] . ';';
				}

				if ( self::$child_args['outercirclebordersize'] && intval( self::$child_args['outercirclebordersize'] ) ) {
					$attr['style'] .= 'border-width:' . self::$child_args['outercirclebordersize'] . ';';
				}

				$attr['style'] .= 'border-style:solid;';

				if ( self::$child_args['circlebordercolor'] ) {
					$attr['style'] .= 'background-color:' . self::$child_args['circlebordercolor'] . ';';
				}

				if ( 'icon-boxed' === self::$parent_args['layout'] ) {
					$attr['style'] .= 'position:absolute;top:-' . ( 50 + self::$parent_args['icon_size'] + intval( self::$child_args['circlebordersize'] ) ) . 'px;margin-left:-' . ( self::$parent_args['icon_size'] + intval( self::$child_args['circlebordersize'] ) ) . 'px;';
				}

				if ( 'round' === self::$parent_args['icon_circle_radius'] ) {
					self::$parent_args['icon_circle_radius'] = '100%';
				}

				if ( in_array( self::$parent_args['layout'], array( 'icon-on-side', 'timeline-vertical', 'clean-horizontal' ), true ) ) {
					$margin_direction = 'margin-right';
					if ( 'right' === self::$parent_args['icon_align'] ) {
						$margin_direction = 'margin-left';
					}

					$margin = '20px';
					if ( 'timeline-vertical' === self::$parent_args['layout'] && 'right' === self::$parent_args['icon_align'] ) {
						$margin = '10px';
					}

					$attr['style'] .= $margin_direction . ':' . $margin . ';';
				}

				$attr['style'] .= 'box-sizing:content-box;';
				$attr['style'] .= 'border-radius:' . self::$parent_args['icon_circle_radius'] . ';';
			}
		}

		return $attr;

	}

	/**
	 * Builds the attributes array.
	 *
	 * @access public
	 * @since 1.0
	 * @return array
	 */
	public function icon_attr() {

		$attr = array(
			'style' => '',
		);

		if ( self::$child_args['image'] ) {
			$attr['class'] = 'image';

			if ( 'icon-boxed' === self::$parent_args['layout'] && self::$child_args['image_width'] && self::$child_args['image_height'] ) {
				$attr['style']  = 'margin-left:-' . ( self::$child_args['image_width'] / 2 ) . 'px;';
				$attr['style'] .= 'top:-' . ( self::$child_args['image_height'] / 2 + 50 ) . 'px;';
			}
		} elseif ( self::$child_args['icon'] ) {

			$attr['class'] = 'fa fontawesome-icon ' . FusionBuilder::font_awesome_name_handler( self::$child_args['icon'] );

			// Set parent values if child values are unset to get downwards compatibility.
			if ( ! self::$child_args['circle'] ) {
				self::$child_args['circle'] = self::$parent_args['circle'];
			}

			if ( 'yes' === self::$parent_args['icon_circle'] ) {

				$attr['class'] .= ' circle-yes';

				if ( self::$child_args['circlebordercolor'] ) {
					$attr['style'] .= 'border-color:' . self::$child_args['circlebordercolor'] . ';';
				}

				self::$child_args['circlebordersize'] = FusionBuilder::validate_shortcode_attr_value( self::$child_args['circlebordersize'], 'px' );

				if ( self::$child_args['circlebordersize'] ) {
					$attr['style'] .= 'border-width:' . self::$child_args['circlebordersize'] . ';';
				}

				if ( self::$child_args['circlecolor'] ) {
					$attr['style'] .= 'background-color:' . self::$child_args['circlecolor'] . ';';
				}

				$attr['style'] .= 'height:' . ( self::$parent_args['icon_size'] * 2 ) . 'px;width:' . ( self::$parent_args['icon_size'] * 2 ) . 'px;line-height:' . ( self::$parent_args['icon_size'] * 2 ) . 'px;';

				if ( 'icon-boxed' === self::$parent_args['layout'] && ( ! self::$child_args['outercirclebordercolor'] || ! self::$child_args['outercirclebordersize'] || ! intval( self::$child_args['outercirclebordersize'] ) ) ) {
					$attr['style'] .= 'top:-' . ( 50 + self::$parent_args['icon_size'] ) . 'px;margin-left:-' . intval( self::$parent_args['icon_size'] ) . 'px;';
				}

				if ( 'round' === self::$parent_args['icon_circle_radius'] ) {
					self::$parent_args['icon_circle_radius'] = '100%';
				}

				$attr['style'] .= 'border-radius:' . self::$parent_args['icon_circle_radius'] . ';';

				if ( self::$child_args['outercirclebordercolor'] && self::$child_args['outercirclebordersize'] && intval( self::$child_args['outercirclebordersize'] ) ) {
					// If there is a thick border, kill border width and make it center aligned positioned.
					$attr['style'] .= 'border-width:0;';
					$attr['style'] .= 'position:relative;';
					$attr['style'] .= 'top:' . self::$child_args['circlebordersize'] . ';';
					$attr['style'] .= 'left:' . self::$child_args['circlebordersize'] . ';';
					$attr['style'] .= 'margin:0;';
					$attr['style'] .= 'border-radius:' . self::$parent_args['icon_circle_radius'] . ';';
				}
			} else {

				$attr['class'] .= ' circle-no';

				$attr['style'] .= 'background-color:transparent;border-color:transparent;height:auto;width: ' . FusionBuilder::get_value_with_unit( self::$parent_args['icon_size'] ) . ';line-height:normal;';

				if ( 'icon-boxed' === self::$parent_args['layout'] ) {
					$attr['style'] .= 'position:relative;left:auto;right:auto;top:auto;margin-left:auto;margin-right:auto;';
				}
			}

			if ( self::$child_args['iconcolor'] ) {
				$attr['style'] .= 'color:' . self::$child_args['iconcolor'] . ';';
			}

			if ( self::$child_args['iconrotate'] ) {
				$attr['class'] .= ' fa-rotate-' . self::$child_args['iconrotate'];
			}

			if ( 'yes' === self::$child_args['iconspin'] ) {
				$attr['class'] .= ' fa-spin';
			}

			$attr['style'] .= 'font-size:' . self::$parent_args['icon_size'] . ';';
		}

		return $attr;

	}

	/**
	 * Builds the attributes array.
	 *
	 * @access public
	 * @since 1.0
	 * @return array
	 */
	public function content_container_attr() {
		$attr = array(
			'class' => 'content-container',
			'style' => '',
		);

		if ( in_array( self::$parent_args['layout'], array( 'icon-on-side', 'timeline-vertical', 'clean-horizontal' ), true ) && self::$child_args['image'] && self::$child_args['image_width'] && self::$child_args['image_height'] ) {
			if ( 'clean-horizontal' === self::$parent_args['layout'] ) {
				$attr['style'] .= 'padding-left:' . ( self::$child_args['image_width'] + 20 ) . 'px;';
			} else {
				if ( 'right' === self::$parent_args['icon_align'] ) {
					$attr['style'] .= 'padding-right:' . ( self::$child_args['image_width'] + 20 ) . 'px;';
				} else {
					$attr['style'] .= 'padding-left:' . ( self::$child_args['image_width'] + 20 ) . 'px;';
				}
			}

			if ( 'timeline-vertical' === self::$parent_args['layout'] ) {
				$image_height = self::$child_args['image_height'];
				if ( $image_height > self::$parent_args['title_size'] && $image_height - self::$parent_args['title_size'] - 15 > 0 ) {
					$attr['style'] .= 'margin-top:-' . ( $image_height - self::$parent_args['title_size'] ) . 'px;';
				}
			}
		} elseif ( in_array( self::$parent_args['layout'], array( 'icon-on-side', 'timeline-vertical', 'clean-horizontal' ), true ) && self::$child_args['icon'] ) {
			if ( 'yes' === self::$parent_args['icon_circle'] ) {
				$full_icon_size = ( self::$parent_args['icon_size'] + intval( self::$child_args['circlebordersize'] ) + intval( self::$child_args['outercirclebordersize'] ) ) * 2;
			} else {
				$full_icon_size = self::$parent_args['icon_size'];
			}

			if ( 'clean-horizontal' === self::$parent_args['layout'] ) {
				$attr['style'] .= 'padding-left:' . ( $full_icon_size + 20 ) . 'px;';
			} else {
				if ( 'right' === self::$parent_args['icon_align'] ) {
					$attr['style'] .= 'padding-right:' . ( $full_icon_size + 20 ) . 'px;';
				} else {
					$attr['style'] .= 'padding-left:' . ( $full_icon_size + 20 ) . 'px;';
				}
			}

			if ( 'timeline-vertical' === self::$parent_args['layout'] ) {
				if ( $full_icon_size > self::$parent_args['title_size'] && $full_icon_size - self::$parent_args['title_size'] - 15 > 0 ) {
					if ( 'timeline-vertical' === self::$parent_args['layout'] ) {
						$attr['style'] .= 'margin-top:-' . ( ( $full_icon_size - self::$parent_args['title_size'] ) / 2 ) . 'px;';
					} else {
						$attr['style'] .= 'margin-top:-' . ( $full_icon_size - self::$parent_args['title_size'] ) . 'px;';
					}
				}
			}
		}

		if ( 'right' === self::$parent_args['icon_align'] && isset( $attr['style'] ) && ( in_array( self::$parent_args['layout'], array( 'icon-on-side', 'icon-with-title', 'timeline-vertical', 'clean-horizontal' ), true ) ) ) {
			$attr['style'] .= ' text-align:' . self::$parent_args['icon_align'] . ';';
		} elseif ( 'right' === self::$parent_args['icon_align'] && ! isset( $attr['style'] ) && ( in_array( self::$parent_args['layout'], array( 'icon-on-side', 'icon-with-title', 'timeline-vertical', 'clean-horizontal' ), true ) ) ) {
			$attr['style'] .= ' text-align:' . self::$parent_args['icon_align'] . ';';
		}

		if ( self::$parent_args['body_color'] ) {
			$attr['style'] .= 'color:' . self::$parent_args['body_color'] . ';';
		}

		return $attr;

	}

	/**
	 * Builds the attributes array.
	 *
	 * @access public
	 * @since 1.0
	 * @return array
	 */
	public function timeline_attr() {
		$attr = array();
		if ( 'timeline-horizontal' === self::$parent_args['layout'] ) {
			$attr['class'] = 'content-box-shortcode-timeline';
			$attr['style'] = '';

			$border_color = '';
			if ( 'yes' === self::$parent_args['icon_circle'] ) {
				if ( intval( self::$child_args['outercirclebordersize'] ) ) {
					$full_icon_size = ( intval( self::$parent_args['icon_size'] ) + intval( self::$child_args['circlebordersize'] ) + intval( self::$child_args['outercirclebordersize'] ) ) * 2;
				} else {
					$full_icon_size = intval( self::$parent_args['icon_size'] ) * 2;
				}
			} else {
				$full_icon_size = intval( self::$parent_args['icon_size'] );
			}

			$position_top = $full_icon_size / 2;

			if ( self::$child_args['backgroundcolor'] && 'transparent' !== self::$child_args['backgroundcolor'] && '0' != Avada_Color::new_color( self::$child_args['backgroundcolor'] )->alpha ) {
				$position_top += 35;
			}

			if ( self::$child_args['circlebordercolor'] ) {
				$border_color = self::$child_args['circlebordercolor'];
			}

			if ( self::$child_args['outercirclebordercolor'] && self::$child_args['outercirclebordersize'] ) {
				$border_color = self::$child_args['outercirclebordercolor'];
			}

			if ( ! self::$child_args['circlebordercolor'] && ! self::$child_args['outercirclebordercolor'] ) {
				$border_color = '#f6f6f6';
			}

			if ( $border_color ) {
				$attr['style'] .= 'border-color:' . $border_color . ';';
			}

			if ( $position_top ) {
				$attr['style'] .= 'top:' . intval( $position_top ) . 'px;';
			}
		} elseif ( 'timeline-vertical' === self::$parent_args['layout'] ) {
			$attr['class'] = 'content-box-shortcode-timeline-vertical';
			$attr['style'] = '';

			$border_color = '';

			if ( 'yes' === self::$parent_args['icon_circle'] ) {
				if ( intval( self::$child_args['outercirclebordersize'] ) ) {
					$full_icon_size = ( intval( self::$parent_args['icon_size'] ) + intval( self::$child_args['circlebordersize'] ) + intval( self::$child_args['outercirclebordersize'] ) ) * 2;
				} else {
					$full_icon_size = intval( self::$parent_args['icon_size'] ) * 2;
				}
			} else {
				$full_icon_size = intval( self::$parent_args['icon_size'] );
			}

			$position_top        = $full_icon_size;
			$position_horizontal = $full_icon_size / 2 + 15;
			if ( self::$child_args['backgroundcolor'] && 'transparent' !== self::$child_args['backgroundcolor'] && '0' != Avada_Color::new_color( self::$child_args['backgroundcolor'] )->alpha ) {
				$position_top        += 35;
				$position_horizontal += 35;
			}

			if ( self::$child_args['circlebordercolor'] ) {
				$border_color = self::$child_args['circlebordercolor'];
			}

			if ( self::$child_args['outercirclebordercolor'] && self::$child_args['outercirclebordersize'] ) {
				$border_color = self::$child_args['outercirclebordercolor'];

			}

			if ( ! self::$child_args['circlebordercolor'] && ! self::$child_args['outercirclebordercolor'] ) {
				$border_color = '#f6f6f6';
			}

			if ( $border_color ) {
				$attr['style'] .= 'border-color:' . $border_color . ';';
			}

			if ( $position_horizontal ) {
				if ( 'right' === self::$parent_args['icon_align'] ) {
					$attr['style'] .= 'right:' . intval( $position_horizontal ) . 'px;';
				} else {
					$attr['style'] .= 'left:' . intval( $position_horizontal ) . 'px;';
				}
			}

			if ( $position_top ) {
				$attr['style'] .= 'top:' . $position_top . 'px;';
			}
		}

		if ( self::$parent_args['animation_delay'] ) {
			$animation_delay = self::$parent_args['animation_delay'];
			$attr['style'] .= '-webkit-transition-duration: ' . $animation_delay . 'ms;';
			$attr['style'] .= 'animation-duration: ' . $animation_delay . 'ms;';
		}

		return $attr;
	}

	/**
	 * Builds the attributes array.
	 *
	 * @access public
	 * @since 1.0
	 * @return array
	 */
	public function content_box_heading_attr() {
		$attr = array(
			'class' => 'content-box-heading',
			'style' => '',
		);

		if ( self::$parent_args['title_size'] ) {
			$font_size = FusionBuilder::strip_unit( self::$parent_args['title_size'] );

			$attr['style'] = 'font-size:' . $font_size . 'px;line-height:' . ( $font_size + 5 ) . 'px;';
		}

		if ( 'icon-on-side' === self::$parent_args['layout'] || 'clean-horizontal' === self::$parent_args['layout'] ) {

			if ( self::$child_args['image'] && self::$child_args['image_width'] && self::$child_args['image_height'] ) {

				if ( 'right' === self::$parent_args['icon_align'] ) {
					$attr['style'] .= 'padding-right:' . ( self::$child_args['image_width'] + 20 ) . 'px;';
				} else {
					$attr['style'] .= 'padding-left:' . ( self::$child_args['image_width'] + 20 ) . 'px;';
				}
			} elseif ( self::$child_args['icon'] ) {
				if ( 'yes' === self::$parent_args['icon_circle'] ) {
					$full_icon_size = ( self::$parent_args['icon_size'] + intval( self::$child_args['circlebordersize'] ) + intval( self::$child_args['outercirclebordersize'] ) ) * 2;
				} else {
					$full_icon_size = self::$parent_args['icon_size'];
				}

				if ( 'right' === self::$parent_args['icon_align'] ) {
					$attr['style'] .= 'padding-right:' . ( $full_icon_size + 20 ) . 'px;';
				} else {
					$attr['style'] .= 'padding-left:' . ( $full_icon_size + 20 ) . 'px;';
				}
			}
		}

		return $attr;
	}
}
new FusionSC_ContentBoxes();

/**
 * Map shortcode to Fusion Builder.
 *
 * @since 1.0
 */
function fusion_element_content_boxes() {
	fusion_builder_map( array(
		'name'          => esc_attr__( 'Content Boxes', 'fusion-builder' ),
		'shortcode'     => 'fusion_content_boxes',
		'multi'         => 'multi_element_parent',
		'element_child' => 'fusion_content_box',
		'icon'          => 'fusiona-newspaper',
		'preview'       => FUSION_BUILDER_PLUGIN_DIR . 'js/previews/fusion-content-boxes-preview.php',
		'preview_id'    => 'fusion-builder-block-module-content-boxes-preview-template',
		'params'        => array(
			array(
				'type'        => 'tinymce',
				'heading'     => esc_attr__( 'Content', 'fusion-builder' ),
				'description' => esc_attr__( 'Enter some content for this contentbox', 'fusion-builder' ),
				'param_name'  => 'element_content',
				'value'       => '[fusion_content_box title="Your Content Goes Here" backgroundcolor="" icon="" iconflip="" iconrotate="" iconspin="no" iconcolor="" circlecolor="" circlebordercolor="" image="" image_width="35" image_height="35" link="" linktext="Read More" linktarget="default" animation_type="" animation_direction="left" animation_speed="0.3" ]Your Content Goes Here[/fusion_content_box]',
			),
			array(
				'type'        => 'select',
				'heading'     => esc_attr__( 'Box Layout', 'fusion-builder' ),
				'description' => esc_attr__( 'Select the layout for the content box', 'fusion-builder' ),
				'param_name'  => 'layout',
				'default'     => 'icon-with-title',
				'value'       => array(
					esc_attr__( 'Classic Icon With Title', 'fusion-builder' ) => 'icon-with-title',
					esc_attr__( 'Classic Icon On Top', 'fusion-builder' )     => 'icon-on-top',
					esc_attr__( 'Classic Icon On Side', 'fusion-builder' )    => 'icon-on-side',
					esc_attr__( 'Classic Icon Boxed', 'fusion-builder' )      => 'icon-boxed',
					esc_attr__( 'Clean Layout Vertical', 'fusion-builder' )   => 'clean-vertical',
					esc_attr__( 'Clean Layout Horizontal', 'fusion-builder' ) => 'clean-horizontal',
					esc_attr__( 'Timeline Vertical', 'fusion-builder' )       => 'timeline-vertical',
					esc_attr__( 'Timeline Horizontal', 'fusion-builder' )     => 'timeline-horizontal',
				),
			),
			array(
				'type'        => 'range',
				'heading'     => esc_attr__( 'Number of Columns', 'fusion-builder' ),
				'description' => esc_attr__( 'Set the number of columns per row.', 'fusion-builder' ),
				'param_name'  => 'columns',
				'value'       => '1',
				'min'         => '1',
				'max'         => '6',
				'step'        => '1',
			),
			array(
				'type'        => 'textfield',
				'heading'     => esc_attr__( 'Title Size', 'fusion-builder' ),
				'description' => esc_attr__( 'Controls the size of the title.  In pixels ex: 18px.', 'fusion-builder' ),
				'param_name'  => 'title_size',
				'value'       => '',
			),
			array(
				'type'        => 'colorpicker',
				'heading'     => esc_attr__( 'Title Font Color', 'fusion-builder' ),
				'description' => esc_attr__( 'Controls the color of the title font.  ex: #000.', 'fusion-builder' ),
				'param_name'  => 'title_color',
				'value'       => '',
			),
			array(
				'type'        => 'colorpicker',
				'heading'     => esc_attr__( 'Body Font Color', 'fusion-builder' ),
				'description' => esc_attr__( 'Controls the color of the body font.  ex: #000.', 'fusion-builder' ),
				'param_name'  => 'body_color',
				'value'       => '',
			),
			array(
				'type'        => 'colorpickeralpha',
				'heading'     => esc_attr__( 'Content Box Background Color', 'fusion-builder' ),
				'description' => '',
				'param_name'  => 'backgroundcolor',
				'value'       => '',
			),
			array(
				'type'        => 'colorpicker',
				'heading'     => esc_attr__( 'Icon Color', 'fusion-builder' ),
				'description' => '',
				'param_name'  => 'iconcolor',
				'value'       => '',
			),
			array(
				'type'             => 'radio_button_set',
				'heading'          => esc_attr__( 'Icon Background', 'fusion-builder' ),
				'description'      => esc_attr__( 'Choose to show a background behind the icon. Select default for theme option selection.', 'fusion-builder' ),
				'param_name'       => 'icon_circle',
				'value'            => array(
					esc_attr__( 'Default', 'fusion-builder' ) => '',
					esc_attr__( 'Yes', 'fusion-builder' )     => 'yes',
					esc_attr__( 'No', 'fusion-builder' )      => 'no',
				),
				'child_dependency' => true,
				'default'          => '',
			),
			array(
				'type'        => 'textfield',
				'heading'     => esc_attr__( 'Icon Background Radius', 'fusion-builder' ),
				'description' => esc_attr__( 'Choose the border radius of the icon background.  In pixels (px), ex: 1px, or "round".', 'fusion-builder' ),
				'param_name'  => 'icon_circle_radius',
				'value'       => '',
				'dependency'  => array(
					array(
						'element'  => 'icon_circle',
						'value'    => 'no',
						'operator' => '!=',
					),
				),
			),
			array(
				'type'        => 'colorpickeralpha',
				'heading'     => esc_attr__( 'Icon Background Color', 'fusion-builder' ),
				'description' => '',
				'param_name'  => 'circlecolor',
				'value'       => '',
				'dependency'  => array(
					array(
						'element'  => 'icon_circle',
						'value'    => 'no',
						'operator' => '!=',
					),
				),
			),
			array(
				'type'        => 'range',
				'heading'     => esc_attr__( 'Icon Background Inner Border Size', 'fusion-builder' ),
				'description' => '',
				'param_name'  => 'circlebordersize',
				'value'       => '',
				'min'         => '0',
				'max'         => '20',
				'step'        => '1',
				'default'     => '',
				'dependency'  => array(
					array(
						'element'  => 'icon_circle',
						'value'    => 'no',
						'operator' => '!=',
					),
				),
			),
			array(
				'type'        => 'colorpickeralpha',
				'heading'     => esc_attr__( 'Icon Background Inner Border Color', 'fusion-builder' ),
				'description' => '',
				'param_name'  => 'circlebordercolor',
				'value'       => '',
				'dependency'  => array(
					array(
						'element'  => 'icon_circle',
						'value'    => 'no',
						'operator' => '!=',
					),
					array(
						'element'  => 'circlebordersize',
						'value'    => '0',
						'operator' => '!=',
					),
				),
			),
			array(
				'type'        => 'range',
				'heading'     => esc_attr__( 'Icon Background Outer Border Size', 'fusion-builder' ),
				'description' => '',
				'param_name'  => 'outercirclebordersize',
				'value'       => '',
				'min'         => '0',
				'max'         => '20',
				'step'        => '1',
				'default'     => '',
				'dependency'  => array(
					array(
						'element'  => 'icon_circle',
						'value'    => 'no',
						'operator' => '!=',
					),
				),
			),
			array(
				'type'        => 'colorpickeralpha',
				'heading'     => esc_attr__( 'Icon Background Outer Border Color', 'fusion-builder' ),
				'description' => '',
				'param_name'  => 'outercirclebordercolor',
				'value'       => '',
				'dependency'  => array(
					array(
						'element'  => 'icon_circle',
						'value'    => 'no',
						'operator' => '!=',
					),
					array(
						'element'  => 'outercirclebordersize',
						'value'    => '0',
						'operator' => '!=',
					),
				),
			),
			array(
				'type'        => 'range',
				'heading'     => esc_attr__( 'Icon Size', 'fusion-builder' ),
				'description' => esc_attr__( 'Controls the size of the icon. In pixels.', 'fusion-builder' ),
				'param_name'  => 'icon_size',
				'value'       => '',
				'min'         => '0',
				'max'         => '250',
				'step'        => '1',
			),
			array(
				'type'        => 'select',
				'heading'     => esc_attr__( 'Icon Hover Animation Type', 'fusion-builder' ),
				'description' => esc_attr__( 'Select the animation type for icon on hover. Select default for theme option selection.', 'fusion-builder' ),
				'param_name'  => 'icon_hover_type',
				'value'       => array(
					esc_attr__( 'Default', 'fusion-builder' ) => '',
					esc_attr__( 'None', 'fusion-builder' )    => 'none',
					esc_attr__( 'Fade', 'fusion-builder' )    => 'fade',
					esc_attr__( 'Slide', 'fusion-builder' )   => 'slide',
					esc_attr__( 'Pulsate', 'fusion-builder' ) => 'pulsate',
				),
				'default'     => '',
			),
			array(
				'type'        => 'colorpickeralpha',
				'heading'     => esc_attr__( 'Hover Animation Color', 'fusion-builder' ),
				'description' => esc_attr__( 'Select an accent color for the hover animation. ', 'fusion-builder' ),
				'param_name'  => 'hover_accent_color',
				'value'       => '',
				'dependency'  => array(
					array(
						'element'  => 'icon_hover_type',
						'value'    => 'none',
						'operator' => '!=',
					),
				),
			),
			array(
				'type'        => 'radio_button_set',
				'heading'     => esc_attr__( 'Link Type', 'fusion-builder' ),
				'description' => esc_attr__( 'Select the type of link that should show in the content box. Select default for theme option selection.', 'fusion-builder' ),
				'param_name'  => 'link_type',
				'value'       => array(
					esc_attr__( 'Default', 'fusion-builder' )    => '',
					esc_attr__( 'Text', 'fusion-builder' )       => 'text',
					esc_attr__( 'Button Bar', 'fusion-builder' ) => 'button-bar',
					esc_attr__( 'Button', 'fusion-builder' )     => 'button',
				),
				'default'     => '',
			),
			array(
				'type'        => 'select',
				'heading'     => esc_attr__( 'Link Area', 'fusion-builder' ),
				'description' => esc_attr__( 'Select which area the link will be assigned to. Select default for theme option selection.', 'fusion-builder' ),
				'param_name'  => 'link_area',
				'value'       => array(
					esc_attr__( 'Default', 'fusion-builder' )            => '',
					esc_attr__( 'Link+Icon', 'fusion-builder' )          => 'link-icon',
					esc_attr__( 'Entire Content Box', 'fusion-builder' ) => 'box',
				),
				'default'     => '',
			),
			array(
				'type'        => 'radio_button_set',
				'heading'     => esc_attr__( 'Link Target', 'fusion-builder' ),
				'description' => __( '_self = open in same window <br />_blank = open in new window', 'fusion-builder' ),
				'param_name'  => 'link_target',
				'value'       => array(
					esc_attr__( 'Default', 'fusion-builder' ) => '',
					esc_attr__( 'Same Window', 'fusion-builder' )   => '_self',
					esc_attr__( 'New Window/Tab', 'fusion-builder' )  => '_blank',
				),
				'default'     => '',
			),
			array(
				'type'        => 'radio_button_set',
				'heading'     => esc_attr__( 'Content Alignment', 'fusion-builder' ),
				'description' => esc_attr__( 'Works with "Classic Icon With Title" and "Classic Icon On Side" layout options.', 'fusion-builder' ),
				'param_name'  => 'icon_align',
				'value'       => array(
					esc_attr__( 'Left', 'fusion-builder' )  => 'left',
					esc_attr__( 'Right', 'fusion-builder' ) => 'right',
				),
				'default'     => 'left',
			),
			array(
				'type'        => 'select',
				'heading'     => esc_attr__( 'Animation Type', 'fusion-builder' ),
				'description' => esc_attr__( 'Select the type of animation to use on the element.', 'fusion-builder' ),
				'param_name'  => 'animation_type',
				'value'       => fusion_builder_available_animations(),
				'default'     => '',
			),
			array(
				'type'        => 'textfield',
				'heading'     => esc_attr__( 'Animation Delay', 'fusion-builder' ),
				'description' => esc_attr__( 'Controls the delay of animation between each element in a set. In milliseconds, 1000 = 1 second.', 'fusion-builder' ),
				'param_name'  => 'animation_delay',
				'value'       => '',
				'dependency'  => array(
					array(
						'element'  => 'animation_type',
						'value'    => '',
						'operator' => '!=',
					),
				),
			),
			array(
				'type'        => 'select',
				'heading'     => esc_attr__( 'Offset of Animation', 'fusion-builder' ),
				'description' => esc_attr__( 'Controls when the animation should start.', 'fusion-builder' ),
				'param_name'  => 'animation_offset',
				'value'       => array(
					esc_attr__( 'Default', 'fusion-builder' )                                => '',
					esc_attr__( 'Top of element hits bottom of viewport', 'fusion-builder' ) => 'top-into-view',
					esc_attr__( 'Top of element hits middle of viewport', 'fusion-builder' ) => 'top-mid-of-view',
					esc_attr__( 'Bottom of element enters viewport', 'fusion-builder' )      => 'bottom-in-view',
				),
				'default'     => '',
				'dependency'  => array(
					array(
						'element'  => 'animation_type',
						'value'    => '',
						'operator' => '!=',
					),
				),
			),
			array(
				'type'        => 'radio_button_set',
				'heading'     => esc_attr__( 'Direction of Animation', 'fusion-builder' ),
				'description' => esc_attr__( 'Select the incoming direction for the animation.', 'fusion-builder' ),
				'param_name'  => 'animation_direction',
				'value'       => array(
					esc_attr__( 'Top', 'fusion-builder' )    => 'down',
					esc_attr__( 'Right', 'fusion-builder' )  => 'right',
					esc_attr__( 'Bottom', 'fusion-builder' ) => 'up',
					esc_attr__( 'Left', 'fusion-builder' )   => 'left',
					esc_attr__( 'Static', 'fusion-builder' ) => 'static',
				),
				'default'     => 'left',
				'dependency'  => array(
					array(
						'element'  => 'animation_type',
						'value'    => '',
						'operator' => '!=',
					),
				),
			),
			array(
				'type'        => 'select',
				'heading'     => esc_attr__( 'Speed of Animation', 'fusion-builder' ),
				'description' => esc_attr__( 'Type in speed of animation in seconds (0.1 - 1).', 'fusion-builder' ),
				'param_name'  => 'animation_speed',
				'value'       => array(
					'1'   => '1',
					'0.1' => '0.1',
					'0.2' => '0.2',
					'0.3' => '0.3',
					'0.4' => '0.4',
					'0.5' => '0.5',
					'0.6' => '0.6',
					'0.7' => '0.7',
					'0.8' => '0.8',
					'0.9' => '0.9',
				),
				'default'     => '0.3',
				'dependency'  => array(
					array(
						'element'  => 'animation_type',
						'value'    => '',
						'operator' => '!=',
					),
				),
			),
			array(
				'type'             => 'dimension',
				'remove_from_atts' => true,
				'heading'          => esc_attr__( 'Margin', 'fusion-builder' ),
				'description'      => esc_attr__( 'Spacing above and below the content boxes. In px, em or %, e.g. 10px.', 'fusion-builder' ),
				'param_name'       => 'dimensions',
				'value'            => array(
					'margin_top'    => '',
					'margin_bottom' => '',
				),
			),
			array(
				'type'        => 'checkbox_button_set',
				'heading'     => esc_attr__( 'Element Visibility', 'fusion-builder' ),
				'param_name'  => 'hide_on_mobile',
				'value'       => fusion_builder_visibility_options( 'full' ),
				'default'     => fusion_builder_default_visibility( 'array' ),
				'description' => esc_attr__( 'Choose to show or hide the element on small, medium or large screens. You can choose more than one at a time.', 'fusion-builder' ),
			),
			array(
				'type'        => 'textfield',
				'heading'     => esc_attr__( 'CSS Class', 'fusion-builder' ),
				'description' => esc_attr__( 'Add a class to the wrapping HTML element.', 'fusion-builder' ),
				'param_name'  => 'class',
				'value'       => '',
				'group'       => esc_attr__( 'General', 'fusion-builder' ),
			),
			array(
				'type'        => 'textfield',
				'heading'     => esc_attr__( 'CSS ID', 'fusion-builder' ),
				'description' => esc_attr__( 'Add an ID to the wrapping HTML element.', 'fusion-builder' ),
				'param_name'  => 'id',
				'value'       => '',
				'group'       => esc_attr__( 'General', 'fusion-builder' ),
			),
		),
	) );
}
add_action( 'fusion_builder_before_init', 'fusion_element_content_boxes' );

/**
 * Map shortcode to Fusion Builder.
 *
 * @since 1.0
 */
function fusion_element_content_box() {
	fusion_builder_map( array(
		'name'              => esc_attr__( 'Content Box', 'fusion-builder' ),
		'description'       => esc_attr__( 'Enter some content for this textblock', 'fusion-builder' ),
		'shortcode'         => 'fusion_content_box',
		'hide_from_builder' => true,
		'allow_generator'   => true,
		'params'            => array(
			array(
				'type'        => 'textfield',
				'heading'     => esc_attr__( 'Title', 'fusion-builder' ),
				'description' => esc_attr__( 'The box title.', 'fusion-builder' ),
				'param_name'  => 'title',
				'value'       => 'Your Content Goes Here',
				'placeholder' => true,
			),
			array(
				'type'        => 'colorpickeralpha',
				'heading'     => esc_attr__( 'Content Box Background Color', 'fusion-builder' ),
				'description' => '',
				'param_name'  => 'backgroundcolor',
				'value'       => '',
			),
			array(
				'type'        => 'iconpicker',
				'heading'     => esc_attr__( 'Icon', 'fusion-builder' ),
				'param_name'  => 'icon',
				'value'       => '',
				'description' => esc_attr__( 'Click an icon to select, click again to deselect.', 'fusion-builder' ),
				'dependency'  => array(
					array(
						'element'  => 'image',
						'value'    => '',
						'operator' => '==',
					),
				),
			),
			array(
				'type'        => 'radio_button_set',
				'heading'     => esc_attr__( 'Flip Icon', 'fusion-builder' ),
				'description' => esc_attr__( 'Choose to flip the icon.', 'fusion-builder' ),
				'param_name'  => 'iconflip',
				'value'       => array(
					esc_attr__( 'None', 'fusion-builder' )       => '',
					esc_attr__( 'Horizontal', 'fusion-builder' ) => 'horizontal',
					esc_attr__( 'Vertical', 'fusion-builder' )   => 'vertical',
				),
				'default'     => '',
				'dependency'  => array(
					array(
						'element'  => 'icon',
						'value'    => '',
						'operator' => '!=',
					),
					array(
						'element'  => 'image',
						'value'    => '',
						'operator' => '==',
					),
				),
			),
			array(
				'type'        => 'radio_button_set',
				'heading'     => esc_attr__( 'Rotate Icon', 'fusion-builder' ),
				'description' => esc_attr__( 'Choose to rotate the icon.', 'fusion-builder' ),
				'param_name'  => 'iconrotate',
				'value'       => array(
					esc_attr__( 'None', 'fusion-builder' ) => '',
					'90'                                   => '90',
					'180'                                  => '180',
					'270'                                  => '270',
				),
				'default'     => '',
				'dependency'  => array(
					array(
						'element'  => 'icon',
						'value'    => '',
						'operator' => '!=',
					),
					array(
						'element'  => 'image',
						'value'    => '',
						'operator' => '==',
					),
				),
			),
			array(
				'type'        => 'radio_button_set',
				'heading'     => esc_attr__( 'Spinning Icon', 'fusion-builder' ),
				'description' => esc_attr__( 'Choose to let the icon spin.', 'fusion-builder' ),
				'param_name'  => 'iconspin',
				'value'       => array(
					esc_attr__( 'Yes', 'fusion-builder' ) => 'yes',
					esc_attr__( 'No', 'fusion-builder' )  => 'no',
				),
				'default'     => 'no',
				'dependency'  => array(
					array(
						'element'  => 'icon',
						'value'    => '',
						'operator' => '!=',
					),
					array(
						'element'  => 'image',
						'value'    => '',
						'operator' => '==',
					),
				),
			),
			array(
				'type'        => 'colorpicker',
				'heading'     => esc_attr__( 'Icon Color', 'fusion-builder' ),
				'description' => esc_attr__( 'Controls the color of the icon. ', 'fusion-builder' ),
				'param_name'  => 'iconcolor',
				'value'       => '',
				'dependency'  => array(
					array(
						'element'  => 'icon',
						'value'    => '',
						'operator' => '!=',
					),
					array(
						'element'  => 'image',
						'value'    => '',
						'operator' => '==',
					),
				),
			),
			array(
				'type'        => 'colorpickeralpha',
				'heading'     => esc_attr__( 'Icon Background Color', 'fusion-builder' ),
				'description' => esc_attr__( 'Choose to show a background behind the icon.', 'fusion-builder' ),
				'param_name'  => 'circlecolor',
				'value'       => '',
				'dependency'  => array(
					array(
						'element'  => 'icon',
						'value'    => '',
						'operator' => '!=',
					),
					array(
						'element'  => 'image',
						'value'    => '',
						'operator' => '==',
					),
					array(
						'element'  => 'parent_icon_circle',
						'value'    => 'no',
						'operator' => '!=',
					),
				),
			),
			array(
				'type'        => 'range',
				'heading'     => esc_attr__( 'Icon Background Inner Border Size', 'fusion-builder' ),
				'description' => '',
				'param_name'  => 'circlebordersize',
				'value'       => '',
				'min'         => '0',
				'max'         => '20',
				'step'        => '1',
				'default'     => '',
				'dependency'  => array(
					array(
						'element'  => 'icon',
						'value'    => '',
						'operator' => '!=',
					),
					array(
						'element'  => 'image',
						'value'    => '',
						'operator' => '==',
					),
					array(
						'element'  => 'parent_icon_circle',
						'value'    => 'no',
						'operator' => '!=',
					),
				),
			),
			array(
				'type'        => 'colorpickeralpha',
				'heading'     => esc_attr__( 'Icon Background Inner Border Color', 'fusion-builder' ),
				'description' => '',
				'param_name'  => 'circlebordercolor',
				'value'       => '',
				'dependency'  => array(
					array(
						'element'  => 'icon',
						'value'    => '',
						'operator' => '!=',
					),
					array(
						'element'  => 'image',
						'value'    => '',
						'operator' => '==',
					),
					array(
						'element'  => 'slidercirclebordersize',
						'value'    => '0',
						'operator' => '!=',
					),
					array(
						'element'  => 'parent_icon_circle',
						'value'    => 'no',
						'operator' => '!=',
					),
				),
			),
			array(
				'type'        => 'range',
				'heading'     => esc_attr__( 'Icon Background Outer Border Size', 'fusion-builder' ),
				'description' => '',
				'param_name'  => 'outercirclebordersize',
				'value'       => '',
				'min'         => '0',
				'max'         => '20',
				'step'        => '1',
				'default'     => '',
				'dependency'  => array(
					array(
						'element'  => 'icon',
						'value'    => '',
						'operator' => '!=',
					),
					array(
						'element'  => 'image',
						'value'    => '',
						'operator' => '==',
					),
					array(
						'element'  => 'parent_icon_circle',
						'value'    => 'no',
						'operator' => '!=',
					),
				),
			),
			array(
				'type'        => 'colorpickeralpha',
				'heading'     => esc_attr__( 'Icon Background Outer Border Color', 'fusion-builder' ),
				'description' => '',
				'param_name'  => 'outercirclebordercolor',
				'value'       => '',
				'dependency'  => array(
					array(
						'element'  => 'icon',
						'value'    => '',
						'operator' => '!=',
					),
					array(
						'element'  => 'image',
						'value'    => '',
						'operator' => '==',
					),
					array(
						'element'  => 'slideroutercirclebordersize',
						'value'    => '0',
						'operator' => '!=',
					),
					array(
						'element'  => 'parent_icon_circle',
						'value'    => 'no',
						'operator' => '!=',
					),
				),
			),
			array(
				'type'        => 'upload',
				'heading'     => esc_attr__( 'Icon Image', 'fusion-builder' ),
				'description' => esc_attr__( 'To upload your own icon image, deselect the icon above and then upload your icon image.', 'fusion-builder' ),
				'param_name'  => 'image',
				'value'       => '',
			),
			array(
				'type'        => 'textfield',
				'heading'     => esc_attr__( 'Icon Image Width', 'fusion-builder' ),
				'description' => esc_attr__( 'If using an icon image, specify the image width in pixels but do not add px, ex: 35.', 'fusion-builder' ),
				'param_name'  => 'image_width',
				'value'       => '35',
				'dependency'  => array(
					array(
						'element'  => 'image',
						'value'    => '',
						'operator' => '!=',
					),
				),
			),
			array(
				'type'        => 'textfield',
				'heading'     => esc_attr__( 'Icon Image Height', 'fusion-builder' ),
				'description' => esc_attr__( 'If using an icon image, specify the image height in pixels but do not add px, ex: 35.', 'fusion-builder' ),
				'param_name'  => 'image_height',
				'value'       => '35',
				'dependency'  => array(
					array(
						'element'  => 'image',
						'value'    => '',
						'operator' => '!=',
					),
				),
			),
			array(
				'type'        => 'textfield',
				'heading'     => esc_attr__( 'Read More Link Url', 'fusion-builder' ),
				'description' => esc_attr__( "Add the link's url ex: http://example.com.", 'fusion-builder' ),
				'param_name'  => 'link',
				'value'       => '',
			),
			array(
				'type'        => 'textfield',
				'heading'     => esc_attr__( 'Read More Link Text', 'fusion-builder' ),
				'description' => esc_attr__( 'Insert the text to display as the link.', 'fusion-builder' ),
				'param_name'  => 'linktext',
				'value'       => '',
				'dependency'  => array(
					array(
						'element'  => 'link',
						'value'    => '',
						'operator' => '!=',
					),
				),
			),
			array(
				'type'        => 'radio_button_set',
				'heading'     => esc_attr__( 'Read More Link Target', 'fusion-builder' ),
				'description' => __( 'Default = use option selected in parent.', 'fusion-builder' ),
				'param_name'  => 'link_target',
				'value'       => array(
					esc_attr__( 'Default', 'fusion-builder' )        => '',
					esc_attr__( 'Same Window', 'fusion-builder' )    => '_self',
					esc_attr__( 'New Window/Tab', 'fusion-builder' ) => '_blank',
				),
				'default'     => '',
				'dependency'  => array(
					array(
						'element'  => 'link',
						'value'    => '',
						'operator' => '!=',
					),
				),
			),
			array(
				'type'        => 'tinymce',
				'heading'     => esc_attr__( 'Content Box Content', 'fusion-builder' ),
				'description' => esc_attr__( 'Add content for content box.', 'fusion-builder' ),
				'param_name'  => 'element_content',
				'value'       => 'Your Content Goes Here',
				'placeholder' => true,
			),
			array(
				'type'        => 'select',
				'heading'     => esc_attr__( 'Animation Type', 'fusion-builder' ),
				'description' => esc_attr__( 'Select the type of animation to use on the element.', 'fusion-builder' ),
				'param_name'  => 'animation_type',
				'value'       => fusion_builder_available_animations(),
				'default'     => '',
				'group'       => esc_attr__( 'Animation', 'fusion-builder' ),
			),
			array(
				'type'        => 'radio_button_set',
				'heading'     => esc_attr__( 'Direction of Animation', 'fusion-builder' ),
				'description' => esc_attr__( 'Select the incoming direction for the animation.', 'fusion-builder' ),
				'param_name'  => 'animation_direction',
				'value'       => array(
					esc_attr__( 'Top', 'fusion-builder' )    => 'down',
					esc_attr__( 'Right', 'fusion-builder' )  => 'right',
					esc_attr__( 'Bottom', 'fusion-builder' ) => 'up',
					esc_attr__( 'Left', 'fusion-builder' )   => 'left',
					esc_attr__( 'Static', 'fusion-builder' ) => 'static',
				),
				'default'     => 'left',
				'group'       => esc_attr__( 'Animation', 'fusion-builder' ),
				'dependency'  => array(
					array(
						'element'  => 'animation_type',
						'value'    => '',
						'operator' => '!=',
					),
				),
			),
			array(
				'type'        => 'range',
				'heading'     => esc_attr__( 'Speed of Animation', 'fusion-builder' ),
				'description' => esc_attr__( 'Type in speed of animation in seconds (0.1 - 1).', 'fusion-builder' ),
				'param_name'  => 'animation_speed',
				'min'         => '0.1',
				'max'         => '1',
				'step'        => '0.1',
				'value'       => '0.3',
				'group'       => esc_attr__( 'Animation', 'fusion-builder' ),
				'dependency'  => array(
					array(
						'element'  => 'animation_type',
						'value'    => '',
						'operator' => '!=',
					),
				),
			),
			array(
				'type'        => 'select',
				'heading'     => esc_attr__( 'Offset of Animation', 'fusion-builder' ),
				'description' => esc_attr__( 'Controls when the animation should start.', 'fusion-builder' ),
				'param_name'  => 'animation_offset',
				'value'       => array(
					esc_attr__( 'Default', 'fusion-builder' )                                => '',
					esc_attr__( 'Top of element hits bottom of viewport', 'fusion-builder' ) => 'top-into-view',
					esc_attr__( 'Top of element hits middle of viewport', 'fusion-builder' ) => 'top-mid-of-view',
					esc_attr__( 'Bottom of element enters viewport', 'fusion-builder' )      => 'bottom-in-view',
				),
				'default'     => '',
				'group'       => esc_attr__( 'Animation', 'fusion-builder' ),
				'dependency'  => array(
					array(
						'element'  => 'animation_type',
						'value'    => '',
						'operator' => '!=',
					),
				),
			),
		),
	) );
}
add_action( 'fusion_builder_before_init', 'fusion_element_content_box' );
