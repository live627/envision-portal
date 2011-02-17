<?php
/**************************************************************************************
* Subs-EnvisionPortal.php                                                             *
/**************************************************************************************
* EnvisionPortal                                                                      *
* Community Portal Application for SMF                                                *
* =================================================================================== *
* Software by:                  EnvisionPortal (http://envisionportal.net/)           *
* Software for:                 Simple Machines Forum                                 *
* Copyright 2011 by:            EnvisionPortal (http://envisionportal.net/)           *
* Support, News, Updates at:    http://envisionportal.net/                            *
**************************************************************************************/

if (!defined('SMF'))
	die('Hacking attempt...');

/*	This file contains functions vital for the performance of Envision
	Portal. It provides the following functions:

	void loadDefaultModuleConfigs(array installed_mods = array(), boolean new_layout = false)
		- Initializes the default Module settings.
		- installed_mods get passed an array of the name, files, functions.  It is important
		  to note that these are modules that get installed via the Add Modules section.
		- new_layout is used to determine if this information is to be used for a newly created layout.

	void loadParameter(array file_input = array(), string param_type, string param_value)
		- Reads the information for each parameter individually and returns a clean string based on the
		  parameter type sent to it.
		- returns the new value to be used for that parameter within the module's function.

	void parseString(string str = '', string type = 'filepath', boolean replace = true)
		- Reads the input string (str) and returns either a new string or an integer value.
		- when replace = false, returns 1 if invalid characters are found within str, else 0.
		- when replace = true, replaces all invalid characters with ''.
		- Note:  Their are a few types that don't accept replace = false, in those types, if
		  replace is set to true, it will simply return str without doing anything to it.

	void module_error(string type = 'error', string error_type = 'general', boolean log_error = false, boolean echo = true)
		- Echoes an error message within a module if echo = true.  Note:  This module doesn't do any error handling
		  for you, you must do this yourself for your modules.  This just provides an error message of some sort for
		  you to use within your module.  Make sure you return after calling this function in your modules, or it will
		  continue running your code.
		- possible string types are:  error, mod_not_installed, not_allowed, no_language, query_error, empty.  You can
		  also define you own string to be passed in here that will output that message instead of any of the pre-defined
		  messages listed above.
		- possible error_type strings are: general, critical, database, undefined_vars, user, template, and debug.  If
		  critical is defined for the error_type, than the error message will output red colored text.
		- log_error = whether or not to log the error into the Admin -> Error Log.
		- If echo = true will output it directly, if false, returns the information to be used within a variable.

	void loadFiles(array file_input = array())
		- Loads up all files for any given id_param via the file_input parameter type.

	void createFile(array fileOptions)
		- Handles uploaded files via the file_input parameter type.
		- Places the information for each file uploaded into the ep_module_files database table.

	void AllowedFileExtensions(string file_mime)
		- Returns all possible extensions for any given mime type supplied within the file_mime separated by commas.

	void getFilename(string filename, string file_id, string path, boolean new = false, string file_hash = '')
		- Gets/Sets a files encrypted filename via the file_input parameter type.

	void getLegacyFilename(string filename, string file_id, string path, boolean new = false)
		- Returns a clean, encrypted path and file hash.

	void GetEnvisionModuleInfo(string scripts, string mod_functions, string dirname, string file, string name = '', boolean install = false)
		- Gets all of the data from the info.xml file for a module.
		- Returns an array of data, or false if an error occurred such as mandatory fields are missing, etc..

	void GetEnvisionAddedModules()
		- Gets all Uploaded Module information for output into the Add Modules section of the Envision Admin.
		- Determines whether or not a module is installed.

	void GetEnvisionInstalledModules(array installed_mods = array())
		- Gets all installed modules and output it into an array that we can use.
		- If installed_mods is an empty array, than it will query the database to get the
		  information needed from installed modules.

	void loadLayout(string $url)
		- Loads the layout for the action specified by $url
		- Called from ep_main() in DrweamPortal.php
		- If $url = [home] (square backets included), the request is from the home page
		- Calls ProcessModule() to prepare the module for use within the template
		- Sets $context['envision_columns'] with the layout data

	array ProcessModule(array $data)
		- Calls loadDefaultModuleConfigs() to get the function to call for the default modules
		- Prepares the raw module data generated by loadLayout() for use in the template
		- Returns an array of the data

	array load_envision_menu()
		- Prepares all the user added buttons for the menu
		- Returns an array of the data

	array add($index, $position, $array, $add, $add_key)
		- adds something to an array
*/

