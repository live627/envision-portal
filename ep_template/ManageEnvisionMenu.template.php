<?php
// Version 1.0; ManageEnvisionMenu

function template_main()
{
	global $context, $scripturl, $boardurl, $txt, $smcFunc, $settings;

	echo '
	<script type="text/javascript" src="http://code.jquery.com/jquery.min.js"></script>
	<script type="text/javascript">

		$(document).ready(function() {
			$("#bnbox").keyup(function() {
				var button_name = $(this).val();
				$("#bn").html(ajax_notification_text);
				$.ajax({
					type: "GET",
					url: "', $boardurl, '/ep_ajax.php?button=true;bn=" + button_name + ";id=', $context['button_data']['id'], '",
					success: function(data) {
						$("#bn").html(data);
					},
					error: function(XMLHttpRequest, textStatus, errorThrown) {
						$("#bn").html(textStatus);
					}
				});
			});
		});
	</script>
		<form action="', $scripturl, '?action=admin;area=epmenu;sa=epsavebutton" method="post" accept-charset="', $context['character_set'], '" name="postmodify" id="postmodify" class="flow_hidden">
			<div class="cat_bar">
				<h3 class="catbg">
					', $context['page_title'], '
				</h3>
			</div>
			<span class="upperframe"><span></span></span>
				<div class="roundframe">';

	// If an error occurred, explain what happened.
	if (!empty($context['post_error']))
	{
		echo '
					<div class="errorbox" id="errors">
						<strong>', $txt[$context['error_title']], '</strong>
						<ul>';

		foreach ($context['post_error'] as $error)
			echo '
							<li>', $txt[$error], '</li';

		echo '
						</ul>
					</div>';
	}

	echo '
					<dl id="post_header">
						<dt>
							<strong>', $txt['ep_envision_menu_button_name'], ':</strong>
						</dt>
						<dd>
							<input type="text" name="name" id="bnbox" value="', $context['button_data']['name'], '" tabindex="1" class="input_text" class="full_width" />
							<div id="bn"></div>
						</dd>
						<dt>
							<strong>', $txt['ep_envision_menu_button_position'], ':</strong>
						</dt>
						<dd>
							<select name="position" size="10" class="a_fifth" onchange="this.form.position.disabled = this.options[this.selectedIndex].value == \'\';">
								<option value="after"', $context['button_data']['position'] == 'after' ? ' selected="selected"' : '', '>' . $txt['mboards_order_after'] . '...</option>
								<option value="child_of"', $context['button_data']['position'] == 'child_of' ? ' selected="selected"' : '', '>' . $txt['mboards_order_child_of'] . '...</option>
								<option value="before"', $context['button_data']['position'] == 'before' ? ' selected="selected"' : '', '>' . $txt['mboards_order_before'] . '...</option>
							</select>
							<select name="parent" size="10" class="three_quarters">';

	foreach ($context['menu_buttons'] as $buttonIndex => $buttonData)
	{
		echo '
									<option value="', $buttonIndex, '"', $context['button_data']['parent'] == $buttonIndex ? ' selected="selected"' : '', '>', $buttonData['title'], '</option>';

		if (!empty($buttonData['sub_buttons']))
		{
			foreach ($buttonData['sub_buttons'] as $childButton => $childButtonData)
				echo '
									<option value="', $childButton, '"', $context['button_data']['parent'] == $childButton ? ' selected="selected"' : '', '>- ', $childButtonData['title'], '</option>';

			if (!empty($childButtonData['sub_buttons']))
				foreach ($childButtonData['sub_buttons'] as $grandChildButton => $grandChildButtonData)
					echo '
									<option value="', $grandChildButton, '"', $context['button_data']['parent'] == $grandChildButton ? ' selected="selected"' : '', '>- ', $grandChildButtonData['title'], '</option>';
		}
	}

	echo '
							</select>
						</dd>
						<dt>
							<strong>', $txt['ep_envision_menu_button_type'], ':</strong>
						</dt>
						<dd>
							<input type="radio" class="input_check" name="type" value="forum"', $context['button_data']['type'] == 'forum' ? ' checked="checked"' : '', '/>', $txt['ep_envision_menu_forum'], '<br />
							<input type="radio" class="input_check" name="type" value="external"', $context['button_data']['type'] == 'external' ? ' checked="checked"' : '', '/>', $txt['ep_envision_menu_external'], '
						</dd>
						<dt>
							<strong>', $txt['ep_envision_menu_link_type'], ':</strong>
						</dt>
						<dd>
							<input type="radio" class="input_check" name="target" value="_self"', $context['button_data']['target'] == '_self' ? ' checked="checked"' : '', '/>', $txt['ep_envision_menu_same_window'], '<br />
							<input type="radio" class="input_check" name="target" value="_blank"', $context['button_data']['target'] == '_blank' ? ' checked="checked"' : '', '/>', $txt['ep_envision_menu_new_tab'], '
						</dd>
						<dt>
							<strong>', $txt['ep_envision_menu_button_link'], ':</strong><br />
						</dt>
						<dd>
							<input type="text" name="link" value="', $context['button_data']['link'], '" tabindex="1" class="input_text" class="full_width" />
							<span class="smalltext">', $txt['ep_envision_menu_button_link_desc'], '</span>
						</dd>
						<dt>
							<strong>', $txt['ep_envision_menu_button_perms'], ':</strong>
						</dt>
						<dd>
							<fieldset id="group_perms">
								<legend><a href="javascript:void(0);" onclick="document.getElementById(\'group_perms\').style.display = \'none\';document.getElementById(\'group_perms_groups_link\').style.display = \'block\'; return false;">', $txt['avatar_select_permission'], '</a></legend>';

	$all_checked = true;

	// List all the groups to configure permissions for.
	foreach ($context['button_data']['permissions'] as $permission)
	{
		echo '
								<div id="permissions_', $permission['id'], '">
									<label for="check_group', $permission['id'], '">
										<input type="checkbox" class="input_check" name="permissions[]" value="', $permission['id'], '" id="check_group', $permission['id'], '"', $permission['checked'] ? ' checked="checked"' : '', ' />
										<span', ($permission['is_post_group'] ? ' class="border-bottom" title="' . $txt['mboards_groups_post_group'] . '"' : ''), '>', $permission['name'], '</span>
									</label>
								</div>';

		if (!$permission['checked'])
			$all_checked = false;
	}

	echo '
								<input type="checkbox" class="input_check" onclick="invertAll(this, this.form, \'permissions[]\');" id="check_group_all"', $all_checked ? ' checked="checked"' : '', ' />
								<label for="check_group_all"><em>', $txt['check_all'], '</em></label><br />
							</fieldset>
							<a href="javascript:void(0);" onclick="document.getElementById(\'group_perms\').style.display = \'block\'; document.getElementById(\'group_perms_groups_link\').style.display = \'none\'; return false;" id="group_perms_groups_link" style="display: none;">[ ', $txt['avatar_select_permission'], ' ]</a>
							<script type="text/javascript"><!-- // --><![CDATA[
								document.getElementById("group_perms").style.display = "none";
								document.getElementById("group_perms_groups_link").style.display = "";
							// ]]></script>
						</dd>
						<dt>
							<strong>', $txt['ep_envision_menu_button_status'], ':</strong>
						</dt>
						<dd>
							<input type="radio" class="input_check" name="status" value="1"', $context['button_data']['status'] == '1' ? ' checked="checked"' : '', ' />', $txt['ep_envision_menu_button_active'], ' <br />
							<input type="radio" class="input_check" name="status" value="0"', $context['button_data']['status'] == '0' ? ' checked="checked"' : '', ' />', $txt['ep_envision_menu_button_inactive'], '
						</dd>
					</dl>
					<input name="bid" value="', $context['button_data']['id'], '" type="hidden" />
					<div class="righttext padding">
						<input name="submit" value="', $txt['ep_admin_manage_menu_submit'], '" class="button_submit" type="submit" />
					</div>
				</div>
			</form>
			<span class="lowerframe"><span></span></span>
			<br class="clear" />';
}

?>