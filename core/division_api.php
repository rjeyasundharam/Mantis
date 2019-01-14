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
 * division API
 *
 * @package CoreAPI
 * @subpackage divisionAPI
 * @copyright Copyright 2000 - 2002  Kenzaburo Ito - kenito@300baud.org
 * @copyright Copyright 2002  MantisBT Team - mantisbt-dev@lists.sourceforge.net
 * @link http://www.mantisbt.org
 *
 * @uses config_api.php
 * @uses constant_inc.php
 * @uses database_api.php
 * @uses error_api.php
 * @uses helper_api.php
 * @uses history_api.php
 * @uses lang_api.php
 * @uses project_api.php
 * @uses project_hierarchy_api.php
 * @uses utility_api.php
 */

require_api( 'config_api.php' );
require_api( 'constant_inc.php' );
require_api( 'database_api.php' );
require_api( 'error_api.php' );
require_api( 'helper_api.php' );
require_api( 'history_api.php' );
require_api( 'lang_api.php' );
require_api( 'project_api.php' );
require_api( 'project_hierarchy_api.php' );
require_api( 'utility_api.php' );

# division data cache (to prevent excessive db queries)
$g_division_cache = array();

/**
 * Check whether the division exists in the project
 * @param integer $p_division_id A division identifier.
 * @return boolean Return true if the division exists, false otherwise
 * @access public
 */
function division_exists( $p_division_id ) {
	$t_division_row = division_get_row( $p_division_id, /* error_if_not_exists */ false );
	return $t_division_row !== false;
}

/**
 * Check whether the division exists in the project
 * Trigger an error if it does not
 * @param integer $p_division_id A division identifier.
 * @return void
 * @access public
 */
function division_ensure_exists( $p_division_id ) {
	if( !division_exists( $p_division_id ) ) {
		trigger_error( ERROR_DIVISION_NOT_FOUND, ERROR );
	}
}

/**
 * Check whether the division is unique within a project
 * @param integer $p_project_id A project identifier.
 * @param string  $p_name       Project name.
 * @return boolean Returns true if the division is unique, false otherwise
 * @access public
 */
function division_is_unique($p_name ) {
	db_param_push();
	$t_query = 'SELECT COUNT(*) FROM {division}
					WHERE division=' . db_param();
	$t_count = db_result( db_query( $t_query, array( $p_name ) ) );
	if( 0 < $t_count ) {
		return false;
	} else {
		return true;
	}
}

/**
 * Check whether the division is unique within a project
 * Trigger an error if it is not
 * @param integer $p_project_id Project identifier.
 * @param string  $p_name       division Name.
 * @return void
 * @access public
 */
function division_ensure_unique( $p_name ) {
	if( !division_is_unique( $p_name ) ) {
		trigger_error( ERROR_division_DUPLICATE, ERROR );
	}
}

/**
 * Add a new division to the project
 * @param integer $p_project_id Project identifier.
 * @param string  $p_name       division Name.
 * @return integer division ID
 * @access public
 */
function division_add( $p_division_name ) {
	if( is_blank( $p_division_name ) ) {
		error_parameters( lang_get( 'division' ) );
		trigger_error( ERROR_EMPTY_FIELD, ERROR );
	}

	division_ensure_unique( $p_division_name );

	db_param_push();
	$t_query = 'INSERT INTO {division} ( division )
				  VALUES ( ' . db_param() . ' )';
	db_query( $t_query, array( $p_division_name) );

	# db_query() errors on failure so:
	return db_insert_id( db_get_table( 'division' ) );
}

/**
 * Update the name and user associated with the division
 * @param integer $p_division_id division identifier.
 * @param string  $p_name        division Name.
 * @param integer $p_assigned_to User ID that division is assigned to.
 * @return void
 * @access public
 */
function division_update( $p_division_id, $p_division) {
	if( is_blank( $p_division ) ) {
		error_parameters( lang_get( 'division' ) );
		trigger_error( ERROR_EMPTY_FIELD, ERROR );
	}

	db_param_push();
	$t_query = 'UPDATE {division} SET division=' . db_param() . '
				  WHERE division_id=' . db_param();
	db_query( $t_query, array( $p_division, $p_division_id ) );

}

/**
 * Remove a division from the project
 * @param integer $p_division_id     division identifier.
 * @param integer $p_new_division_id New division id (to replace existing division).
 * @return void
 * @access public
 */
function division_remove( $p_division_id ) {
	check_division_users( $p_division_id );
	check_division_departments( $p_division_id );
	db_param_push();
	$t_query = 'DELETE FROM {division} WHERE division_id=' . db_param();
	db_query( $t_query, array( $p_division_id ) );

	db_param_push();
	$t_query = 'DELETE FROM {department} WHERE division_id=' . db_param();
	db_query( $t_query, array( $p_division_id ) );
}


/**
 * Remove a department from the project before check user exist in department
 * @param integer $p_department_id     department identifier.
 * @param integer $p_new_department_id New department id (to replace existing department).
 * @return void
 * @access public
 */
function check_division_users( $p_division_id ) {
	$t_query = 'SELECT * FROM {user} WHERE division_id=' . db_param();
	$t_result = db_query( $t_query, array( $p_division_id  ) );
	if( 0 < db_num_rows( $t_result ) ) {		
		trigger_error( ERROR_DIVISION_DELETE, ERROR );
	}
}

/**
 * Remove a department from the project before check user exist in department
 * @param integer $p_department_id     department identifier.
 * @param integer $p_new_department_id New department id (to replace existing department).
 * @return void
 * @access public
 */