function ep_load_module_context($installed_mods = array(), $new_layout = false)
{
	global $txt;

	// Default module configurations.
	$envisionModules = array(
		'announce' => array(
			'module_title' => $txt['ep_module_announce'],
			'module_icon' => 'world.png',
			'fields' => array(
				'msg' => array(
					'type' => 'large_text',
					'value' => 'Welcome to Envision Portal!',
				),
			),
		),
		'usercp' => array(
			'module_title' => $txt['ep_module_usercp'],
			'module_icon' => 'heart.png',
			'module_link' => 'action=profile',
		),
		'stats' => array(
			'module_title' => $txt['ep_module_stats'],
			'module_icon' => 'stats.png',
			'module_link' => 'action=stats',
			'fields' => array(
				'stat_choices' => array(
					'type' => 'checklist',
					'value' => '0,1,2,5,6:members;posts;topics;categories;boards;ontoday;onever:order',
				),
			),
		),
		'online' => array(
			'module_title' => $txt['ep_module_online'],
			'module_icon' => 'user.png',
			'module_link' => 'action=who',
			'fields' => array(
				'online_pos' => array(
					'type' => 'select',
					'value' => '0:top;bottom',
				),
				'show_online' => array(
					'type' => 'checklist',
					'value' => '0,1,2:users;buddies;guests;hidden;spiders:order',
				),
				'online_groups' => array(
					'type' => 'list_groups',
					'value' => '-3:-1,0,3:order',
				),
			),
		),
		'news' => array(
			'module_title' => $txt['ep_module_news'],
			'module_icon' => 'cog.png',
			'fields' => array(
				'board' => array(
					'type' => 'list_boards',
					'value' => '1',
				),
				'limit' => array(
					'type' => 'int',
					'value' => '5',
				),
			),
		),
		'recent' => array(
			'module_title' => $txt['ep_module_topics'],
			'module_icon' => 'pencil.png',
			'module_link' => 'action=recent',
			'fields' => array(
				'post_topic' => array(
					'type' => 'select',
					'value' => '1:posts;topics',
				),
				'show_avatars' => array(
					'type' => 'check',
					'value' => '1',
				),
				'num_recent' => array(
					'type' => 'int',
					'value' => '10',
				),
			),
		),
		'search' => array(
			'module_title' => $txt['ep_module_search'],
			'module_icon' => 'magnifier.png',
			'module_link' => 'action=search',
		),
		'calendar' => array(
			'module_title' => $txt['ep_module_calendar'],
			'module_icon' => 'cal.png',
			'fields' => array(
				'display' => array(
					'type' => 'select',
					'value' => '0:month;info',
				),
				'show_months' => array(
					'type' => 'select',
					'value' => '1:year;asdefined',
				),
				'previous' => array(
					'type' => 'int',
					'value' => '1',
				),
				'next' => array(
					'type' => 'int',
					'value' => '1',
				),
				'show_options' => array(
					'type' => 'checklist',
					'value' => '0,1,2:events;holidays;birthdays:order',
				),
			),
		),
		'poll' => array(
			'module_title' => $txt['ep_module_poll'],
			'module_icon' => 'comments.png',
			'fields' => array(
				'options' => array(
					'type' => 'select',
					'value' => '0:showPoll;topPoll;recentPoll',
				),
				'topic' => array(
					'type' => 'int',
					'value' => '0',
				),
			),
		),
		'top_posters' => array(
			'module_title' => $txt['ep_module_topPosters'],
			'module_icon' => 'rosette.png',
			'fields' => array(
				'show_avatar' => array(
					'type' => 'check',
					'value' => '1',
				),
				'show_postcount' => array(
					'type' => 'check',
					'value' => '1',
				),
				'num_posters' => array(
					'type' => 'int',
					'value' => '10',
				),
			),
		),
		'theme_select' => array(
			'module_title' => $txt['ep_module_theme_select'],
			'module_icon' => 'palette.png',
			'module_link' => 'action=theme;sa=pick',
		),
		'new_members' => array(
			'module_title' => $txt['ep_module_new_members'],
			'module_icon' => 'overlays.png',
			'module_link' => 'action=stats',
			'fields' => array(
				'limit' => array(
					'type' => 'int',
					'value' => '3',
				),
				'list_type' => array(
					'type' => 'select',
					'value' => '0:0;1;2',
				),
			),
		),
		'staff' => array(
			'module_title' => $txt['ep_module_staff'],
			'fields' => array(
				'list_type' => array(
					'type' => 'select',
					'value' => '1:0;1;2',
				),
				'name_type' => array(
					'type' => 'select',
					'value' => '0:0;1;2',
				),
				'groups' => array(
					'type' => 'list_groups',
					'value' => '1,2:-1,0:order',
				),
			),
		),
		'sitemenu' => array(
			'module_title' => $txt['ep_module_sitemenu'],
			'module_icon' => 'star.png',
			'fields' => array(
				'onesm' => array(
					'type' => 'check',
					'value' => '0',
				),
			),
		),
		'shoutbox' => array(
			'module_title' => $txt['ep_module_shoutbox'],
			'module_icon' => 'comments.png',
			'fields' => array(
				'id' => array(
					'type' => 'db_select',
					'value' => '1;id_shoutbox:{db_prefix}ep_shoutboxes;name:custom',
				),
				'refresh_rate' => array(
					'type' => 'int',
					'value' => '1',
				),
				'max_count' => array(
					'type' => 'int',
					'value' => '15',
				),
				'max_chars' => array(
					'type' => 'int',
					'value' => '128',
				),
				'text_size' => array(
					'type' => 'select',
					'value' => '1:small;medium',
				),
				'member_color' => array(
					'type' => 'check',
					'value' => '1',
				),
				'message' => array(
					'type' => 'text',
					'value' => '',
				),
				'message_position' => array(
					'type' => 'select',
					'value' => '1:top;after;bottom',
				),
				'message_groups' => array(
					'type' => 'list_groups',
					'value' => '-3:3',
				),
				'mod_groups' => array(
					'type' => 'list_groups',
					'value' => '1:-1,0,3',
				),
				'mod_own' => array(
					'type' => 'list_groups',
					'value' => '0,1,2:-1,3',
				),
				'bbc' => array(
					'type' => 'list_bbc',
					'value' => 'b;i;u;s;pre;left;center;right;url;sup;sub;php;nobbc;me',
				),
			),
		),
		'custom' => array(
			'module_title' => $txt['ep_module_custom'],
			'module_icon' => 'comments.png',
			'fields' => array(
				'code_type' => array(
					'type' => 'select',
					'value' => '1:0;1;2',
				),
				'code' => array(
					'type' => 'rich_edit',
					'value' => '',
				),
			),
		),
	);

	// Any modules installed?
	if (count($installed_mods) >= 1 || $new_layout)
		return array_merge($envisionModules, GetEnvisionInstalledModules($installed_mods));
	else
		return $envisionModules;
}

