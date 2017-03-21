<?php

/**
 * Shortcode class.
 *
 * @package fusion-builder
 * @since 1.0
 */
class FusionSC_Blog {

	/**
	 * Blog SC counter.
	 *
	 * @access private
	 * @since 1.0
	 * @var int
	 */
	private $blog_sc_counter = 1;

	/**
	 * Posts counter.
	 *
	 * @access private
	 * @since 1.0
	 * @var int
	 */
	private $post_count = 1;

	/**
	 * The post ID.
	 *
	 * @access private
	 * @since 1.0
	 * @var int
	 */
	private $post_id = 0;

	/**
	 * The month of the post.
	 *
	 * @access private
	 * @since 1.0
	 * @var null|int|string
	 */
	private $post_month = null;

	/**
	 * The post's year.
	 *
	 * @access private
	 * @since 1.0
	 * @var null|int|string
	 */
	private $post_year = null;

	/**
	 * An array of meta settings.
	 *
	 * @access private
	 * @since 1.0
	 * @var array
	 */
	private $meta_info_settings = array();

	/**
	 * Header arguments.
	 *
	 * @access private
	 * @since 1.0
	 * @var array
	 */
	private $header = array();

	/**
	 * The Query.
	 *
	 * @access private
	 * @since 1.0
	 * @var string|array|object
	 */
	private $query = '';

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

		// Containers.
		add_action( 'fusion_blog_shortcode_before_loop', array( $this, 'before_loop' ) );
		add_action( 'fusion_blog_shortcode_before_loop_timeline', array( $this, 'before_loop_timeline' ) );
		add_action( 'fusion_blog_shortcode_after_loop', array( $this, 'after_loop' ) );

		// Post / loop basic structure.
		add_action( 'fusion_blog_shortcode_loop_header', array( $this, 'loop_header' ) );
		add_action( 'fusion_blog_shortcode_loop_footer', array( $this, 'loop_footer' ) );
		add_action( 'fusion_blog_shortcode_loop_content', array( $this, 'loop_content' ) );
		add_action( 'fusion_blog_shortcode_loop_content', array( $this, 'page_links' ) );
		add_action( 'fusion_blog_shortcode_loop', array( $this, 'loop' ) );

		// Special blog layout structure.
		add_action( 'fusion_blog_shortcode_wrap_loop_open', array( $this, 'wrap_loop_open' ) );
		add_action( 'fusion_blog_shortcode_wrap_loop_close', array( $this, 'wrap_loop_close' ) );
		add_action( 'fusion_blog_shortcode_date_and_format', array( $this, 'add_date_box' ) );
		add_action( 'fusion_blog_shortcode_date_and_format', array( $this, 'add_format_box' ) );
		add_action( 'fusion_blog_shortcode_timeline_date', array( $this, 'timeline_date' ) );

		// Element attributes.
		add_filter( 'fusion_attr_blog-shortcode', array( $this, 'attr' ) );
		add_filter( 'fusion_attr_blog-shortcode-posts-container', array( $this, 'posts_container_attr' ) );
		add_filter( 'fusion_attr_blog-shortcode-loop', array( $this, 'loop_attr' ) );
		add_filter( 'fusion_attr_blog-shortcode-post-title', array( $this, 'post_title_attr' ) );

		add_shortcode( 'fusion_blog', array( $this, 'render' ) );

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
				'hide_on_mobile'           => fusion_builder_default_visibility( 'string' ),
				'class'                    => '',
				'id'                       => '',
				'blog_grid_column_spacing' => '40',
				'blog_grid_columns'        => '3',
				'cat_slug'                 => '',
				'excerpt'                  => 'yes',
				'excerpt_length'           => '',
				'exclude_cats'             => '',
				'layout'                   => 'large',
				'meta_all'                 => 'yes',
				'meta_author'              => 'yes',
				'meta_categories'          => 'yes',
				'meta_comments'            => 'yes',
				'meta_date'                => 'yes',
				'meta_link'                => 'yes',
				'meta_read'                => 'yes',
				'meta_tags'                => 'no',
				'number_posts'             => '6',
				'offset'                   => '',
				'order'                    => 'DESC',
				'orderby'                  => 'date',
				'paging'                   => 'yes',
				'show_title'               => 'yes',
				'scrolling'                => 'infinite',
				'strip_html'               => 'yes',
				'thumbnail'                => 'yes',
				'title_link'               => 'yes',
				'posts_per_page'           => '-1',
				'taxonomy'                 => 'category',

