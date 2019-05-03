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
 * Allow access control origin
 */
function add_cors_http_header(){
    header("Access-Control-Allow-Origin: *");
}
add_action('init','add_cors_http_header');

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
    //neighbourhood
    if ( ! current_user_can( 'edit_user', $user_id ) ) {
   	 return false;
    }
    if ( empty( $_POST['neighbourhood'] ) ) {
   	 return false;
    }
    update_usermeta( $user_id, 'neighbourhood', $_POST['neighbourhood'] );
    //first_name
    if ( ! current_user_can( 'edit_user', $user_id ) ) {
   	 return false;
    }
    if ( empty( $_POST['first_name'] ) ) {
   	 return false;
    }
    update_usermeta( $user_id, 'first_name', $_POST['first_name'] );
    //last_name
    if ( ! current_user_can( 'edit_user', $user_id ) ) {
   	 return false;
    }
    if ( empty( $_POST['last_name'] ) ) {
   	 return false;
    }
    update_usermeta( $user_id, 'last_name', $_POST['last_name'] );
    var_dump($_POST);
}
add_action( 'personal_options_update', 'biodi_save_profile_fields' );
add_action( 'edit_user_profile_update', 'biodi_save_profile_fields' );
add_action( 'user_register', 'biodi_save_profile_fields' );


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
 * Add information in user's meta JSON response
 */
register_meta('user', 'neighbourhood', array(
    "type" => "string",
    "show_in_rest" => true, 
));
register_meta('user', 'first_name', array(
    'type' => "string",
    'show_in_rest' => true,
));
register_meta('user', 'last_name', array(
    'type' => "string",
    'show_in_rest' => true,
));
register_meta('user', 'newsletter', array(
    "type" => "integer",
    "single" => true,
    "show_in_rest" => true, 
));

/**
 * ==================ROUTES=====================
 */
