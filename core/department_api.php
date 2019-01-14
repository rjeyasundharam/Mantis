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
 * department API
 *
 * @package CoreAPI
 * @subpackage departmentAPI
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

# department data cache (to prevent excessive db queries)
$g_department_cache = array();

/**
 * Check whether the department exists in the project
 * @param integer $p_department_id A department identifier.
 * @return boolean Return true if the department exists, false otherwise
 * @access public
 */
function department_exists( $p_department_id ) {
	$t_department_row = department_get_row( $p_department_id, /* error_if_not_exists */ false );
	return $t_department_row !== false;
}

/**
 * Check whether the department exists in the project
 * Trigger an error if it does not
 * @param integer $p_department_id A department identifier.
 * @return void
 * @access public
 */
function department_ensure_exists( $p_department_id ) {
	if( !department_exists( $p_department_id ) ) {
		trigger_error( ERROR_department_NOT_FOUND, ERROR );
	}
}

/**
 * Check whether the department is unique within a project
 * @param integer $p_project_id A project identifier.
 * @param string  $p_name       Project name.
 * @return boolean Returns true if the department is unique, false otherwise
 * @access public
 */
function department_is_unique($p_division_id,$p_department ) {
	db_param_push();
	$t_query = 'SELECT COUNT(*) FROM {department}
					WHERE department=' . db_param() .' AND division_id='.db_param();
	$t_count = db_result( db_query( $t_query, array( $p_department,$p_division_id ) ) );
	if( 0 < $t_count ) {
		return false;
	} else {
		return true;
	}
}

/**
 * Check whether the department is unique within a project
 * Trigger an error if it is not
 * @param integer $p_project_id Project identifier.
 * @param string  $p_name       department Name.
 * @return void
 * @access public
 */
function department_ensure_unique( $p_division_id,$p_department ) {
	if( !department_is_unique( $p_division_id,$p_department ) ) {
		trigger_error( ERROR_DEPARTMENT_DUPLICATE, ERROR );
	}
}

/**
 * Add a new department to the project
 * @param integer $p_project_id Project identifier.
 * @param string  $p_name       department Name.
 * @return integer department ID
 * @access public
 */
function department_add( $p_department,$p_division_id ) {
	if( is_blank( $p_department) ) {
		error_parameters( lang_get( 'department' ) );
		trigger_error( ERROR_EMPTY_FIELD, ERROR );
	}
	if( is_blank( $p_division_id ) ) {
		error_parameters( lang_get( 'division' ) );
		trigger_error( ERROR_EMPTY_FIELD, ERROR );
	}


	department_ensure_unique( $p_division_id,$p_department );

	db_param_push();
	$t_query = 'INSERT INTO {department} ( department,division_id )
				  VALUES ( ' . db_param() . ' ,'.db_param() .' )';

	echo "Query =". $t_query;
	db_query( $t_query, array( $p_department,$p_division_id) );

}

/**
 * Update the name and user associated with the department
 * @param integer $p_department_id department identifier.
 * @param string  $p_name        department Name.
 * @param integer $p_assigned_to User ID that department is assigned to.
 * @return void
 * @access public
 */
function department_update( $p_department_id, $p_department, $p_division_id) {
	if( is_blank( $p_department ) ) {
		error_parameters( lang_get( 'department' ) );
		trigger_error( ERROR_EMPTY_FIELD, ERROR );
	}
	if( is_blank( $p_division_id ) ) {
		error_parameters( lang_get( 'department' ) );
		trigger_error( ERROR_EMPTY_FIELD, ERROR );
	}
	$t_query = 'UPDATE {department} SET department=' . db_param() . ', division_id=' . db_param() . ' WHERE department_id=' . db_param();
	db_query( $t_query, array( $p_department, $p_division_id, $p_department_id ) );

}

/**
 * Remove a department from the project before check user exist in department
 * @param integer $p_department_id     department identifier.
 * @param integer $p_new_department_id New department id (to replace existing department).
 * @return void
 * @access public
 */
