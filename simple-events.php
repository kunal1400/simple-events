<?php 
/**
 * The plugin bootstrap file
 *
 * This file is read by WordPress to generate the plugin information in the plugin
 * admin area. This file also includes all of the dependencies used by the plugin,
 * registers the activation and deactivation functions, and defines a function
 * that starts the plugin.
 *
 * @link              http://example.com
 * @since             1.0.0
 * @package           Master Node
 *
 * @wordpress-plugin
 * Plugin Name:       Simple Events
 * Plugin URI:        http://example.com/plugin-name-uri/
 * Description:       This is a short description of what the plugin does. It's displayed in the WordPress admin area.
 * Version:           1.0.0
 * Author:            Kunal malviya
 * Author URI:        http://example.com/
 * License:           GPL-2.0+
 * License URI:       http://www.gnu.org/licenses/gpl-2.0.txt
 * Text Domain:       master-node
 * Domain Path:       /languages
 */

// If this file is called directly, abort.
if ( ! defined( 'WPINC' ) ) {
	die;
}


/**
* Registering the custom posttype for Jobs
**/
add_action( 'init', 'events_posttype_callback' );
function events_posttype_callback() {
	$labels = array(
		'name'               => _x( 'Events', 'post type general name', 'your-plugin-textdomain' ),
		'singular_name'      => _x( 'Event', 'post type singular name', 'your-plugin-textdomain' ),
		'menu_name'          => _x( 'Events', 'admin menu', 'your-plugin-textdomain' ),
		'name_admin_bar'     => _x( 'Event', 'add new on admin bar', 'your-plugin-textdomain' ),
		'add_new'            => _x( 'Add New', 'Event', 'your-plugin-textdomain' ),
		'add_new_item'       => __( 'Add New Event', 'your-plugin-textdomain' ),
		'new_item'           => __( 'New Event', 'your-plugin-textdomain' ),
		'edit_item'          => __( 'Edit Event', 'your-plugin-textdomain' ),
		'view_item'          => __( 'View Event', 'your-plugin-textdomain' ),
		'all_items'          => __( 'All Events', 'your-plugin-textdomain' ),
		'search_items'       => __( 'Search Events', 'your-plugin-textdomain' ),
		'parent_item_colon'  => __( 'Parent Events:', 'your-plugin-textdomain' ),
		'not_found'          => __( 'No Event found.', 'your-plugin-textdomain' ),
		'not_found_in_trash' => __( 'No Event found in Trash.', 'your-plugin-textdomain' )
	);

	$args = array(
		'labels'             => $labels,
		'description'        => __( 'Description.', 'your-plugin-textdomain' ),
		'public'             => true,
		'publicly_queryable' => true,
		'show_ui'            => true,
		'show_in_menu'       => true,
		'query_var'          => true,
		'rewrite'            => array( 'slug' => 'jobs' ),
		'capability_type'    => 'post',
		'has_archive'        => true,
		'hierarchical'       => false,
		'menu_position'      => null,
		'supports'           => array( 'title' )
	);

	register_post_type( 'simple_events', $args );
}


/**
* Adding submenu page in Wordpress Admin for simple jobs post type
**/
add_action('admin_menu', 'add_events_users_requested_submenu');
function add_events_users_requested_submenu() {
  add_submenu_page( 
      'edit.php?post_type=simple_events'
    , 'Users Applied To Events' 
    , 'Users Applied To Events'
    , 'manage_options'
    , 'simple_jobs_users_requested'
    , 'simple_events_requested_callback'
  );
}

/**
* Callback function for simple_jobs_users_requested_callback
**/
function simple_events_requested_callback() {
	global $title;
	global $wpdb;
	echo "<h2>$title</h2>";

	$the_query = new WP_Query( array( 'post_type' => 'simple_events',
				'orderby' 	=> 'ID',
				'order' 	=> 'DESC',
				'meta_key'  => 'applied_users_data') 
			);

	if ( $the_query->have_posts() ) {
		$i = 1;
		while ( $the_query->have_posts() ) {
			$i++;
			$the_query->the_post();
			
			$title 		 = get_the_title();
			$postId 	 = get_the_ID();
			$appliedJobs = get_post_meta(get_the_ID(), 'applied_users_data', ARRAY_A);
			$permalink 	 = get_the_permalink(get_the_ID());
			
			echo "<h3>JOB TITLE: $title</h3>";
			echo '<table class="widefat fixed" cellspacing="0" >';
			echo "<thead><tr>
					<th width='40'>S.No</th>					
					<th>Name</th>
					<th>Email</th>
					<th>Description</th>
				</tr><thead>";
			echo "<tbody>";
			if($appliedJobs) {
				$appliedJobs = json_decode($appliedJobs, ARRAY_A);
				foreach ($appliedJobs as $key => $userData) {
					echo "<tr>";
					echo "<td>".($key+1)."</td>";
					echo "<td>".$userData['userName']."</td>";
					echo "<td>".$userData['userEmail']."</td>";
					echo "<td>".$userData['userDescription']."</td>";					
					echo "</tr>";					
				}
			}
			echo "<tbody>";
			echo '</table>';
			echo "<br/>";
			
		}
		wp_reset_postdata();
	} else {
		echo "No user requested on any job";
	}

}


