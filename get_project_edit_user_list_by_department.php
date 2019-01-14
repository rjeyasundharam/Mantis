<?php
# MantisBT - A PHP based bugtracking system

# MantisBT is free software: you can redistribute it and/or modify
# it under the terms of the GNU General Public License as published by
# the Free Software Foundation, either version 2 of the License, or
# (at your option) any later version.
#
# MantisBT is distributed in the hope that it will be useful,
# but WITHOUT ANY WARRANTY; without even the implied warranty of
# MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
# GNU General Public License for more details.
#
# You should have received a copy of the GNU General Public License
# along with MantisBT.  If not, see <http://www.gnu.org/licenses/>.

/**
 * Edit Project Categories
 *
 * @package MantisBT
 * @copyright Copyright 2000 - 2002  Kenzaburo Ito - kenito@300baud.org
 * @copyright Copyright 2002  MantisBT Team - mantisbt-dev@lists.sourceforge.net
 * @link http://www.mantisbt.org
 *
 * @uses core.php
 * @uses access_api.php
 * @uses authentication_api.php
 * @uses category_api.php
 * @uses config_api.php
 * @uses form_api.php
 * @uses gpc_api.php
 * @uses helper_api.php
 * @uses html_api.php
 * @uses lang_api.php
 * @uses print_api.php
 * @uses string_api.php
 */

require_once( 'core.php' );
require_api( 'access_api.php' );
require_api( 'authentication_api.php' );
require_api( 'config_api.php' );
require_api( 'form_api.php' );
require_api( 'gpc_api.php' );
require_api( 'helper_api.php' );
require_api( 'html_api.php' );
require_api( 'division_api.php' );
require_api( 'user_api.php' );
require_api( 'department_api.php' );
require_api( 'database_api.php' );

auth_reauthenticate();

$p_division_id	= gpc_get_string( 'division_id' );
$p_department_id	= gpc_get_string( 'department_id' );
$p_project_id	= gpc_get_int( 'project_id' );

$t_adm = config_get_global( 'admin_site_threshold' );

	db_param_push();
	$param_array=array($p_project_id , $t_adm ,true);
	$t_query = 'SELECT DISTINCT u.id, u.username, u.realname
				FROM {user} u
				LEFT JOIN {project_user_list} p
				ON p.user_id=u.id AND p.project_id=' . db_param() .'
					WHERE u.access_level<' . db_param() . 
					' AND u.enabled = ' . db_param();

	if($p_division_id != "all"){
		$t_query .= ' AND u.division_id=' . db_param();
		array_push($param_array,$p_division_id);
	}

	if($p_department_id != "all"){
		$t_query .=' AND u.department_id=' . db_param();
		array_push($param_array,$p_department_id);
	}
	$t_query .=	' AND p.user_id IS NULL
				ORDER BY u.realname, u.username';

	$t_result = db_query( $t_query, $param_array );


	$t_display = array();
	$t_sort = array();
	$t_users = array();

	while( $t_row = db_fetch_array( $t_result ) ) {
		$t_users[] = (int)$t_row['id'];
		$t_display[] = user_get_expanded_name_from_row( $t_row );
		$t_sort[] = user_get_name_for_sorting_from_row( $t_row );
	}

	array_multisort( $t_sort, SORT_ASC, SORT_STRING, $t_users, $t_display );

	$t_count = count( $t_sort );
	$t_user_list = array();
	for( $i = 0;$i < $t_count; $i++ ) {
		$t_user_list[$t_users[$i]] = $t_display[$i];
	}
	if( 0 < db_num_rows( $t_result ) ) {
		foreach( $t_user_list AS $t_user_id=>$t_display_name ) {
			echo '<option value="', $t_user_id, '">'. string_attribute( $t_display_name ).'</option>';
		}
	}
	else{
		echo "<option>No user found </option>";
	}
 
access_ensure_global_level( config_get( 'manage_user_threshold' ) );





?>
