<?php
/**************************************************************************************
* Subs-EnvisionPlugins.php                                                            *
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

/**
 * Calls a given integration hook at the related point in the code.
 *
 * Each of the hooks is an array of functions within $modSettings['hooks'], to be called at relevant points in the code. Do note that the file-inclusion hooks may not be called with this.
 *
 * The contents of the $modSettings['ep_hooks'] value is a comma separated list of function names to be called at the relevant point. These are either procedural functions or static class methods (classname::method).
 *
 * @param string $hook the name of the hook as given in $modSettings.
 * @param array $parameters parameters to be passed to the hooked functions. The list of parameters each method is exposed to is dependent on the calling code, and parameters passed by reference will be passed to hook functions as such.
 * @return array an array of results, one element per hooked function. This will be solely dependent on the hooked function.
 */
function ep_call_hook($hook, $parameters = array())
{
	global $modSettings;

	if (empty($modSettings['ep_hooks']))
		$functions = empty($modSettings['ep_permanented_hooks']) ? '' : $modSettings['ep_permanented_hooks'];
	else
		$functions = $modSettings['ep_hooks'] + $modSettings['ep_permanented_hooks'];

	if (empty($functions[$hook]))
		return array();

	$results = array();

	// Loop through each function.
	foreach ($functions[$hook] as $function)
	{
		$function = trim($function);
		$call = strpos($function, '::') !== false ? explode('::', $function) : $function;

		// If it isn't valid, remove it from our list.
		if (is_callable($call))
			$results[$function] = call_user_func_array($call, $parameters);
		else
			ep_remove_hook($hook, $function);
	}

	return $results;
}

/**
 * {@see ep_call_hook}
 *
 * The file-inclusion hooks should be called with this.
 *
 * The contents of the $modSettings['ep_hooks'] value is a comma separated list of files to be included at the relevant point.
 *
 * @param string $hook the name of the hook as given in $modSettings.
 * @param array $base the base path of the file.
 * @return bool true if the specified file was found and included; false otherwise.
 */
function ep_include_hook($hook, $base = '')
{
	global $modSettings, $themedir, $sourcedir, $boarddir;

	if (empty($modSettings['ep_hooks']))
		$files = $modSettings['ep_permanented_hooks'];
	else
		$files = $modSettings['ep_hooks'] + $modSettings['ep_permanented_hooks'];

	if (empty($files[$hook]))
		return false;

	// Plugin directories
	$plugin_dirs = array(
		'$template' => $themedir . '/ep_plugin_template',
		'$source' => $sourcedir . '/ep_plugin_source',
		'$extra' => $boarddir . '/ep_plugin_extra'
	);

	// Loop through each file and remove the odd strand if present...
	foreach ($files[$hook] as $file)
	{
		if (empty($base))
			$base = strtr($file, $plugin_dirs);

		if (file_exists($base . '/' . $file))
			require_once($base . '/' . $file);
		else
		{
			ep_remove_hook($hook, $file);
			return false;
		}
	}

	return true;
}

/**
 * {@see ep_call_hook}
 *
 * The language file-inclusion hooks should be called with this.
 *
 * The contents of the $modSettings['ep_hooks'] value is a comma separated list of files to be included at the relevant point.
 *
 * @param string $hook the name of the hook as given in $modSettings.
 * @param array $base the base path of the language file.
 * @param array $lang a valid language string to use.
 * $param int $try if first try will redo the function if language not found; second try will remove the hook entirely if the language specified wasn't found. It's highly recommended to leave this parameter alone.
 * @return bool true if the specified language file was found and included; false otherwise.
 */