//!!! Loads up the modules parameters. Does some editing to the parameter value based on the type and outputs its new value.
function loadParameter($file_input = array(), $param_type, $param_value)
{
	global $context;

	// Loading up all files are we?
	if (count($file_input) >= 1)
	{
		$mod_param = loadFiles($file_input);
		return $mod_param;
	}

	// Need to handle all selects here.
	if (trim(strtolower($param_type)) == 'select')
	{
		$select_params = array();
		$values = array();

		$select_params = explode(':', $param_value);
		if (!empty($select_params))
		{
			$opt_value = (int) $select_params[0];
			if (isset($select_params[1]))
				$values = explode(';', $select_params[1]);
		}

		// Need to make sure its fine before setting this.
		if (count($values) >= 1 && $opt_value < count($values))
			$mod_param = $values[$opt_value];
		else
			// Error, set to empty and let the module function handle this instead.
			$mod_param = '';
	}
	elseif(trim(strtolower($param_type)) == 'db_select')
	{
		// Only returning the selected value for this parameter.
		$db_select = explode(':', $param_value);
		if (isset($db_select[0]))
		{
			$db_select_value = explode(';', $db_select[0]);

			if (isset($db_select_value[0]))
				$mod_param = (string) $db_select_value[0];
			else
				$mod_param = '';
		}
		else
			$mod_param = '';
	}
	elseif(trim(strtolower($param_type)) == 'checklist')
	{
		$list_params = explode(':', $param_value);

		// Set a few booleans here.
		$has_checks = !empty($list_params) && isset($list_params[0]) && trim($list_params[0]) != '' && !stristr(trim($list_params[0]), 'order');
		$has_strings = isset($list_params[1]) && trim($list_params[1]) != '';
		$has_order = !empty($list_params[2]) && isset($list_params[2]) && strlen(stristr(trim($list_params[2]), 'order')) > 0;

		if ($has_order)
		{
			$order = array();
			$order = explode(';', $list_params[2]);
				if (!isset($order[1]) || trim($order[1]) == '')
					unset($order);
		}

		if ($has_checks)
		{
			$mod_param = array();

			// Grab the checked values.
			$mod_param['checked'] = $list_params[0];

			// Order me timbers...
			if ($has_order && isset($order))
				$mod_param['order'] = $order[1];
			else
			{
				if ($has_order && !isset($order))
				{
					if ($has_strings)
						$mod_param['order'] = implode(',', array_keys(explode(';', $list_params[1])));
					else
						$mod_param['order'] = '';
				}
			}
		}
		else
			// Error, set checked to empty and let the module function handle this instead.
			$mod_param = '';

		// We're done with this now.
		unset($list_params);
	}
	elseif(trim(strtolower($param_type)) == 'list_groups')
	{
		$group_params = explode(':', $param_value);

		if (!empty($group_params) && isset($group_params[0]) && trim($group_params[0]) != '' && !stristr(trim($group_params[0]), 'order'))
		{
			// Are there any group ids that are not allowed?
			if (isset($group_params[1]) && !stristr(trim($group_params[1]), 'order'))
			{
				$checked = explode(',', $group_params[0]);
				$unallowed = explode(',', $group_params[1]);

				// We have values not allowed, let's filter them out now.
				$checked = array_diff($checked, $unallowed);

				// Note:  If (value < -1), than nothing is checked for this in the Admin.
				// 		  But we will let the Customizer choose what to do about it and keep it's value as is!
				if (count($checked) >= 1)
				{
					// Rebuild the array keys.
					$checked = array_values($checked);

					// Put it back together and return it.
					$mod_param = implode(',', $checked);

					// No longer needed.
					unset($checked, $unallowed);
				}
				// Opps, no group ids are being used.  Let the module function handle this instead.
				else
					$mod_param = '';
			}
			else
				// All groups are enabled, return the values.
				$mod_param = $group_params[0];
		}
		else
			// Error, set to empty and let the module function handle this instead.
			$mod_param = '';

		// We're done with this now.
		unset($group_params);
	}
	elseif(trim(strtolower($param_type)) == 'list_bbc')
		$mod_param = $param_value;
	else
		$mod_param = trim(strtolower($param_type)) == 'html' ? html_entity_decode($param_value, ENT_QUOTES, $context['character_set']) : $param_value;

	return $mod_param;
}

function parseString($str = '', $type = 'filepath', $replace = true)
{
	if ($str == '')
		return '';

	switch ((string) $type)
	{
		// Only accepts replace.
		case 'module_name':
			$find = array('/\s/', '/\.[\.]+/', '/[^\w_\.\-]/');
			$replace_str = array('_', '_', '');
			$valid_str = $replace ? preg_replace($find, $replace_str, $str) : $str;
			break;
		// trims away the first and last slashes, or matches against it.
		case 'folderpath':
			$valid_str = $replace ? 0 : (strpos($str, ' ') !== false ? 1 : 0);
			$find = $replace ? '#^/|/$|[^A-Za-z0-9_\/s/\-/]#' : '#^(\w+/){0,2}\w+-$#';
			$replace_str = '';
			$valid_str = $replace ? preg_replace($find, $replace_str, $str) : (!empty($valid_str) ? $valid_str : preg_match($find, $str));
			break;
		case 'function_name':
			$find = '~[^A-Za-z0-9_]~';
			$replace_str = '';
			$valid_str = $replace ? preg_replace($find, $replace_str, $str) : preg_match($find, $str);
			break;
		// Only accepts replace.
		case 'uploaded_file':
			$find = array('/\s/', '/\.[\.]+/', '/[^\w_\.\-]/');
			$replace_str = array('_', '.', '');
			$valid_str = $replace ? preg_replace($find, $replace_str, $str) : $str;
			break;
		// Only accepts replace.
		case 'phptags':
			$find = array('/<\?php/s', '/\?>/s', '/<\?/s');
			$replace_str = '';
			$valid_str = $replace ? preg_replace($find, $replace_str, $str) : $str;
			break;
		// Example: THIS STRING:  /my%root/p:a;t h/my file-english.php/  BECOMES THIS: myroot/path/myfile-english.php
		default:
			$valid_str = $replace ? 0 : (strpos($str, ' ') !== false ? 1 : 0);
			$find = $replace ? '#^/|/$|[^A-Za-z0-9_.\/s/\-/]#' : '#^(\w+/){0,2}\w+-\.\w+$#';
			$replace_str = '';
			$valid_str = $replace ? preg_replace($find, $replace_str, $str) : (!empty($valid_str) ? $valid_str : preg_match($find, $str));
			break;
	}
	return $valid_str;
}

function module_error($type = 'error', $error_type = 'general', $log_error = false, $echo = true)
{
	global $txt;

	// All possible pre-defined types.
	$valid_types = array(
		'mod_not_installed' => $type == 'mod_not_installed' ? 1 : 0,
		'not_allowed' => $type == 'not_allowed' ? 1 : 0,
		'no_language' => $type == 'no_language' ? 1 : 0,
		'query_error' => $type == 'query_error' ? 1 : 0,
		'empty' => $type == 'empty' ? 1 : 0,
		'error' => $type == 'error' ? 1 : 0,
	);

	$error_string = !empty($valid_types[$type]) ? $txt['ep_module_' . $type] : $type;
	$error_html = $error_type == 'critical' ? array('<p class="error">', '</p>') : array('', '');

	// Don't need this anymore!
	unset($valid_types);

	// Should it be logged?
	if ($log_error)
		log_error($error_string, $error_type);

	$return = implode($error_string, $error_html);

	// Echo...? Echo...?
	if ($echo)
		echo $return;
	else
		return $return;
}

function envisionBuffer($buffer)
{
	global $portal_ver, $context;

	// Add our copyright. Please have a thought for the developers and keep it in place.
	$search_array = array(
		', Simple Machines LLC</a>',
		'class="copyright"',
	);
	$replace_array = array(
		', Simple Machines LLC</a><br /><a class="new_win" href="http://envisionportal.net/" target="_blank">Envision Portal v' . $portal_ver . ' &copy; 2011 Envision Portal Team</a>',
		'class="copyright" style="line-height: 1;"',
	);

	if (!empty($context['has_ep_layout']))
	{
		// Prevent the Envision table from overflowing the SMF theme
		$search_array[] = '<body>';
		$search_array[] = '</body>';

		$replace_array[] = '<body><div id="envision_container">';
		$replace_array[] = '</div></body>';
	}

	return (isset($_REQUEST['xml']) ? $buffer : str_replace($search_array, $replace_array, $buffer));
}

