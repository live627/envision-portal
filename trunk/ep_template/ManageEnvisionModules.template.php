<?php
// Version 1.0; ManageEnvisionModules

/**
 * This file handles showing Envision Portal's module management settings.
 *
 * @package template
 * @since 1.0
*/

/**
 * Template used to modify the options of modules/clones.
 *
 * @since 1.0
 */
function template_modify_modules()
{
	global $txt, $context, $scripturl, $settings, $modSettings, $boardurl;

	echo '
	<div id="admincenter">
		<form name="epmodule" id="epmodule" action="', $scripturl, '?action=admin;area=epmodules;sa=modify2;in=', $_GET['in'], ';', $context['session_var'], '=', $context['session_id'], '" method="post" accept-charset="', $context['character_set'], '">';

	echo '
			<div class="title_bar">
				<h3 class="titlebg">
					', /*(!empty($context['mod_info'][$context['ep_modid']]['help']) ? '<a href="' . $scripturl . '?action=helpadmin;help=' . $context['mod_info'][$context['ep_modid']]['help'] . '" onclick="return reqWin(this.href);" class="help"><img src="' . $settings['images_url'] . '/helptopics.gif" alt="' . $txt['help'] . '" /></a>' : ''), */$txt['ep_module_' . $context['ep_module_type']] . $txt['ep_modsettings'], '
				</h3>
			</div>';

	if (isset($txt['epmodinfo_' . $context['ep_module_type']]))
		echo '
			<p class="information">', $txt['epmodinfo_' . $context['ep_module_type']], '</p>';

	echo '
			<span class="upperframe"><span></span></span>
			<div class="roundframe">
			<dl class="settings">';

	// Now loop through all the parameters.
	foreach ($context['ep_module'] as $key => $field)
	{
		echo '
				<dt>';

		if (!empty($field['help']))
			echo '
					<a id="setting_' . $key . '" href="' . $scripturl . '?action=helpadmin;help=' . $field['help'] . '" onclick="return reqWin(this.href);" class="help"><img src="' . $settings['images_url'] . '/helptopics.gif" alt="' . $txt['help'] . '" border="0" /></a>
					<span>';

		echo '
						<label for="', $field['label'], '">', $txt[$field['label']], '</label>
					</span>
				</dt>
				<dd>';

		switch ($field['type'])
		{
			case 'text':
				echo '
					<input type="text" name="', $key, '" id="', $field['label'], '"value="', $field['value'], '" class="input_text" />';
				break;

			case 'large_text': case 'html':
				echo '
					<textarea class="w100" name="', $key, '" id="', $field['label'], '">', $field['value'], '</textarea>';
				break;

			case 'check':
				echo '
					<input type="checkbox" name="', $key, '" id="', $field['label'], '"', (!empty($field['value']) ? ' checked="checked"' : ''), ' value="1" class="input_check" />';
				break;

			case 'select': case 'file_select': case 'icon_select':
				echo '
					<select name="', $key, '" id="', $field['label'], '"';

				if ($field['type'] == 'icon_select')
					echo ' onchange="javascript:document.getElementById(\'', $field['label'], '_preview\').src = \'', $field['url'], '\' + this.options[this.selectedIndex].value;"';

				echo '>';

				foreach ($field['options'] as $option)
					echo '
						<option value="', $option, '"', ($option == $field['value'] ? ' selected="selected"' : ''), '>', $txt['ep_' . $key . '_' . $option], '</option>';

				echo '
					</select>';

				if ($field['type'] == 'icon_select')
					echo '
					<img id="', $field['label'], '_preview" class="iconpreview" src="', $field['url'], $field['value'], '" />';
				break;

			case 'list_groups':
				echo '
					<fieldset id="', $field['label'], '_group_perms">
						<legend>
							<a href="#" onclick="document.getElementById(\'', $field['label'], '_group_perms\').style.display = \'none\';document.getElementById(\'', $field['label'], '_group_perms_groups_link\').style.display = \'block\'; return false;">', $txt['avatar_select_permission'], '</a>
						</legend>';

		$all_checked = true;

		// List all the groups to configure permissions for.
		foreach ($field['options'] as $group)
		{
			echo '
							<div id="permissions_', $group['id'], '">
								<label for="check_group', $group['id'], '">
									<input type="checkbox" class="input_check" name="', $key, '[]" value="', $group['id'], '" id="check_group', $group['id'], '"', $group['checked'] ? ' checked="checked"' : '', ' />
									<span', ($group['is_post_group'] ? ' style="border-bottom: 1px dotted;" title="' . $txt['mboards_groups_post_group'] . '"' : ''), '>', $group['name'], '</span>
								</label>
							</div>';

			if (!$group['checked'])
				$all_checked = false;
		}

		echo '
						<input type="checkbox" class="input_check" onclick="invertAll(this, this.form, \'', $field['label'], '_groups[]\');" id="check_group_all"', $all_checked ? ' checked="checked"' : '', ' />
						<label for="check_group_all">
							<em>', $txt['check_all'], '</em>
						</label>
						<br />
					</fieldset>
					<a href="#" onclick="document.getElementById(\'', $field['label'], '_group_perms\').style.display = \'block\'; document.getElementById(\'', $field['label'], '_group_perms_groups_link\').style.display = \'none\'; return false;" id="', $field['label'], '_group_perms_groups_link" style="display: none;">[ ', $txt['avatar_select_permission'], ' ]</a>
					<script type="text/javascript"><!-- // --><![CDATA[
						document.getElementById("', $field['label'], '_group_perms").style.display = "none";
						document.getElementById("', $field['label'], '_group_perms_groups_link").style.display = "";
					// ]]></script>';
		}
	}

	$counter = 0;
	$hiddentags = '';
	foreach ($context['config_params'] as $config_id => $config_param)
	{
		$counter++;
		$help = '<a id="setting_' . $config_param['name'] . '"></a></dt><dt>';

		// Show the [?] button.
		if (!empty($config_param['help']))
			$help = '
				<dt>
					<a id="setting_' . $config_param['name'] . '" href="' . $scripturl . '?action=helpadmin;help=' . $config_param['help'] . '" onclick="return reqWin(this.href);" class="help"><img src="' . $settings['images_url'] . '/helptopics.gif" alt="' . $txt['help'] . '" border="0" /></a>
					<span>';

		echo $help, '
						<label for="', $config_param['label_id'], '">', $txt[$config_param['label']], '</label>
					</span>
				</dt>
				<dd>';

			if ($config_param['type'] == 'check')
				echo '
					<input type="checkbox" name="', $config_param['name'], '" id="', $config_param['label_id'], '"', (!empty($config_param['value']) ? ' checked="checked"' : ''), ' value="1" class="input_check" />';

			elseif ($config_param['type'] == 'db_select' && $config_param['db_select_custom'])
			{
				if (!isset($load_script))
				{
					$load_script = true;
					echo '
						<script type="text/javascript" src="' . $settings['default_theme_url'] . '/scripts/epAdmin.js"></script>';
				}

				echo '
					<div id="db_select_option_list_', $config_id, '"">';

				foreach ($config_param['db_select_options'] as $key => $select_value)
					echo '
							<div id="db_select_container_', $config_param['label_id'], '_', $key, '"><input type="radio" name="', $config_param['name'], '" id="', $config_param['label_id'], '_', $key, '" value="', $key, '"', ($key == $config_param['db_selected'] ? ' checked="checked"' : ''), ' class="input_check" /> <label for="', $config_param['label_id'], '_', $key, '" id="label_', $config_param['label_id'], '_', $key, '">', $select_value ,'</label> <span id="db_select_edit_', $config_param['label_id'], '_', $key, '" class="smalltext">(<a href="#" onclick="epEditDbSelect(', $config_id, ', \'', $config_param['label_id'], '_', $key, '\'); return false;" id="', $config_param['label_id'], '_', $key, '_db_custom_more">', $txt['ep_edit'], '</a>', $key != 1 ? ' - <a href="#" onclick="epDeleteDbSelect(' . $config_id . ', \'' . $config_param['label_id'] . '_' . $key . '\'); return false;" id="' . $config_param['label_id'] . '_' . $key . '_db_custom_delete">' . $txt['delete'] . '</a>' : '', ')</span></div>';

				echo '
					</div>
						<input type="hidden" name="param_opts', $config_id, '" value="', $config_param['options'], '" />
						<script type="text/javascript"><!-- // --><![CDATA[
							function epEditDbSelect(config_id, key)
							{
								var parent = document.getElementById(\'db_select_edit_\' + key);
								var child = document.getElementById(key + \'_db_custom_more\');
								var newElement = document.createElement("input");
								newElement.type = "text";
								newElement.value = document.getElementById(\'label_\' + key).innerHTML;
								newElement.name = "edit_" + key;
								newElement.id = "edit_" + key;
								newElement.className = "input_text";
								newElement.setAttribute("size", 30);

								parent.insertBefore(newElement, child);
								newElement.focus();
								newElement.select();

								document.getElementById(\'label_\' + key).style.display = \'none\';
								child.style.display = \'none\';

								newElement = document.createElement("span");
								newElement.innerHTML = " <a href=\"#\" onclick=\"epSubmitEditDbSelect(" + config_id + ", \'" + key + "\'); return false;\">', $txt['ep_submit'], '</a> - <a href=\"#\" onclick=\"epCancelEditDbSelect(" + config_id + ", \'" + key + "\'); return false;\">', $txt['ep_cancel'], '</a> - ";
								newElement.id = "db_select_edit_buttons_" + key;

								document.getElementById(\'db_select_edit_\' + key).insertBefore(newElement, document.getElementById(key + \'_db_custom_delete\'));

								return true;
							}

							function epSubmitEditDbSelect(config_id, key)
							{
								var send_data = "data=" + escape(document.getElementById("edit_" + key).value.replace(/&#/g, "&#").php_to8bit()).replace(/\+/g, "%2B") + "&config_id=" + config_id + "&key=" + key;
								var url = smf_prepareScriptUrl(smf_scripturl) + "action=envision;sa=dbSelect;xml";

								sendXMLDocument(url, send_data);

								var parent = document.getElementById(\'db_select_edit_\' + key);

								document.getElementById(key + \'_db_custom_more\').style.display = \'\';
								document.getElementById(\'label_\' + key).innerHTML = document.getElementById("edit_" + key).value;
								document.getElementById(\'label_\' + key).style.display = \'\';
								parent.removeChild(document.getElementById(\'db_select_edit_buttons_\' + key));
								parent.removeChild(document.getElementById(\'edit_\' + key));

								return true;
							}

							function epCancelEditDbSelect(config_id, key)
							{
								var parent = document.getElementById(\'db_select_edit_\' + key);

								parent.removeChild(document.getElementById(\'db_select_edit_buttons_\' + key));
								parent.removeChild(document.getElementById(\'edit_\' + key));
								document.getElementById(key + \'_db_custom_more\').style.display = \'\';
								document.getElementById(\'label_\' + key).style.display = \'\';

								return true;
							}

							function epDeleteDbSelect(config_id, key)
							{
								var parent = document.getElementById(\'db_select_container_\' + key);

								newElement = document.createElement("span");
								newElement.innerHTML = document.getElementById(\'label_\' + key).innerHTML + " <span class=\"smalltext\">(', $txt['ep_deleted'], ' - <a href=\"#\" onclick=\"epRestoreDbSelect(" + config_id + ", \'" + key + "\'); return false;\">', $txt['ep_restore'], '</a>)</span>";
								newElement.id = "db_select_deleted_" + key;

								parent.appendChild(newElement);
								oHidden = addHiddenElement("epModule", document.getElementById(\'label_\' + key).innerHTML, "epDeletedDbSelects_" + config_id);
								oHidden.id = "epDeletedDbSelects_" + key;
								oHidden.name = "epDeletedDbSelects_" + config_id + "[]";

								document.getElementById(key).style.display = \'none\';
								document.getElementById(\'label_\' + key).style.display = \'none\';
								document.getElementById(\'db_select_edit_\' + key).style.display = \'none\';

								return true;
							}

							function epRestoreDbSelect(config_id, key)
							{
								var parent = document.getElementById(\'db_select_container_\' + key);
								var child = document.getElementById(\'db_select_deleted_\' + key);

								parent.removeChild(child);
								document.forms["epModule"].removeChild(document.getElementById("epDeletedDbSelects_" + key));

								document.getElementById(key).style.display = \'\';
								document.getElementById(\'label_\' + key).style.display = \'\';
								document.getElementById(\'db_select_edit_\' + key).style.display = \'\';

								return true;
							}

							function epInsertBefore(oParent, oChild, sType)
							{
								var parent = document.getElementById(oParent);
								var child = document.getElementById(oChild);
								var newElement = document.createElement("input");
								newElement.type = sType;
								newElement.value = "";
								newElement.name = "', $config_param['name'], '_db_custom[]";
								newElement.className = "input_text";
								newElement.setAttribute("size", "' . $config_param['size'] . '");
								newElement.setAttribute("style", "display: block");

								parent.insertBefore(newElement, child);

								return true;
							}
						// ]]></script>
					<div id="', $config_param['name'], '_db_custom_container" class="smalltext">
							<a href="#" onclick="epInsertBefore(\'', $config_param['name'], '_db_custom_container\', \'', $config_param['name'], '_db_custom_more\', \'text\'); return false;" id="', $config_param['name'], '_db_custom_more">(', $txt['ep_add_another'], ')</a>
					</div>';
			}
			elseif ($config_param['type'] == 'int')
				echo '
					<input type="text" name="', $config_param['name'], '" id="', $config_param['label_id'], '" value="', $config_param['value'], '"', ($config_param['size'] ? ' size="' . $config_param['size'] . '" ' : ' '), 'class="input_text" />';
			elseif ($config_param['type'] == 'large_text' || $config_param['type'] == 'html')
				echo '
					<textarea rows="', (!empty($config_param['size']) ? $config_param['size'] : 4), '" cols="60" name="', $config_param['name'], '" id="', $config_param['label_id'], '">', $config_param['value'], '</textarea>';
			elseif ($config_param['type'] == 'select' || $config_param['type'] == 'list_boards' || ($config_param['type'] == 'db_select' && !$config_param['db_select_custom']))
			{
				echo '
					<select name="', $config_param['name'], '" id="', $config_param['label_id'], '">';

					// Show all boards within a Category for each Category.
					if ($config_param['type'] == 'list_boards')
					{
						foreach ($config_param['select_options'] as $key => $option)
						{
							echo '
										<optgroup label="', $option['category'], '">';

							foreach ($option['board'] as $boardid => $board)
								echo '
												<option value="', $boardid, '"', ((strval($boardid) == $config_param['select_value'] || (trim($config_param['select_value']) == '' && empty($boardid))) ? ' selected="selected"' : ''), '>', $board, '</option>';

							echo '
										</optgroup>';
						}
					}
					elseif ($config_param['type'] == 'db_select' && !$config_param['db_select_custom'])
					{
						if(!isset($load_script))
						{
							$load_script = true;
							echo '
										<script type="text/javascript" src="' . $settings['default_theme_url'] . '/scripts/epAdmin.js"></script>';
						}

						foreach ($config_param['db_select_options'] as $key => $select_value)
							echo '
											<option value="', $key, '"', ($key == $config_param['db_selected'] ? ' selected="selected"' : ''), '>', $select_value, '</option>';
					}
					elseif ($config_param['type'] == 'list_boards')
					{
						foreach ($config_param['select_options'] as $key => $option)
						{
							echo '
										<option value="', $key, '"', ($key == $config_param['select_value'] || (trim($config_param['select_value']) == '' && empty($key)) ? ' selected="selected"' : ''), '>', $txt['epmod_' . $config_param['name'] . '_' . $option], '</option>';
						}
					}
					else
					{
						foreach ($config_param['select_options'] as $key => $option)
						{
							if ($config_param['type'] == 'select')
								$option = $txt['epmod_' . $config_param['name'] . '_' . $option];

							echo '
										<option value="', $key, '"', ($key == $config_param['select_value'] || (trim($config_param['select_value']) == '' && empty($key)) ? ' selected="selected"' : ''), '>', $option, '</option>';
						}
					}

					echo '
									</select>';

				if ($config_param['type'] == 'select' || ($config_param['type'] == 'db_select' && !$config_param['db_select_custom']))
					echo '
									<input type="hidden" name="param_opts', $config_id, '" value="', $config_param['options'], '" />';
			}

			// Rich Edit text area.
			elseif ($config_param['type'] == 'rich_edit')
				template_control_richedit($config_param['post_box_name']);

			// BBC list...
			elseif ($config_param['type'] == 'list_bbc')
			{
				if(!isset($load_script))
				{
					$load_script = true;
					echo '
					<script type="text/javascript" src="' . $settings['default_theme_url'] . '/scripts/epAdmin.js"></script>';
				}

					echo '
							<fieldset id="', $config_param['name'], '">
								<legend>', $txt['bbcTagsToUse_select'], '</legend>
									<ul class="reset">';

					foreach ($config_param['bbc_columns'] as $bbcColumn)
					{
						foreach ($bbcColumn as $bbcTag)
							echo '
										<li class="list_bbc align_left">
											<input type="checkbox" name="', $config_param['name'], '_enabledTags[]" id="tag_', $config_param['name'], '_', $bbcTag['tag'], '" value="', $bbcTag['tag'], '"', isset($config_param['bbc_sections'][$bbcTag['tag']]['disabled']) && !in_array($bbcTag['tag'], $config_param['bbc_sections'][$bbcTag['tag']]['disabled']) ? ' checked="checked"' : '', ' class="input_check" /> <label for="tag_', $config_param['name'], '_', $bbcTag['tag'], '">', $bbcTag['tag'], '</label>', $bbcTag['show_help'] ? ' (<a href="' . $scripturl . '?action=helpadmin;help=tag_' . $bbcTag['tag'] . '" onclick="return reqWin(this.href);">?</a>)' : '', '
										</li>';
					}
					echo '			</ul>
					<input type="checkbox" id="select_all', $config_id, '" onclick="invertAll(this, this.form, \'', $config_param['name'], '_enabledTags\');"', $config_param['bbc_all_selected'] ? ' checked="checked"' : '', ' class="input_check" /> <label for="select_all', $config_id, '"><em>', $txt['bbcTagsToUse_select_all'], '</em></label>
							</fieldset>';
			}

			// List Groups or Checklist.
			elseif ($config_param['type'] == 'list_groups' || $config_param['type'] == 'checklist')
			{
				$checkCount = 0;

				$checkid = $config_param['type'] == 'list_groups' ? 'grp' : 'chk';
				$checkname = $config_param['type'] == 'list_groups' ? 'group' : 'check';

				if($config_param['check_order'] && !isset($load_script))
				{
					$load_script = true;
					echo '
					<script type="text/javascript" src="' . $settings['default_theme_url'] . '/scripts/epAdmin.js"></script>';
				}

				foreach($config_param['check_options'] as $check)
				{
					$checkCount++;
		echo '
							<div id="', $checkid, '_', $counter . '_' . $checkCount, '"><label for="', $checkname . 's_' . $counter . $check['id'], '"><input type="checkbox" name="', $checkname . 's' . $counter, '[]" value="', $check['id'], '" id="', $checkname . 's_' . $counter . $check['id'], '" ', ($check['checked'] ? 'checked="checked" ' : ''), 'class="input_check" /><span', ($config_param['type'] == 'list_groups' ? ($check['is_post_group'] ? ' style="border-bottom: 1px dotted;" title="' . $txt['mboards_groups_post_group'] . '"' : '') : ''), '>', $check['name'], '</span></label>', $config_param['check_order'] ? '<span style="padding-left: 10px;"><a href="javascript:void(0);" onClick="moveUp(this.parentNode.parentNode); orderChecks(\'' . $checkid . '_' . $counter . '_' . $checkCount . '\', \'order' . $checkid . '_' . $counter . '\');">' . $txt['checks_order_up'] . '</a> | <a href="javascript:void(0);" onClick="moveDown(this.parentNode.parentNode); orderChecks(\'' . $checkid . '_' . $counter . '_' . $checkCount . '\', \'order' . $checkid . '_' . $counter . '\');">' . $txt['checks_order_down'] . '</a></span>' : '', '</div>';
				}
	echo '
								<em>', $txt['check_all'], '</em> <input type="checkbox" class="input_check" onclick="invertAll(this, this.form, \'', $checkname . 's' . $counter, '[]\');" /><br />
								<br />
								<input type="hidden" name="conval' . $checkid . '_' . $counter . '" value="' . $config_param['check_value'] . '" />', ($config_param['check_order'] ? '
								<input type="hidden" id="order' . $checkid . '_' . $counter . '" name="order' . $checkid . '_' . $counter . '" value="' . $context[$checkname . '_order' . $config_param['id']] . '" />' : ''), '
							</dd>';
			}
			// Just show a regular textbox.
			else
			{
				echo '
							<input type="text" name="', $config_param['name'], '" id="', $config_param['label_id'], '" value="', $config_param['value'], '"', ($config_param['size'] ? ' size="' . $config_param['size'] . '"' : ''), ' class="input_text" />';
			}

		// Holds all parameters param_name1, param_name2, param_name3, and so on.
		echo '
							<input type="hidden" name="param_name', $counter, '" value="', $config_param['name'], '" />
							<input type="hidden" name="param_id', $counter, '" value="', $config_id, '" />
							<input type="hidden" name="param_type', $counter, '" value="', $config_param['type'], '" />';

		echo '
			</dd>';
	}

	echo '
		</dl>
			<hr class="hrcolor" />
		<p class="righttext">
		<input type="submit" name="save" value="', $txt['save'], '" class="button_submit" />
		</p>
		</div>
		<span class="lowerframe"><span></span></span>
			</div>
			<input type="hidden" name="', $context['session_var'], '" value="', $context['session_id'], '" />
			</form>
			</div>
			<br class="clear" />';
}

/**
 * Template used to manage the position of modules/clones.
 *
 * @since 1.0
 */
function template_manage_modules()
{
	global $txt, $context, $scripturl, $settings, $user_info, $options;

	// Build the normal button array.
	$envision_buttons = array(
		'add' => array('text' => 'add_layout', 'image' => 'reply.gif', 'lang' => true, 'url' => $scripturl . '?action=admin;area=epmodules;sa=epaddlayout;' . $context['session_var'] . '=' . $context['session_id']),
		'edit' => array('text' => 'edit_layout', 'image' => 'reply.gif', 'lang' => true, 'url' => 'javascript:void(0);', 'custom' => 'onclick="javascript:submitLayout(\'editlayout\', \'' . $scripturl . '?action=admin;area=epmodules;sa=epeditlayout;\', \'' . $context['session_var'] . '\', \'' . $context['session_id'] . '\');"'),
		'del' => array('text' => 'delete_layout', 'image' => 'reply.gif', 'lang' => true, 'url' => 'javascript:void(0);', 'custom' => 'onclick="javascript:submitLayout(\'' . $txt['confirm_delete_layout'] . '\', \'' . $scripturl . '?action=admin;area=epmodules;sa=epdellayout;\', \'' . $context['session_var'] . '\', \'' . $context['session_id'] . '\');"'),
	);

	if ($_SESSION['selected_layout']['name'] == 'Homepage')
		unset($envision_buttons['del']);

	echo '
	<div class="floatleft w100">
		<div class="floatright">
			<form name="urLayouts" id="epmod_change_layout" action="', $scripturl, '?action=admin;area=epmodules;sa=epmanmodules;', $context['session_var'], '=', $context['session_id'], '" method="post" accept-charset="', $context['character_set'], '">
				<select onchange="document.forms[\'epmod_change_layout\'].submit();" name="layout_picker" class="w100">';

		foreach ($_SESSION['layouts'] as $id_layout => $layout_name)
			echo '
					<option value="', $id_layout, '"', ($_SESSION['selected_layout']['id_layout'] == $id_layout ? ' selected="selected"' : ''), '>', $layout_name, '</option>';

	echo '
				</select>
				<input type="hidden" name="', $context['session_var'], '" value="', $context['session_id'], '" />
			</form>';

	template_button_strip($envision_buttons, 'right');

	echo '
		</div>
				<div id="messages"></div></div>
				<div class="module_page floatright">
					<div class="disabled module_holder">
						<div class="cat_bar block_header">
							<h3 class="catbg centertext">
								', $txt['ep_admin_modules_manage_col_disabled'], '
							</h3>
						</div>
						<div class="roundframe blockframe" id="disabled_module_container">';

	if (!empty($context['ep_all_modules']))
		foreach($context['ep_all_modules'] as $module)
			echo '
							<div class="DragBox plainbox draggable_module disabled_module_container centertext" id="envisionmod_' . $module['type'] . '">
								<p>
									', $module['module_title'], '
								</p>
							</div>';

	echo '
						</div>
						<span class="lowerframe"><span></span></span>
					</div>
					<div class="clear"></div>
				</div>
				<div class="module_page floatleft">';

	echo '
					<table>';

	foreach ($context['ep_columns'] as $row_id => $row_data)
	{
		echo '
						<tr class="tablerow', $row_id, '" valign="top">';

		foreach ($row_data as $column_id => $column_data)
		{
				echo '
							<td class="tablecol_', $column_id, '"', $column_data['colspan'], '>

								<div id="module_container_', $column_data['id_layout_position'], '" class="enabled w100">
									<div class="cat_bar block_header">
										<h3 class="catbg centertext">
											', (!$column_data['is_smf'] ? '<input type="checkbox" ' . (!empty($column_data['enabled']) ? 'checked="checked" ' : '') . 'id="column_' . $column_data['id_layout_position'] . '" class="check_enabled input_check" /><label for="column_' . $column_data['id_layout_position'] . '">' . $txt['ep_admin_modules_manage_col_section'] . '</label>' : $txt['ep_is_smf_section']), '
										</h3>
									</div>
									<div class="roundframe blockframe ', (!$column_data['is_smf'] ? 'module' : 'smf'), '_container" id="ep', (!$column_data['is_smf'] ? 'col_' . $column_data['id_layout_position'] : 'smf'), '">';

					if (!empty($column_data['modules']))
					{
						foreach ($column_data['modules'] as $module => $id)
						{
							if ($id['is_smf'])
							{
								echo '
											<div class="smf_content" id="smfmod_', $id['id'], '"><strong>', $txt['ep_smf_mod'], '</strong></div>
											<script type="text/javascript"><!-- // --><![CDATA[
												var smf_container = document.getElementById("smfmod_', $id['id'], '").parentNode;
												smf_container.className = "roundframe blockframe";
											// ]]></script>';
								continue;
							}
							echo '
											<div class="DragBox plainbox draggable_module centertext" id="envisionmod_' . $id['id'] . '">
												<p>
													', $id['module_title'], ' ', $id['modify_link'], '
												</p>
											</div>';
						}
					}
					echo '
										</div>
										<span class="lowerframe"><span></span></span>
									</div>
								</td>';
		}

				echo '
							</tr>';
	}
	echo '
						</table>
						<span class="botslice"><span></span></span>
					</div>
					<br class="clear" />
						<div class="padding righttext">
							<input type="submit" name="save" id="save" value="', $txt['save'], '" class="button_submit" />
						</div>';
}

/**
 * Template used to upload and activate/deactivate and delete third-party modules.
 *
 * @since 1.0
 */
function template_add_modules()
{
	global $txt, $context, $scripturl, $settings;

	echo '
			<div id="admincenter">';

	echo '
				<div class="cat_bar">
					<h3 class="catbg">
						', $context['page_title'], '
					</h3>
				</div>';

	if (empty($context['module_info']))
		echo '
				<div class="information">', $txt['no_modules'], '</div>';

	if (!empty($context['module_info']))
	{
		echo '
				<table border="0" width="100%" cellspacing="1" cellpadding="4" class="tborder" id="stats">
					<tr class="titlebg" valign="middle" align="center">
					<td align="left" width="25%">', $txt['module_name'], '</td>
					<td align="left" width="75%">', $txt['module_description'], '</td>
				</tr>';

		// Print the available modules
		foreach ($context['module_info'] as $name => $module)
		{
			$alternate = 0;
			echo '
					<tr class="windowbg', $alternate ? '2' : '', '" valign="middle" align="center">
						<td align="left" width="25%"><strong>', $module['title'], '</strong><br />',
				(isset($module['install_link']) ? '<a href="' . $module['install_link'] . '">' . $txt['module_install'] . '</a>' : ''),
				(isset($module['uninstall_link']) ? '<a href="' . $module['uninstall_link'] . '">' . $txt['module_uninstall'] . '</a>' : ''),
				(isset($module['settings_link']) ? ' | <a href="' . $module['settings_link'] . '">' . $txt['module_settings'] . '</a>' : ''), ' | <a href="' . $module['delete_link'] . '">' . $txt['module_delete'] . '</a>',
						'</td>
						<td align="left" width="75%">', $module['description'], '</td>';

			echo '
					</tr>';

				// Switch alternate to whatever it wasn't this time. (true -> false -> true -> false, etc.)
				$alternate = !$alternate;
		}

			echo '
				</table>';
	}
	echo '
				<br />
				<div class="cat_bar">
					<h3 class="catbg">
						', $txt['ep_upload_module'], '
					</h3>
				</div>
				<div class="windowbg">
					<span class="topslice"><span></span></span>
					<div class="content">
						<form action="', $scripturl, '?action=admin;area=epmodules;sa=epaddmodules" method="post" accept-charset="', $context['character_set'], '" enctype="multipart/form-data" style="margin-bottom: 0;">
							<dl class="settings">
								<dt>
									<strong>', $txt['module_to_upload'], '</strong>
								</dt>
								<dd>
									<input name="ep_modules" type="file" class="input_file" size="38">
								</dd>
							</dl>
							<div class="righttext">
								<input name="upload" type="submit" value="' . $txt['module_upload'] . '" class="button_submit">
								<input type="hidden" name="' . $context['session_var'] . '" value="' . $context['session_id'] . '" />
							</div>
						</form>
					</div>
					<span class="botslice"><span></span></span>
				</div></div>
			<br class="clear" />';
}

/**
 * Template used to add a new layout.
 *
 * @since 1.0
 */
function template_add_layout()
{
	global $txt, $context, $scripturl, $settings;

		echo '
			<script type="text/javascript"><!-- // --><![CDATA[
				var nonallowed_actions = \''. implode('|', $context['unallowed_actions']) . '\';
				var exceptions = nonallowed_actions.split("|");
			// ]]></script>
			<script type="text/javascript" src="' . $settings['default_theme_url'] . '/scripts/epAdmin.js"></script>
			<div class="cat_bar">
				<h3 class="catbg">
					', $txt['add_layout'], '
				</h3>
			</div>
			<div class="windowbg">
				<span class="topslice"><span></span></span>
			<form name="epFlayouts" id="epLayouts" ', isset($context['ep_file_input']) ? 'enctype="multipart/form-data" ' : '', 'action="', $scripturl, '?action=admin;area=epmodules;sa=epaddlayout2;', $context['session_var'], '=', $context['session_id'], '" method="post" accept-charset="', $context['character_set'], '">
					<div class="content">';

						// If there were errors when adding the Layout, show them.
						if (!empty($context['layout_error']['messages']))
						{
							echo '
									<div class="errorbox">
										<strong>', $txt['layout_error_header'], '</strong>
										<ul>';

							foreach ($context['layout_error']['messages'] as $error)
								echo '
											<li class="error">', $error, '</li>';

							echo '
										</ul>
									</div>';
						}

					echo '
						<dl class="settings">
							<dt>
								<a id="setting_layoutname" href="', $scripturl, '?action=helpadmin;help=ep_layout_name" onclick="return reqWin(this.href);" class="help"><img src="', $settings['images_url'], '/helptopics.gif" alt="', $txt['help'], '" border="0" /></a><span', (isset($context['layout_error']['no_layout_name']) || isset($context['layout_error']['layout_exists']) ? ' class="error"' : ''), '>', $txt['ep_layout_name'], ':</span>
							</dt>
							<dd>
									<input type="text" name="layout_name" ', (!empty($context['layout_name']) ? 'value="' . $context['layout_name'] . '" ' : ''), 'class="input_text" style="width: 295px;" />
							<dd>
							<dt>
							<a id="setting_actions" href="', $scripturl, '?action=helpadmin;help=ep_layout_actions" onclick="return reqWin(this.href);" class="help"><img src="', $settings['images_url'], '/helptopics.gif" alt="', $txt['help'], '" border="0" /></a><span><strong>', $txt['ep_action_type'], '</strong><br />
							<input type="radio" onclick="swap_action(this); return true;" name="action_choice" id="action_choice_smf_actions" value="smf_actions" checked="checked" class="input_radio" /><label for="action_choice_smf_actions">' . $txt['select_smf_actions'] . '</label><br />', '
								<input type="radio" onclick="swap_action(this); return true;" name="action_choice" id="action_choice_user_defined" value="user_defined" class="input_radio" /><label for="action_choice_user_defined">' . $txt['select_user_defined_actions'] . '</label></span>
							</dt>
							<dd>
							<div class="floatleft" id="action_smf_actions">
									<select id="actions" name="epLayout_smf_actions" style="max-width: 300px;" onfocus="selectRadioByName(document.forms.epFlayouts.action_choice, \'smf_actions\');">';
									foreach($context['available_actions'] as $action)
										echo '
											<option value="', $action, '">', $action, '</option>';
									echo '
									</select>
							</div>
							<div id="action_user_defined2" class="smalltext">', $txt['select_user_defined_actions_desc'], '</div>
							<div class="floatleft" id="action_user_defined">
								<input id="udefine" type="text" name="epLayout_user_defined" size="34" value="" onfocus="selectRadioByName(document.forms.epFlayouts.action_choice, \'user_defined\');" class="input_text" />
							</div>
							<div style="float: left; margin-left: 5px;"><input type="button" value="', $txt['ep_add_action'], '" onclick="javascript:addAction();" class="button_submit smalltext"></div>';
			echo '
								<script type="text/javascript"><!-- // --><![CDATA[
								// This is shown by default.
								document.getElementById("action_smf_actions").style.display = "";
								document.getElementById("action_user_defined").style.display = "none";
								document.getElementById("action_user_defined2").style.display = "none";
								document.getElementById("action_choice_smf_actions").checked = true;
								// ]]></script>
							</dd>
							<dt><span><a id="setting_curr_actions" href="', $scripturl, '?action=helpadmin;help=ep_layout_curr_actions" onclick="return reqWin(this.href);" class="help"><img src="', $settings['images_url'], '/helptopics.gif" alt="', $txt['help'], '" border="0" /></a>', $txt['layout_actions'], '
							</span></dt>
							<dd>
									<select id="actions_list" name="layouts" multiple style="height: 128px; width: 300px;', (isset($context['layout_error']['no_actions']) ? ' border: 1px solid red;' : ''), '">';
							foreach($context['current_actions'] as $cur_action)
								echo '
									<option value="', $cur_action, '">', $cur_action, '</option>';

		echo '
									</select><br /><input type="button" value="', $txt['ep_remove_actions'], '" onclick="javascript:removeActions();" class="button_submit smalltext">
							</dd>
							<dt><span><a id="setting_layout_style" href="', $scripturl, '?action=helpadmin;help=ep_layout_style" onclick="return reqWin(this.href);" class="help"><img src="', $settings['images_url'], '/helptopics.gif" alt="', $txt['help'], '" border="0" /></a>', $txt['layout_style'], '</span></dt>
							<dd>
									<select name="layout_style" style="width: 300px;">';

		foreach ($context['layout_styles'] as $num => $layout_style)
			echo '
										<option value="', $num, '"', ($context['selected_layout'] == $num ? ' selected="selected"' : ''), '>', $txt['layout_style_' . $layout_style], '</option>';

		echo '
									</select>
							</dd>
						</dl>
						<hr class="hrcolor">
						<div id="lay_right" class="righttext">
							<input type="hidden" name="', $context['session_var'], '" value="', $context['session_id'], '" />
							<input type="hidden" name="seqnum" value="', $context['form_sequence_number'], '" />';

		foreach($context['current_actions'] as $k => $cur_action)
			echo
									'<input id="envision_action', $k, '" name="layout_actions[]" type="hidden" value="', $cur_action, '" />';

		echo '
							<input type="submit" name="save" id="save" value="', $txt['save'], '" class="button_submit" />
						</div>
					</div>
					</form>
				<span class="botslice"><span></span></span>
			</div>';
}

/**
 * Template used to edit an existing layout.
 *
 * @since 1.0
 */
function template_edit_layout()
{
	global $txt, $context, $scripturl, $settings;

	echo '
			<script type="text/javascript"><!-- // --><![CDATA[
				var nonallowed_actions = \''. implode('|', $context['unallowed_actions']) . '\';
				var exceptions = nonallowed_actions.split("|");
			// ]]></script>
			<script type="text/javascript" src="' . $settings['default_theme_url'] . '/scripts/epAdmin.js"></script>
			<div class="cat_bar">
				<h3 class="catbg">
					', $txt['edit_layout'], '
				</h3>
			</div>
			<div class="windowbg">
				<span class="topslice"><span></span></span>
			<form name="epFlayouts" id="epLayouts" ', isset($context['ep_file_input']) ? 'enctype="multipart/form-data" ' : '', 'action="', $scripturl, '?action=admin;area=epmodules;sa=epeditlayout2;', $context['session_var'], '=', $context['session_id'], '" method="post" accept-charset="', $context['character_set'], '" onsubmit="beforeLayoutEditSubmit()">
					<div class="content">';

	// If there were errors when editing the Layout, show them.
	if (!empty($context['layout_error']['messages']))
	{
		echo '
									<div class="errorbox">
										<strong>', $txt['edit_layout_error_header'], '</strong>
										<ul>';

		foreach ($context['layout_error']['messages'] as $error)
			echo '
											<li class="error">', $error, '</li>';

		echo '
										</ul>
									</div>';
	}

		echo '
						<dl class="settings">';

	if ($context['show_smf'])
	{
		echo '
							<dt>
								<a id="setting_layoutname" href="', $scripturl, '?action=helpadmin;help=ep_layout_name" onclick="return reqWin(this.href);" class="help"><img src="', $settings['images_url'], '/helptopics.gif" alt="', $txt['help'], '" border="0" /></a><span', (isset($context['layout_error']['no_layout_name']) || isset($context['layout_error']['layout_exists']) ? ' class="error"' : ''), '>', $txt['ep_layout_name'], ':</span>
							</dt>
							<dd>
									<input type="text" name="layout_name" value="' . $context['layout_name'], '" class="input_text" style="width: 295px;', (isset($context['layout_error']['no_layout_name']) ? ' border: 1px solid red;' : ''), '" />
							<dd>
							<dt>
							<a id="setting_actions" href="', $scripturl, '?action=helpadmin;help=ep_layout_actions" onclick="return reqWin(this.href);" class="help"><img src="', $settings['images_url'], '/helptopics.gif" alt="', $txt['help'], '" border="0" /></a><span><strong>', $txt['ep_action_type'], '</strong><br />
							<input type="radio" onclick="swap_action(this); return true;" name="action_choice" id="action_choice_smf_actions" value="smf_actions" checked="checked" class="input_radio" /><label for="action_choice_smf_actions">' . $txt['select_smf_actions'] . '</label><br />', '
								<input type="radio" onclick="swap_action(this); return true;" name="action_choice" id="action_choice_user_defined" value="user_defined" class="input_radio" /><label for="action_choice_user_defined">' . $txt['select_user_defined_actions'] . '</label></span>
							</dt>
							<dd>
							<div class="floatleft" id="action_smf_actions">
									<select id="actions" name="epLayout_smf_actions" style="max-width: 300px;" onfocus="selectRadioByName(document.forms.epFlayouts.action_choice, \'smf_actions\');">';

		foreach ($context['available_actions'] as $action)
			echo '
											<option value="', $action, '">', $action, '</option>';

		echo '
									</select>
							</div>
							<div id="action_user_defined2" class="smalltext">', $txt['select_user_defined_actions_desc'], '</div>
							<div class="floatleft" id="action_user_defined">
								<input id="udefine" type="text" name="epLayout_user_defined" size="34" value="" onfocus="selectRadioByName(document.forms.epFlayouts.action_choice, \'user_defined\');" class="input_text" />
							</div>
							<div style="float: left; margin-left: 5px;"><input type="button" value="', $txt['ep_add_action'], '" onclick="javascript:addAction();" class="button_submit smalltext"></div>';

		echo '
								<script type="text/javascript"><!-- // --><![CDATA[
									// This is shown by default.
									document.getElementById("action_smf_actions").style.display = "";
									document.getElementById("action_user_defined").style.display = "none";
									document.getElementById("action_user_defined2").style.display = "none";
									document.getElementById("action_choice_smf_actions").checked = true;
								// ]]></script>
							</dd>
							<dt><span', (isset($context['layout_error']['no_actions']) ? ' class="error"' : ''), '><a id="setting_curr_actions" href="', $scripturl, '?action=helpadmin;help=ep_layout_curr_actions" onclick="return reqWin(this.href);" class="help"><img src="', $settings['images_url'], '/helptopics.gif" alt="', $txt['help'], '" border="0" /></a>', $txt['layout_actions'], '
							</span></dt>
							<dd>
									<select id="actions_list" name="layouts" multiple style="height: 128px; width: 300px;', (isset($context['layout_error']['no_actions']) ? ' border: 1px solid red;' : ''), '">';

		foreach($context['current_actions'] as $cur_action)
			echo '
									<option value="', $cur_action, '">', $cur_action, '</option>';

		echo '
									</select><br /><input type="button" value="', $txt['ep_remove_actions'], '" onclick="javascript:removeActions();" class="button_submit smalltext">
							</dd>';
	}

	echo '
							<dt><span', (isset($context['layout_error']['no_sections']) ? ' class="error"' : ''), '><a id="setting_curr_actions" href="', $scripturl, '?action=helpadmin;help=ep_layout_curr_sections" onclick="return reqWin(this.href);" class="help"><img src="', $settings['images_url'], '/helptopics.gif" alt="', $txt['help'], '" border="0" /></a>', $txt['layout_sections'], '
							</span></dt>
							<dd></dd>
							<table class="table_grid" width="100%" cellspacing="0" id="edit_layout">
								<thead>
								<tr class="catbg">
									<th class="first_th" scope="col">', $txt['ep_columns_header'],'</th>
									<th scope="col">', $txt['colspans'],'</th>
									<th scope="col">', $txt['enabled'], '</th>';

	if ($context['show_smf'])
		echo '
									<th scope="col">', $txt['ep_is_smf_section'], '</th>';

								echo '<th class="last_th" scope="col"><input id="all_checks" type="checkbox" class="input_check" onclick="invertChecks(this, this.form, \'check_\');" /></th>
								</tr></thead>';

		// Some js variables to make this easier.
		echo '<script type="text/javascript"><!-- // --><![CDATA[
					var checkClass = "input_check";
					var textClass = "input_text";
					var radioClass = "input_radio";
					var columnString = \'', $txt['ep_column'], '\';
					var rowString = \'', $txt['ep_row'], '\';
					var newColumns = 0;
					var totalColumns = ', $context['total_columns'], ';
					var totalRows = ', $context['total_rows'], ';
					// Some error variables here.
					var delAllRowsError = \'', $txt['ep_cant_delete_all'], '\';
					// ]]></script>';

	$rows = array();
	$xRow = 0;
	$i = 0;

	echo '<tbody id="edit_layout_tbody">';

	foreach($context['current_sections'] as $column)
	{
		$rows[] = $xRow + 1;
		$windowbg = '';
		$pCol = 0;

		echo '
								<tr class="titlebg2" id="row_', $xRow, '"><td align="center" colspan="', ($context['show_smf'] ? '6' : '5'), '"><label for="inputrow_', $xRow, '">', $txt['ep_row'], ' ', ($xRow + 1), '</label> <input id="inputrow_', $xRow, '" type="checkbox" class="input_check" onclick="invertChecks(this, this.form, \'check_', $xRow, '_\');" /></td></tr>';

		foreach($column as $section)
		{
			$i++;

			if ($section['is_smf'] && $context['show_smf'])
			{
				$smfRow = $xRow;
				$smfCol = $pCol;
				$smfSection = $section['id_layout_position'];
			}

	echo '
							<tr class="windowbg', $windowbg, '" id="tr_', $xRow, '_', $pCol, '_', $section['id_layout_position'], '">
								<td id="tdcolumn_', $xRow, '_', $pCol, '_', $section['id_layout_position'], '"><div class="floatleft"><a href="javascript:void(0);" onclick="javascript:columnUp(this.parentNode.parentNode.parentNode);" onfocus="if(this.blur)this.blur();"><img src="' . $context['epadmin_image_url'] . '/ep_up.gif" style="width: 12px; height: 11px;" border="0" /></a> <a href="javascript:void(0);" onclick="javascript:columnDown(this.parentNode.parentNode.parentNode);" onfocus="if(this.blur)this.blur();"><img src="', $context['epadmin_image_url'], '/ep_down.gif" style="width: 12px; height: 11px;" border="0" /></a></div><span class="ep_edit_column" id="column_', $xRow, '_', $pCol, '_', $section['id_layout_position'], '">', $txt['ep_column'], ' ', $pCol + 1, '</span></td>
								<td id="tdcspans_', $xRow, '_', $pCol, '_', $section['id_layout_position'], '" style="text-align: center;"><input type="text" id="cspans_', $xRow, '_', $pCol, '_', $section['id_layout_position'], '" name="colspans[', $section['id_layout_position'], ']" size="5" value="', (isset($_POST['colspans'][$section['id_layout_position']]) ? $_POST['colspans'][$section['id_layout_position']] : $section['colspans']), '"', (in_array($section['id_layout_position'], $context['colspans_error_ids']) ? ' style="border: 1px solid red;"' : ''), ' class="input_text" /></td>
								<td style="text-align: center;" id="tdenabled_', $xRow, '_', $pCol, '_', $section['id_layout_position'], '">', (!$section['is_smf'] ? '<input type="checkbox" id="enabled_' . $xRow . '_' . $pCol . '_' . $section['id_layout_position'] . '" name="enabled[' . $section['id_layout_position'] . ']"' . ($section['enabled'] ? ' checked="checked"' : '') . ' class="input_check" />' : ''), '</td>';

if ($context['show_smf'])
	echo '
								<td style="text-align: center;" id="tdradio_', $xRow, '_', $pCol, '_', $section['id_layout_position'], '"><input type="radio" id="radio_', $xRow, '_', $pCol, '_', $section['id_layout_position'], '" name="smf_radio"', ' onfocus="if(this.blur)this.blur();" onclick="javascript:smfRadio(\'', $xRow, '\', \'', $pCol, '\', \'', $section['id_layout_position'], '\');" value="' . $section['id_layout_position'], '" class="input_radio" /></td>';

	echo '
								<td style="text-align: center;" id="tdcheck_', $xRow, '_', $pCol, '_', $section['id_layout_position'], '">', (!$section['is_smf'] ? '<input type="checkbox" id="check_' . $xRow . '_' . $pCol . '_' . $section['id_layout_position'] . '" name="section[]" class="input_check" />' : ''), '</td>
							<input type="hidden" name="layout_position[]" value="', $section['id_layout_position'], '" />';

	if ($context['show_smf'] && $section['is_smf'])
	{
		$smf_section = $section['id_layout_position'];

		echo '
							<input type="hidden" name="old_smf_pos" value="', $section['id_layout_position'], '" />';
	}

		echo '
			</tr>';

			$windowbg = $windowbg == '2' ? '' : '2';

			$pCol++;
		}

		$xRow++;
	}

		echo '
	</tbody></table>';

		echo '
			<script type="text/javascript"><!-- // --><![CDATA[';

			if ($context['show_smf'])
				echo '
					var smfLayout = true;
					var rowPos = ', $smfRow, ';
					var colPos = ', $smfCol, ';
					var layoutPos = ', $smfSection, ';
					createEventListener(window);
					window.addEventListener("load", checkSMFRadio, false);';
			else
				echo '
						var smfLayout = false;
						var rowPos = -1;
						var colPos = -1;
						var layoutPos = -1;';

		echo '
			// ]]></script>';

			echo '
			</dl>
			<div class="floatright">
			<p style="text-align: right;"><label for="add_column">', $txt['ep_add_column'], '</label> <select id="selAddColumn">';

					foreach($rows as $key => $value)
						echo '
							<option value="', $key, '">', $txt['ep_row'], ' ', $value, '</option>';

		echo '
			</select> <input type="button" class="button_submit" value="', $txt['ep_add_column_button'], '" onclick="javascript:addColumn();" />
			</p>
			<p style="text-align: right;">
			<input type="button" class="button_submit" value="', $txt['ep_add_row'], '" onclick="javascript:addRow();" /> <input type="button" class="button_submit" value="', $txt['ep_edit_remove_selected'], '" onclick="javascript:deleteSelected(\'', $txt['confirm_remove_selected'], '\');" />
			</p></div>

					<div style="clear: right;">
						<hr class="hrcolor">
						<div id="lay_right" class="righttext">', ($context['show_smf'] ? '
							<input type="hidden" id="smf_section" name="smf_id_layout_position" value="' . $smf_section . '" />' : ''), '
							<input type="hidden" name="disabled_section" value="', $context['disabled_section'], '" />
							<input type="hidden" name="', $context['session_var'], '" value="', $context['session_id'], '" />
							<input type="hidden" id="layout_picker" name="layout_picker" value="', $_POST['layout_picker'], '" />
							<input type="hidden" id="remove_positions" name="remove_positions" value="" />
							<input type="hidden" name="seqnum" value="', $context['form_sequence_number'], '" />';

	foreach($context['current_actions'] as $k => $cur_action)
		echo '
							<input id="envision_action', $k, '" name="layout_actions[]" type="hidden" value="', $cur_action, '" />';

	echo '
							<input type="submit" name="save" id="save" value="', $txt['save'], '" class="button_submit" />
						</div>
					</div>
				</div>
			</form>
				<span class="botslice"><span></span></span>
			</div>';
}

?>