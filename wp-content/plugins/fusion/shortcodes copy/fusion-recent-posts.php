<?php

/**
 * Shortcode class.
 *
 * @package fusion-builder
 * @since 1.0
 */
class FusionSC_RecentPosts {

	/**
	 * An array of the shortcode arguments.
	 *
	 * @static
	 * @access public
	 * @since 1.0
	 * @var array
	 */
	public static $args;


	/**
	 * Constructor.
	 *
	 * @access public
	 * @since 1.0
	 */
	public function __construct() {

		add_filter( 'fusion_attr_recentposts-shortcode', array( $this, 'attr' ) );
		add_filter( 'fusion_attr_recentposts-shortcode-section', array( $this, 'section_attr' ) );
		add_filter( 'fusion_attr_recentposts-shortcode-column', array( $this, 'column_attr' ) );
		add_filter( 'fusion_attr_recentposts-shortcode-slideshow', array( $this, 'slideshow_attr' ) );
		add_filter( 'fusion_attr_recentposts-shortcode-img', array( $this, 'img_attr' ) );
		add_filter( 'fusion_attr_recentposts-shortcode-img-link', array( $this, 'link_attr' ) );

		add_shortcode( 'fusion_recent_posts', array( $this, 'render' ) );

	}

	/**
	 * Render the shortcode
	 *
	 * @access public
	 * @since 1.0
	 * @param  array  $args    Shortcode parameters.
	 * @param  string $content Content between shortcode.
	 * @return string          HTML output.
	 */
	public function render( $args, $content = '' ) {

		$defaults = FusionBuilder::set_shortcode_defaults(
			array(
				'hide_on_mobile'      => fusion_builder_default_visibility( 'string' ),
				'class'               => '',
				'id'                  => '',
				'cat_id'              => '',
				'cat_slug'            => '',
				'columns'             => 3,
				'excerpt'             => 'no',
				'exclude_cats'        => '',
				'excerpt_length'      => '',
				'excerpt_words'       => '15', // Deprecated.
				'hover_type'          => 'none',
				'layout'              => 'default',
				'meta'                => 'yes',
				'number_posts'        => 4,
				'offset'              => '',
				'strip_html'          => 'yes',
				'title'               => 'yes',
				'thumbnail'           => 'yes',
				'animation_direction' => 'left',
				'animation_speed'     => '',
				'animation_type'      => '',
				'animation_offset'    => ( class_exists( 'Avada' ) ) ? Avada()->settings->get( 'animation_offset' ) : '',
			), $args
		);

		if ( '0' === $defaults['offset']  ) {
			$defaults['offset'] = '';
		}

		if ( $defaults['columns'] > 6 ) {
			$defaults['columns'] = 6;
		}

		$defaults['strip_html'] = ( 'yes' === $defaults['strip_html'] || 'true' === $defaults['strip_html'] ) ? true : false;

		if ( $defaults['number_posts'] ) {
			$defaults['posts_per_page'] = $defaults['number_posts'];
		}

		if ( $defaults['excerpt_length'] || '0' === $defaults['excerpt_length'] ) {
			$defaults['excerpt_words'] = $defaults['excerpt_length'];
		}

		// Check for cats to exclude; needs to be checked via exclude_cats param
		// and '-' prefixed cats on cats param, exclution via exclude_cats param.
		$cats_to_exclude = explode( ',' , $defaults['exclude_cats'] );
		if ( $cats_to_exclude ) {
			foreach ( $cats_to_exclude as $cat_to_exclude ) {
				$id_obj = get_category_by_slug( $cat_to_exclude );
				if ( $id_obj ) {
					$cats_id_to_exclude[] = $id_obj->term_id;
				}
			}
			if ( isset( $cats_id_to_exclude ) && $cats_id_to_exclude ) {
				$defaults['category__not_in'] = $cats_id_to_exclude;
			}
		}

		// Setting up cats to be used and exclution using '-' prefix on cats param; transform slugs to ids.
		$cat_ids = '';
		$categories = explode( ',' , $defaults['cat_slug'] );
		if ( isset( $categories ) && $categories ) {
			foreach ( $categories as $category ) {
				if ( $category ) {
					$cat_obj = get_category_by_slug( $category );
					if ( isset( $cat_obj->term_id ) ) {
						// @codingStandardsIgnoreStart
						if ( 0 === strpos( $category, '-' ) ) {
							$cat_ids .= '-' .$cat_obj->cat_ID . ',';
						} else {
							$cat_ids .= $cat_obj->cat_ID . ',';
						}
						// @codingStandardsIgnoreEnd
					}
				}
			}
		}
		$defaults['cat'] = substr( $cat_ids, 0, -1 ) . $defaults['cat_id'];

		$items = '';

		$args = array(
			'posts_per_page'      => $defaults['number_posts'],
			'ignore_sticky_posts' => 1,
		);

		if ( $defaults['offset'] ) {
			$args['offset'] = $defaults['offset'];
		}

		if ( $defaults['cat'] ) {
			$args['cat'] = $defaults['cat'];
		}

		if ( isset( $defaults['category__not_in'] ) && is_array( $defaults['category__not_in'] ) ) {
			$args['category__not_in'] = $defaults['category__not_in'];
		}

		extract( $defaults );

		self::$args = $defaults;

		$recent_posts = fusion_builder_cached_query( $args );

		$count = 1;

		if ( ! $recent_posts->have_posts() ) {
			return fusion_builder_placeholder( 'post', 'blog posts' );
		}

		while ( $recent_posts->have_posts() ) {
			$recent_posts->the_post();

			$attachment = $date_box = $slideshow = $slides = $content = '';

			if ( 'date-on-side' == $layout ) {

				switch ( get_post_format() ) {
					case 'gallery':
						$format_class = 'images';
						break;
					case 'link':
						$format_class = 'link';
						break;
					case 'image':
						$format_class = 'image';
						break;
					case 'quote':
						$format_class = 'quotes-left';
						break;
					case 'video':
						$format_class = 'film';
						break;
					case 'audio':
						$format_class = 'headphones';
						break;
					case 'chat':
						$format_class = 'bubbles';
						break;
					default:
						$format_class = 'pen';
						break;
				}

				$date_box = '<div ' . FusionBuilder::attributes( 'fusion-date-and-formats' ) . '><div ' . FusionBuilder::attributes( 'fusion-date-box updated' ) . '><span ' . FusionBuilder::attributes( 'fusion-date' ) . '>' . get_the_time( FusionBuilder::get_theme_option( 'alternate_date_format_day' ) ) . '</span><span ' . FusionBuilder::attributes( 'fusion-month-year' ) . '>' . get_the_time( FusionBuilder::get_theme_option( 'alternate_date_format_month_year' ) ) . '</span></div><div ' . FusionBuilder::attributes( 'fusion-format-box' ) . '><i ' . FusionBuilder::attributes( 'fusion-icon-' . $format_class ) . '></i></div></div>';
			}

			if ( 'yes' === $thumbnail  && 'date-on-side' !== $layout && ! post_password_required( get_the_ID() ) ) {

				if ( 'default' == $layout ) {
					$image_size = 'recent-posts';
				} elseif ( 'thumbnails-on-side' == $layout ) {
					$image_size = 'portfolio-five';
				}

				if ( has_post_thumbnail() || get_post_meta( get_the_ID(), 'pyre_video', true ) ) {
					if ( get_post_meta( get_the_ID(), 'pyre_video', true ) ) {
						$slides .= '<li><div ' . FusionBuilder::attributes( 'full-video' ) . '>' . get_post_meta( get_the_ID(), 'pyre_video', true ) . '</div></li>';
					}

					if ( has_post_thumbnail() ) {
						$attachment_image   = wp_get_attachment_image_src( get_post_thumbnail_id(), $image_size );
						$attachment_img_tag = wp_get_attachment_image( get_post_thumbnail_id(), $image_size );

						$attachment_img_tag_custom = '<img src="' . $attachment_image[0] . '" alt="' . get_post_meta( get_post_thumbnail_id(), '_wp_attachment_image_alt', true ) . '" />';
						$full_image = wp_get_attachment_image_src( get_post_thumbnail_id(), 'full' );
						$attachment_data = wp_get_attachment_metadata( get_post_thumbnail_id() );
						$attachment = get_post( get_post_thumbnail_id() );

						$slides .= '<li><a href="' . get_permalink( get_the_ID() ) . '" ' . FusionBuilder::attributes( 'recentposts-shortcode-img-link' ) . '>' . $attachment_img_tag_custom . '</a></li>';
					}

					$i = 2;
					$posts_slideshow_number = FusionBuilder::get_theme_option( 'posts_slideshow_number' );
					if ( '' === $posts_slideshow_number ) {
						$posts_slideshow_number = 5;
					}
					while ( $i <= $posts_slideshow_number ) {
						$attachment_new_id = kd_mfi_get_featured_image_id( 'featured-image-' . $i, 'post' );
						if ( $attachment_new_id ) {
							$attachment_image          = wp_get_attachment_image_src( $attachment_new_id, $image_size );
							$attachment_img_tag        = wp_get_attachment_image( $attachment_new_id, $image_size );
							$attachment_img_tag_custom = '<img src="' . $attachment_image[0] . '" alt="' . get_post_meta( $attachment_new_id, '_wp_attachment_image_alt', true ) . '" />';
							$full_image      = wp_get_attachment_image_src( $attachment_new_id, 'full' );
							$attachment_data = wp_get_attachment_metadata( $attachment_new_id );

							$slides .= '<li><a href="' . get_permalink( get_the_ID() ) . '" ' . FusionBuilder::attributes( 'recentposts-shortcode-img-link' ) . '>' . $attachment_img_tag_custom . '</a></li>';
						}
						$i++;
					}

					$slideshow = '<div ' . FusionBuilder::attributes( 'recentposts-shortcode-slideshow' ) . '><ul ' . FusionBuilder::attributes( 'slides' ) . '>' . $slides . '</ul></div>';
				}
			}

			if ( 'yes' == $title ) {
				$content .= ( function_exists( 'avada_render_rich_snippets_for_pages' ) ) ? avada_render_rich_snippets_for_pages( false ) : '';
				$entry_title = '';
				if ( FusionBuilder::get_theme_option( 'disable_date_rich_snippet_pages' ) ) {
					$entry_title = 'entry-title';
				}
				$content .= '<h4 class="' . $entry_title . '"><a href="' . get_permalink( get_the_ID() ) . '">' . get_the_title() . '</a></h4>';
			} else {
				$content .= avada_render_rich_snippets_for_pages();
			}

			if ( 'yes' == $meta ) {

				$comments = $comments_link = '';
				ob_start();
				comments_popup_link( esc_attr__( '0 Comments', 'fusion-builder' ), esc_attr__( '1 Comment', 'fusion-builder' ), esc_attr__( '% Comments', 'fusion-builder' ) );
				$comments_link = ob_get_contents();
				ob_get_clean();

				$comments = '<span ' . FusionBuilder::attributes( 'meta-separator' ) . '>|</span><span>' . $comments_link . '</span>';

				$content .= '<p ' . FusionBuilder::attributes( 'meta' ) . '><span><span ' . FusionBuilder::attributes( 'date' ) . '>' . get_the_time( FusionBuilder::get_theme_option( 'date_format' ), get_the_ID() ) . '</span></span>' . $comments . '</p>';

			}

			if ( 'yes' == $excerpt ) {
				$content .= fusion_get_post_content( '', 'yes', $excerpt_words, $strip_html );
			}

			if ( $count == self::$args['columns'] ) {
				$count = 0;
				$items .= '<div ' . FusionBuilder::attributes( 'recentposts-shortcode-column' ) . '>' . $date_box . $slideshow . '<div ' . FusionBuilder::attributes( 'recent-posts-content' ) . '>' . $content . '</div></div><div class="fusion-clearfix"></div>';
			} else {
				$items .= '<div ' . FusionBuilder::attributes( 'recentposts-shortcode-column' ) . '>' . $date_box . $slideshow . '<div ' . FusionBuilder::attributes( 'recent-posts-content' ) . '>' . $content . '</div></div>';
			}

			$count++;

		}

		$html = '<div ' . FusionBuilder::attributes( 'recentposts-shortcode' ) . '><section ' . FusionBuilder::attributes( 'recentposts-shortcode-section' ) . '>' . $items . '</section></div>';

		wp_reset_query();

		return $html;

	}