//-----------------REST API-----------------------------------------------------
add_action('rest_api_init', function () {
    register_rest_route('testone', 'loggedinuser', array(
        'methods'  => 'GET',
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
    register_rest_route('v1', 'contents/(?P<id>\d+)', array(
        'methods' => 'GET',
        'callback' => 'listcontent',
    ));
});

add_action('rest_api_init', function ()
{
    register_rest_route('v1', 'balconies/(?P<id>\d+)/contents/(?P<idArrangement>\d+)', array(
        'methods' => 'GET',
        'callback' => 'listcontentsforbalcony',
    ));
});
add_action('rest_api_init', function ()
{
    register_rest_route('v1', 'arrangementsBalcony/(?P<id>\d+)', array(
        'methods' => 'PATCH',
        'callback' => 'updatearrangementsbalcony',
    ));
});
add_action('rest_api_init', function ()
{
    register_rest_route('v1', 'balconies/(?P<id>\d+)/contents', array(
        'methods' => 'PATCH',
        'callback' => 'updatecontent',
    ));
});
add_action('rest_api_init', function ()
{
  register_rest_route( 'v1', 'points',array(
  'methods' => 'GET',
  'callback' => 'getpoints'
  ));
});

/**
 * ==================ROUTE FUNCTIONS CALLBACKS=====================
 */
/**
	Retrieve all balconies for a specific user.
*/
function listbalconies(){
	global $wpdb;
    $user_id = get_current_user_id();
    $results = $wpdb->get_results("SELECT * FROM balconies WHERE user_id=".$user_id, OBJECT);

    return $results;
}
/**
	Retrieve the balcony depending on the ID given in params.
	Take with it all data about arrangements and contents.
*/
function listbalcony(WP_REST_Request $request){
    global $wpdb;
    $id = $request->get_param('id');
    $results = $wpdb->get_results("SELECT contentsbalcony.*,contents.name AS 'nomPlante',contents.description AS 'descPlante',contents.img AS 'imgSrc' FROM balconies INNER JOIN contentsbalcony ON balconies.id = contentsbalcony._balconyId INNER JOIN contents ON contentsbalcony._content = contents.id WHERE balconies.id=". $id, OBJECT);

    return $results;
}
/**
	Insert a new balcony.
*/
function addbalcony(WP_REST_Request $request){
    global $wpdb;
    $wpdb->show_errors();
    $body = json_decode($request->get_body());
    $user_id = get_current_user_id();
    $name = $body->name;
    $size = $body->size;
    $sunlight = $body->sunlight;
    $pet = $body->pet;
    $environnement = $body->environnement;
    $floor = $body->floor;
    $goal = $body->goal;
    if(is_null($name) || is_null($user_id) || is_null($size) || is_null($sunlight) || is_null($pet) || is_null($environnement) || is_null($floor) || is_null($goal)){
    	return 'Params missing';
    }
    $insertedBalcony = [$wpdb->insert('balconies', array(
        'name'               => $name,
        'size'               => $size,
        'sunlight'           => $sunlight,
        'pet'                => $pet,
        'environnement'      => $environnement,
        'floor'              => $floor,
        'goal'         		 => $goal,
        'user_id'            => $user_id,
    )),
    $wpdb->insert_id];
    if($insertedBalcony == false)
    {
    	return 'Issue during balcony insert';
    }
    $newBalconyId = $wpdb->insert_id;

    //Pot
    for ($i=1; $i <= 7; $i++) { 
    	
    	$insertContentsBalcony = $wpdb->insert('contentsbalcony',array(
    		'location'		=> $i,
    		'_balconyId'	=> $newBalconyId,
    		'_arrangement'	=> 1,
    		'_content'		=> NULL
    	));
    	if ($insertContentsBalcony == false) {
    		return 'Issue during contentBalcony insert';
    	}
    }
    //Suspended
    $insertContentsBalcony = $wpdb->insert('contentsbalcony',array(
    		'location'		=> 8,
    		'_balconyId'	=> $newBalconyId,
    		'_arrangement'	=> 3,
    		'_content'		=> NULL
    	));
    	if ($insertContentsBalcony == false) {
    		return 'Issue during contentBalcony insert';
    	}
    //wall
    $insertContentsBalcony = $wpdb->insert('contentsbalcony',array(
    		'location'		=> 9,
    		'_balconyId'	=> $newBalconyId,
    		'_arrangement'	=> 2,
    		'_content'		=> NULL
    	));
    	if ($insertContentsBalcony == false) {
    		return 'Issue during contentBalcony insert';
    	}
    //Construct
    for ($i=10; $i < 12; $i++) {     	
	    $insertContentsBalcony = $wpdb->insert('contentsbalcony',array(
	    		'location'		=> $i,
	    		'_balconyId'	=> $newBalconyId,
	    		'_arrangement'	=> 4,
	    		'_content'		=> NULL
	    	));
	    	if ($insertContentsBalcony == false) {
	    		return 'Issue during contentBalcony insert';
	    	}
    }
    $jsonResponse = json_decode('{"id":'.$newBalconyId.'}');
    return $jsonResponse;
}

/**
	Retrieve a content
*/
function listcontent(WP_REST_Request $request){
	global $wpdb;
    $id = $request->get_param('id');
    if(is_null($id))
    	return 'Params missing';
    $results = $wpdb->get_results("SELECT * FROM contents WHERE id=".$id, OBJECT);

    return $results[0];
}
/**
	Update arrangement content
*/
function updatearrangementsbalcony(WP_REST_Request $request){
	global $wpdb;
    $id = $request->get_param('id');
    $idPlant = $request->get_param('idPlant');
    $updated = $wpdb->update('arrangementsBalcony', array(
        '_contain'               => $id
    ), array('id' => $idPlant));

    return $updated;
}

//	Retrieve plants that match with balcony's parameters

function listcontentsforbalcony(WP_REST_Request $request)
{
	global $wpdb;
	$wpdb->show_errors();
	$body = $request->get_body();
	$idArrangement = $request->get_param('idArrangement');
	$idBalcony = $request->get_param('id');
	if(is_null($idBalcony) || is_null($idArrangement))
		return 'field missing';
	$results = $wpdb->get_results("SELECT * FROM contents WHERE contents.sunlight <= (SELECT balconies.sunlight FROM balconies WHERE balconies.id = " .$idBalcony . ") AND contents._arrangement = " . $idArrangement, OBJECT);
	
	return $results;
}
/*
Update construction
*/
function updatecontent(WP_REST_Request $request)
{
	
	global $wpdb;
	$wpdb->show_errors();
    $body = json_decode($request->get_body());
    $idBalcony = $request->get_param('id');
    if(is_null($body->location)){
    	var_dump($body);
    	return 'Body params missing';
    }
    
    $idContent = $body->idContent;
    $location = $body->location;
    
    $updated = $wpdb->update('contentsbalcony', array(
        '_content'               => $idContent
    ), array('location' => $location, '_balconyId' => $idBalcony)); 
	
    return $updated;
}

/**
 * count number of posts, users and balconies for the different neighbourhoods
 */
function getpoints(){
    global $wpdb;
    $wpdb->show_errors();
    $testResponse = [];
    for ($i = 0; $i <= 4; $i++) {
        switch ($i) {
            case 0:
                $neighbourhoodName='centre';
        break;
            case 1:
                $neighbourhoodName='sud';
        break;
            case 2:
                $neighbourhoodName='est';
        break;
            case 3:
                $neighbourhoodName='ouest';
        break;
            case 4:
                $neighbourhoodName='nord';
        break;
            default:
                return 'Something wrong happened';
        }

        $users = $wpdb->get_results('SELECT COUNT(id) AS number_of_users FROM wp_users INNER JOIN wp_usermeta ON ID = wp_usermeta.user_id WHERE meta_key="neighbourhood" AND meta_value="'.$neighbourhoodName.'"', OBJECT);
        $balconies = $wpdb->get_results('SELECT COUNT(id) AS number_of_balconies FROM balconies INNER JOIN wp_usermeta ON balconies.user_id = wp_usermeta.user_id WHERE meta_key="neighbourhood" AND meta_value="'.$neighbourhoodName.'"', OBJECT);
        $posts = $wpdb->get_results('SELECT COUNT(id) AS number_of_posts FROM wp_forum_posts INNER JOIN wp_usermeta ON author_id = wp_usermeta.user_id WHERE meta_key="neighbourhood" AND meta_value="'.$neighbourhoodName.'"', OBJECT);

        if(is_null($users) ||is_null($balconies) ||is_null($posts))
            return 'Issue with databse';
        $testResponse[$i] = new stdClass();
        $testResponse[$i]->label = $neighbourhoodName;
        $testResponse[$i]->users = intval($users[0]->number_of_users);
        $testResponse[$i]->balconies = intval($balconies[0]->number_of_balconies);
        $testResponse[$i]->posts = intval($posts[0]->number_of_posts);
    }
    return $testResponse;
}

require_once ('admin_plants_page.php');