function check_department_users( $p_department_id ) {
	$t_query = 'SELECT * FROM {user} WHERE department_id=' . db_param();
	$t_result = db_query( $t_query, array( $p_department_id  ) );
	if( 0 < db_num_rows( $t_result ) ) {		
		trigger_error( ERROR_DEPARTMENT_DELETE, ERROR );
	}
}

/**
 * Remove a department from the project
 * @param integer $p_department_id     department identifier.
 * @param integer $p_new_department_id New department id (to replace existing department).
 * @return void
 * @access public
 */
function department_remove( $p_department_id ) {
	check_department_users( $p_department_id );
	db_param_push();
	$t_query = 'DELETE FROM {department} WHERE department_id=' . db_param();
	db_query( $t_query, array( $p_department_id ) );
}

/**
 * Remove all categories associated with a project.
 * This will skip processing of categories that can't be deleted.
 * @param integer $p_project_id      A Project identifier.
 * @param integer $p_new_department_id New department id (to replace existing department).
 * @return boolean
 * @access public
 */
function department_remove_all( $p_project_id, $p_new_department_id = 0 ) {
	project_ensure_exists( $p_project_id );
	if( 0 != $p_new_department_id ) {
		department_ensure_exists( $p_new_department_id );
	}

	# cache department names
	department_get_all_rows( $p_project_id );

	# get a list of affected categories
	db_param_push();
	$t_query = 'SELECT id FROM {department} WHERE project_id=' . db_param();
	$t_result = db_query( $t_query, array( $p_project_id ) );

	$t_department_ids = array();
	while( $t_row = db_fetch_array( $t_result ) ) {
		# Don't add department to the list if it can't be deleted
		if( !department_can_remove( $t_row['id'] ) ) {
			continue;
		}
		$t_department_ids[] = $t_row['id'];
	}

	# Handle projects with no categories
	if( count( $t_department_ids ) < 1 ) {
		return true;
	}

	$t_department_ids = join( ',', $t_department_ids );

	# update bug history entries
	$t_query = 'SELECT id, department_id FROM {bug} WHERE department_id IN ( ' . $t_department_ids . ' )';
	$t_result = db_query( $t_query );

	while( $t_bug_row = db_fetch_array( $t_result ) ) {
		history_log_event_direct( $t_bug_row['id'], 'department', department_full_name( $t_bug_row['department_id'], false ), department_full_name( $p_new_department_id, false ) );
	}

	# update bug data
	db_param_push();
	$t_query = 'UPDATE {bug} SET department_id=' . db_param() . ' WHERE department_id IN ( ' . $t_department_ids . ' )';
	db_query( $t_query, array( $p_new_department_id ) );

	# delete categories
	db_param_push();
	$t_query = 'DELETE FROM {department} WHERE project_id=' . db_param();
	db_query( $t_query, array( $p_project_id ) );

	return true;
}

/**
 * Return the definition row for the department
 * @param integer $p_department_id department identifier.
 * @param boolean $p_error_if_not_exists true: error if not exists, otherwise return false.
 * @return array An array containing department details.
 * @access public
 */