function GetEnvisionModuleInfo($scripts, $mod_functions, $dirname, $file, $name = '', $install = false)
{
	global $boarddir, $context, $modSettings, $scripturl, $smcFunc;

	// Are we allowed to use this name?
	if (in_array($file, $context['ep_restricted_names'])) return false;

	// Optional check: does it exist? (Mainly for installation)
	if (!empty($name) && $name != $file) return false;

	// If the required info file does not exist let's silently die...
	if (!file_exists($dirname . '/' . $file . '/info.xml')) return false;

	// And finally, get the file's contents
	$file_info = file_get_contents($dirname . '/' . $file . '/info.xml');

	// Parse info.xml into an xmlArray.
	loadClassFile('Class-Package.php');
	$module_info1 = new xmlArray($file_info);
	$module_info1 = $module_info1->path('module[0]');

	// Required XML elements and attributes. Quit if any one is missing.
	if (!$module_info1->exists('title')) return false;
	if (!$module_info1->exists('description')) return false;

	if ($module_info1->exists('target'))
	{
		switch ($module_info1->fetch('target'))
		{
			case '_self':
				$module_info2 = 1;
			case '_parent':
				$module_info2 = 2;
			case '_top':
				$module_info2 = 3;
			case '_blank':
				$module_info2 = 0;
			default:
				$module_info2 = 0;
		}
	}
	else
		$module_info2 = 0;

	$other_functions = array();
	$all_files = array();
	$all_functions = array();
	$main_function = array();

	// Getting all functions and files.
	if ($module_info1->exists('file'))
	{
		$filetag = $module_info1->set('file');

		foreach ($filetag as $modfiles => $filepath)
		{
			if ($filepath->exists('function'))
			{
				$functag = $filepath->set('function');

				foreach($functag as $func => $function)
				{
					if ($function->exists('main'))
						$main_function[] = $function->fetch('main');
					else
						$other_functions[] = $function->fetch('');
				}
			}
			else
				return false;

			// Now grabbing all filepaths for each file.
			if ($filepath->exists('@path'))
				$all_files[] = $filepath->fetch('@path');
			else
				return false;
		}

		$all_functions = array_merge($main_function, $other_functions);
	}

	// And now for the parameters. Remember, they are optional!
	$param_array = array();
	if ($module_info1->exists('param'))
	{
		$fields = $module_info1->set('param');
		foreach ($fields as $name => $param)
		{
			if ($param->exists('@name') && $param->exists('@type'))
				$param_array[$param->fetch('@name')] = array(
					'type' => $param->fetch('@type'),
					'value' => $param->fetch('.'),
				);
		}
	}

	// Grabbing it from the database here.
	if (!empty($scripts) && !empty($mod_functions))
	{
		return array(
			'title' => $module_info1->fetch('title'),
			'files' => $scripts,
			'target' => $module_info2,
			'icon' => ($module_info1->exists('icon') ? $name . '/' . $module_info1->fetch('icon') : ''),
			'title_link' => ($module_info1->exists('url') ? $module_info1->fetch('url') : ''),
			'functions' => $mod_functions,
			'fields' => $param_array,
		);
	}
	else
	{
		return array(
			'title' => $module_info1->fetch('title'),
			'description' => ($module_info1->exists('description/@parsebbc')) ? ($module_info1->fetch('description/@parsebbc') ? parse_bbc($module_info1->fetch('description')) : $module_info1->fetch('description')) : $module_info1->fetch('description'),
			'desc_parse_bbc' => ($module_info1->exists('description/@parsebbc') ? $module_info1->fetch('description/@parsebbc') : false),
			'delete_link' => $scripturl . '?action=admin;area=epmodules;sa=epdeletemodule;name=' . $file . ';' . $context['session_var'] . '=' . $context['session_id'],
			'install_link' => $scripturl . '?action=admin;area=epmodules;sa=epinstallmodule;name=' . $file . ';' . $context['session_var'] . '=' . $context['session_id'],
			'icon_link' => ($module_info1->exists('icon') ? $boarddir . '/' . $modSettings['ep_icon_directory'] . '/' . $module_info1->fetch('icon') : ''),
			'icon' => ($module_info1->exists('icon') ? $module_info1->fetch('icon') : ''),
			'target' => $module_info2,
			'target_english' => ($module_info1->exists('target') ? $module_info1->fetch('target') : ''),
			'files' => count($all_files) == 1 ? $all_files[0] : implode('+', $all_files),
			'functions' => implode('+', $all_functions),
			'title_link' => ($module_info1->exists('url') ? $module_info1->fetch('url') : ''),
			'version' => ($module_info1->exists('version') ? $module_info1->fetch('version') : ''),
			'author' => ($module_info1->exists('author') ? $module_info1->fetch('author') : ''),
			'author_link' => ($module_info1->exists('author/@url') ? $module_info1->fetch('author/@url') : ''),
			'fields' => $param_array,
		);
	}
}

function GetEnvisionAddedModules()
{
	global $boarddir, $context, $modSettings, $sourcedir, $scripturl, $smcFunc, $txt;

	// We want to define our variables now...
	$AvailableModules = array();

	$added_mods = array();

	// Let's loop throuugh each folder and get their module data. If anything goes wrong we shall skip it.
	// !!! Use scandir()... don't tell me we're supporting PHP4!
	if ($dir = @opendir($context['epmod_modules_dir']))
	{
		$dirs = array();
		while ($file = readdir($dir))
		{
			$retVal = GetEnvisionModuleInfo('', '', $context['epmod_modules_dir'], $file, false);
			if ($retVal === false)
				continue;
			else
			{
				$added_mods[] = $file;
				$module_info[$file] = $retVal;
			}
		}
	}

	if (isset($module_info))
	{
		// Find out if any of these are installed.
		$request = $smcFunc['db_query']('', '
			SELECT id_module, name
			FROM {db_prefix}ep_modules
			WHERE name IN ({array_string:module_names})',
			array(
				'module_names' => $added_mods,
			)
		);

		while ($row = $smcFunc['db_fetch_assoc']($request))
		{
			if (!isset($info[$row['name']]))
			{
				// It's installed, so remove the install link, and add uninstall and settings links.
				unset($module_info[$row['name']]['install_link']);
				$module_info[$row['name']] += array(
					'uninstall_link' => $scripturl . '?action=admin;area=epmodules;sa=epuninstallmodule;name=' . $row['name'] . ';' . $context['session_var'] . '=' . $context['session_id'],
					'settings_link' => $scripturl . '?action=admin;area=epmodules;sa=modifymod;modid=' . $row['id_module'] . ';' . $context['session_var'] . '=' . $context['session_id'],
				);
			}
		}

		return $module_info;
	}
	else
		return array();
}

