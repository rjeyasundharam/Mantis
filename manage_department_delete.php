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
 * Remove Project Category
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
 * @uses database_api.php
 * @uses form_api.php
 * @uses gpc_api.php
 * @uses helper_api.php
 * @uses html_api.php
 * @uses lang_api.php
 * @uses print_api.php
 */

require_once( 'core.php' );
require_api( 'access_api.php' );
require_api( 'authentication_api.php' );
require_api( 'config_api.php' );
require_api( 'constant_inc.php' );
require_api( 'database_api.php' );
require_api( 'form_api.php' );
require_api( 'gpc_api.php' );
require_api( 'helper_api.php' );
require_api( 'html_api.php' );
require_api( 'lang_api.php' );
require_api( 'print_api.php' );
require_api( 'string_api.php' );

require_api( 'department_api.php' );

form_security_validate( 'manage_department_delete' );

auth_reauthenticate();

$f_department_id = gpc_get_int( 'id' );

$t_row = department_get_row( $f_department_id );
$t_name = $t_row['department'];

check_department_users( $f_department_id );

access_ensure_global_level( config_get( 'manage_user_threshold' ) );

# Confirm with the user
helper_ensure_confirmed( sprintf( lang_get( 'department_delete_confirm_msg' ), string_display_line( $t_name ) ),
	lang_get( 'delete_department_button' ) );

department_remove( $f_department_id );

form_security_purge( 'manage_department_delete' );

$t_redirect_url = 'manage_department_page.php';

layout_page_header( null, $t_redirect_url );

layout_page_begin( 'manage_overview_page.php' );

html_operation_successful( $t_redirect_url );

layout_page_end();
