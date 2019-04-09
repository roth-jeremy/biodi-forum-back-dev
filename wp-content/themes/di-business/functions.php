<?php

if( ! defined( 'ABSPATH' ) ) {
	die( "No Direct access" );
}

// Load init.php file.
require_once get_template_directory() . '/inc/init.php';

/*
* Do not edit any code of theme, use child theme instead
*/

/**
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
 * Save additional profile fields.
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