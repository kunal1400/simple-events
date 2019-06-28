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
		'public'             => false,
		'publicly_queryable' => true,
		'show_ui'            => true,
		'show_in_menu'       => true,
		'query_var'          => true,
		'rewrite'            => array( 'slug' => 'jobs' ),
		'capability_type'    => 'post',
		'has_archive'        => false,
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
    , 'Registered Users' 
    , 'Registered Users'
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
			
			echo '<table>
				<tr>
					<td><h3>'.$title.'</h3></td>
					<td align="right">
						<form method="post" id="download_form"  action="'.plugins_url( 'download.csv.php' , __FILE__ ).'">
				            <input type="hidden" name="download" value="'.get_home_path().'" />
				            <input type="hidden" name="post_id" value="'.$postId.'" />
				            <input type="submit" name="download_csv"  class="button-primary" value="Download CSV" />
			        	</form>
			        </td>
				</tr>
			</table>';			

			echo '<table class="widefat fixed" cellspacing="0" >';
			echo "<thead><tr>					
					<th>Name</th>
					<th>Email</th>
					<th>Company</th>
					<th>Additional Information</th>
				</tr><thead>";
			echo "<tbody>";
			if($appliedJobs) {
				$appliedJobs = json_decode($appliedJobs, ARRAY_A);
				foreach ($appliedJobs as $key => $userData) {
					echo "<tr>";
					echo "<td>".@$userData['userName']."</td>";
					echo "<td>".@$userData['userEmail']."</td>";
					echo "<td>".@$userData['userCompany']."</td>";
					echo "<td>".@$userData['userDescription']."</td>";					
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
		$event_name 	= get_post_meta($_GET['post'], '_event_name', ARRAY_A);		
		$event_venu 	= get_post_meta($_GET['post'], '_event_venu', ARRAY_A);		
		$event_location = get_post_meta($_GET['post'], '_event_location', ARRAY_A);		
		$event_information_text = get_post_meta($_GET['post'], '_event_information_text', ARRAY_A);
		$event_dateandtime = get_post_meta($_GET['post'], '_event_dateandtime', ARRAY_A);		
		$email_description = get_post_meta($_GET['post'], '_email_description', ARRAY_A);		
		echo "<h3>Shortcode: [simple_events id='".$_GET['post']."']</h3>";
	}
	else {
		$event_name = $event_venu = $event_location = $event_information_text = $event_dateandtime = $email_description = "";
	}
	echo '<div id="feedsGeneratorId1">			
		<form action="" method="post">			
			<table style="width:100%">
				<tr>
		        	<td width="20%"><label for="event_name">Event Name: </label></td>
		        	<td width="80%"><input type="text" name="event_name" id="event_name" value="'.$event_name.'"></td>
		        </tr>
		        <tr>
		        	<td width="20%"><label for="event_venu">Venue: </label></td>
		        	<td width="80%"><input type="text" name="event_venu" id="event_venu" value="'.$event_venu.'"></td>
		        </tr>
		        <tr>
		        	<td width="20%"><label for="event_location">Location: </label></td>
		        	<td width="80%"><input type="text" name="event_location" id="event_location" value="'.$event_location.'"></td>
		        </tr>
		        <tr>
		        	<td width="20%"><label for="event_dateandtime">Date and Time: </label></td>
		        	<td width="80%"><input type="text" name="event_dateandtime" id="event_dateandtime" value="'.$event_dateandtime.'"></td>
		        </tr>
		        <tr>
		        	<td width="20%"><label for="event_information_text">Event Information text: </l width="80%"abel></td>
		        	<td><textarea name="event_information_text" id="event_information_text">'.$event_information_text.'</textarea></td>
		        </tr>
		        <tr>
		        	<td width="20%"><label for="email_description">Confirmation email text: </l width="80%"abel></td>
		        	<td><textarea name="email_description" id="email_description">'.$email_description.'</textarea></td>
		        </tr>
		    </table>		    		    
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
	if( !empty($_POST['event_name']) && $post_id ){
		update_post_meta($post_id, '_event_name', $_POST['event_name']);		
	}
	if( !empty($_POST['event_venu']) && $post_id ){
		update_post_meta($post_id, '_event_venu', $_POST['event_venu']);		
	}
	if( !empty($_POST['event_location']) && $post_id ){
		update_post_meta($post_id, '_event_location', $_POST['event_location']);		
	}
	if( !empty($_POST['event_information_text']) && $post_id ){
		update_post_meta($post_id, '_event_information_text', $_POST['event_information_text']);
	}
	if( !empty($_POST['event_dateandtime']) && $post_id ){
		update_post_meta($post_id, '_event_dateandtime', $_POST['event_dateandtime']);		
	}
	if( !empty($_POST['email_description']) && $post_id ){
		update_post_meta($post_id, '_email_description', $_POST['email_description']);		
	}
}


/**
 * Enqueuing the js and css files on frontend
 */