function ep_include_language_hook($hook, $base, $lang = '', $try = 1)
{
	global $modSettings, $language, $user_info;

	if (empty($modSettings['ep_hooks']))
		$files = $modSettings['ep_permanented_hooks'];
	else
		$files = $modSettings['ep_hooks'] + $modSettings['ep_permanented_hooks'];

	if (empty($files[$hook]))
		return false;

	// Default to the user's language. Fall back to forum's global language
	if ($lang == '')
		$lang = isset($user_info['language']) ? $user_info['language'] : $language;

	// Loop through each file and remove the odd strand if present...
	foreach ($files[$hook] as $file)
		if (file_exists($base . '/' . $file . '/' . $lang . '.php'))
			template_include($base . '/' . $file . '/' . $lang . '.php');
		else
			if ($try == 1)
				ep_include_language_hook($hook, $base, '', 2);
			else
			{
				ep_remove_hook($hook, $file);
				return false;
			}

	return true;
}

/**
 * Add a function to one of the integration hook stacks.
 *
 * This function adds a function to be called (or file to be loaded, for the pre_include hook). This function also prevents the same function being added to the same hook twice.
 *
 * @param string $hook The name of the hook that has zero or more functions attached, that the function will be added to.
 * @param string $function the name of the function whose name should be added to the named hook.
 * @param bool $permanent whether the named function will be added to the hook registry permanently (default), or simply for the current page load only.
 * @return bool true on success; false otherwise.
*/
function ep_add_hook($hook, $function, $permanent = true)
{
	global $modSettings;

	// Do nothing if it's already there, except if we're asking for registration and it isn't permanented yet.
	if ((!$permanent || in_array($function, $modSettings['ep_permanented_hooks'][$hook])) && ($in_hook = @in_array($function, $modSettings['ep_hooks'][$hook])))
		return false;

	// Add it!
	if (!$in_hook)
		$modSettings['ep_hooks'][$hook][] = $function;

	if (!$permanent)
		return true;

	// Add it to the permanent hooks list.
	$modSettings['ep_permanented_hooks'][$hook][] = $function;
	$hooks = $modSettings['ep_permanented_hooks'];
	updateSettings(array('ep_permanented_hooks' => serialize($hooks)));
	$modSettings['ep_permanented_hooks'] = $hooks;

	return true;
}

/**
 * Remove a function from one of the integration hook stacks.
 *
 * This function not only removes the hook from the local registry, but also from the master registry. Note that it does not check whether the named function is callable, simply that it is part of the stack - it can be used on the file-inclusion hooks as well. If the function is not attached to the named hook, this function will simply return false for failure.
 *
 * @param string $hook the name of the hook that has one or more functions attached.
 * @param string $function the name of the function whose name should be removed from the named hook.
 * @return bool true on success; false otherwise.
 */
function ep_remove_hook($hook, $function)
{
	global $modSettings;

	// You can only remove it if it's available.
	if (!empty($modSettings['ep_hooks'][$hook]) && in_array($function, $modSettings['ep_hooks'][$hook]))
	{
		$modSettings['ep_hooks'][$hook] = array_diff($modSettings['ep_hooks'][$hook], (array) $function);

		return true;
	}

	if (!empty($modSettings['ep_permanented_hooks'][$hook]) && in_array($function, $modSettings['ep_permanented_hooks'][$hook]))
	{
		$modSettings['ep_permanented_hooks'][$hook] = array_diff($modSettings['ep_permanented_hooks'][$hook], (array) $function);
		updateSettings(array('ep_permanented_hooks' => serialize($modSettings['ep_permanented_hooks'])));

		return true;
	}

	return false;
}

/**
 * Checks for an enabled plugin and runs an element of it.
 *
 * @param string $name The name of the plugin to call.
 * @param string $function The name of the function within the plugin to call. Does not call any function if:
 * - the function wasn't found
 * - an empty string was passed
 * @param array $parameters The parameters used when calling the function, if specified.
 * @return mixed/bool False on failure; the return value of the called function; true if nno function called but the specifiedd plugin was found to be enabled.
 */
