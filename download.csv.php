<?php
$core = $_POST['download'].'wp-load.php';

if(isset($_POST['download']) && is_file($core)){
	include $core;

	if( !empty($_POST['post_id']) ) {
		$postId 	 = $_POST['post_id'];
		$appliedJobs = get_post_meta($postId, 'applied_users_data', ARRAY_A);
		$rows = "Name, Email, Company, Description\n";
		if($appliedJobs) {
			$appliedJobs = json_decode($appliedJobs, ARRAY_A);
			foreach ($appliedJobs as $key => $userData) {
				$rows .= @$userData['userName'].','.@$userData['userEmail'].','.@$userData['userCompany'].','.@$userData['userDescription']."\n";
			}

			$filename = "event_results.csv";
		    $now = gmdate('D, d M Y H:i:s') . ' GMT';
		    header( 'Content-Type: application/octet-stream' );
		    header( 'Content-Disposition: attachment; filename="' . $filename .'"' );
		    header( 'Pragma: no-cache' );
		    header( 'Expires: ' . $now );
		    echo $rows;
		    exit;

		}
		else {
			echo "No applied jobs";
		}
	}
	else {		
		echo "No Post found";
	}
}