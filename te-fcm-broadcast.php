<?php

/*

Plugin Name: TechExtensor Broadcast Plugin

Description: A Plugin where admin can send broadcast message

Version: 1.0.0

Author: Nilesh

Author URI: http://techextensor.com/

*/



function my_admin_menu() {

add_menu_page(

__( 'Broadcast Menu', 'my-textdomain' ),

__( 'Broadcast Menu', 'my-textdomain' ),

'manage_options',

'broadcast-page',

'my_admin_page_contents',

'dashicons-schedule',

3

);

}



add_action( 'admin_menu', 'my_admin_menu' );



function my_admin_page_contents() {
?>

<h1>

<?php esc_html_e( 'Welcome to Broadcast FCM Zone', 'my-plugin-textdomain' ); ?>

</h1>
<?php  
            if(sizeof($_POST) > 0) {
                $title = isset($_POST['title']) ? $_POST['title'] : '';
                $body = isset($_POST['body']) ? $_POST['body'] : '';
                $link = isset($_POST['link']) ? $_POST['link'] : '';
                TEBroadcastbaseNotification($title,$body,$link);
                echo "Message send successfully";
                //wp_redirect( $_SERVER['HTTP_REFERER'] );
                //exit();
            }
?>
<form method="post" id="mainform" action="" enctype="multipart/form-data">
        

        <table class="form-table">

<tbody><tr valign="top" class="">
							<th scope="row" class="titledesc">
								<label for="document_engine_pdf_button_text">Title </label>
							</th>
							<td class="forminp forminp-text">
								<input name="title"  type="text" style="" value="" class="" placeholder=""> 							</td>
						</tr>
									<tr valign="top" class="">
									<th scope="row" class="titledesc">Body Content</th>
									<td class="forminp forminp-multi-checkbox document-engine-multicheckbox">
                                        <textarea name="body"></textarea>
									</td>
								</tr>
													<tr valign="top">
							<th scope="row" class="titledesc">
								<label for="document_engine_pdf_button_action">Link </label>
							</th>
							<td class="forminp forminp-select">
                            <input name="link"  type="text" style="" value="" class="" placeholder="">					</td>
						</tr>
												
						</tbody></table>        <p class="submit">
                            <button name="save" class="button-primary document-engine-save-button" type="submit" value="Save changes">Send!</button>
                        </p>
    </form>

<?php

}



function register_my_plugin_scripts() {

wp_register_style( 'my-plugin', plugins_url( 'ddd/css/plugin.css' ) );

wp_register_script( 'my-plugin', plugins_url( 'ddd/js/plugin.js' ) );

}



add_action( 'admin_enqueue_scripts', 'register_my_plugin_scripts' );



function load_my_plugin_scripts( $hook ) {



if( $hook != 'toplevel_page_sample-page' ) {

return;

}

// Load style & scripts.

wp_enqueue_style( 'my-plugin' );

wp_enqueue_script( 'my-plugin' );

}



add_action( 'admin_enqueue_scripts', 'load_my_plugin_scripts' );

function TEBroadcastbaseNotification($title,$body,$link) {
	
	//FCM API end-point
	$url = 'https://fcm.googleapis.com/fcm/send';
	//api_key in Firebase Console -> Project Settings -> CLOUD MESSAGING -> Server key
	$serverKey = '';
	//header with content_type api key
	$headers = array(
		'Content-Type:application/json',
		//'Authorization:key='.$server_key
	);
	$url = "https://fcm.googleapis.com/fcm/send";
	// $notification = array('title' =>"", 'body' => $body, 'sound' => 'default', 'badge' => '1');
	// $arrayToSend = array('to'=>$token,'mutable_content'=>true,'data'=>['link'=>$link],'notification' => $notification,'priority'=>'high');

    $notification_data = array(
        'click_action'          => 'FLUTTER_NOTIFICATION_CLICK',
        'message'               => $body,
        'title'                 => $title,
        'url'                   => $link,
        'link'                   => $link,
        //'show_in_notification'  => $showLocalNotification,
        //'command'               => $command,
        'dialog_title'          => $title,
        // 'dialog_text'           => _mb_strlen($resume) == 0 ? _mb_substr(wp_strip_all_tags($content), 0, 100) . '...' : $resume,
        // 'dialog_image'          => $image,
        'sound'                 => 'default',
        //'customm_fields'        => $arrCustomFieldsValues
    );
    $notification = array(
        'title'                 => $title,
        'body'                  => $body,
        'content_available'     => true,
        'android_channel_id'    => 'default',
        'click_action'          => 'FLUTTER_NOTIFICATION_CLICK',
        'sound'                 => 'default',
        'image'                 => '',
    );

    $arrayToSend = array(
        //'to'                    => '/topics/' . $topic,
        'to'                    => '/topics/all',
        'collapse_key'          => 'type_a',
        'notification'          => $notification,
        'priority'              => 'high',
        'data'                  => $notification_data,
        'timeToLive'            => 10,
    );

	$json = json_encode($arrayToSend);
	$headers = array();
	$headers[] = 'Content-Type: application/json';
	$headers[] = 'Authorization: key='. $serverKey;
	$ch = curl_init();
	curl_setopt($ch, CURLOPT_URL, $url);
	curl_setopt($ch, CURLOPT_CUSTOMREQUEST,"POST");
	curl_setopt($ch, CURLOPT_POSTFIELDS, $json);
	curl_setopt($ch, CURLOPT_HTTPHEADER,$headers);
	//Send the request
	$response = curl_exec($ch);
	//Close request
	if ($response === FALSE) {
	    die('FCM Send Error: ' . curl_error($ch));
	}
//	curl_close($ch);
}