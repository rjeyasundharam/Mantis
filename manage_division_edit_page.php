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
require_api( 'lang_api.php' );
require_api( 'print_api.php' );
require_api( 'string_api.php' );
require_api( 'division_api.php' );

auth_reauthenticate();

$f_division_id	= gpc_get_int( 'id' );

$t_row = division_get_row( $f_division_id );
$t_division = $t_row['division'];

access_ensure_global_level( config_get( 'manage_user_threshold' ) );

layout_page_header( lang_get( 'manage_division_link' ) );

layout_page_begin( 'manage_overview_page.php' );

print_manage_menu( 'manage_division_page.php' );
?>

<div class="col-md-12 col-xs-12">
	<div class="space-10"></div>
	<div id="manage-proj-category-update-div" class="form-container">
	<form id="manage-proj-category-update-form" method="post" action="manage_division_update.php">
	<div class="widget-box widget-color-blue2">
		<div class="widget-header widget-header-small">
			<h4 class="widget-title lighter">
				<i class="ace-icon fa fa-sitemap"></i>
				<?php echo lang_get('edit_division_title') ?>
			</h4>
		</div>
		<div class="widget-body">
		<div class="widget-main no-padding">
		<div class="table-responsive">
		<table class="table table-bordered table-condensed table-striped">
		<fieldset>
			<?php echo form_security_field( 'manage_division_update' ) ?>
			<input type="hidden" name="division_id" value="<?php echo $f_division_id; ?>"/>
			<tr>
				<td class="category">
					<?php echo lang_get( 'division' ) ?>
				</td>
				<td>
					<input type="text" id="proj-category-name" name="division" class="input-sm" size="32" maxlength="128" value="<?php echo string_attribute( $t_division ) ?>" />
				</td>
			</tr>
		</fieldset>
		</table>
		</div>
		</div>
		</div>
		<div class="widget-toolbox padding-8 clearfix">
			<input type="submit" class="btn btn-primary btn-white btn-round" value="<?php echo lang_get( 'update_division_button' ) ?>" />
		</div>
	</div>
	</form>
	</div>
</div>

<div class="col-md-12 col-xs-12">
	<form method="post" action="manage_division_delete.php" class="pull-right">
		<fieldset>
			<?php echo form_security_field( 'manage_division_delete' ) ?>
			<input type="hidden" name="id" value="<?php echo string_attribute( $f_division_id ) ?>" />
			<input type="submit" class="btn btn-sm btn-primary btn-white btn-round" value="<?php echo lang_get( 'delete_division_button' ) ?>" />
		</fieldset>
	</form>
</div><?php

layout_page_end();
