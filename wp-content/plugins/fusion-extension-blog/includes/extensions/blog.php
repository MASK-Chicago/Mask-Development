<?php
/**
 * @package Fusion_Extension_Blog
 */

/**
 * Blog Extension.
 *
 * Function for adding a Blog element to the Fusion Engine
 *
 * @since 1.0.0
 */

/**
 * Map Shortcode
 */

add_action('init', 'fsn_init_blog', 12);
function fsn_init_blog() {
 
	if (function_exists('fsn_map')) {
		$image_sizes_array = fsn_get_image_sizes();
	
		fsn_map(array(
			'name' => __('Blog', 'fusion-extension-blog'),
			'shortcode_tag' => 'fsn_blog',
			'description' => __('Add a blog. Displays a roll of all posts created on the site. Show or hide any of the options below using the checkboxes.', 'fusion-extension-blog'),
			'icon' => 'question_answer',
			'params' => array(
				array(
					'type' => 'checkbox',
					'param_name' => 'show_featured_image',
					'label' => __('Featured Image', 'fusion-extension-blog')
				),
				array(
					'type' => 'select',
					'options' => $image_sizes_array,
					'param_name' => 'featured_image_size',
					'label' => __('Featured Image Size', 'fusion-extension-blog'),
					'help' => __('Default is "Large".', 'fusion-extension-blog'),
					'dependency' => array(
						'param_name' => 'show_featured_image',
						'not_empty' => true
					)
				),
				array(
					'type' => 'checkbox',
					'param_name' => 'show_excerpt',
					'label' => __('Excerpt', 'fusion-extension-blog')
				),
				array(
					'type' => 'checkbox',
					'param_name' => 'show_author',
					'label' => __('Author', 'fusion-extension-blog')
				),
				array(
					'type' => 'checkbox',
					'param_name' => 'show_date',
					'label' => __('Date', 'fusion-extension-blog')
				),
				array(
					'type' => 'checkbox',
					'param_name' => 'show_categories',
					'label' => __('Categories', 'fusion-extension-blog')
				),
				array(
					'type' => 'checkbox',
					'param_name' => 'show_tags',
					'label' => __('Tags', 'fusion-extension-blog')
				)
			)
		));
	}
}

/**
 * Output Shortcode
 */

function fsn_blog_shortcode( $atts ) {
	extract( shortcode_atts( array(
		'show_featured_image' => '',
		'featured_image_size' => 'large',
		'show_excerpt' => '',
		'show_author' => '',
		'show_date' => '',
		'show_categories' => '',
		'show_tags' => ''
	), $atts ) );
	
	$output = '<div class="fsn-blog '. fsn_style_params_class($atts) .'">';
		$posts_per_page = get_option('posts_per_page');
		$paged = get_query_var( 'paged' ) ? get_query_var( 'paged' ) : 1;
		$args = apply_filters('fsn_blog_query_args', array(
			'post_type' => 'post',
			'post_status' => 'publish',
			'posts_per_page' => $posts_per_page,
			'paged' => $paged
		));
		$blog_query = new WP_Query($args);
		
		ob_start();
		if($blog_query->have_posts()) : while($blog_query->have_posts()) : $blog_query->the_post();
		?>
		<article id="post-<?php the_ID(); ?>" <?php post_class(); ?>>
			<?php if (has_post_thumbnail() && !empty($show_featured_image)) {
				$attachment_id = get_post_thumbnail_id();
				echo '<a href="'. get_permalink() .'" class="blogroll-post-image">'. fsn_get_dynamic_image($attachment_id, 'img-responsive', $featured_image_size, 'mobile') .'</a>';
			} ?>
			<h2><a href="<?php the_permalink(); ?>" title="<?php the_title_attribute(); ?>"><?php the_title(); ?></a></h2>
			<?php !empty($show_excerpt) ? the_excerpt() : ''; ?>
			<?php if (!empty($show_author) || !empty($show_date) || !empty($show_categories) || !empty($show_tags)) : ?>
			<footer class="post-metadata">
				<?php 
					$author = !empty($show_author) ? true : false;
					$date = !empty($show_date) ? true : false;
					$categories = !empty($show_categories) ? true : false;
					$tags = !empty($show_tags) ? true : false;
					$args = array(
						'author' => $author,
						'date' => $date,
						'categories' => $categories,
						'tags' => $tags
					);
					echo fsn_get_post_meta($args); 
				?>
				<?php edit_post_link( __( 'edit', 'fusion-extension-blog' ), '', '', 0, 'post-edit-link btn btn-default btn-xs'); ?>
			</footer>
			<?php endif; ?>
		</article>
		<?php endwhile;
		//pagination
		fsn_pagination($blog_query->max_num_pages);
		
		else: ?>
		<h2><?php _e('No Posts Found', 'fusion-extension-blog') ;?></h2>
		<p><?php _e('Sorry, there\'s nothing here.', 'fusion-extension-blog'); ?></p>
		<?php endif;
		
		//reset postdata
		wp_reset_postdata();
		$output .= ob_get_clean();
	$output .= '</div>';
	
	return $output;
}
add_shortcode('fsn_blog', 'fsn_blog_shortcode');
 
?>