function GetEnvisionInstalledModules($installed_mods = array())
{
	global $smcFunc, $user_info, $context, $txt;

	// We'll need to build up a list of modules that are installed.
	if (count($installed_mods) < 1)
	{
		$installed_mods = array();
		// Let's collect all installed modules...
		$request = $smcFunc['db_query']('', '
			SELECT name, files, functions
			FROM {db_prefix}ep_modules
			WHERE files != {string:empty_string} AND functions != {string:empty_string}',
			array(
				'empty_string' => '',
			)
		);

		while ($row = $smcFunc['db_fetch_assoc']($request))
		{
			$installed_mods[] = array(
				'name' => $row['name'],
				'files' => $row['files'],
				'functions' => $row['functions'],
			);
		}

		$smcFunc['db_free_result']($request);
	}

	foreach($installed_mods as $installed)
	{
		$retVal = GetEnvisionModuleInfo($installed['files'], $installed['functions'], $context['epmod_modules_dir'], $installed['name'], $installed['name']);
		if ($retVal === false)
			continue;

		$module_info[$installed['name']] = $retVal;
	}

	return isset($module_info) ? $module_info : array();
}

function ep_insert_column()
{
	global $smcFunc, $context;

	isAllowedTo('admin_forum');

	$sdata = explode('_', $_GET['insert']);

	$columns = array(
		'id_layout' => 'int',
		'column' => 'string',
		'row' => 'string',
		'enabled' => 'int',
	);

	$data = array(
		$_GET['layout'],
		$sdata[1] . ':0',
		$sdata[0] . ':0',
		-2,
	);

	$keys = array(
		'id_layout_position',
		'id_layout',
	);

	$smcFunc['db_insert']('insert', '{db_prefix}ep_layout_positions',  $columns, $data, $keys);

	$iid = $smcFunc['db_insert_id']('{db_prefix}ep_layout_positions', 'id_layout_position');

	loadTemplate('ep_template/Xml');
	$context['sub_template'] = 'generic_xml';
	$xml_data = array(
		'items' => array(
			'identifier' => 'item',
			'children' => array(
				array(
					'attributes' => array(
						'insertid' => $iid,
					),
					'value' => $_GET['insert'] . '_' . $iid,
				),
			),
		),
	);
	$context['xml_data'] = $xml_data;
}

function ep_edit_db_select()
{
	global $smcFunc, $context;

	isAllowedTo('admin_forum');

	// Make sure we have a valid parameter ID of the right type.
	$request = $smcFunc['db_query']('', '
		SELECT
			emp.value
		FROM {db_prefix}ep_module_parameters AS emp
		WHERE emp.id_param = {int:config_id} AND emp.type = {string:type}',
		array(
			'config_id' => $_POST['config_id'],
			'type' => 'db_select',
		)
	);

	$row = $smcFunc['db_fetch_assoc']($request);

	$db_options = explode(':', $row['value']);
	$db_select_options = explode(';', $row['value']);
	$db_custom = isset($db_options[2]) && stristr(trim($db_options[2]), 'custom');

	if (isset($db_options[0], $db_options[1]))
	{
		$db_input = explode(';', $db_options[0]);
		$db_output = explode(';', $db_options[1]);

		if (isset($db_input[0], $db_input[1], $db_output[0], $db_output[1]))
		{
			$db_select = array();
			$db_select_params = '';
			$db_selected = $db_input[0];
			$db_select['select2'] = $db_input[1];

			if (isset($db_select_options[0], $db_select_options[1], $db_select_options[2]))
			{
				unset($db_select_options[0]);
				$db_select_params = implode(';', $db_select_options);
			}

			if (stristr(trim($db_output[0]), '{db_prefix}'))
			{
				$db_select['table'] = $db_output[0];
				$db_select['select1'] = $db_output[1];
			}
			elseif (stristr(trim($db_output[1]), '{db_prefix}'))
			{
				$db_select['table'] = $db_output[1];
				$db_select['select1'] = $db_output[0];
			}
			else
				unset($db_select);
		}
	}

	if (isset($_POST['data']))
	{
		$key = explode('_', $_POST['key']);

		$smcFunc['db_query']('', '
			UPDATE ' . $db_select['table'] . '
			SET {raw:query_select} = {string:data}
			WHERE {raw:key_select} = {string:key}',
			array(
				'data' => $_POST['data'],
				'key' => $key[count($key) - 1],
				'query_select' =>  $db_select['select1'],
				'key_select' =>  $db_select['select2'],
			)
		);

		die();
	}
	else
	{
		// Needed for db_list_indexes...
		db_extend('packages');

		$columns = array(
			$db_select['select1'] => 'string',
		);

		$values = $new_db_vals;

		$keys = array(
			$smcFunc['db_list_indexes']($db_select['table']),
		);

		$smcFunc['db_insert']('insert', $db_select['table'], $columns, $values, $keys);

		$iid = $smcFunc['db_insert_id']('{db_prefix}ep_layout_positions', 'id_layout_position');

		loadTemplate('ep_template/Xml');
		$context['sub_template'] = 'generic_xml';
		$xml_data = array(
			'items' => array(
				'identifier' => 'item',
				'children' => array(
					array(
						'value' => $_GET['insert'] . '_' . $iid,
					),
				),
			),
		);
		$context['xml_data'] = $xml_data;
	}

}