function simple_events_enqueue_script() {
    wp_enqueue_script( 'simple_events_js', plugin_dir_url( __FILE__ ) . 'js/script.js', array('jquery'), '1.0' );
    wp_localize_script( 'simple_events_js', 'simple_events_js_var', array( 'ajax_url' => admin_url( 'admin-ajax.php' ) ) );
    wp_enqueue_style( 'simple_events_frontend_css', plugin_dir_url( __FILE__ ) . 'css/front.css' );
}
add_action('wp_enqueue_scripts', 'simple_events_enqueue_script');

/**
 * Enqueuing the js and css files on backend
 */
function simple_events_enqueue_script_backend() {    
    wp_enqueue_style( 'simple_events_backend_css', plugin_dir_url( __FILE__ ) . 'css/admin.css' );
}
add_action('admin_enqueue_scripts', 'simple_events_enqueue_script_backend');


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
			$appliedUsers = array($_POST);	
		}		

		$updateuserMetaFlag = update_post_meta( $_POST['postId'], 'applied_users_data', json_encode( $appliedUsers ) );
		
		$email_description = $event_name = $event_venu = $event_location = $event_information_text = $event_dateandtime = "";

		$email_description 	= get_post_meta($_POST['postId'], '_email_description', ARRAY_A);
		$event_name 		= get_post_meta($_POST['postId'], '_event_name', ARRAY_A);		
		$event_venu 		= get_post_meta($_POST['postId'], '_event_venu', ARRAY_A);		
		$event_location 	= get_post_meta($_POST['postId'], '_event_location', ARRAY_A);		
		$event_information_text = get_post_meta($_POST['postId'], '_event_information_text', ARRAY_A);
		$event_dateandtime 	= get_post_meta($_POST['postId'], '_event_dateandtime', ARRAY_A);		
		
		$emailBody 	= "Hi ".$_POST['userName'];
		$emailBody 	.= "\n$email_description\n";
		$emailBody 	.= "\nEvent Name: $event_name\n";
		$emailBody 	.= "\nEvent Venue: $event_venu\n";
		$emailBody 	.= "\nEvent Location: $event_location\n";
		$emailBody 	.= "\nEvent Information: $event_information_text\n";
		$emailBody 	.= "\nEvent Date and time: $event_dateandtime\n";
		$emailBody 	.= "\nUser Name: ".$_POST['userName']."\n";
		$emailBody 	.= "\nUser Email: ".$_POST['userEmail']."\n";
		$emailBody 	.= "\nUser Company: ".$_POST['userCompany']."\n";
		$emailBody 	.= "\nUser Description: ".$_POST['userDescription']."\n";

		wp_mail($currentUserEmail, "Successfully applied to the event", $emailBody);
	}
	else {
		echo "post id is required";
	}
	wp_die();
}


/**
* Adding shortcode for this plugin
**/
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

	$postStatus = get_post_status($a['id']);

	if($postStatus == 'publish') {
		ob_start();
	    include __DIR__ . '/templates/frontend.php';
	    return ob_get_clean();
	}
	else {
		return "shortcode not seems to be publish";
	}	
}

/***
* https://www.smashingmagazine.com/2017/12/customizing-admin-columns-wordpress/
**/
// add_filter( 'manage_edit-simple_events', 'my_edit_simple_events' ) ;
// function my_edit_simple_events( $columns ) {
// 	$columns = array(
// 		'cb' => '&lt;input type="checkbox" />',
// 		'title' => __( 'Movie' ),
// 		'duration' => __( 'Duration' ),
// 		'genre' => __( 'Genre' ),
// 		'date' => __( 'Date' )
// 	);

// 	return $columns;
// }


/**
* Adding columns for events post type in admin area
**/
add_filter( 'manage_simple_events_posts_columns', 'simple_events_filter_posts_columns' );
function simple_events_filter_posts_columns( $columns ) {	
	$columns['eventname'] = __( 'Event Name' );
	$columns['venu'] = __( 'Venue', 'smashing' );
	$columns['location'] = __( 'Location', 'smashing' );
	$columns['datetime'] = __( 'Date and Time' );
	$columns['shortcode'] = __( 'Shortcode' );
	return $columns;
}


/**
* Populating columns for events post type in admin area
**/
add_action( 'manage_simple_events_posts_custom_column', 'simple_events_realestate_column', 10, 2);
function simple_events_realestate_column( $column, $post_id ) {
	// Image column
	if ( 'shortcode' === $column ) {
		echo get_the_post_thumbnail( $post_id, array(80, 80) );
		echo "[simple_events id='".$post_id."']";
	}
	if ( 'venu' === $column ) {
		echo get_post_meta($post_id, '_event_venu', ARRAY_A);
	}
	if ( 'eventname' === $column ) {
		echo get_post_meta($post_id, '_event_name', ARRAY_A);
	}
	if ( 'location' === $column ) {
		echo get_post_meta($post_id, '_event_location', ARRAY_A);
	}
	if ( 'datetime' === $column ) {
		echo get_post_meta($post_id, '_event_dateandtime', ARRAY_A);
	}
}