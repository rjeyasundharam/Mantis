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
 * User Create Page
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
 * @uses form_api.php
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
require_api( 'form_api.php' );
require_api( 'helper_api.php' );
require_api( 'html_api.php' );
require_api( 'lang_api.php' );
require_api( 'print_api.php' );
require_api( 'department_api.php' );
require_api( 'division_api.php' );

auth_reauthenticate();

access_ensure_global_level( config_get( 'manage_user_threshold' ) );

layout_page_header();

layout_page_begin( 'manage_overview_page.php' );

print_manage_menu( 'manage_department_page.php' );
?>
<div class="col-md-12 col-xs-12">
<div class="space-10"></div>
<div id="manage-user-create-div" class="form-container">
	<form id="manage-department-create-form" method="post" action="manage_department_add.php">
	<div class="widget-box widget-color-blue2">
		<div class="widget-header widget-header-small">
			<h4 class="widget-title lighter">
				<i class="ace-icon fa fa-user"></i>
				<?php echo lang_get( 'create_new_department_title' ) ?>
			</h4>
		</div>
		<div class="widget-body">
		<div class="widget-main no-padding">
		<div class="table-responsive">
		<table class="table table-bordered table-condensed table-striped">
		<fieldset>
			<?php echo form_security_field( 'manage_department_add' ) ?>
			<tr>
				<td class="category">
					<span class="required">*</span> <?php echo lang_get( 'division' ) ?>
				</td>
				<td>
					<select name="division" class="input-sm" required>
						<?php echo  get_division_list(); ?>
					</select>
				</td>
			</tr>
			<tr>
				<td class="category">
					<span class="required">*</span> <?php echo lang_get( 'department' ) ?>
				</td>
				<td>
					<input type="text"  name="department" class="input-sm" size="32"  />
				</td>
			</tr>

			
			</fieldset>
			</table>
			</div>
			</div>
			</div>

			<div class="widget-toolbox padding-8 clearfix">
				<input type="submit" class="btn btn-primary btn-white btn-round" value="<?php echo lang_get( 'create_department_button' ) ?>" />
			</div>
		</div>
		</div>
	</form>
</div>

<?php
layout_page_end();