function loadLayout($url)
{
	global $smcFunc, $context, $scripturl, $txt, $user_info;

	if (is_int($url))
	{
		$request = $smcFunc['db_query']('', '
			SELECT
				*
			FROM {db_prefix}ep_layouts AS el
				LEFT JOIN {db_prefix}ep_layout_positions AS elp ON (elp.id_layout = el.id_layout)
				LEFT JOIN {db_prefix}ep_module_positions AS emp ON (emp.id_layout_position = elp.id_layout_position)
				LEFT JOIN {db_prefix}ep_modules AS em ON (em.id_module = emp.id_module)
			WHERE el.id_layout = {int:id_layout}',
			array(
				'zero' => 0,
				'id_layout' => $url,
			)
		);
	}
	else
	{
		$match = (!empty($_REQUEST['board']) ? '[board]=' . $_REQUEST['board'] : (!empty($_REQUEST['topic']) ? '[topic]=' . (int) $_REQUEST['topic'] : (!empty($_REQUEST['page']) ? '[page]=' . $_REQUEST['page'] : $url)));
		$general_match = (!empty($_REQUEST['board']) ? '[board]' : (!empty($_REQUEST['topic']) ? '[topic]' : (!empty($_REQUEST['page']) ? '[page]' : (!empty($_REQUEST['action']) ? '[all_actions]' : ''))));

		$request = $smcFunc['db_query']('', '
			SELECT
				el.id_layout
			FROM {db_prefix}ep_layouts AS el
				LEFT JOIN {db_prefix}ep_layout_actions AS ela ON (ela.action = {string:current_action})
			WHERE el.id_member = {int:zero}',
			array(
				'current_action' => $match,
				'zero' => 0,
			)
		);

		$num2 = $smcFunc['db_num_rows']($request);
		$smcFunc['db_free_result']($request);

		if (empty($num2))
			$match = $general_match;

		// If this is empty, e.g. index.php?action or index.php?action=
		if (empty($match))
		{
			$match = '[home]';
			$context['ep_home'] = true;
		}

		// Let's grab the data necessary to show the correct layout!
		$request = $smcFunc['db_query']('', '
			SELECT
				*
			FROM {db_prefix}ep_layouts AS el
				JOIN {db_prefix}ep_layout_actions AS ela ON (ela.action = {string:current_action})
				LEFT JOIN {db_prefix}ep_layout_positions AS elp ON (elp.id_layout = el.id_layout)
				LEFT JOIN {db_prefix}ep_module_positions AS emp ON (emp.id_layout_position = elp.id_layout_position)
				LEFT JOIN {db_prefix}ep_modules AS em ON (em.id_module = emp.id_module)
			WHERE el.id_member = {int:zero}',
			array(
				'zero' => 0,
				'current_action' => $match,
			)
		);

		$num = $smcFunc['db_num_rows']($request);
		if (empty($num))
			return;

		$old_row = 0;
		$view_groups = array();

		// Let the theme know we have a layout.
		$context['has_ep_layout'] = true;
	}

	while ($row = $smcFunc['db_fetch_assoc']($request))
	{
		$smf_col = empty($row['id_module']) && !is_null($row['id_position']);

		if (!$smf_col && $row['status'] == 'inactive')
			continue;

		if (!isset($ep_modules[$row['x_pos']][$row['y_pos']]) && !empty($row['id_layout_position']))
			$ep_modules[$row['x_pos']][$row['y_pos']] = array(
				'is_smf' => $smf_col,
				'id_layout_position' => $row['id_layout_position'],
				'colspan' => $row['colspan'] >= 2 ? ' colspan="' . $row['colspan'] . '"' : '',
				'html' => ($row['colspan'] >= 2 ? ' colspan="' . $row['colspan'] . '"' : '') . ($context['ep_home'] && in_array($row['y_pos'], array(0, 2)) || !$context['ep_home'] && $row['y_pos'] <= 1 && !$smf_col ? ' style="width: 200px;"' : ''),
			);

		if (!is_null($row['id_position']) && !empty($row['id_layout_position']))
		{
			// Store $context variables for each module.  Mod Authors can use these for unique ID values, function names, etc.
			// !!! Is this really needed?
			if (!isset($ep_modules[$row['x_pos']][$row['y_pos']]['modules'][$row['position']]))
				if (empty($context['ep_mod_' . $row['type']]))
					$context['ep_mod_' . $row['type']] = $row['type'] .  '_' . $row['id_position'];

			$ep_modules[$row['x_pos']][$row['y_pos']]['modules'][$row['position']] = array(
				'is_smf' => $smf_col,
				'modify_link' => $user_info['is_admin'] ? ' [<a href="' . $scripturl . '?action=admin;area=epmodules;sa=modify;in=' . $row['id_module'] . ';' . $context['session_var'] . '=' . $context['session_id'] . '">' . $txt['modify'] . '</a>]' : '',
				'type' => $row['type'],
				'id' => $row['id_position'],
			);
		}
	}

	// Shouldn't be empty, but we check anyways!
	if (!empty($ep_modules))
	{
		// Open a script tag because some Javascript is coming... I wish I had no need to do this.
		$context['insert_after_template'] .= '
		<script type="text/javascript"><!-- // --><![CDATA[';

		ksort($ep_modules);

		foreach ($ep_modules as $k => $ep_module_rows)
		{
			ksort($ep_modules[$k]);
			foreach ($ep_modules[$k] as $key => $ep)
				if (is_array($ep_modules[$k][$key]))
					foreach($ep_modules[$k][$key] as $pos => $mod)
					{
						if ($pos != 'modules' || !is_array($ep_modules[$k][$key][$pos]))
							continue;

						ksort($ep_modules[$k][$key][$pos]);
					}
		}

		$module_context = ep_load_module_context();

		foreach ($ep_modules as $row_id => $row_data)
			foreach ($row_data as $column_id => $column_data)
				if (isset($column_data['modules']))
						foreach ($column_data['modules'] as $module => $id)
							if (!empty($id['type']))
								$ep_modules[$row_id][$column_id]['modules'][$module] = ep_process_module($module_context, $id, !is_int($url));

		if (is_int($url))
			$context['ep_columns'] = $ep_modules;
		else
			$context['envision_columns'] = $ep_modules;

		// We are done with the modules' Javascript, sir!
		$context['insert_after_template'] .= '
		// ]]></script>';
	}
}

