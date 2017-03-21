<?php

/**
 * Shortcode class.
 *
 * @package fusion-builder
 * @since 1.0
 */
class FusionSC_Faq {

	/**
	 * FAQ counter.
	 *
	 * @access private
	 * @since 1.0
	 * @var int
	 */
	private $faq_counter = 1;

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
		add_shortcode( 'fusion_faq', array( $this, 'render' ) );
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
				'hide_on_mobile' => fusion_builder_default_visibility( 'string' ),
				'class'          => '',
				'id'             => '',
				'cats_slug'      => '',
				'exclude_cats'   => '',
				'featured_image' => ( class_exists( 'Avada' ) ) ? Avada()->settings->get( 'faq_featured_image' ) : '',
				'filters'        => ( class_exists( 'Avada' ) ) ? Avada()->settings->get( 'faq_filters' ) : '',
			), $args
		);

		$defaults['cat_slugs'] = $defaults['cats_slug'];

		extract( $defaults );

		self::$args = $defaults;

		// Transform $cat_slugs to array.
		if ( $cat_slugs ) {
			$cat_slugs = preg_replace( '/\s+/', '', $cat_slugs );
			$cat_slugs = explode( ',', $cat_slugs );
		} else {
			$cat_slugs = array();
		}

		// Transform $cats_to_exclude to array.
		if ( $exclude_cats ) {
			$cats_to_exclude = preg_replace( '/\s+/', '', $exclude_cats );
			$cats_to_exclude = explode( ',' , $cats_to_exclude );
		} else {
			$cats_to_exclude = array();
		}

		// Initialize the query array.
		$args = array(
			'post_type'      => 'avada_faq',
			'posts_per_page' => -1,
			'has_password'   => false,
		);

		// Check if the are categories that should be excluded.
		if ( ! empty( $cats_to_exclude ) ) {

			// Exclude the correct cats from tax_query.
			$args['tax_query'] = array(
				array(
					'taxonomy' => 'faq_category',
					'field'    => 'slug',
					'terms'    => $cats_to_exclude,
					'operator' => 'NOT IN',
				),
			);

			// Include the correct cats in tax_query.
			if ( ! empty( $cat_slugs ) ) {
				$args['tax_query']['relation'] = 'AND';
				$args['tax_query'][]           = array(
					'taxonomy' => 'faq_category',
					'field'    => 'slug',
					'terms'    => $cat_slugs,
					'operator' => 'IN',
				);
			}
		} else {
			// Include the cats from $cat_slugs in tax_query.
			if ( ! empty( $cat_slugs ) ) {
				$args['tax_query'] = array(
					array(
						'taxonomy' => 'faq_category',
						'field'    => 'slug',
						'terms'    => $cat_slugs,
					),
				);
			}
		}

		$class = fusion_builder_visibility_atts( $hide_on_mobile, $class );

		$html = '<div class="fusion-faq-shortcode ' . $class . '">';

		// Setup the filters.
		$faq_terms = get_terms( 'faq_category' );

		// Check if we should display filters.
		if ( $faq_terms && 'no' != $filters ) {

			$html .= '<ul class="fusion-filters clearfix">';

			// Check if the "All" filter should be displayed.
			$first_filter = true;
			if ( 'yes' == $filters ) {
				$html .= '<li class="fusion-filter fusion-filter-all fusion-active">';
				$html .= '<a data-filter="*" href="#">' . apply_filters( 'fusion_faq_all_filter_name', esc_html( 'All', 'fusion-builder' ) ) . '</a>';
				$html .= '</li>';
				$first_filter = false;
			}

			// Loop through the terms to setup all filters.
			foreach ( $faq_terms as $faq_term ) {
				// Only display filters of non excluded categories.
				if ( ! in_array( $faq_term->slug, $cats_to_exclude ) ) {
					// Check if current term is part of chosen terms, or if no terms at all have been chosen.
					if ( ( ! empty( $cat_slugs ) && in_array( $faq_term->slug, $cat_slugs ) ) || empty( $cat_slugs ) ) {
						// If the "All" filter is disabled, set the first real filter as active.
						if ( $first_filter ) {
							$html .= '<li class="fusion-filter fusion-active">';
							$html .= '<a data-filter=".' . urldecode( $faq_term->slug ) . '" href="#">' . $faq_term->name . '</a>';
							$html .= '</li>';
							$first_filter = false;
						} else {
							$html .= '<li class="fusion-filter fusion-hidden">';
							$html .= '<a data-filter=".' . urldecode( $faq_term->slug ) . '" href="#">' . $faq_term->name . '</a>';
							$html .= '</li>';
						}
					}
				}
			}

			$html .= '</ul>';
		}

		// Setup the posts.
		$faq_items = fusion_builder_cached_query( $args );

		if ( ! $faq_items->have_posts() ) {
			return fusion_builder_placeholder( 'avada_faq', 'FAQ posts' );
		}

		$html .= '<div class="fusion-faqs-wrapper">';
		$html .= '<div class="accordian fusion-accordian">';
		$html .= '<div class="panel-group" id="accordian-' . $this->faq_counter . '">';

		$this_post_id = get_the_ID();

		while ( $faq_items->have_posts() ) :  $faq_items->the_post();

			// If used on a faq item itself, thzis is needed to prevent an infinite loop.
			if ( get_the_ID() === $this_post_id ) {
				continue;
			}

			// Get all terms of the post and it as classes; needed for filtering.
			$post_classes = '';
			$post_id = get_the_ID();
			$post_terms = get_the_terms( $post_id, 'faq_category' );
			if ( $post_terms ) {
				foreach ( $post_terms as $post_term ) {
					$post_classes .= urldecode( $post_term->slug ) . ' ';
				}
			}

			$html .= '<div class="fusion-panel panel-default fusion-faq-post ' . $post_classes . '">';
			// Get the rich snippets for the post.
			$html .= avada_render_rich_snippets_for_pages();

			$html .= '<div class="panel-heading">';
			$html .= '<h4 class="panel-title toggle">';
			$html .= '<a data-toggle="collapse" class="collapsed" data-parent="#accordian-' . $this->faq_counter . '" data-target="#collapse-' . $this->faq_counter . '-' . $post_id . '" href="#collapse-' . $this->faq_counter . '-' . $post_id . '">';
			$html .= '<div class="fusion-toggle-icon-wrapper"><i class="fa-fusion-box"></i></div>';
			$html .= '<div class="fusion-toggle-heading">' . get_the_title() . '</div>';
			$html .= '</a>';
			$html .= '</h4>';
			$html .= '</div>';

			$html .= '<div id="collapse-' . $this->faq_counter . '-' . $post_id . '" class="panel-collapse collapse">';
			$html .= '<div class="panel-body toggle-content post-content">';

			// Render the featured image of the post.
			if ( ( '1' == $featured_image || 'yes' == $featured_image ) && has_post_thumbnail() ) {
				$featured_image_src = wp_get_attachment_image_src( get_post_thumbnail_id(), 'full' );

				if ( $featured_image_src[0] ) {
					$html .= '<div class="fusion-flexslider flexslider fusion-flexslider-loading post-slideshow fusion-post-slideshow">';
					$html .= '<ul class="slides">';
					$html .= '<li>';
					$html .= '<a href="' . $featured_image_src[0] . '" data-rel="iLightbox[gallery]" data-title="' . get_post_field( 'post_title', get_post_thumbnail_id() ) . '" data-caption="' . get_post_field( 'post_excerpt', get_post_thumbnail_id() ) . '">';
					$html .= '<span class="screen-reader-text">' . esc_attr__( 'View Larger Image', 'fusion-builder' ) . '</span>';
					$html .= get_the_post_thumbnail( $post_id, 'blog-large' );
					$html .= '</a>';
					$html .= '</li>';
					$html .= '</ul>';
					$html .= '</div>';
				}
			}
			ob_start();
			the_content();
			$html .= ob_get_clean();
			$html .= '</div>';
			$html .= '</div>';
			$html .= '</div>';

		endwhile; // Loop through faq_items.
		wp_reset_postdata();

		$html .= '</div>';
		$html .= '</div>';
		$html .= '</div>';

		$html .= '</div>';

		$this->faq_counter++;

		return $html;

	}

	/**
	 * Gets the query arguments.
	 *
	 * @access private
	 * @since 1.0
	 * @param array $term_slugs       The term slugs.
	 * @param array $terms_to_exclude The terms we wish to exclude.
	 */
	private function get_query_args( $term_slugs, $terms_to_exclude ) {

	}
}
new FusionSC_Faq();