/**
* Registering the meta boxes for other job information
**/
add_action( 'add_meta_boxes_simple_events', 'adding_simple_events_boxes', 10, 2 );
function adding_simple_events_boxes() {
    $screen = get_current_screen();
    add_meta_box(
        'other-job-informations-meta-box',
        __( 'Other Informations' ),
        'render_simple_events_meta_box'
    );    
}


/**
* Callback function of other information meta box
**/
function render_simple_events_meta_box() {
	if( !empty($_GET['post']) ) {
		$email_description 	= get_post_meta($_GET['post'], '_email_description', ARRAY_A);
		echo "<h3>Shortcode: [simple_events id='".$_GET['post']."']</h3>";
	}
	else {
		$email_description = $job_salary = $client_name = $client_address = $client_email = $client_telephone_number = "";
	}
	echo '<div id="feedsGeneratorId1">			
		<form action="" method="post">			
			<ul>
		        <li>
		        	<label for="email_description">Text to send on Email: </label>
		        	<textarea name="email_description" id="email_description">'.$email_description.'</textarea>
		        </li>		        
		    </ul>		    		    
		</form>		
	</div>';
}

/**
* Hooking the save post action
**/
add_action('save_post', 'simple_events_save_postdata');
function simple_events_save_postdata($post_id) {	
    if( !empty($_POST['email_description']) && $post_id) {
	    update_post_meta($post_id, '_email_description', $_POST['email_description']);		
    }    
}


/**
 * Enqueuing the js and css files on frontend
 */
function simple_events_enqueue_script() {
    wp_enqueue_script( 'simple_events_js', plugin_dir_url( __FILE__ ) . 'js/script.js', array('jquery'), '1.0' );
    wp_localize_script( 'simple_events_js', 'simple_events_js_var', array( 'ajax_url' => admin_url( 'admin-ajax.php' ) ) );
}
add_action('wp_enqueue_scripts', 'simple_events_enqueue_script');
add_action('admin_enqueue_scripts', 'simple_events_enqueue_script');


/**
 * Request lead detail ajax handler
 */
add_action( 'wp_ajax_submit_simple_event', 'submit_simple_event_callback' );
add_action( 'wp_ajax_nopriv_submit_simple_event', 'submit_simple_event_callback' );
function submit_simple_event_callback() {	
	if( $_POST['postId'] ) {
		$jobTitle 		  = get_the_title( $_POST['postId'] );		
		$currentUserEmail = $_POST['userEmail'];
		$userAlreadyApplied = false;

		// Getting all old requested jobs
		$appliedUsers = get_post_meta($_POST['postId'], 'applied_users_data', ARRAY_A);		
		if( $appliedUsers ) {
			$appliedUsers = json_decode($appliedUsers, ARRAY_A);
			foreach ($appliedUsers as $key => $userData) {
				if( $userData['userEmail'] == $currentUserEmail	) {
					//echo "$currentUserEmail already applied to this event";
					$userAlreadyApplied = true;
				}
				else {
					$userAlreadyApplied = false;					
				}
			}
			if(!$userAlreadyApplied) {
				$appliedUsers[] = $_POST;
			}
		}
		else {
			$appliedUsers[] = $_POST;	
		}		

		$updateuserMetaFlag = update_post_meta( $_POST['postId'], 'applied_users_data', json_encode( $appliedUsers ) );
		$email_description 	= get_post_meta($_POST['postId'], '_email_description', ARRAY_A);

		// echo "<pre>";
		// print_r($email_description);
		// echo "</pre>";

		wp_mail($currentUserEmail, "Successfully applied to the event", $email_description);
	}
	else {
		echo "post id is required";
	}
	wp_die();
}

add_shortcode( 'simple_events', 'simple_events_shortcode_callback' );
function simple_events_shortcode_callback( $atts ) {
	// Get shortcodes
	$a = shortcode_atts( array(
		"id" => "",
	), $atts );

	$output = "";
	if( empty($a['id']) ) {
		$output = "id parameter is required";
		return;
	}

	$output = "<form method='post' class='simpleEventAjaxForm'>
		<input type='hidden' name='action' value='submit_simple_event'>
		<input type='hidden' name='postId' value='".$a['id']."'>
		<input type='text' name='userName' placeholder='Your Name' required>
		<input type='email' name='userEmail' placeholder='Your email address' required>
		<input type='email' name='userEmail' placeholder='Company'>
		<textarea name='userDescription'>Useful information, food allergy, special needs etc</textarea>
		<input type='checkbox' required>Terms accepted
		<button type='submit'>Submit</button>
	</form>";

	return $output;
}