function ep_process_module($module_context, $data, $full_layout)
{
	global $context, $modSettings, $settings, $options, $txt, $user_info, $scripturl, $smcFunc;

	$info = $module_context[$data['type']];
	$data = array_merge_recursive($data, $info);

	/*if ($data['id']==3) die(var_dump(array_merge_recursive($data, $info), $data, $info));
	if ($data['id']==3){
	// Now grab the custom fields assosiated with this module.
	$request = $smcFunc['db_query']('', '
		SELECT
			name, type, options, value
		FROM {db_prefix}ep_module_field_data AS emd
			LEFT JOIN {db_prefix}ep_module_fields AS emf ON (emf.id_module_position = {in:id_position})
		WHERE emd.id_field = emf.id_field',
		array(
			'id_position' => $data['id'],
		)
	);

	while ($row = $smcFunc['db_fetch_assoc']($request))
		$data['fields'][] = array(
			$row['name'] => array(
				'type' => $row['type'],
				'options' => $row['options'],
				'value' => $row['value'],
			),
		);
}*/

		if ($full_layout === false)
			return $data;

		if (file_exists($context['epmod_modules_dir'] . '/' . $data['type'] . '/main.php'))
			require_once($context['epmod_modules_dir'] . '/' . $data['type'] . '/main.php');

		// Load the module template.
		if (empty($data['template']) || !empty($data['template']) && !file_exists($context['epmod_template'] . $data['template'].'.php'))
			$data['template'] = 'default';

		require_once($context['epmod_template'] . $data['template'] . '.php');

		// Correct the title target...
		if (!isset($data['module_target']))
			$data['module_target'] = 1;

		switch ((int) $data['module_target'])
		{
			case 1:
				$data['module_target'] = '_self';
				break;
			case 2:
				$data['module_target'] = '_parent';
				break;
			case 3:
				$data['module_target'] = '_top';
				break;
			default:
				$data['module_target'] = '_blank';
				break;
		}

		if (!empty($data['module_icon']));
			$data['module_icon'] = '<img src="' . $context['epmod_icon_url'] . $data['module_icon'] . '" alt="" title="' . $data['module_title'] . '" class="icon" style="margin-left: 0px;" />&nbsp;';

		if (isset($data['module_link']))
		{
			$http = stristr($data['module_link'], 'http://') !== false || stristr($data['module_link'], 'www.') !== false;

			if ($http)
				$data['module_title'] = '<a href="' . $data['module_link'] . '" target="' . $data['module_target'] . '">' . $data['module_title'] . '</a>';
			else
				$data['module_title'] = '<a href="' . $scripturl . '?' . $data['module_link'] . '" target="' . $data['module_target'] . '">' . $data['module_title'] . '</a>';
		}

		if (!empty($data['fields']))
		{
			$fields = $data['fields'];
			$data['fields'] = array();

			foreach ($fields as $key => $field)
				$data['fields'][$key] = loadParameter(array(), $field['type'], $field['value']);
		}

		$data['function'] = 'module_' . $data['type'];

	$data['is_collapsed'] = $user_info['is_guest'] ? !empty($_COOKIE[$data['type'] . 'module_' . $data['id']]) : !empty($options[$data['type'] . 'module_' . $data['id']]);

	if (isset($data['header_display']) && $data['header_display'] == 2)
	{
		$data['is_collapsed'] = false;
		$data['hide_upshrink'] = true;
	}
	else
		$data['hide_upshrink'] = false;

	if (!isset($data['header_display']))
		$data['header_display'] = 1;

	// Which function to call?
	$toggleModule = !empty($modSettings['ep_module_enable_animations']) ? 'Anim('  : '(';
	$toggleModule .= '\'' . $data['type'] . '\', \'' . $data['id'] . '\'';

	if (!empty($modSettings['ep_module_enable_animations']))
	{
		$toggleModule .= ', \'' . $data['type'] . 'module_' . $data['id'] . '\'';
		$toggleModule .= ', \'' . (intval($modSettings['ep_module_animation_speed']) + 1) . '\');';
	}
	else
		$toggleModule .= ');';

	if (!$data['hide_upshrink'])
		$context['insert_after_template'] .= '
		var ' . $data['type'] . 'toggle_' . $data['id'] . ' = new smc_Toggle({
			bToggleEnabled:  ' . (!$data['hide_upshrink'] ? 'true' : 'false') . ',
			bCurrentlyCollapsed: ' . ($data['is_collapsed'] ? 'true' : 'false') . ',
			funcOnBeforeCollapse: function () {
				collapseModule' . $toggleModule . '
			},
			funcOnBeforeExpand: function () {
				expandModule' . $toggleModule . '
			},
			aSwappableContainers: [' . (empty($modSettings['ep_module_enable_animations']) ? '
				\'' . $data['type'] . 'module_' . $data['id'] . '\'' : '') . '
			],
			aSwapImages: [
				{
					sId: \'' . $data['type'] . 'collapse_' . $data['id'] . '\',
					srcExpanded: smf_images_url + \'/collapse.gif\',
					altExpanded: ' . JavaScriptEscape($txt['upshrink_description']) . ',
					srcCollapsed: smf_images_url + \'/expand.gif\',
					altCollapsed: ' . JavaScriptEscape($txt['upshrink_description']) . '
				}
			],
			oThemeOptions: {
				bUseThemeSettings: ' . ($user_info['is_guest'] ? 'false' : 'true') . ',
				sOptionName: \'' . $data['type'] . 'collapse_' . $data['id'] . '\',
				sSessionVar: ' . JavaScriptEscape($context['session_var']) . ',
				sSessionId: ' . JavaScriptEscape($context['session_id']) . '
			},
			oCookieOptions: {
				bUseCookie: ' . ($user_info['is_guest'] ? 'true' : 'false') . ',
				sCookieName: \'' . $data['type'] . 'collapse_' . $data['id'] . '\'
			}
		});';

	return $data;
}

function load_envision_menu($menu_buttons)
{
	global $smcFunc, $user_info, $scripturl, $context;

	$request = $smcFunc['db_query']('', '
		SELECT *
		FROM {db_prefix}ep_envision_menu
		ORDER BY id_button ASC',
		array(
			'db_error_skip' => true,
		)
	);

	if (!empty($smcFunc['db_error']))
		return $menu_buttons;

	$new_menu_buttons = array();

	while ($row = $smcFunc['db_fetch_assoc']($request))
	{
		$permissions = explode(',', $row['permissions']);

		$ep_temp_menu = array(
			'title' => $row['name'],
			'href' => ($row['target'] == 'forum' ? $scripturl : '') . $row['link'],
			'show' => (array_intersect($user_info['groups'], $permissions)) && ($row['status'] == 'active' || (allowedTo('admin_forum') && $row['status'] == 'inactive')),
			'target' => $row['target'],
			'active_button' => false,
		);

		foreach ($menu_buttons as $area => $info)
		{
			if ($area == $row['parent'] && $row['position'] == 'before')
				$new_menu_buttons[$row['slug']] = $ep_temp_menu;

			$new_menu_buttons[$area] = $info;

			if ($area == $row['parent'] && $row['position'] == 'after')
				$new_menu_buttons[$row['slug']] = $ep_temp_menu;

			if ($area == $row['parent'] && $row['position'] == 'child_of')
				$new_menu_buttons[$row['parent']]['sub_buttons'][$row['slug']] = $ep_temp_menu;

			if ($row['position'] == 'child_of' && isset($info['sub_buttons']) && array_key_exists($row['parent'], $info['sub_buttons']))
				$new_menu_buttons[$area]['sub_buttons'][$row['parent']]['sub_buttons'][$row['slug']] = $ep_temp_menu;
		}
	}

	if (!empty($new_menu_buttons))
		$menu_buttons = $new_menu_buttons;

	return $menu_buttons;
}

