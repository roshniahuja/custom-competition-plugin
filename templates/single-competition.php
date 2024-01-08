<?php
/**
 * The template for displaying all single posts
 */

get_header();

/* Start the Loop */
while ( have_posts() ) :
	the_post();
	$competition_slug = get_post_field( 'post_name', get_the_ID() );

	$url = home_url( "/$competition_slug/submit-entry" );
	// Display the competition details
	echo '<div>';
	echo '<h2>' . esc_html( get_the_title() ) . '</h2>';
	echo '<p>' . wp_kses_post ( get_the_content() ) . '</p>';
	echo '<a href="' . esc_url( $url ) . '" class="submit-entry-button">Submit Entry</a>';
	echo '</div>';
	
endwhile; // End of the loop.

get_footer();
