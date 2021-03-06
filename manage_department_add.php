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
 * Project Category Add
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
 * @uses error_api.php
 * @uses form_api.php
 * @uses gpc_api.php
 * @uses lang_api.php
 * @uses print_api.php
 * @uses utility_api.php
 */

require_once( 'core.php' );
require_api( 'access_api.php' );
require_api( 'authentication_api.php' );
require_api( 'config_api.php' );
require_api( 'constant_inc.php' );
require_api( 'error_api.php' );
require_api( 'form_api.php' );
require_api( 'gpc_api.php' );
require_api( 'lang_api.php' );
require_api( 'print_api.php' );
require_api( 'utility_api.php' );
require_api( 'division_api.php' );
require_api( 'department_api.php' );

form_security_validate( 'manage_department_add' );

auth_reauthenticate();

$f_division	= gpc_get_string( 'division' );
$f_department = gpc_get_string( 'department' );

access_ensure_global_level( config_get( 'manage_user_threshold' ) );

	echo "Name = ".$f_department;
if( is_blank( $f_department ) ) {
	error_parameters( lang_get( 'department' ) );
	trigger_error( ERROR_EMPTY_FIELD, ERROR );
}

	$t_department = trim( $f_department );
	$t_division = $f_division;
echo "Name = ".$f_department;
	if( department_is_unique($t_department,$t_division ) ) {
		department_add( $t_department,$t_division );
		
		echo "Name = ".$f_department;
	} else  {
		# We only error out on duplicates when a single value was
		#  given.  If multiple values were given, we just add the
		#  ones we can.  The others already exist so it isn't really
		#  an error.

		trigger_error( ERROR_CATEGORY_DUPLICATE, ERROR );
	}
echo "Name = ".$f_department;
form_security_purge( 'manage_department_add' );

$t_redirect_url = 'manage_department_page.php';

print_header_redirect( $t_redirect_url );