function add_ep_menu_buttons($menu_buttons)
{
	global $txt, $context, $scripturl;

	// Adding the Forum button to the main menu.
	$envisionportal = array(
		'title' => (!empty($txt['forum']) ? $txt['forum'] : 'Forum'),
		'href' => $scripturl . '?action=forum',
		'show' => (!empty($modSettings['ep_portal_mode']) && allowedTo('ep_view') ? true : false),
		'active_button' => false,
	);

	$new_menu_buttons = array();
	foreach ($menu_buttons as $area => $info)
	{
		$new_menu_buttons[$area] = $info;
		if ($area == 'home')
			$new_menu_buttons['forum'] = $envisionportal;
	}

	$menu_buttons = $new_menu_buttons;

	// Adding the Envision Portal submenu to the Admin button.
	if (isset($menu_buttons['admin']))
	{
		$envisionportal = array(
			'envisionportal' => array(
				'title' => $txt['ep_'],
				'href' => $scripturl . '?action=admin;area=epmodules;sa=epmanmodules',
				'show' => allowedTo('admin_forum'),
				'is_last' => true,
			),
		);

		$i = 0;
		$new_subs = array();
		$count = count($menu_buttons['admin']['sub_buttons']);
		foreach($menu_buttons['admin']['sub_buttons'] as $subs => $admin)
		{
			$i++;
			$new_subs[$subs] = $admin;
			if($subs == 'permissions')
			{
				$permissions = true;
				// Remove is_last if set.
				if (isset($buttons['admin']['sub_buttons']['permissions']['is_last']))
					unset($buttons['admin']['sub_buttons']['permissions']['is_last']);

					$new_subs['envisionportal'] = $envisionportal['envisionportal'];

				// set is_last to envisionportal if it's the last.
				if ($i != $count)
					unset($new_subs['envisionportal']['is_last']);
			}
		}

		// If permissions doesn't exist for some reason, we'll put it at the end.
		if (!isset($permissions))
			$menu_buttons['admin']['sub_buttons'] += $envisionportal;
		else
			$menu_buttons['admin']['sub_buttons'] = $new_subs;
	}
}

function add_ep_admin_areas($admin_areas)
{
	global $txt;

	// Building the Envision Portal admin areas
	$envisionportal = array(
		'title' => $txt['ep_'],
		'areas' => array(
			'epconfig' => array(
				'label' => $txt['ep_admin_config'],
				'file' => 'ep_source/ManageEnvisionSettings.php',
				'function' => 'Configuration',
				'icon' => 'epconfiguration.png',
				'subsections' => array(
					'epinfo' => array($txt['ep_admin_information'], ''),
					'epgeneral' => array($txt['ep_admin_general'], ''),
					'epmodulesettings' => array($txt['ep_admin_module_settings'], ''),
				),
			),
			'epmodules' => array(
				'label' => $txt['ep_admin_modules'],
				'file' => 'ep_source/ManageEnvisionModules.php',
				'function' => 'Modules',
				'icon' => 'epmodules.png',
				'subsections' => array(
					'epmanmodules' => array($txt['ep_admin_manage_modules'], ''),
					'epaddmodules' => array($txt['ep_admin_add_modules'], ''),
				),
			),
			'eppages' => array(
				'label' => $txt['ep_admin_pages'],
				'file' => 'ep_source/ManageEnvisionPages.php',
				'function' => 'Pages',
				'icon' => 'eppages.png',
				'subsections' => array(
					'epmanpages' => array($txt['ep_admin_manage_pages'], ''),
					'epadepage' => array($txt['ep_admin_add_page'], ''),
				),
			),
			'epmenu' => array(
				'label' => $txt['ep_admin_menu'],
				'file' => 'ep_source/ManageEnvisionMenu.php',
				'function' => 'Menu',
				'icon' => 'epmenu.png',
				'subsections' => array(
					'epmanmenu' => array($txt['ep_admin_manage_menu'], ''),
					'epaddbutton' => array($txt['ep_admin_add_button'], ''),
				),
			),
		),
	);

	$new_admin_areas = array();
	foreach ($admin_areas as $area => $info)
	{
		$new_admin_areas[$area] = $info;
		if ($area == 'config')
			$new_admin_areas['portal'] = $envisionportal;
	}

	$admin_areas = $new_admin_areas;
}

function envision_whos_online($actions)
{
	global $txt, $smcFunc, $user_info;

	$data = array();

	if (isset($actions['page']))
	{
		$data = $txt['who_hidden'];

		if (is_numeric($actions['page']))
			$where = 'id_page = {int:numeric_id}';
		else
			$where = 'page_name = {string:name}';

		$result = $smcFunc['db_query']('', '
			SELECT id_page, page_name, title, permissions, status
			FROM {db_prefix}ep_envision_pages
			WHERE ' . $where,
			array(
				'numeric_id' => $actions['page'],
				'name' => $actions['page'],
			)
		);
		$row = $smcFunc['db_fetch_assoc']($result);

		// Invalid page? Bail.
		if (empty($row))
			return $data;

		// Skip this turn if they cannot view this...
		if ((!array_intersect($user_info['groups'], explode(',', $row['permissions'])) || !allowedTo('admin_forum')) && ($row['status'] != 1 || !allowedTo('admin_forum')))
			return $data;

		$page_data = array(
			'id' => $row['id_page'],
			'page_name' => $row['page_name'],
			'title' => $row['title'],
		);

		// Good. They are allowed to see this page, so let's list it!
		if (is_numeric($actions['page']))
			$data = sprintf($txt['ep_who_page'], $page_data['id'], censorText($page_data['title']));
		else
			$data = sprintf($txt['ep_who_page'], $page_data['page_name'], censorText($page_data['title']));
	}

	return $data;
}

function envision_integrate_actions(&$action_array)
{
	$action_array['envision'] = array('ep_source/EnvisionPortal.php', 'envisionActions');
	$action_array['envisionFiles'] = array('ep_source/EnvisionPortal.php', 'envisionFiles');
	$action_array['forum'] = array('BoardIndex.php', 'BoardIndex');
}

function envision_integrate_pre_load()
{
	global $modSettings, $sourcedir;

	// Is Envision Portal enabled in the Core Features?
	$modSettings['ep_portal_mode'] = isset($modSettings['admin_features']) ? in_array('ep', explode(',', $modSettings['admin_features'])) : false;

	require_once($sourcedir . '/ep_source/EnvisionPortal.php');
	require_once($sourcedir . '/ep_source/Subs-EnvisionModules.php');
	require_once($sourcedir . '/ep_source/EnvisionModules.php');
}

function envision_integrate_load_theme()
{
	global $context, $maintenance, $modSettings;

	// Load the portal layer, making sure we didn't arleady add it.
	if (!empty($context['template_layers']) && !in_array('portal', $context['template_layers']))
		// Checks if the forum is in maintenance, and if the portal is disabled.
		if (($maintenance && !allowedTo('admin_forum')) || empty($modSettings['ep_portal_mode']) || !allowedTo('ep_view'))
			$context['template_layers'] = array('html', 'body');
		else
			$context['template_layers'][] = 'portal';

	if (!empty($modSettings['ep_portal_mode']) && allowedTo('ep_view'))
	{
		if (!loadLanguage('ep_languages/EnvisionPortal'))
			loadLanguage('ep_languages/EnvisionPortal');

		loadTemplate('ep_template/EnvisionPortal', 'ep_css/envisionportal');
	}

	// Kick off time!
	ep_init();
}

?>