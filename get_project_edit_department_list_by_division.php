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
	db_param_push();

	if($p_division_id == "all"){
		$department_list="<option>--None--</option>";
	}
	else{
		$t_query = 'SELECT department_id,department FROM {department} WHERE division_id = '.db_param();


		$t_result=db_query( $t_query, array( $p_division_id) );
		$department_list="<option value='all'>All Departments</option>";

		while( $t_row = db_fetch_array( $t_result ) ) {
			$id=(string)$t_row['department_id'];
			$department_list.="<option value='$id'>".$t_row['department']."</option>";
		}
	}
	echo $department_list;
access_ensure_global_level( config_get( 'manage_user_threshold' ) );
?>
