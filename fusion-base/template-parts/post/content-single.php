<?php
/**
 * Template part for displaying single posts
 *
 * @link https://codex.wordpress.org/Template_Hierarchy
 *
 * @package WordPress
 * @subpackage Fusion_Base
 * @since 1.2.0
 */
?>

<article id="post-<?php the_ID(); ?>" <?php post_class('container'); ?>>
	<h2 class="post-title"><a href="<?php the_permalink(); ?>" title="<?php the_title_attribute(); ?>"><?php the_title(); ?></a></h2>
	<?php the_content(); ?>
	<?php wp_link_pages(); ?>
</article>