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
 * User Page
 *
 * @package MantisBT
 * @copyright Copyright 2000 - 2002  Kenzaburo Ito - kenito@300baud.org
 * @copyright Copyright 2002  MantisBT Team - mantisbt-dev@lists.sourceforge.net
 * @link http://www.mantisbt.org
 *
 * @uses core.php
 * @uses access_api.php
 * @uses authentication_api.php
 * @uses config_api.php
 * @uses constant_inc.php
 * @uses database_api.php
 * @uses gpc_api.php
 * @uses helper_api.php
 * @uses html_api.php
 * @uses icon_api.php
 * @uses lang_api.php
 * @uses print_api.php
 * @uses string_api.php
 * @uses utility_api.php
 */

require_once( 'core.php' );
require_api( 'access_api.php' );
require_api( 'authentication_api.php' );
require_api( 'config_api.php' );
require_api( 'constant_inc.php' );
require_api( 'database_api.php' );
require_api( 'gpc_api.php' );
require_api( 'helper_api.php' );
require_api( 'html_api.php' );
require_api( 'icon_api.php' );
require_api( 'lang_api.php' );
require_api( 'print_api.php' );
require_api( 'string_api.php' );
require_api( 'utility_api.php' );
require_api( 'department_api.php' );

auth_reauthenticate();

access_ensure_global_level( config_get( 'manage_user_threshold' ) );


layout_page_header( lang_get( 'manage_department_link' ) );

layout_page_begin( 'manage_overview_page.php' );

print_manage_menu( 'manage_department_page.php' );

echo '<div class="col-md-12 col-xs-12">';
echo '<div class = "space-10"></div>';

?>
<div class="widget-box widget-color-blue2">
<div class="widget-header widget-header-small">
<h4 class="widget-title lighter">
	<i class="ace-icon fa fa-users"></i>
	<?php echo lang_get('manage_department_title') ?>
</h4>
</div>

<div class="widget-body">
<div class="widget-toolbox padding-8 clearfix">
	<div id="manage-user-div" class="form-container">
		<div class="pull-left">
			<?php print_link_button( 'manage_department_create_page.php', lang_get( 'create_new_department_link' ),'btn-sm' ) ?>
		</div>
		
	<div class="pull-right">
	<form id="manage-user-filter" method="post" action="manage_user_page.php" class="form-inline">
		<fieldset>
			
		</fieldset>
	</form>
		</div>
	</div>
</div>

<div class="widget-main no-padding">
	<div class="table-responsive">
		<table class="table table-striped table-bordered table-condensed table-hover">
		<?php
		$t_departments = department_get_all_rows();
		$t_can_update_global_cat = access_has_global_level( config_get( 'manage_site_threshold' ) );

		if( count( $t_departments ) > 0 ) {
?>
		<thead>
			<tr>
				<td><?php echo lang_get( 'department_id' ) ?></td>
				<td><?php echo lang_get( 'department' ) ?></td>
				<td><?php echo lang_get( 'division' ) ?></td>
				<?php if( $t_can_update_global_cat ) { ?>
				<td class="center"><?php echo lang_get( 'actions' ) ?></td>
				<?php } ?>
			</tr>
		</thead>

		<tbody>
<?php
			foreach( $t_departments as $t_department ) {
				$department_id = $t_department['department_id'];
				$department = $t_department['department'];
				$division = $t_department['division'];
?>
			<tr>
				<td><?php echo $department_id;  ?></td>
				<td><?php echo $department; ?></td>
				<td><?php echo $division; ?></td>
				<?php if( $t_can_update_global_cat ) { ?>
				<td class="center">
<?php
					$t_id = urlencode( $department_id );
					echo '<div class="btn-group inline">';
					echo '<div class="pull-left">';
					print_form_button( "manage_department_edit_page.php?id=$t_id", lang_get( 'edit_link' ) );
					echo '</div>';
					echo '<div class="pull-left">';
					print_form_button( "manage_department_delete.php?id=$t_id", lang_get( 'delete_link' ) );
					echo '</div>';
?>
				</td>
			<?php } ?>
			</tr>
<?php
			} # end for loop
?>
		</tbody>
<?php
		} # end if
?>
	</table>


</div>
</div>




</div>

<?php
layout_page_end();