				'excerpt_words'            => '50', // Deprecated.
				'title'                    => '',   // Deprecated.
			), $args
		);

		$defaults['blog_grid_column_spacing'] = FusionBuilder::validate_shortcode_attr_value( $defaults['blog_grid_column_spacing'], '' );

		extract( $defaults );

		// Since WP 4.4 'title' param is reserved.
		if ( $defaults['title'] ) {
			$defaults['show_title'] = $defaults['title'];
		}
		unset( $defaults['title'] );

		$paged = ( get_query_var( 'paged' ) ) ? get_query_var( 'paged' ) : 1;
		if ( is_front_page() || is_home() ) {
			$paged = ( get_query_var( 'paged' ) ) ? get_query_var( 'paged' ) : ( ( get_query_var( 'page' ) ) ? get_query_var( 'page' ) : 1 );
		}

		$defaults['paged'] = $paged;

		// Convert all attributes to correct values for WP query.
		$defaults['posts_per_page'] = $defaults['number_posts'];

		if ( -1 == $defaults['posts_per_page'] ) {
			$defaults['paging'] = 'no';

		}

		if ( '0' === $defaults['offset']  ) {
			$defaults['offset'] = '';
		}

		// Add hyphens for alternate layout options.
		if ( 'large alternate' === $defaults['layout'] ) {
			$defaults['layout'] = 'large-alternate';
		} elseif ( 'medium alternate' === $defaults['layout'] ) {
			$defaults['layout'] = 'medium-alternate';
		}

		$defaults['load_more'] = false;
		if ( 'pagination' !== $defaults['scrolling'] ) {
			$defaults['paging'] = 'yes';

			if ( 'load_more_button' === $defaults['scrolling'] ) {
				$defaults['load_more'] = true;
			}

			$defaults['scrolling'] = 'infinite';
		}

		$defaults['meta_all']        = ( 'yes' === $defaults['meta_all'] );
		$defaults['meta_author']     = ( 'yes' === $defaults['meta_author'] );
		$defaults['meta_categories'] = ( 'yes' === $defaults['meta_categories'] );
		$defaults['meta_comments']   = ( 'yes' === $defaults['meta_comments'] );
		$defaults['meta_date']       = ( 'yes' === $defaults['meta_date'] );
		$defaults['meta_link']       = ( 'yes' === $defaults['meta_link'] );
		$defaults['meta_tags']       = ( 'yes' === $defaults['meta_tags'] );
		$defaults['paging']          = ( 'yes' === $defaults['paging'] );
		$defaults['strip_html']      = ( 'yes' === $defaults['strip_html'] );
		$defaults['thumbnail']       = ( 'yes' === $defaults['thumbnail'] );
		$defaults['show_title']      = ( 'yes' === $defaults['show_title'] );
		$defaults['title_link']      = ( 'yes' === $defaults['title_link'] );

		if ( $defaults['excerpt_length'] || '0' === $defaults['excerpt_length'] ) {
			$defaults['excerpt_words'] = $defaults['excerpt_length'];
		}

		// Combine meta info into one variable.
		$defaults['meta_info_combined'] = $defaults['meta_all'] * ( $defaults['meta_author'] + $defaults['meta_date'] + $defaults['meta_categories'] + $defaults['meta_tags'] + $defaults['meta_comments'] + $defaults['meta_link'] );
		// Create boolean that holds info whether content should be excerpted.
		$defaults['is_zero_excerpt'] = ( 'yes' === $defaults['excerpt'] && $defaults['excerpt_words'] < 1 ) ? 1 : 0;

		// Check for cats to exclude; needs to be checked via exclude_cats param
		// and '-' prefixed cats on cats param exclution via exclude_cats param.
		$cats_to_exclude = explode( ',' , $defaults['exclude_cats'] );
		$cats_id_to_exclude = array();
		if ( $cats_to_exclude ) {
			foreach ( $cats_to_exclude as $cat_to_exclude ) {
				$id_obj = get_category_by_slug( $cat_to_exclude );
				if ( $id_obj ) {
					$cats_id_to_exclude[] = $id_obj->term_id;
				}
			}
			if ( $cats_id_to_exclude ) {
				$defaults['category__not_in'] = $cats_id_to_exclude;
			}
		}

		// Setting up cats to be used and exclution using '-' prefix on cats param; transform slugs to ids.
		$cat_ids = '';
		if ( '' !== $defaults['cat_slug'] ) {
			$categories = explode( ',' , $defaults['cat_slug'] );
			if ( isset( $categories ) && $categories ) {
				foreach ( $categories as $category ) {

					$id_obj = get_category_by_slug( $category );

					if ( $id_obj ) {
						// @codingStandardsIgnoreStart
						$cat_ids .= ( 0 === strpos( $category, '-' ) ) ? '-' . $id_obj->cat_ID . ',' : $id_obj->cat_ID . ',';
						// @codingStandardsIgnoreEnd
					}
				}
			}
		}
		$defaults['cat'] = substr( $cat_ids, 0, -1 );

		if ( '0' === $defaults['blog_grid_column_spacing'] ) {
			$defaults['blog_grid_column_spacing'] = '0.0';
		}

		$defaults['blog_sc_query'] = true;

		self::$args = $defaults;

		// Set the meta info settings for later use.
		$this->meta_info_settings['post_meta']          = $defaults['meta_all'];
		$this->meta_info_settings['post_meta_author']   = $defaults['meta_author'];
		$this->meta_info_settings['post_meta_date']     = $defaults['meta_date'];
		$this->meta_info_settings['post_meta_cats']     = $defaults['meta_categories'];
		$this->meta_info_settings['post_meta_tags']     = $defaults['meta_tags'];
		$this->meta_info_settings['post_meta_comments'] = $defaults['meta_comments'];

		$fusion_query = fusion_builder_cached_query( $defaults );

		$this->query = $fusion_query;

		// Prepare needed wrapping containers.
		$html = '';

		$html .= '<div ' . FusionBuilder::attributes( 'blog-shortcode' ) . '>';

		if ( 'grid' === self::$args['layout'] && self::$args['blog_grid_column_spacing'] ) {
			$html .= '<style type="text/css">.fusion-blog-shortcode-' . $this->blog_sc_counter . ' .fusion-blog-layout-grid .fusion-post-grid{padding:' . ( $defaults['blog_grid_column_spacing'] / 2 ) . 'px;}.fusion-blog-shortcode-' . $this->blog_sc_counter . ' .fusion-posts-container{margin-left: -' . ( $defaults['blog_grid_column_spacing'] / 2 ) . 'px !important; margin-right:-' . $defaults['blog_grid_column_spacing'] / 2 . 'px !important;}</style>';
		}

		$html .= '<div ' . FusionBuilder::attributes( 'blog-shortcode-posts-container' ) . '>';

		ob_start();
		do_action( 'fusion_blog_shortcode_wrap_loop_open' );
		$wrap_loop_open = ob_get_contents();
		ob_get_clean();

		$html .= $wrap_loop_open;

		// Initialize the time stamps for timeline month/year check.
		if ( 'timeline' === self::$args['layout'] ) {
			$this->post_count = 1;

			$prev_post_timestamp = null;
			$prev_post_month = null;
			$prev_post_year = null;
			$first_timeline_loop = false;
		}

		// Do the loop.
		if ( $fusion_query->have_posts() ) {
			while ( $fusion_query->have_posts() ) : $fusion_query->the_post();

				$this->post_id = get_the_ID();

				if ( 'timeline' === self::$args['layout'] ) {
					// Set the time stamps for timeline month/year check.
					$post_timestamp = get_the_time( 'U' );
					$this->post_month = date( 'n', $post_timestamp );
					$this->post_year = get_the_date( 'Y' );
					$current_date = get_the_date( 'Y-n' );

					$date_params['prev_post_month'] = $prev_post_month;
					$date_params['post_month'] = $this->post_month;
					$date_params['prev_post_year'] = $prev_post_year;
					$date_params['post_year'] = $this->post_year;

					// Set the timeline month label.
					ob_start();
					do_action( 'fusion_blog_shortcode_timeline_date', $date_params );
					$timeline_date = ob_get_contents();
					ob_get_clean();

					$html .= $timeline_date;
				}

				ob_start();
				do_action( 'fusion_blog_shortcode_before_loop' );
				$before_loop_action = ob_get_contents();
				ob_get_clean();

				$html .= $before_loop_action;

				if ( 'grid' === self::$args['layout'] || 'timeline' === self::$args['layout'] ) {
					$html .= '<div ' . FusionBuilder::attributes( 'fusion-post-wrapper' ) . '>';
				}

				$this->header = array(
					'title_link' => true,
				);

				ob_start();
				do_action( 'fusion_blog_shortcode_loop_header' );

				do_action( 'fusion_blog_shortcode_loop_content' );

				do_action( 'fusion_blog_shortcode_loop_footer' );

				do_action( 'fusion_blog_shortcode_after_loop' );
				$loop_actions = ob_get_contents();
				ob_get_clean();

				$html .= $loop_actions;

				if ( 'timeline' === self::$args['layout'] ) {
					$prev_post_timestamp = $post_timestamp;
					$prev_post_month = $this->post_month;
					$prev_post_year = $this->post_year;
					$this->post_count++;
				}

			endwhile;
		} else {

			$this->blog_sc_counter++;
			return fusion_builder_placeholder( 'post', 'blog posts' );

		}

		ob_start();
		do_action( 'fusion_blog_shortcode_wrap_loop_close' );

		$wrap_loop_close_action = ob_get_contents();
		ob_get_clean();

		$html .= $wrap_loop_close_action;

		$html .= '</div>';

		if ( self::$args['paging'] ) {
			ob_start();
			fusion_pagination( $this->query->max_num_pages, $range = 2, $this->query );
			$pagination = ob_get_contents();
			ob_get_clean();

			$html .= $pagination;
		}

		// If infinite scroll with "load more" button is used.
		if ( self::$args['load_more'] && -1 != self::$args['posts_per_page'] ) {
			$html .= '<div class="fusion-load-more-button fusion-blog-button fusion-clearfix">' . apply_filters( 'avada_load_more_posts_name', esc_attr__( 'Load More Posts', 'fusion-builder' ) ) . '</div>';
		}

		$html .= '</div>';

		wp_reset_query();

		$this->blog_sc_counter++;

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

		$attr = array();

		// Set the correct layout class.
		$blog_layout = 'fusion-blog-layout-' . self::$args['layout'];
		if ( 'timeline' === self::$args['layout'] ) {
			$blog_layout = 'fusion-blog-layout-timeline-wrapper';
		} elseif ( 'grid' === self::$args['layout'] ) {
			$blog_layout = 'fusion-blog-layout-grid-wrapper';
		}

		$attr['class'] = 'fusion-blog-shortcode fusion-blog-shortcode-' . $this->blog_sc_counter . ' fusion-blog-archive ' . $blog_layout . ' fusion-blog-' . self::$args['scrolling'];

		$attr = fusion_builder_visibility_atts( self::$args['hide_on_mobile'], $attr );

		if ( ! self::$args['thumbnail'] ) {
			$attr['class'] .= ' fusion-blog-no-images';
		}

		if ( self::$args['class'] ) {
			$attr['class'] .= ' ' . self::$args['class'];
		}

		if ( '0' == self::$args['blog_grid_column_spacing'] || '0px' === self::$args['blog_grid_column_spacing'] ) {
			$attr['class'] .= ' fusion-no-col-space';
		}

		if ( self::$args['id'] ) {
			$attr['id'] = self::$args['id'];
		}

		return $attr;

	}

	/**
	 * Builds the posts-container attributes array.
	 *
	 * @access public
	 * @since 1.0
	 * @return array
	 */
	public function posts_container_attr() {
		global $post;

		$attr = array();

		$load_more = '';
		if ( self::$args['load_more'] ) {
			$load_more = ' fusion-posts-container-load-more';
		}

		$attr['class'] = 'fusion-posts-container fusion-posts-container-' . self::$args['scrolling'] . $load_more;

		$attr['data-pages'] = $this->query->max_num_pages;

		if ( 'grid' === self::$args['layout'] ) {
			 $attr['class'] .= ' fusion-blog-layout-grid fusion-blog-layout-grid-' . self::$args['blog_grid_columns'] . ' isotope';

			if ( self::$args['blog_grid_column_spacing'] || '0' === self::$args['blog_grid_column_spacing'] ) {
				$attr['data-grid-col-space'] = self::$args['blog_grid_column_spacing'];
			}

			 $negative_margin = ( -1 ) * self::$args['blog_grid_column_spacing'] / 2;

			 $attr['style'] = 'margin: ' . $negative_margin . 'px ' . $negative_margin . 'px 0;height:500px;';
		}

		return $attr;

	}

	/**
	 * Opens the wrapper.
	 *
	 * @access public
	 * @since 1.0
	 */
	public function wrap_loop_open() {
		global $post;

		$wrapper = $class_timeline_icon = '';

		if ( 'timeline' === self::$args['layout'] ) {

			$wrapper  = '<div ' . FusionBuilder::attributes( 'fusion-timeline-icon' . $class_timeline_icon ) . '>';
			$wrapper .= '<i ' . FusionBuilder::attributes( 'fusion-icon-bubbles' ) . '></i>';
			$wrapper .= '</div>';
			$wrapper .= '<div ' . FusionBuilder::attributes( 'fusion-blog-layout-timeline fusion-clearfix' ) . '>';
			$wrapper .= '<div class="fusion-timeline-line"></div>';
		}

		echo $wrapper;

	}

	/**
	 * Closes the wrapper.
	 *
	 * @access public
	 * @since 1.0
	 */
	public function wrap_loop_close() {

		$wrapper = '';

		if ( 'timeline' === self::$args['layout'] ) {
			if ( $this->post_count > 1 ) {
				$wrapper = '</div>';
			}
			$wrapper .= '</div>';
		}

		if ( 'grid' === self::$args['layout'] ) {
			$wrapper .= '<div class="fusion-clearfix"></div>';
		}

		echo $wrapper;

	}

	/**
	 * Add HTML before the loop.
	 *
	 * @access public
	 * @since 1.0
	 */
	public function before_loop() {
		echo '<article ' . FusionBuilder::attributes( 'blog-shortcode-loop' ) . '>' . "\n";
	}

	/**
	 * Adds markup after the loop.
	 *
	 * @access public
	 * @since 1.0
	 */
	public function after_loop() {
		if ( 'grid' === self::$args['layout'] || 'timeline' === self::$args['layout'] ) {
			echo '</div>' . "\n";
			echo '</article>' . "\n";
		} else {
			echo '</article>' . "\n";
		}
	}

	/**
	 * Builds the loop attributes array.
	 *
	 * @access public
	 * @since 1.0
	 * @return array
	 */
	public function loop_attr() {

		$defaults = array(
			'post_id' => '',
			'post_count' => '',
		);

		$attr['id'] = 'post-' . $this->post_id;

		$extra_classes = array();

		// Add the correct post class.
		$extra_classes[] = 'fusion-post-' . self::$args['layout'];

		// Set the correct column class for every post.
		if ( 'timeline' === self::$args['layout'] ) {

			if ( ( $this->post_count % 2 ) > 0 ) {
				$timeline_align = ' fusion-left-column';
			} else {
				$timeline_align = ' fusion-right-column';
			}

			$extra_classes[] = 'fusion-clearfix' . $timeline_align;
		}

		// Set the has-post-thumbnail if a video is used. This is needed if no featured image is present.
		if ( get_post_meta( get_the_ID(), 'pyre_video', true ) ) {
			$extra_classes[] = 'has-post-thumbnail';
		}

		$post_class = get_post_class( $extra_classes, $this->post_id );

		if ( $post_class && is_array( $post_class ) ) {
			$classes = implode( ' ', get_post_class( $extra_classes, $this->post_id ) );
			$attr['class'] = $classes;
		}

		return $attr;

	}

	/**
	 * Gets the HTML for slideshows.
	 *
	 * @access public
	 * @since 1.0
	 * @return string
	 */
	public function get_slideshow() {

		$html = '';

		if ( ! post_password_required( get_the_ID() ) && class_exists( 'Avada_Init' ) ) {

			$slideshow = array(
				'images' => $this->get_post_thumbnails( get_the_ID(), FusionBuilder::get_theme_option( 'posts_slideshow_number' ) ),
			);

			if ( get_post_meta( $this->post_id, 'pyre_video', true ) ) {
				$slideshow['video'] = get_post_meta( $this->post_id, 'pyre_video', true );
			}

			if ( 'medium' === self::$args['layout'] || 'medium alternate' === self::$args['layout'] ) {
				$slideshow['size'] = 'blog-medium';
			}

			ob_start();
			$atts = self::$args;
			include( locate_template( 'new-slideshow-blog-shortcode.php', false ) );
			$post_slideshow_action = ob_get_contents();
			ob_get_clean();

			$html .= $post_slideshow_action;

		}

		return $html;
	}

	/**
	 * Gets the post thumbnails.
	 *
	 * @access public
	 * @since 1.0
	 * @param int $post_id The post-ID.
	 * @param int $count   How many thumbnails.
	 * @return array
	 */
	public function get_post_thumbnails( $post_id, $count = '' ) {

		$attachment_ids = array();

		if ( get_post_thumbnail_id( $post_id ) ) {
			$attachment_ids[] = get_post_thumbnail_id( $post_id );
		}

		$i = 2;
		$posts_slideshow_number = FusionBuilder::get_theme_option( 'posts_slideshow_number' );
		if ( '' === $posts_slideshow_number ) {
			$posts_slideshow_number = 5;
		}
		while ( $i <= $posts_slideshow_number ) {

			if ( kd_mfi_get_featured_image_id( 'featured-image-' . $i, 'post' ) ) {
				$attachment_ids[] = kd_mfi_get_featured_image_id( 'featured-image-' . $i, 'post' );
			}

			$i++;
		}

		if ( isset( $count ) && $count >= 1 ) {
			$attachment_ids = array_slice( $attachment_ids, 0, $count );
		}

		return $attachment_ids;

	} // End get_post_thumbnails().


	/**
	 * Adds the loop-header HTML.
	 *
	 * @access public
	 * @since 1.0
	 */
	public function loop_header() {

		$defaults = array(
			'title_link' => false,
		);

		$args = wp_parse_args( $this->header, $defaults );

		$pre_title_content = $meta_data = $content_sep = $link = '';

		if ( self::$args['thumbnail'] && 'medium-alternate' !== self::$args['layout'] ) {
			$pre_title_content = $this->get_slideshow();
		}

		if ( 'medium-alternate' === self::$args['layout'] || 'large-alternate' === self::$args['layout'] ) {
			$pre_title_content .= '<div ' . FusionBuilder::attributes( 'fusion-date-and-formats' ) . '>';
			ob_start();
			do_action( 'fusion_blog_shortcode_date_and_format' );
			$pre_title_content .= ob_get_contents();
			ob_get_clean();
			$pre_title_content .= '</div>';

			if ( self::$args['thumbnail'] && 'medium-alternate' === self::$args['layout'] ) {
				$pre_title_content .= $this->get_slideshow();
			}

			if ( self::$args['meta_all'] ) {
				$meta_data .= avada_render_post_metadata( 'alternate', $this->meta_info_settings );
			}
		}

		if ( 'grid' === self::$args['layout'] || 'timeline' === self::$args['layout'] ) {
			$content_wrapper_styles = '';

			if ( self::$args['meta_info_combined'] > 0 && ! self::$args['is_zero_excerpt'] ) {
				$content_sep = '<div ' . FusionBuilder::attributes( 'fusion-content-sep' ) . '></div>';
			}

			if ( ! self::$args['meta_info_combined'] && self::$args['is_zero_excerpt'] && ! self::$args['show_title'] ) {
				$content_wrapper_styles = 'style="display:none;"';
			}

			if ( self::$args['meta_all'] ) {
				$meta_data .= avada_render_post_metadata( 'grid_timeline', $this->meta_info_settings );
			}

			$pre_title_content .= '<div ' . FusionBuilder::attributes( 'fusion-post-content-wrapper' ) . $content_wrapper_styles . '>';
		}

		$pre_title_content .= '<div ' . FusionBuilder::attributes( 'fusion-post-content post-content' ) . '>';

		if ( self::$args['show_title'] ) {
			if ( self::$args['title_link'] ) {
				$link_target = '';
				if ( 'yes' === fusion_get_page_option( 'link_icon_target', get_the_ID() ) || 'yes' === fusion_get_page_option( 'post_links_target', get_the_ID() ) ) {
					$link_target = ' target="_blank" rel="noopener noreferrer"';
				}
				$link = '<a href="' . get_permalink() . '"' . $link_target . '>' . get_the_title() . '</a>';
			} else {
				$link = get_the_title();
			}
		}

		if ( 'timeline' === self::$args['layout'] ) {
			$pre_title_content .= '<div ' . FusionBuilder::attributes( 'fusion-timeline-circle' ) . '></div>';
			$pre_title_content .= '<div ' . FusionBuilder::attributes( 'fusion-timeline-arrow' ) . '></div>';
		}

		$html = $pre_title_content . '<h2 ' . FusionBuilder::attributes( 'blog-shortcode-post-title' ) . '>' . $link . '</h2>' . $meta_data . $content_sep;

		echo $html;

	} // End loop_header().


	/**
	 * Builds the post-title attributes array.
	 *
	 * @access public
	 * @since 1.0
	 * @return array
	 */
	public function post_title_attr() {

		$attr = array();

		$attr['class'] = 'blog-shortcode-post-title';

		if ( FusionBuilder::get_theme_option( 'disable_date_rich_snippet_pages' ) ) {
			$attr['class'] .= ' entry-title';
		}

		return $attr;

	}

	/**
	 * Adds the loop-footer HTML.
	 *
	 * @access public
	 * @since 1.0
	 */
	public function loop_footer() {

		if ( 'grid' === self::$args['layout'] || 'timeline' === self::$args['layout'] ) {
			echo '</div>';

			if ( self::$args['meta_info_combined'] > 0 ) {
				$inner_content = $this->read_more();
				$inner_content .= $this->grid_timeline_comments();

				echo '<div class="fusion-meta-info">' . $inner_content . '</div>';
			}
		}

		echo '</div>';
		echo '<div class="fusion-clearfix"></div>';

		if ( 0 < self::$args['meta_info_combined'] && in_array( self::$args['layout'], array( 'large', 'medium' ), true ) ) {
			echo '<div class="fusion-meta-info">' . avada_render_post_metadata( 'standard', $this->meta_info_settings ) . $this->read_more() . '</div>';
		}

		if ( self::$args['meta_all'] && in_array( self::$args['layout'], array( 'large-alternate', 'medium-alternate' ), true ) ) {
			echo $this->read_more();
		}

	}

	/**
	 * Adds the date box.
	 *
	 * @access public
	 * @since 1.0
	 */
	public function add_date_box() {

		$inner_content  = '<div ' . FusionBuilder::attributes( 'fusion-date-box updated' ) . '>';
		$inner_content .= '<span ' . FusionBuilder::attributes( 'fusion-date' ) . '>' . get_the_time( FusionBuilder::get_theme_option( 'alternate_date_format_day' ) ) . '</span>';
		$inner_content .= '<span ' . FusionBuilder::attributes( 'fusion-month-year' ) . '>' . get_the_time( FusionBuilder::get_theme_option( 'alternate_date_format_month_year' ) ) . '</span>';
		$inner_content .= '</div>';

		echo $inner_content;

	}

	/**
	 * Adds the format box.
	 *
	 * @access public
	 * @since 1.0
	 */
	public function add_format_box() {

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

		$inner_content  = '<div ' . FusionBuilder::attributes( 'fusion-format-box' ) . '>';
		$inner_content .= '<i ' . FusionBuilder::attributes( 'fusion-icon-' . $format_class ) . '></i>';
		$inner_content .= '</div>';

		echo $inner_content;

	}

	/**
	 * Adds the timeline date.
	 *
	 * @access public
	 * @since 1.0
	 * @param array $date_params The date parameters.
	 */
	public function timeline_date( $date_params ) {

		$defaults = array(
			'prev_post_month' => null,
			'post_month'      => null,
			'prev_post_year'  => null,
			'post_year'       => null,
		);

		$args = wp_parse_args( $date_params, $defaults );
		$inner_content = '';

		if ( $args['prev_post_month'] != $args['post_month'] || $args['prev_post_year'] != $args['post_year'] ) {

			if ( $this->post_count > 1 ) {
				$inner_content = '</div>';
			}

			$inner_content .= '<h3 ' . FusionBuilder::attributes( 'fusion-timeline-date' ) . '>' . get_the_date( FusionBuilder::get_theme_option( 'timeline_date_format' ) ) . '</h3>';
			$inner_content .= '<div class="fusion-collapse-month">';
		}

		echo $inner_content;

	}

	/**
	 * The timeline comments for grids.
	 *
	 * @access public
	 * @since 1.0
	 * @return string
	 */
	public function grid_timeline_comments() {

		if ( self::$args['meta_comments'] ) {

			$comments_icon = '<i ' . FusionBuilder::attributes( 'fusion-icon-bubbles' ) . '></i>&nbsp;';

			$comments = '<i class="fusion-icon-bubbles"></i>&nbsp;' . esc_attr__( 'Protected', 'fusion-builder' );

			if ( ! post_password_required( get_the_ID() ) ) {
				ob_start();
				comments_popup_link( $comments_icon . '0', $comments_icon . '1', $comments_icon . '%' );
				$comments = ob_get_contents();
				ob_get_clean();
			}

			return '<div ' . FusionBuilder::attributes( 'fusion-alignright' ) . '>' . $comments . '</div>';

		}

	}

	/**
	 * The read-more element.
	 *
	 * @access public
	 * @since 1.0
	 * @return string
	 */
	public function read_more() {

		if ( self::$args['meta_link'] ) {
			$inner_content = '';

			if ( self::$args['meta_read'] ) {

				$read_more_wrapper_class = 'fusion-alignright';
				if ( 'grid' === self::$args['layout'] || 'timeline' === self::$args['layout'] ) {
					$read_more_wrapper_class = 'fusion-alignleft';
				}

				$link_target = '';
				if ( 'yes' === fusion_get_page_option( 'link_icon_target', get_the_ID() ) || 'yes' === fusion_get_page_option( 'post_links_target', get_the_ID() ) ) {
					$link_target = ' target="_blank" rel="noopener noreferrer"';
				}

				$inner_content .= '<div ' . FusionBuilder::attributes( $read_more_wrapper_class ) . '>';
				$inner_content .= '<a class="fusion-read-more" href="' . get_permalink() . '"' . $link_target . '>';
				$inner_content .= apply_filters( 'avada_read_more_name', esc_attr__( 'Read More', 'fusion-builder' ) );
				$inner_content .= '</a>';
				$inner_content .= '</div>';

				if ( 'large-alternate' === self::$args['layout'] || 'medium-alternate' === self::$args['layout'] ) {
					$inner_content = '<div class="fusion-meta-info">' . $inner_content . '</div>';
				}
			}

			return $inner_content;
		}

	}

	/**
	 * The loop content.
	 *
	 * @access public
	 * @since 1.0
	 * @return void
	 */
	public function loop_content() {

		$content = fusion_get_post_content( '', self::$args['excerpt'], self::$args['excerpt_words'], self::$args['strip_html'] );

		echo '<div class="fusion-post-content-container">' . $content . '</div>';

	}

	/**
	 * The page links.
	 *
	 * @access public
	 * @since 1.0
	 */
	public function page_links() {
		avada_link_pages();
	}
}