/**
 * Map shortcode to Fusion Builder.
 *
 * @since 1.0
 */
function fusion_element_faq() {
	fusion_builder_map( array(
		'name'       => esc_attr__( 'FAQ', 'fusion-builder' ),
		'shortcode'  => 'fusion_faq',
		'icon'       => 'fa fa-lg fa-info-circle',
		'preview'    => FUSION_BUILDER_PLUGIN_DIR . 'js/previews/fusion-faq-preview.php',
		'preview_id' => 'fusion-builder-block-module-faq-preview-template',
		'params'     => array(
			array(
				'type'        => 'radio_button_set',
				'heading'     => esc_attr__( 'Display Filters', 'fusion-builder' ),
				'description' => esc_attr__( 'Display the FAQ filters.', 'fusion-builder' ),
				'param_name'  => 'filters',
				'value'       => array(
					esc_attr__( 'Default', 'fusion-builder' )    => '',
					esc_attr__( 'Show', 'fusion-builder' )       => 'yes',
					__( 'Show without "All"', 'fusion-builder' ) => 'yes-without-all',
					esc_attr__( 'Hide', 'fusion-builder' )       => 'no',
				),
				'default'     => '',
			),
			array(
				'type'        => 'radio_button_set',
				'heading'     => esc_attr__( 'Display Featured Images', 'fusion-builder' ),
				'description' => esc_attr__( 'Display the FAQ featured images.', 'fusion-builder' ),
				'param_name'  => 'featured_image',
				'value'       => array(
					esc_attr__( 'Default', 'fusion-builder' ) => '',
					esc_attr__( 'Yes', 'fusion-builder' )     => 'yes',
					esc_attr__( 'No', 'fusion-builder' )      => 'no',
				),
				'default'     => '',
			),
			array(
				'type'        => 'multiple_select',
				'heading'     => esc_attr__( 'Categories', 'fusion-builder' ),
				'description' => esc_attr__( 'Select categories to include or leave blank for all.', 'fusion-builder' ),
				'param_name'  => 'cats_slug',
				'value'       => fusion_builder_shortcodes_categories( 'faq_category' ),
				'default'     => '',
			),
			array(
				'type'        => 'multiple_select',
				'heading'     => esc_attr__( 'Exclude Categories', 'fusion-builder' ),
				'description' => esc_attr__( 'Select categories to exclude.', 'fusion-builder' ),
				'param_name'  => 'exclude_cats',
				'value'       => fusion_builder_shortcodes_categories( 'faq_category' ),
				'default'     => '',
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
add_action( 'fusion_builder_before_init', 'fusion_element_faq' );