	/**
	 * Builds the attributes array.
	 *
	 * @access public
	 * @since 1.0
	 * @return array
	 */
	public function attr() {

		$attr = fusion_builder_visibility_atts( self::$args['hide_on_mobile'], array(
			'class' => 'fusion-recent-posts avada-container layout-' . self::$args['layout'] . ' layout-columns-' . self::$args['columns'],
		) );

		if ( self::$args['class'] ) {
			$attr['class'] .= ' ' . self::$args['class'];
		}

		if ( self::$args['id'] ) {
			$attr['id'] = self::$args['id'];
		}

		return $attr;

	}

	/**
	 * Builds the section attributes array.
	 *
	 * @access public
	 * @since 1.0
	 * @return array
	 */
	public function section_attr() {
		return array(
			'class' => 'fusion-columns columns fusion-columns-' . self::$args['columns'] . ' columns-' . self::$args['columns'],
		);
	}

	/**
	 * Builds the column attributes array.
	 *
	 * @access public
	 * @since 1.0
	 * @return array
	 */
	public function column_attr() {

		$columns = 3;
		if ( self::$args['columns'] ) {
			$columns = 12 / self::$args['columns'];
		}

		$attr = array(
			'class' => 'fusion-column column col col-lg-' . $columns . ' col-md-' . $columns . ' col-sm-' . $columns . '',
		);

		if ( '5' == self::$args['columns'] ) {
			$attr['class'] = 'fusion-column column col-lg-2 col-md-2 col-sm-2';
		}

		if ( self::$args['animation_type'] ) {
			$animations = FusionBuilder::animations( array(
				'type'      => self::$args['animation_type'],
				'direction' => self::$args['animation_direction'],
				'speed'     => self::$args['animation_speed'],
				'offset'    => self::$args['animation_offset'],
			) );

			$attr = array_merge( $attr, $animations );

			$attr['class'] .= ' ' . $attr['animation_class'];
			unset( $attr['animation_class'] );
		}

		return $attr;

	}

