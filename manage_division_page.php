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
require_api( 'division_api.php' );

auth_reauthenticate();

access_ensure_global_level( config_get( 'manage_user_threshold' ) );

layout_page_header( lang_get( 'manage_division_link' ) );

layout_page_begin( 'manage_overview_page.php' );

print_manage_menu( 'manage_division_page.php' );

echo '<div class="col-md-12 col-xs-12">';
echo '<div class="space-10"></div >';

?>

	<div id="divisions" class="form-container">

	<div class="widget-box widget-color-blue2">
	<div class="widget-header widget-header-small">
		<h4 class="widget-title lighter">
			<i class="ace-icon fa fa-sitemap"></i>
			<?php echo lang_get( 'manage_divisions_title' ) ?>
		</h4>
	</div>
	<div class="widget-body">
		<div class="widget-main no-padding">
		<div class="table-responsive">
		<table class="table table-striped table-bordered table-condensed table-hover">
<?php
		$t_divisions = division_get_all_rows();
		$t_can_update_global_cat = access_has_global_level( config_get( 'manage_site_threshold' ) );

		if( count( $t_divisions ) > 0 ) {
?>
		<thead>
			<tr>
				<td><?php echo lang_get( 'division_id' ) ?></td>
				<td><?php echo lang_get( 'division' ) ?></td>
				<?php if( $t_can_update_global_cat ) { ?>
				<td class="center"><?php echo lang_get( 'actions' ) ?></td>
				<?php } ?>
			</tr>
		</thead>

		<tbody>
<?php
			foreach( $t_divisions as $t_division ) {
				$t_id = $t_division['division_id'];
				$t_division = $t_division['division'];
?>
			<tr>
				<td><?php echo $t_id;  ?></td>
				<td><?php echo $t_division; ?></td>
				<?php if( $t_can_update_global_cat ) { ?>
				<td class="center">
<?php
					$t_id = urlencode( $t_id );
					echo '<div class="btn-group inline">';
					echo '<div class="pull-left">';
					print_form_button( "manage_division_edit_page.php?id=$t_id", lang_get( 'edit_link' ) );
					echo '</div>';
					echo '<div class="pull-left">';
					print_form_button( "manage_division_delete.php?id=$t_id", lang_get( 'delete_link' ) );
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

<?php if( $t_can_update_global_cat ) { ?>
	<form method="post" action="manage_division_add.php" class="form-inline">
		<div class="widget-toolbox padding-8 clearfix">
			<?php echo form_security_field( 'manage_division_add' ) ?>
			<input type="text" name="division_name" class="input-sm" size="32" maxlength="128" />
			<input type="submit" class="btn btn-primary btn-sm btn-white btn-round" value="<?php echo lang_get( 'add_division_button' ) ?>" />
			
		</div>
	</form>
<?php } ?>
</div>
</div>
<?php
layout_page_end();