new FusionSC_Blog();

// Add needed action and filter to make sure queries with offset have correct pagination.
add_action( 'pre_get_posts', 'fusion_query_offset', 1 );
/**
 * Adds offset to the query.
 *
 * @since 1.0
 * @param object $query The query.
 */
function fusion_query_offset( &$query ) {
	// Check if we are in a blog shortcode query and if offset is set.
	if ( isset( $query ) && is_array( $query->query ) && ! array_key_exists( 'blog_sc_query', $query->query ) ||
		 ! $query->query['offset']
	) {
		return;
	}

	// The query is paged.
	if ( $query->is_paged ) {
		// Manually determine page query offset (offset + ( current page - 1 ) x posts per page ).
		$page_offset = $query->query['offset'] + ( ( $query->query_vars['paged'] - 1 ) * $query->query['posts_per_page'] );

		// Apply adjusted page offset.
		$query->set( 'offset', $page_offset );

		// This is the first page, so we can just use the offset.
	} else {
		$query->set( 'offset', $query->query['offset'] );
	}
}

add_filter( 'found_posts', 'fusion_adjust_offset_pagination', 1, 2 );
/**
 * Adds an offset to the pagination.
 *
 * @since 1.0
 * @param int    $found_posts How many posts we found.
 * @param object $query       The query.
 * @return int
 */
