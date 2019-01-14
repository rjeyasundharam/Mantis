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
 * Update Project Categories
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
 * @uses constant_inc.php
 * @uses form_api.php
 * @uses gpc_api.php
 * @uses html_api.php
 * @uses lang_api.php
 * @uses print_api.php
 * @uses utility_api.php
 */

require_once( 'core.php' );
require_api( 'access_api.php' );
require_api( 'authentication_api.php' );
require_api( 'config_api.php' );
require_api( 'constant_inc.php' );
require_api( 'form_api.php' );
require_api( 'gpc_api.php' );
require_api( 'html_api.php' );
require_api( 'lang_api.php' );
require_api( 'print_api.php' );
require_api( 'utility_api.php' );

require_api( 'department_api.php' );

form_security_validate( 'manage_department_update' );

auth_reauthenticate();

$f_department_id = gpc_get_int( 'department_id' );
$f_department = trim( gpc_get_string( 'department' ) );
$f_division_id = trim( gpc_get_string( 'division_id' ) );

access_ensure_global_level( config_get( 'manage_user_threshold' ) );

if( is_blank( $f_department ) ) {
	trigger_error( ERROR_EMPTY_FIELD, ERROR );
}
if( is_blank( $f_division_id ) ) {
	trigger_error( ERROR_EMPTY_FIELD, ERROR );
}

$t_row = department_get_row( $f_department_id );
$t_department = $t_row['department'];

# check for duplicate
department_ensure_unique( $f_division_id ,$f_department );

department_update( $f_department_id, $f_department,$f_division_id );

form_security_purge( 'manage_department_update' );

$t_redirect_url = 'manage_department_page.php';

layout_page_header( null, $t_redirect_url );

layout_page_begin( 'manage_overview_page.php' );

html_operation_successful( $t_redirect_url );

layout_page_end();