function ep_call_plugin($name, $function = '', $parameters = array())
{
	global $context, $modSettings;

	if (!isset($modSettings['ep_plugins']))
		return false;

	$file = $context['ep_plugins_dir'] . '/' . $name . '/scripts/script.php';
	$plugins = unserialize($modSettings['ep_plugins']);

	if (!in_array($name, $plugins))
		return false;

	if (!file_exists($file))
		return false;

	require_once($file);

	if (is_callable($function))
		return call_user_func_array($function_name, $parameters);

	return true;
}

/**
 * Checks for an enabled plugin specified by $name and attempts to include its language specified by $lang. It defaults to the user's language if set and falls back to forum's global language.
 *
 * @param string $name The name of the plugin to call.
 * @param array $lang a valid language string to use.
 * $param int $try if first try will redo the function if language not found. It's highly recommended to leave this parameter alone.
 * @return bool true if the specified language file was found and included; false otherwise.
 */
function ep_include_plugin_language($name, $lang = '', $try = 1)
{
	global $modSettings, $language, $user_info;

	if (!isset($modSettings['ep_plugins']))
		return false;

	$base = $context['ep_plugins_dir'] . '/' . $name . '/languages';
	$plugins = unserialize($modSettings['ep_plugins']);

	if (!in_array($name, $plugins))
		return false;

	// Default to the user's language. Fall back to forum's global language
	if ($lang == '')
		$lang = isset($user_info['language']) ? $user_info['language'] : $language;

	if (file_exists($base . '/' . $file . '/' . $lang . '.php'))
		template_include($base . '/' . $file . '/' . $lang . '.php');
	else
		if ($try == 1)
			ep_include_plugin_language($name, '', 2);
		else
			return false;

	return true;
}

function ep_get_plugin_info($dirname, $file, $installing = false)
{
	global $boarddir, $context, $modSettings, $scripturl, $smcFunc;

	// If the required info file does not exist let's silently die...
	if (!file_exists($dirname . '/' . $file . '/plugin.xml'))
		return false;

	// And finally, get the file's contents
	$file_info = file_get_contents($dirname . '/' . $file . '/plugin.xml');
	$file_setting = $dirname . '/' . $file . '/scripts/script.php';
	$func_name = 'plugin_' . $file . '_info';

	// Parse info.xml into an xmlArray.
	loadClassFile('Class-Package.php');
	$plugin_info1 = new xmlArray($file_info);
	$plugin_info1 = $plugin_info1->path('plugin[0]');

	// Required XML elements and attributes. Quit if any one is missing.
	if (!$plugin_info1->exists('title')) return false;
	if (!$plugin_info1->exists('description')) return false;
	if (!file_exists($file_setting)) return false;
	require_once($file_setting);

	// If installing, run the script.
	if ($installing && is_callable($func_name))
		$results = $func_name();
	else
		$results = array();

	return array_merge(array(
		'title' => $plugin_info1->fetch('title'),
		'description' => ($plugin_info1->exists('description/@parsebbc')) ? ($plugin_info1->fetch('description/@parsebbc') ? parse_bbc($plugin_info1->fetch('description')) : $plugin_info1->fetch('description')) : $plugin_info1->fetch('description'),
	), $results);
}

function listPlugins($installing = false)
{
	global $context, $modSettings, $sourcedir, $scripturl, $smcFunc, $txt;
	$plugin_names = array();

	// Let's loop throuugh each folder and get their plugin data. If anything goes wrong we shall skip it.
	$files = scandir($context['ep_plugins_dir']);

	foreach ($files as $file)
	{
		$retVal = ep_get_plugin_info($context['ep_plugins_dir'], $file, $installing);
		if ($retVal === false)
			continue;
		else
		{
			$plugin_names[] = $file;
			$plugin_info[$file] = $retVal;
			$plugin_info[$file]['enabled'] = ep_call_plugin($file);
		}
	}

	if (isset($plugin_info))
		return $plugin_info;
	else
		return array();
}

?>