function department_get_row( $p_department_id, $p_error_if_not_exists = true ) {
	
	$p_department_id = (int)$p_department_id;
	$t_query = 'SELECT * FROM {department} WHERE department_id=' . db_param();
	$t_result = db_query( $t_query, array( $p_department_id ) );
	$t_row = db_fetch_array( $t_result );
	if( !$t_row ) {
		if( $p_error_if_not_exists ) {
			trigger_error( ERROR_department_NOT_FOUND, ERROR );
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
 *	@return array A unique array of department names
 */
function department_get_filter_list( $p_project_id = null ) {
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
		$t_categories = array_merge( $t_categories, department_get_all_rows( $t_id ) );
	}

	$t_unique = array();
	foreach( $t_categories as $t_department ) {
		if( !in_array( $t_department['name'], $t_unique ) ) {
			$t_unique[] = $t_department['name'];
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
function department_get_all_rows( ) {
	global $g_department_cache, $g_cache_department_project;


	$t_query = 'SELECT department_id, department, division
  	FROM {department} C LEFT JOIN {division} O
    ON O.division_id = C.division_id
	ORDER BY department_id';
	
	$t_result = db_query( $t_query );
	$t_rows = array();
	while( $t_row = db_fetch_array( $t_result ) ) {
		$t_rows[] = $t_row;
	}

	return $t_rows;
}

/**
 * Cache an set of department ids
 * @param array $p_cat_id_array Array of department identifiers.
 * @return void
 * @access public
 */

/**
 * Given a department id and a field name, this function returns the field value.
 * An error will be triggered for a non-existent department id or department id = 0.
 * @param integer $p_department_id A department identifier.
 * @param string  $p_field_name  Field name.
 * @return string field value
 * @access public
 */
function department_get_field( $p_department_id, $p_field_name ) {
	$t_row = department_get_row( $p_department_id );
	return $t_row[$p_field_name];
}

/**
 * Given a department id, this function returns the department name.
 * An error will be triggered for a non-existent department id or department id = 0.
 * @param integer $p_department_id A department identifier.
 * @return string department name
 * @access public
 */
function department_get_name( $p_department_id ) {
	return department_get_field( $p_department_id, 'name' );
}

/**
 * Given a department name and project, this function returns the department id.
 * An error will be triggered if the specified project does not have a
 * department with that name.
 * @param string  $p_department_name  department name to retrieve.
 * @param integer $p_project_id     A project identifier.
 * @param boolean $p_trigger_errors Whether to trigger error on failure.
 * @return boolean
 * @access public
 */
function department_get_id_by_name( $p_department_name, $p_project_id, $p_trigger_errors = true ) {
	$t_project_name = project_get_name( $p_project_id );

	db_param_push();
	$t_query = 'SELECT id FROM {department} WHERE name=' . db_param() . ' AND project_id=' . db_param();
	$t_result = db_query( $t_query, array( $p_department_name, (int)$p_project_id ) );
	$t_id = db_result( $t_result );
	if( $t_id === false ) {
		if( $p_trigger_errors ) {
			error_parameters( $p_department_name, $t_project_name );
			trigger_error( ERROR_department_NOT_FOUND_FOR_PROJECT, ERROR );
		} else {
			return false;
		}
	}

	return $t_id;
}

/**
 * Retrieves department name (including project name if required)
 * @param string  $p_department_id     department identifier.
 * @param boolean $p_show_project    Show project details.
 * @param integer $p_current_project Current project id override.
 * @return string department full name
 * @access public
 */
function department_full_name( $p_department_id, $p_show_project = true, $p_current_project = null ) {
	if( 0 == $p_department_id ) {
		# No department
		return lang_get( 'no_department' );
	} else if( !department_exists( $p_department_id ) ) {
		return '@' . $p_department_id . '@';
	} else {
		$t_row = department_get_row( $p_department_id );
		$t_project_id = $t_row['project_id'];

		$t_current_project = is_null( $p_current_project ) ? helper_get_current_project() : $p_current_project;

		if( $p_show_project && $t_project_id != $t_current_project ) {
			return '[' . project_get_name( $t_project_id ) . '] ' . $t_row['name'];
		}

		return $t_row['name'];
	}
}

function get_department_list($p_division_id){
	$t_query = 'SELECT department_id,department FROM {department} WHERE division_id = '.db_param();
	$t_result=db_query( $t_query, array( $p_division_id) );
	$department_list="<option>--None--</option>";

	while( $t_row = db_fetch_array( $t_result ) ) {
		$id=(string)$t_row['department_id'];
		$department_list.="<option value='$id'>".$t_row['department']."</option>";
	}
	return $department_list;	
}

function get_edit_department_list($p_department_id,$p_division_id){
	$t_query = 'SELECT department_id,department FROM {department} WHERE division_id = '.db_param();
	$t_result=db_query( $t_query, array( $p_division_id) );
	$department_list="<option>--None--</option>";

	while( $t_row = db_fetch_array( $t_result ) ) {
		$id=(string)$t_row['department_id'];
		if($id==$p_department_id)
			$department_list.="<option value='$id' selected>".$t_row['department']."</option>";
		else
			$department_list.="<option value='$id'>".$t_row['department']."</option>";
	}
	return $department_list;

}