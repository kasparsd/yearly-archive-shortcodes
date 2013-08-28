<?php
/*
Plugin Name: Yearly Archive Shortcode
Description: Provides a <code>[yearly_archive]</code> shortcode
Author: Kaspars Dambis
*/

add_shortcode( 'yearly_archive', 'yas_shortcode' );

function yas_shortcode( $args ) {

	$maybe_cache = get_transient( 'yearly_archive' );

	if ( ! empty( $maybe_cache ) )
		return $maybe_cache;

	$posts = get_posts( array( 'posts_per_page' => -1 ) );

	if ( empty( $posts ) )
		return;

	$archive = array();
	$render = array();

	foreach ( $posts as $post )
		$archive[ date( 'Y', strtotime( $post->post_date ) ) ][] = sprintf(
			'<li>
				<span class="date" title="%s">%s</span>
				<a href="%s" class="link">%s</a>
			</li>',
			esc_attr( date( 'r', strtotime( $post->post_date ) ) ),
			esc_html( date( 'M j Y', strtotime( $post->post_date ) ) ),
			get_permalink( $post ),
			apply_filters( 'the_title', $post->post_title )
		);

	foreach ( $archive as $year => $year_posts )
		$render[] = sprintf(
			'<h2>%s</h2>
			<ul>%s</ul>',
			$year,
			implode( "\n", $year_posts )
		);

	$html = sprintf(
		'<div class="yearly-archive">%s</div>',
		implode( "\n", $render )
	);

	set_transient( 'yearly_archive', $html, 60 * 60 * 24 );

	return $html;
}
