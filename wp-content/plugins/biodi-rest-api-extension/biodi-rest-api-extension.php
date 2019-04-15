<?php

/**
 * Plugin Name:       Biodi REST API Extension
 * Description:       Extension for the API on biodi-vers-city
 * Version:           1.0.0
 * Author:            roth-jeremy
 * Author URI:        https://github.com/roth-jeremy
 * Text Domain:       roth-jeremy
 * License:           GPL-2.0+
 * License URI:       http://www.gnu.org/licenses/gpl-2.0.txt
 */
 
defined( 'ABSPATH' ) or exit;

/**
 * Admin User interface :
 * Add new fields above 'Update' button.
 *
 * @param WP_User $user User object.
 */
function biodi_additional_profile_fields( $user ) {
	$neighbourhoods 	= array( '', 'nord', 'sud', 'ouest', 'est', 'centre');
    $user_neighbourhood = get_the_author_meta( 'neighbourhood', $user->ID );
	?>
    <h3>Extra profile information</h3>
    <table class="form-table">
   	 <tr>
   		 <th><label for="neighbourhood">Neighbourhood</label></th>
   		 <td>
   			 <select id="neighbourhood" name="neighbourhood" ><?php
   				 for ( $i = 0; $i <= 5; $i++ ) {
						if($neighbourhoods[$i] == $user_neighbourhood){
							print("<option value='".$neighbourhoods[$i]."' selected='selected'>".$neighbourhoods[$i]."</option>");
						}else{
							print("<option value='".$neighbourhoods[$i]."'>".$neighbourhoods[$i]."</option>");
						}
					}
					
   			 ?></select>
			</td>
		</tr>
	</table>
<?php 
}
add_action( 'show_user_profile', 'biodi_additional_profile_fields' );
add_action( 'edit_user_profile', 'biodi_additional_profile_fields' );



/**
 * Save additional profile fields about the user's neighbourhood in the database
 *
 * @param  int $user_id Current user ID.
 */
function biodi_save_profile_fields( $user_id ) {
    if ( ! current_user_can( 'edit_user', $user_id ) ) {
   	 return false;
    }
    if ( empty( $_POST['neighbourhood'] ) ) {
   	 return false;
    }
    update_usermeta( $user_id, 'neighbourhood', $_POST['neighbourhood'] );
}
add_action( 'personal_options_update', 'biodi_save_profile_fields' );
add_action( 'edit_user_profile_update', 'biodi_save_profile_fields' );


/**
 * Allow API to list all users without them to have published any posts
 * unsets has_published_posts
 * 
 */
add_filter( 'rest_user_query' , 'custom_rest_user_query' );
function custom_rest_user_query( $prepared_args, $request = null ) {
  unset($prepared_args['has_published_posts']);
  return $prepared_args;
}

/**
 * Example route creation function that returns the ID of the current user. Must be authenticated through JWT auth.
 * 'testone' is the namespace, 'loggedinuser' is the path. 
 * 
 * This route is accessible through "mysite.co/wp-json/testone/loggedinuser"
 */
function checkloggedinuser()
{
$currentuserid_fromjwt = get_current_user_id();
print_r($currentuserid_fromjwt);
exit;
}

add_action('rest_api_init', function ()
{
  register_rest_route( 'testone', 'loggedinuser',array(
  'methods' => 'GET',
  'callback' => 'checkloggedinuser'
  ));
});

/**
 * Add neighbourgood information in user's meta JSON response
 */
register_meta('user', 'neighbourhood', array(
    "type" => "string",
    "show_in_rest" => true, 
  ));

/**
 * ==================ROUTES=====================
 */

add_action('rest_api_init', function ()
{
    register_rest_route( 'testone', 'loggedinuser',array(
        'methods' => 'GET',
        'callback' => 'checkloggedinuser'
    ));
});
add_action('rest_api_init', function ()
{
    register_rest_route('v1', 'balconies', array(
        'methods' => 'GET',
        'callback' => 'listbalconies',
    ));
});
add_action('rest_api_init', function ()
{
    register_rest_route('v1', 'balconies/(?P<id>\d+)', array(
        'methods' => 'GET',
        'callback' => 'listbalcony',
    ));
});
add_action('rest_api_init', function ()
{
    register_rest_route('v1', 'balconies/add', array(
        'methods' => 'POST',
        'callback' => 'addbalcony',
    ));
});
add_action('rest_api_init', function ()
{
    register_rest_route('v1', 'balconies/add', array(
        'methods' => 'POST',
        'callback' => 'addbalcony',
    ));
});

/**
 * ==================ROUTE FUNCTIONS CALLBACKS=====================
 */
function listbalconies(){
    global $wpdb;
    
    $user_id = get_current_user_id();
    $results = $wpdb->get_results("SELECT * FROM balconies WHERE user_id=".$user_id, OBJECT);

    return $results;
}
function listbalcony(WP_REST_Request $request){
    global $wpdb;
    $id = $request->get_param('id');
    print_r($id);
    $results = $wpdb->get_results("SELECT * FROM balconies WHERE id=". $id , OBJECT);

    return $results[0];
}

function addbalcony(WP_REST_Request $request){
    global $wpdb;
    $user_id = get_current_user_id();
    $name = $request->get_param('name');
    $size = $request->get_param('size');
    $sunlight = $request->get_param('sunlight');
    $pet = $request->get_param('pet');
    $environnement = $request->get_param('environnement');
    $floor = $request->get_param('floor');
    $favorising = $request->get_param('favorising');

    return $inserted = $wpdb->insert('balconies', array(
        'name'               => $name,
        'size'               => $size,
        'sunlight'           => $sunlight,
        'pet'                => $pet,
        'environnement'      => $environnement,
        'floor'              => $floor,
        'favorising'         => $favorising,
        'user_id'            => $user_id,
    ));
}