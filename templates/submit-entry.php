<?php
/* Template Name: Submit Entry Page */
get_header();

$competition_transient_value = get_transient('competition_transient');
$post_slug = get_post_field( 'post_name', $competition_transient_value ); 
?>

<div id="primary" class="content-area">
    <main id="main" class="site-main">
        <article id="post-<?php the_ID(); ?>" <?php post_class(); ?>>
            <header class="entry-header">
                <h1 class="entry-title">Submit entry for: <?php echo esc_html ( get_the_title($competition_transient_value) ); ?></h1>
            </header>
            
            <div class="entry-content">
                <!-- Use $competition_slug as needed in your form processing -->
                

                <!-- Submit Entry Form -->
                <form id="submit-entry-form" action="" method="post">
                    <input type="hidden" name="action" value="submit_entry">
                    <input type="hidden" name="competition_slug" value="<?php echo esc_html ( $post_slug ); ?>">
                    
                    <?php

                    if ($competition_transient_value) {
                            // Use $competition_transient_value as needed
                            // For example, you can use it to set the value of the competition ID in your form
                            echo '<input type="hidden" name="competition_id" value="' . esc_attr($competition_transient_value) . '">';
                        }
                        ?>

                    <label for="first_name">First Name:</label>
                    <input type="text" name="first_name" id="first_name" required>

                    <label for="last_name">Last Name:</label>
                    <input type="text" name="last_name" id="last_name" required>

                    <label for="email">Email:</label>
                    <input type="email" name="email" id="email" required>

                    <label for="phone">Phone:</label>
                    <input type="tel" name="phone" id="phone" required>

                    <label for="description">Description:</label>
                    <textarea name="description" id="description" rows="4" required></textarea>

                    <input type="submit" value="Submit Entry">
                </form>
            </div>
        </article>
    </main>
</div>

<?php get_footer(); ?>