	/**
	 * Builds the slideshow attributes array.
	 *
	 * @access public
	 * @since 1.0
	 * @return array
	 */
	public function slideshow_attr() {

		$attr = array(
			'class' => 'fusion-flexslider flexslider',
		);

		if ( 'thumbnails-on-side' == self::$args['layout'] ) {
			$attr['class'] .= ' floated-slideshow';
		}

		if ( self::$args['hover_type'] ) {
			$attr['class'] .= ' flexslider-hover-type-' . self::$args['hover_type'];
		}

		return $attr;

	}

	/**
	 * Builds the image attributes array.
	 *
	 * @access public
	 * @since 1.0
	 * @param array $args The arguments array.
	 * @return array
	 */
	public function img_attr( $args ) {

		$attr = array(
			'src' => $args['src'],
		);

		if ( $args['alt'] ) {
			$attr['alt'] = $args['alt'];
		}

		return $attr;

	}

	/**
	 * Builds the link attributes array.
	 *
	 * @access public
	 * @since 1.0
	 * @param array $args The arguments array.
	 * @return array
	 */
	public function link_attr( $args ) {

		$attr = array();

		if ( self::$args['hover_type'] ) {
			$attr['class'] = 'hover-type-' . self::$args['hover_type'];
		}

		return $attr;

	}
}
new FusionSC_RecentPosts();