function fusion_adjust_offset_pagination( $found_posts, $query ) {
	// Modification only in a blog shortcode query with set offset.
	if ( array_key_exists( 'blog_sc_query', $query->query ) && $query->query['offset'] ) {
		// Reduce found_posts count by the offset.
		return $found_posts - $query->query['offset'];
	}
	return $found_posts;
}

add_filter( 'redirect_canonical', 'fusion_blog_redirect_canonical' );
/**
 * Make sure that the blog pagination also works on front page.
 *
 * @since 1.0
 * @param string $redirect_url The URL we want to redirect to.
 * @return string
 */
function fusion_blog_redirect_canonical( $redirect_url ) {
	global $wp_rewrite, $wp_query;

	if ( $wp_rewrite->using_permalinks() ) {

		$paged = 1;
		// Check the query var.
		if ( get_query_var( 'paged' ) ) {
			$paged = get_query_var( 'paged' );
			// Check query paged.
		} elseif ( ! empty( $wp_query->query['paged'] ) ) {
			$paged = $wp_query->query['paged'];
		}

		if ( 1 < $paged ) {
			return false;
		}
	}

	return $redirect_url;
}

/**
 * Map shortcode to Fusion Builder.
 *
 * @since 1.0
 */
function fusion_element_blog() {
	fusion_builder_map( array(
		'name'       => esc_attr__( 'Blog', 'fusion-builder' ),
		'shortcode'  => 'fusion_blog',
		'icon'       => 'fusiona-blog',
		'preview'    => FUSION_BUILDER_PLUGIN_DIR . 'js/previews/fusion-blog-preview.php',
		'preview_id' => 'fusion-builder-block-module-blog-preview-template',
		'params'     => array(
			array(
				'type'        => 'select',
				'heading'     => esc_attr__( 'Blog Layout', 'fusion-builder' ),
				'description' => esc_attr__( 'Select the layout for the element', 'fusion-builder' ),
				'param_name'  => 'layout',
				'default'     => 'large',
				'value'       => array(
					esc_attr__( 'Large', 'fusion-builder' )            => 'large',
					esc_attr__( 'Medium', 'fusion-builder' )           => 'medium',
					esc_attr__( 'Large Alternate', 'fusion-builder' )  => 'large alternate',
					esc_attr__( 'Medium Alternate', 'fusion-builder' ) => 'medium alternate',
					esc_attr__( 'Grid', 'fusion-builder' )             => 'grid',
					esc_attr__( 'Timeline', 'fusion-builder' )         => 'timeline',
				),
			),
			array(
				'type'        => 'range',
				'heading'     => esc_attr__( 'Grid Layout # of Columns', 'fusion-builder' ),
				'description' => esc_attr__( 'Select whether to display the grid layout in 2, 3, 4, 5 or 6 column.', 'fusion-builder' ),
				'param_name'  => 'blog_grid_columns',
				'value'       => '3',
				'min'         => '1',
				'max'         => '6',
				'step'        => '1',
				'dependency'  => array(
					array(
						'element'  => 'layout',
						'value'    => 'grid',
						'operator' => '==',
					),
				),
			),
			array(
				'type'        => 'range',
				'heading'     => esc_attr__( 'Grid Layout Column Spacing', 'fusion-builder' ),
				'description' => esc_attr__( 'Insert the amount of spacing between blog grid posts.', 'fusion-builder' ),
				'param_name'  => 'blog_grid_column_spacing',
				'value'       => '40',
				'min'         => '0',
				'step'        => '1',
				'max'         => '300',
				'dependency'  => array(
					array(
						'element'  => 'layout',
						'value'    => 'grid',
						'operator' => '==',
					),
				),
			),
			array(
				'type'        => 'range',
				'heading'     => esc_attr__( 'Posts Per Page', 'fusion-builder' ),
				'description' => esc_attr__( 'Select number of posts per page.  Set to -1 to display all. Set to 0 to use number of posts from Settings > Reading.', 'fusion-builder' ),
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
				'dependency'  => array(
					array(
						'element'  => 'number_posts',
						'value'    => '-1',
						'operator' => '!=',
					),
				),
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
				'type'        => 'select',
				'heading'     => esc_attr__( 'Order By', 'fusion-builder' ),
				'description' => esc_attr__( 'Defines how posts should be ordered.', 'fusion-builder' ),
				'param_name'  => 'orderby',
				'default'     => 'date',
				'value'       => array(
					esc_attr__( 'Date', 'fusion-builder' )               => 'date',
					esc_attr__( 'Post Title', 'fusion-builder' )         => 'title',
					esc_attr__( 'Post Slug', 'fusion-builder' )          => 'name',
					esc_attr__( 'Author', 'fusion-builder' )             => 'author',
					esc_attr__( 'Number of Comments', 'fusion-builder' ) => 'comment_count',
					esc_attr__( 'Last Modified', 'fusion-builder' )      => 'modified',
					esc_attr__( 'Random', 'fusion-builder' )             => 'rand',
				),
			),
			array(
				'type'        => 'radio_button_set',
				'heading'     => esc_attr__( 'Order', 'fusion-builder' ),
				'description' => esc_attr__( 'Defines the sorting order of posts.', 'fusion-builder' ),
				'param_name'  => 'order',
				'default'     => 'DESC',
				'value'       => array(
					esc_attr__( 'Descending', 'fusion-builder' ) => 'DESC',
					esc_attr__( 'Ascending', 'fusion-builder' )  => 'ASC',
				),
				'dependency'  => array(
					array(
						'element'  => 'orderby',
						'value'    => 'rand',
						'operator' => '!=',
					),
				),
			),
			array(
				'type'        => 'radio_button_set',
				'heading'     => esc_attr__( 'Show Thumbnail', 'fusion-builder' ),
				'description' => esc_attr__( 'Display the post featured image.', 'fusion-builder' ),
				'param_name'  => 'thumbnail',
				'default'     => 'yes',
				'value'       => array(
					esc_attr__( 'Yes', 'fusion-builder' ) => 'yes',
					esc_attr__( 'No', 'fusion-builder' )  => 'no',
				),
			),
			array(
				'type'        => 'radio_button_set',
				'heading'     => esc_attr__( 'Show Title', 'fusion-builder' ),
				'description' => esc_attr__( 'Display the post title below the featured image.', 'fusion-builder' ),
				'param_name'  => 'title',
				'default'     => 'yes',
				'value'       => array(
					esc_attr__( 'Yes', 'fusion-builder' ) => 'yes',
					esc_attr__( 'No', 'fusion-builder' )  => 'no',
				),
			),
			array(
				'type'        => 'radio_button_set',
				'heading'     => esc_attr__( 'Link Title To Post', 'fusion-builder' ),
				'description' => esc_attr__( 'Choose if the title should be a link to the single post page.', 'fusion-builder' ),
				'default'     => 'yes',
				'param_name'  => 'title_link',
				'value'       => array(
					esc_attr__( 'Yes', 'fusion-builder' ) => 'yes',
					esc_attr__( 'No', 'fusion-builder' )  => 'no',
				),
				'dependency'  => array(
					array(
						'element'  => 'title',
						'value'    => 'yes',
						'operator' => '==',
					),
				),
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
				'default' => 'yes',
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
				'heading'     => esc_attr__( 'Strip HTML from Posts Content', 'fusion-builder' ),
				'description' => esc_attr__( 'Strip HTML from the post excerpt.', 'fusion-builder' ),
				'param_name'  => 'strip_html',
				'default'     => 'yes',
				'value'       => array(
					esc_attr__( 'Yes', 'fusion-builder' ) => 'yes',
					esc_attr__( 'No', 'fusion-builder' )  => 'no',
				),
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
				'heading'     => esc_attr__( 'Show Meta Info', 'fusion-builder' ),
				'description' => esc_attr__( 'Choose to show all meta data.', 'fusion-builder' ),
				'param_name'  => 'meta_all',
				'default'     => 'yes',
				'value'       => array(
					esc_attr__( 'Yes', 'fusion-builder' ) => 'yes',
					esc_attr__( 'No', 'fusion-builder' )  => 'no',
				),
			),
			array(
				'type'        => 'radio_button_set',
				'heading'     => esc_attr__( 'Show Author Name', 'fusion-builder' ),
				'description' => esc_attr__( 'Choose to show the author.', 'fusion-builder' ),
				'param_name'  => 'meta_author',
				'default'     => 'yes',
				'value'       => array(
					esc_attr__( 'Yes', 'fusion-builder' ) => 'yes',
					esc_attr__( 'No', 'fusion-builder' )  => 'no',
				),
				'dependency'  => array(
					array(
						'element'  => 'meta_all',
						'value'    => 'yes',
						'operator' => '==',
					),
				),
			),
			array(
				'type'        => 'radio_button_set',
				'heading'     => esc_attr__( 'Show Categories', 'fusion-builder' ),
				'description' => esc_attr__( 'Choose to show the categories.', 'fusion-builder' ),
				'param_name'  => 'meta_categories',
				'default'     => 'yes',
				'value'       => array(
					esc_attr__( 'Yes', 'fusion-builder' ) => 'yes',
					esc_attr__( 'No', 'fusion-builder' )  => 'no',
				),
				'dependency'  => array(
					array(
						'element'  => 'meta_all',
						'value'    => 'yes',
						'operator' => '==',
					),
				),
			),
			array(
				'type'        => 'radio_button_set',
				'heading'     => esc_attr__( 'Show Comment Count', 'fusion-builder' ),
				'description' => esc_attr__( 'Choose to show the comments.', 'fusion-builder' ),
				'param_name'  => 'meta_comments',
				'default'     => 'yes',
				'value'       => array(
					esc_attr__( 'Yes', 'fusion-builder' ) => 'yes',
					esc_attr__( 'No', 'fusion-builder' )  => 'no',
				),
				'dependency'  => array(
					array(
						'element'  => 'meta_all',
						'value'    => 'yes',
						'operator' => '==',
					),
				),
			),
			array(
				'type'        => 'radio_button_set',
				'heading'     => esc_attr__( 'Show Date', 'fusion-builder' ),
				'description' => esc_attr__( 'Choose to show the date.', 'fusion-builder' ),
				'param_name'  => 'meta_date',
				'default'     => 'yes',
				'value'       => array(
					esc_attr__( 'Yes', 'fusion-builder' ) => 'yes',
					esc_attr__( 'No', 'fusion-builder' )  => 'no',
				),
				'dependency'  => array(
					array(
						'element'  => 'meta_all',
						'value'    => 'yes',
						'operator' => '==',
					),
				),
			),
			array(
				'type'        => 'radio_button_set',
				'heading'     => esc_attr__( 'Show Read More Link', 'fusion-builder' ),
				'description' => esc_attr__( 'Choose to show the Read More link.', 'fusion-builder' ),
				'param_name'  => 'meta_link',
				'default'     => 'yes',
				'value'       => array(
					esc_attr__( 'Yes', 'fusion-builder' ) => 'yes',
					esc_attr__( 'No', 'fusion-builder' )  => 'no',
				),
				'dependency'  => array(
					array(
						'element'  => 'meta_all',
						'value'    => 'yes',
						'operator' => '==',
					),
				),
			),
			array(
				'type'        => 'radio_button_set',
				'heading'     => esc_attr__( 'Show Tags', 'fusion-builder' ),
				'description' => esc_attr__( 'Choose to show the tags.', 'fusion-builder' ),
				'param_name'  => 'meta_tags',
				'default'     => 'yes',
				'value'       => array(
					esc_attr__( 'Yes', 'fusion-builder' ) => 'yes',
					esc_attr__( 'No', 'fusion-builder' )  => 'no',
				),
				'dependency'  => array(
					array(
						'element'  => 'meta_all',
						'value'    => 'yes',
						'operator' => '==',
					),
				),
			),
			array(
				'type'        => 'radio_button_set',
				'heading'     => esc_attr__( 'Show Pagination', 'fusion-builder' ),
				'description' => esc_attr__( 'Show numerical pagination boxes.', 'fusion-builder' ),
				'param_name'  => 'paging',
				'default'     => 'yes',
				'value'       => array(
					esc_attr__( 'Yes', 'fusion-builder' ) => 'yes',
					esc_attr__( 'No', 'fusion-builder' )  => 'no',
				),
			),
			array(
				'type'        => 'radio_button_set',
				'heading'     => esc_attr__( 'Pagination Type', 'fusion-builder' ),
				'description' => esc_attr__( 'Choose the type of pagination.', 'fusion-builder' ),
				'param_name'  => 'scrolling',
				'default'     => 'pagination',
				'value'       => array(
					esc_attr__( 'Pagination', 'fusion-builder' )         => 'pagination',
					esc_attr__( 'Infinite Scrolling', 'fusion-builder' ) => 'infinite',
					esc_attr__( 'Load More Button', 'fusion-builder' )   => 'load_more_button',
				),
				'dependency'  => array(
					array(
						'element'  => 'paging',
						'value'    => 'yes',
						'operator' => '==',
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
			),
			array(
				'type'        => 'textfield',
				'heading'     => esc_attr__( 'CSS ID', 'fusion-builder' ),
				'description' => esc_attr__( 'Add an ID to the wrapping HTML element.', 'fusion-builder' ),
				'param_name'  => 'id',
				'value'       => '',
			),
		),
	) );
}
add_action( 'fusion_builder_before_init', 'fusion_element_blog' );
