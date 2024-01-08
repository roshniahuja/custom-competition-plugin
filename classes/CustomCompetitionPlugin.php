<?php
namespace CustomCompetitionPlugin;

use WP_Query;

class CustomCompetitionPlugin {

    public function __construct() {
        // Hook actions and filters.
        add_action( 'init', array( $this, 'register_post_types' ) );
        add_action( 'add_meta_boxes', array( $this, 'add_custom_fields' ) );
        add_action( 'save_post', array( $this, 'save_custom_fields' ) );
        add_shortcode( 'competition_list', array( $this, 'competition_list_shortcode' ) );
        add_action( 'wp_enqueue_scripts', array( $this, 'enqueue_scripts' ) );
        add_action( 'admin_enqueue_scripts', array( $this, 'enqueue_admin_styles' ) );
        add_action( 'wp_ajax_handle_entry_submission', array( $this, 'handle_ajax_submission' ) );
        add_action( 'wp_ajax_nopriv_handle_entry_submission', array( $this, 'handle_ajax_submission' ) );
        add_filter( 'template_include', array( $this, 'load_single_competition_template' ) );
        add_action( 'wp', array( $this, 'set_competition_transient' ) );
    }

    // Register Competitions post type.
    public function register_post_types() {
        register_post_type( 'competitions',
            array(
                'labels' => array(
                    'name'          => __( 'Competitions' ),
                    'singular_name' => __( 'Competition' ),
                ),
                'public'       => true,
                'has_archive'  => true,
                'show_in_rest' => true,
                'supports'     => array( 'title', 'editor', 'thumbnail', 'comments', 'page-attributes' )
            )
        );

        register_post_type( 'entries',
            array(
                'labels' => array(
                    'name'          => __( 'Entries' ),
                    'singular_name' => __( 'Entry' ),
                ),
                'public'   => true,
                'supports' => array( 'title' ),
            )
        );
    }

    // Add custom fields to Entries post type.
    public function add_custom_fields() {
        add_meta_box( 'custom_entries_fields', 'Entry Details', array( $this, 'custom_fields_callback' ), 'entries', 'normal', 'high' );
    }

    // Callback function to display custom fields.
    public function custom_fields_callback( $post ) {
        // Retrieve existing values for the fields
        $first_name     = get_post_meta( $post->ID, '_first_name', true );
        $last_name      = get_post_meta( $post->ID, '_last_name', true );
        $email          = get_post_meta( $post->ID, '_email', true );
        $phone          = get_post_meta( $post->ID, '_phone', true );
        $description    = get_post_meta( $post->ID, '_description', true );
        $competition_id = get_post_meta( $post->ID, '_competition_id', true );

        // Display the HTML for custom fields.
        ?>
        <p>
            <label for="first_name">First Name:</label>
            <input type="text" name="first_name" value="<?php echo esc_attr( $first_name ); ?>">
        </p>
        <p>
            <label for="last_name">Last Name:</label>
            <input type="text" name="last_name" value="<?php echo esc_attr( $last_name ); ?>">
        </p>
        <p>
            <label for="email">Email:</label>
            <input type="email" name="email" value="<?php echo esc_attr( $email ); ?>">
        </p>
        <p>
            <label for="phone">Phone:</label>
            <input type="tel" name="phone" value="<?php echo esc_attr( $phone ); ?>">
        </p>
        <p>
            <label for="description">Description:</label>
            <textarea name="description"><?php echo esc_textarea( $description ); ?></textarea>
        </p>
        <p>
            <label for="competition_id">Competition ID:</label>
            <input type="text" name="competition_id" value="<?php echo esc_attr( $competition_id ); ?>">
        </p>
        <?php
    }

    // Save custom fields data.
    public function save_custom_fields( $post_id ) {

        // Verify nonce.
        if ( !isset( $_POST['nonce'] ) || ! wp_verify_nonce( sanitize_text_field( $_POST['nonce'] ), 'custom_competition_plugin_nonce' ) ) {
            return;
        }

       // Check if the user has permissions to save data.
        if ( !current_user_can( 'edit_post', $post_id ) ) {
            return;
        }

        // Define an array of custom fields to save.
        $fields = array( 'first_name', 'last_name', 'email', 'phone', 'description', 'competition_id' );

        // Loop through the fields and save their values.
        foreach ( $fields as $field ) {
            if ( isset( $_POST[$field] ) ) {
                update_post_meta( $post_id, '_' . $field, sanitize_text_field( $_POST[$field] ) );
            }
        }
    }