/**
 * Map shortcode to Fusion Builder.
 *
 * @since 1.0
 */
function fusion_element_recent_posts() {
	fusion_builder_map( array(
		'name'       => esc_attr__( 'Recent Posts', 'fusion-builder' ),
		'shortcode'  => 'fusion_recent_posts',
		'icon'       => 'fusiona-feather',
		'preview'    => FUSION_BUILDER_PLUGIN_DIR . 'js/previews/fusion-recent-posts-preview.php',
		'preview_id' => 'fusion-builder-block-module-recent-posts-preview-template',
		'params'     => array(
			array(
				'type'        => 'radio_button_set',
				'heading'     => esc_attr__( 'Layout', 'fusion-builder' ),
				'description' => esc_attr__( 'Select the layout for the element.', 'fusion-builder' ),
				'param_name'  => 'layout',
				'value'       => array(
					esc_attr__( 'Standard', 'fusion-builder' )           => 'default',
					esc_attr__( 'Thumbnails on Side', 'fusion-builder' ) => 'thumbnails-on-side',
					esc_attr__( 'Date on Side', 'fusion-builder' )       => 'date-on-side',
				),
				'default'     => 'default',
			),
			array(
				'type'        => 'radio_button_set',
				'heading'     => esc_attr__( 'Hover Type', 'fusion-builder' ),
				'description' => esc_attr__( 'Select the hover effect type.', 'fusion-builder' ),
				'param_name'  => 'hover_type',
				'value'       => array(
					esc_attr__( 'None', 'fusion-builder' )     => 'none',
					esc_attr__( 'Zoom In', 'fusion-builder' )  => 'zoomin',
					esc_attr__( 'Zoom Out', 'fusion-builder' ) => 'zoomout',
					esc_attr__( 'Lift Up', 'fusion-builder' )  => 'liftup',
				),
				'default'     => 'none',
			),
			array(
				'type'        => 'range',
				'heading'     => esc_attr__( 'Columns', 'fusion-builder' ),
				'description' => esc_attr__( 'Select the number of columns to display.', 'fusion-builder' ),
				'param_name'  => 'columns',
				'value'       => '3',
				'min'         => '1',
				'max'         => '6',
				'step'        => '1',
			),
			array(
				'type'        => 'range',
				'heading'     => esc_attr__( 'Posts Per Page', 'fusion-builder' ),
				'description' => esc_attr__( 'Select number of posts per page.  Set to -1 to display all.', 'fusion-builder' ),
				'param_name'  => 'number_posts',
				'value'       => '6',
				'min'         => '-1',
				'max'         => '25',
				'step'        => '1',
			),
			array(
				'type'        => 'range',
				'heading'     => esc_attr__( 'Post Offset', 'fusion-builder' ),
				'description' => esc_attr__( 'The number of posts to skip. ex: 1.', 'fusion-builder' ),
				'param_name'  => 'offset',
				'value'       => '0',
				'min'         => '0',
				'max'         => '25',
				'step'        => '1',
			),
			array(
				'type'        => 'multiple_select',
				'heading'     => esc_attr__( 'Categories', 'fusion-builder' ),
				'description' => esc_attr__( 'Select a category or leave blank for all.', 'fusion-builder' ),
				'param_name'  => 'cat_slug',
				'value'       => fusion_builder_shortcodes_categories( 'category' ),
				'default'     => '',
			),
			array(
				'type'        => 'multiple_select',
				'heading'     => esc_attr__( 'Exclude Categories', 'fusion-builder' ),
				'description' => esc_attr__( 'Select a category to exclude.', 'fusion-builder' ),
				'param_name'  => 'exclude_cats',
				'value'       => fusion_builder_shortcodes_categories( 'category' ),
				'default'     => '',
			),
			array(
				'type'        => 'radio_button_set',
				'heading'     => esc_attr__( 'Show Thumbnail', 'fusion-builder' ),
				'description' => esc_attr__( 'Display the post featured image.', 'fusion-builder' ),
				'param_name'  => 'thumbnail',
				'value'       => array(
					esc_attr__( 'Yes', 'fusion-builder' ) => 'yes',
					esc_attr__( 'No', 'fusion-builder' )  => 'no',
				),
				'default'     => 'yes',
				'dependency'  => array(
					array(
						'element'  => 'layout',
						'value'    => 'date-on-side',
						'operator' => '!=',
					),
				),
			),
			array(
				'type'        => 'radio_button_set',
				'heading'     => esc_attr__( 'Show Title', 'fusion-builder' ),
				'description' => esc_attr__( 'Display the post title below the featured image.', 'fusion-builder' ),
				'param_name'  => 'title',
				'value'       => array(
					esc_attr__( 'Yes', 'fusion-builder' ) => 'yes',
					esc_attr__( 'No', 'fusion-builder' )  => 'no',
				),
				'default'     => 'yes',
			),
			array(
				'type'        => 'radio_button_set',
				'heading'     => esc_attr__( 'Show Meta', 'fusion-builder' ),
				'description' => esc_attr__( 'Choose to show all meta data.', 'fusion-builder' ),
				'param_name'  => 'meta',
				'value'       => array(
					esc_attr__( 'Yes', 'fusion-builder' ) => 'yes',
					esc_attr__( 'No', 'fusion-builder' )  => 'no',
				),
				'default'     => 'yes',
			),
			array(
				'type'        => 'radio_button_set',
				'heading'     => esc_attr__( 'Show Excerpt', 'fusion-builder' ),
				'description' => esc_attr__( 'Choose to display the post excerpt.', 'fusion-builder' ),
				'param_name'  => 'excerpt',
				'value'       => array(
					esc_attr__( 'Yes', 'fusion-builder' ) => 'yes',
					esc_attr__( 'No', 'fusion-builder' )  => 'no',
				),
				'default'     => 'yes',
			),
			array(
				'type'        => 'range',
				'heading'     => esc_attr__( 'Excerpt Length', 'fusion-builder' ),
				'description' => esc_attr__( 'Insert the number of words/characters you want to show in the excerpt.', 'fusion-builder' ),
				'param_name'  => 'excerpt_length',
				'value'       => '35',
				'min'         => '0',
				'max'         => '500',
				'step'        => '1',
				'dependency'  => array(
					array(
						'element'  => 'excerpt',
						'value'    => 'yes',
						'operator' => '==',
					),
				),
			),
			array(
				'type'        => 'radio_button_set',
				'heading'     => esc_attr__( 'Strip HTML', 'fusion-builder' ),
				'description' => esc_attr__( 'Strip HTML from the post excerpt.', 'fusion-builder' ),
				'param_name'  => 'strip_html',
				'value'       => array(
					esc_attr__( 'Yes', 'fusion-builder' ) => 'yes',
					esc_attr__( 'No', 'fusion-builder' )  => 'no',
				),
				'default'     => 'yes',
				'dependency'  => array(
					array(
						'element'  => 'excerpt',
						'value'    => 'yes',
						'operator' => '==',
					),
				),
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
add_action( 'fusion_builder_before_init', 'fusion_element_recent_posts' );