function check_division_departments( $p_division_id ) {
	$t_query = 'SELECT * FROM {department} WHERE division_id=' . db_param();
	$t_result = db_query( $t_query, array( $p_division_id  ) );
	if( 0 < db_num_rows( $t_result ) ) {		
		trigger_error( ERROR_DIVISION_DELETE, ERROR );
	}
}

/**
 * Return the definition row for the division
 * @param integer $p_division_id division identifier.
 * @param boolean $p_error_if_not_exists true: error if not exists, otherwise return false.
 * @return array An array containing division details.
 * @access public
 */
function division_get_row( $p_division_id, $p_error_if_not_exists = true ) {
	
	$p_division_id = (int)$p_division_id;
	
	$t_query = 'SELECT * FROM {division} WHERE division_id=' . db_param();
	$t_result = db_query( $t_query, array( $p_division_id ) );
	$t_row = db_fetch_array( $t_result );
	if( !$t_row ) {
		if( $p_error_if_not_exists ) {
			trigger_error( ERROR_DIVISION_NOT_FOUND, ERROR );
		} else {
			return false;
		}
	}
	return $t_row;
}

/**
 *	Get a distinct array of categories accessible to the current user for
 *	the specified projects.  If no project is specified, use the current project.
 *	If the current project is ALL_PROJECTS get all categories for all accessible projects.
 *	For all cases, get global categories and subproject categories according to configured inheritance settings.
 *	@param integer|null $p_project_id A specific project or null.
 *	@return array A unique array of division names
 */
function division_get_filter_list( $p_project_id = null ) {
	if( null === $p_project_id ) {
		$t_project_id = helper_get_current_project();
	} else {
		$t_project_id = $p_project_id;
	}

	if( $t_project_id == ALL_PROJECTS ) {
		$t_project_ids = current_user_get_accessible_projects();
	} else {
		$t_project_ids = array( $t_project_id );
	}

	$t_subproject_ids = array();
	foreach( $t_project_ids as $t_project_id ) {
		$t_subproject_ids = array_merge( $t_subproject_ids, current_user_get_all_accessible_subprojects( $t_project_id ) );
	}

	$t_project_ids = array_merge( $t_project_ids, $t_subproject_ids );

	$t_categories = array();
	foreach( $t_project_ids as $t_id ) {
		$t_categories = array_merge( $t_categories, division_get_all_rows( $t_id ) );
	}

	$t_unique = array();
	foreach( $t_categories as $t_division ) {
		if( !in_array( $t_division['name'], $t_unique ) ) {
			$t_unique[] = $t_division['name'];
		}
	}

	return $t_unique;
}

/**
 * Return all categories for the specified project id.
 * Obeys project hierarchies and such.
 * @param integer $p_project_id      A Project identifier.
 * @param boolean $p_inherit         Indicates whether to inherit categories from parent projects, or null to use configuration default.
 * @param boolean $p_sort_by_project Whether to sort by project.
 * @return array array of categories
 * @access public
 */
function division_get_all_rows( ) {
	global $g_division_cache, $g_cache_division_project;


	$t_query = 'SELECT * FROM {division} ORDER BY division_id';
	$t_result = db_query( $t_query );
	$t_rows = array();
	while( $t_row = db_fetch_array( $t_result ) ) {
		$t_rows[] = $t_row;
	}

	return $t_rows;
}

/**
 * Cache an set of division ids
 * @param array $p_cat_id_array Array of division identifiers.
 * @return void
 * @access public
 */

/**
 * Given a division id and a field name, this function returns the field value.
 * An error will be triggered for a non-existent division id or division id = 0.
 * @param integer $p_division_id A division identifier.
 * @param string  $p_field_name  Field name.
 * @return string field value
 * @access public
 */
function division_get_field( $p_division_id, $p_field_name ) {
	$t_row = division_get_row( $p_division_id );
	return $t_row[$p_field_name];
}

/**
 * Given a division id, this function returns the division name.
 * An error will be triggered for a non-existent division id or division id = 0.
 * @param integer $p_division_id A division identifier.
 * @return string division name
 * @access public
 */
function division_get_name( $p_division_id ) {
	return division_get_field( $p_division_id, 'name' );
}



function get_division_list(){
	$t_query = 'SELECT * FROM {division}';
	$t_result = db_query( $t_query );
	if( 0 < db_num_rows( $t_result ) ) {
		$division_list="<option>--None--</option>";
		while( $t_row = db_fetch_array( $t_result ) ) {
			$id=(string)$t_row['division_id'];
			$division_list.="<option value='$id'>".$t_row['division']."</option>";
		}
	}
	else
		$division_list="<option>No division Found</option>";

	return $division_list;	
}

function get_project_division_list(){
	$t_query = 'SELECT * FROM {division}';
	$t_result = db_query( $t_query );
	if( 0 < db_num_rows( $t_result ) ) {
		$division_list="<option value='all'>All Divisions</option>";
		while( $t_row = db_fetch_array( $t_result ) ) {
			$id=(string)$t_row['division_id'];
			$division_list.="<option value='$id'>".$t_row['division']."</option>";
		}
	}
	else
		$division_list="<option>No division Found</option>";

	return $division_list;	
}


function get_edit_division_list($p_division_id){
	$t_query = 'SELECT * FROM {division}';
	$t_result = db_query( $t_query );
	$division_list="<option>--None--</option>";

	while( $t_row = db_fetch_array( $t_result ) ) {
		$id=(string)$t_row['division_id'];
		if($id==$p_division_id)
			$division_list.="<option value='$id' selected>".$t_row['division']."</option>";
		else
			$division_list.="<option value='$id'>".$t_row['division']."</option>";
	}

	return $division_list;	
}