    // Create a page with a list of competitions.
    public function competition_list_page() {
         $args = array(
            'post_type'      => 'competitions',
            'posts_per_page' => -1,
        );

        $competitions = new \WP_Query( $args );
        if ( $competitions->have_posts() ) :
            while ( $competitions->have_posts() ) : $competitions->the_post();
                ?>
                <div>
                    <?php
                    // Display the featured image
                    if ( has_post_thumbnail() ) {
                        the_post_thumbnail( 'mediu' );
                    } else {
                    // Display a placeholder image if no featured image is set
                        echo '<img src="' . esc_url( plugin_dir_url( __DIR__ ) ). 'images/placeholder.png" alt="Placeholder Image">';
                    }
                    ?>
                    <h2><?php the_title(); ?></h2>
                    <p><?php the_content(); ?></p>
                    <a href="<?php echo esc_url( get_permalink() ); ?>">View Details</a>
                </div>
                <?php
            endwhile;
            wp_reset_postdata();
        else :
            echo 'No competitions found.';
        endif;
    }

    
    // Shortcode to display competition list.
    public function competition_list_shortcode() {
        ob_start();
        $this->competition_list_page();
        return ob_get_clean();
    }

    // Enqueue scripts and styles
    public function enqueue_scripts() {
        wp_enqueue_style( 'custom-competition-plugin-style', plugin_dir_url( __DIR__ ) . 'css/styles.css', array(), '1.0', 'all' );
        wp_enqueue_script('jquery');
        wp_enqueue_script( 'custom-competition-plugin-script', plugin_dir_url( __DIR__ ) . 'js/scripts.js', array( 'jquery' ), '1.0', true );
        wp_localize_script( 'custom-competition-plugin-script', 'custom_competition_plugin_ajax', array(
            'ajax_url' => admin_url( 'admin-ajax.php' ),
            'nonce'    => wp_create_nonce( 'custom_competition_plugin_nonce' )
        ) );
    }

    // Add this method to your class.
    public function enqueue_admin_styles() {
        // Enqueue your admin stylesheet.
        wp_enqueue_style( 'custom-competition-plugin-admin-style', plugin_dir_url( __DIR__ ) . 'css/admin-style.css', array(), '1.0' );
    }

    // Handle AJAX submission.
    public function handle_ajax_submission() {

        // Verify nonce.
        $nonce = isset( $_POST['nonce'] ) ? sanitize_text_field( $_POST['nonce'] ) : '';
        if ( ! wp_verify_nonce( $nonce, 'custom_competition_plugin_nonce' ) ) {
            // Return an error message if nonce verification fails.
            echo wp_json_encode( array( 'status' => 'error', 'message' => 'Nonce verification failed.') );
            // Always exit to avoid extra output.
            wp_die();
        }

        // Process the form data.
        $competition_id = isset($_POST['competition_id']) ? sanitize_text_field($_POST['competition_id']) : '';
        $first_name = isset($_POST['first_name']) ? sanitize_text_field($_POST['first_name']) : '';
        $last_name = isset($_POST['last_name']) ? sanitize_text_field($_POST['last_name']) : '';
        $email = isset($_POST['email']) ? sanitize_email($_POST['email']) : '';
        $phone = isset($_POST['phone']) ? sanitize_text_field($_POST['phone']) : '';
        $description = isset($_POST['description']) ? sanitize_textarea_field($_POST['description']) : '';

        // Create a new entry post.
        $entry_post = array(
            'post_title'   => $first_name . ' ' . $last_name,
            'post_content' => $description,
            'post_status'  => 'publish',
            'post_type'    => 'entries',
        );

        $entry_post_id = wp_insert_post( $entry_post );

        // Save meta fields for the entry post.
        update_post_meta( $entry_post_id, '_first_name', $first_name );
        update_post_meta( $entry_post_id, '_last_name', $last_name );
        update_post_meta( $entry_post_id, '_email', $email );
        update_post_meta( $entry_post_id, '_phone', $phone );
        update_post_meta( $entry_post_id, '_description', $description );
        update_post_meta( $entry_post_id, '_competition_id', $competition_id );

        // Remove the stored transient
        delete_transient( 'competition_transient' );

        // Return a success message
        echo wp_json_encode( array( 'status' => 'success', 'message' => 'Entry submitted successfully.' ) );

        // Always exit to avoid extra output
        wp_die();
    }

    // Function to load the custom template for single competition posts and submit-entry.
    public function load_single_competition_template( $template ) {
        if ( is_singular( 'competitions' ) ) {
            return plugin_dir_path( __DIR__ ) . 'templates/single-competition.php';
        }
        if ( is_page ( 'submit-entry' ) ) {
            return plugin_dir_path( __DIR__ ) . 'templates/submit-entry.php';
        }
        return $template;
    }

    // Function to store transient.
    public function set_competition_transient() {
        if ( is_singular( 'competitions' ) ) {
            $competition_id = get_the_ID();
            set_transient( 'competition_transient', $competition_id, 3600 ); // Set the transient for one hour
        }
    }
}