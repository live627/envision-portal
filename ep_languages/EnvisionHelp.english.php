<?php
// Version 1.0; EnvisionHelp

global $helptxt;

$helptxt['ep_collapse_modules_help'] = 'Sets ability collapse of the modules.';
$helptxt['ep_color_members_help'] = 'Enable/Disable showing of nicknames color based on membergroups color.';
$helptxt['ep_inline_copyright_help'] = 'Option to display the copyright on the same line as SMF\'s';
$helptxt['ep_module_display_style_help'] = 'Sets the current display for all modules appearance within Envision Portal.';
$helptxt['ep_module_enable_animationshelp'] = 'Enable or Disable module animations when expanding and collapsing a module.';
$helptxt['ep_module_animation_speed_help'] = 'Sets how fast the modules throughout Envision Portal will expand and collapse.  Note:  Module animations must be enabled for this to take effect.  The default is set at Normal.';
$helptxt['ep_icon_directory_help'] = 'The filepath for your icon directory is already relative to your SMF Root directory. Sets the directory from which to obtain all categories for Envision Module Icons. Categories are folders within the directory that you specify in here. After changing this folder path, you should update each of your enabled Modules with the correct icon you want for them by modifying the modules themselves. No need to input a backslash at the front or at the end of the filepath.';
$helptxt['ep_disable_custommod_icons_help'] = 'Sets whether or not to install all icons associated with Envision Modules that you install via Modules - Add Modules section.';
$helptxt['ep_enable_custommod_icons_help'] = 'Sets whether or not to uninstall all icons associated with Envision Modules when you Delete a Module via Modules - Add Modules section.  If unchecked, all module icons that are associated with modules, once deleted, will not be available for other modules as well.';
$helptxt['ep_layout_name'] = 'This is the name of your layout.  Layout Names are unique on a per group basis.  If the name already exists within this group, you will need to come up with a different Layout Name.';
$helptxt['ep_layout_actions'] = 'Sets the actions, non-actions, and urls that this layout will be used for within SMF. Click the Add Action button to add an action to this layout. You can add as many actions as you need.<br /><br /><strong>Available SMF Actions</strong>:<br />Lists all actions within SMF that are available for this layout minus the actions you are currently using in other layouts of this group.  When a new action becomes available it will be populated within this list. This, globally, sets all urls with this action to use this layout.<br /><br /><strong>User-defined</strong>:<br />You type in the action, non-action, or url that you would like this layout associated with.  Non-actions are defined within brackets, such as [topic], and [board], where [topic] points to index.php?topic, and [board] points to index.php?board.  You can define a url from this as well.  For example:  If you type in <strong>[topic]=1.0</strong>, this url will point to <i>index.php?topic=1.0</i> or <i>index.php/topic,1.0.html</i> (if queryless urls are enabled).  For action urls you do not need brackets, just the action name followed the complete url.  For example: If you type in <strong>profile;area=statistics</strong>, than this layout will be used in the profile stats page located at <i>index.php?action=profile;area=statistics</i>.  Furthermore, it will act globally and also load this same layout for the folowing url: <i>index.php?action=profile;area=statistics;u=3</i> unless you have a layout with a url specifically defined as that.<br /><br /><strong>Important</strong>:  User-defined action urls and user-defined non-action urls take priority over the layouts for globally set actions and non-actions.';
$helptxt['ep_layout_curr_actions'] = 'Lists the actions that are currently in use for this layout.  You can remove any action(s) by selecting them and clicking the Remove Action(s) button.';
$helptxt['ep_layout_style'] = 'This is a list of pre-defined Layouts that you can use for Envision Portal.  Select any of these and it will be created for you.';
$helptxt['ep_layout_curr_sections'] = 'Lists the sections that are currently in use for this layout.  You can manage each section by changing its order, colspan, enabled status, and whether SMF uses it. However, you cannot change the SMF position in the default Envision Portal layout.';

/*********************************************
	 	All Envision Portal Modules
*********************************************/
$helptxt['ep_module_template'] = 'Sets the template for this module.';
$helptxt['ep_module_header_display'] = 'Sets how to be displayed header of this module.';
$helptxt['ep_module_groups'] = 'Specify groups that can view this module.';
$helptxt['ep_module_title'] = 'Sets the title that gets displayed for this module.';
$helptxt['ep_module_link'] = 'Sets the link for the title of this module as well as the target attribute for the link.  If empty, title of module will not have a link associated with it.  If you do not define <strong>http://</strong> or <strong>www.</strong> than links will be relative to <strong>index.php?</strong>.<br />For Example: To go to the SMF Help Menu, type in <strong>action=help</strong> or, if you want a link outside of your forum\'s site, For Example, to link to Envision Portal\'s site: <strong>http://</strong>envisionportal.net or <strong>www.</strong>envisionportal.net';
$helptxt['ep_module_icon'] = 'Sets the icon for this module that gets shown to the left of the module title.';
$helptxt['ep_module_announce_msg'] = 'The Announcement to show within the body of this module. BBC and smilies are allowed!<br />If blank, will load up Envision Portal\'s default welcome message.';
$helptxt['ep_module_stats_stat_choices'] = 'Sets which statitics to display and in which order to display them in.';
$helptxt['ep_module_online_show_online'] = 'Sets whether to show the quantity of online Users, Buddies, Guests, Hidden users, and/or spiders.  Order them around in the order in which you\'d like them to appear in the module.  Unchecking all of these options will not show these at all within the module.';
$helptxt['ep_module_online_online_pos'] = 'Sets whether to display the online list above or below the usergroups';
$helptxt['ep_module_online_online_groups'] = 'Sets which online groups to be displayed and in what order you want them displayed in.';
$helptxt['ep_module_news_board'] = 'Select the board you\'d like to display topics from within this module for displaying News.';
$helptxt['ep_module_news_limit'] = 'Limit the amount of recent topics shown for News.';
$helptxt['ep_module_recent_post_topic'] = 'Sets whether recent posts or recent topics are displayed.';
$helptxt['ep_module_recent_num_recent'] = 'Limit the amount of Recent Posts/Topics to be shown.';
$helptxt['ep_module_recent_show_avatars'] = 'If set, shows the members avatar as well, otherwise the avatar is not shown.';
$helptxt['ep_module_calendar_display'] = 'Two output display options for the Calendar Module are possible.<br /><br /><strong><u>Monthly Grid</u></strong> - Outputs all dates within a monthly calendar grid where holidays, birthdays, and events dates are bold.<br /><br /><strong><u>Text-Based</u></strong> - Outputs todays date, and below that displays all holidays, events, and birthdays for the entire month.';
$helptxt['ep_module_calendar_animate'] = 'Provides an animation effect when switching between Next/Previous Calendar months.  If disabled, no animation effect will occur.';
$helptxt['ep_module_calendar_show_options'] = 'Enable/Disable showing of events, holidays, and/or birthdays.  You can also change the order of how these are shown!<br /><br /><strong><u>Note</u>:</strong> A database query gets used for each of these options.';
$helptxt['ep_module_calendar_show_months'] = 'If <strong>in this year</strong> is selected, the months will reflect all months in this year only, otherwise, the amount of previous/next months within the calendar will be dependant on what gets defined in the <i>Previous Months</i> and <i>Next Months</i> settings.';
$helptxt['ep_module_calendar_previous'] = 'Sets how many months to show prior to the current month.  If set to 0, will show no previous months.<br /><br /><strong><u>Note</u>:</strong> To show, only, the current month in this module, set both Previous and Next months to 0.';
$helptxt['ep_module_calendar_next'] = 'Sets how many months to show after the current month.  If set to 0, will show no months after the current month.<br /><br /><strong><u>Note</u>:</strong> To show, only, the current month in this module, set both Previous and Next months to 0.';
$helptxt['ep_module_poll_options'] = 'Show a poll based on the topic id, most rated poll, or the most recently added poll.';
$helptxt['ep_module_poll_topic'] = 'The topic id of where the poll in this module will come from.  Only gets displayed if "Based on Topic Id" was selected as the Poll option.  If set to 0, no content will be displayed.';
$helptxt['ep_module_top_posters_show_avatar'] = 'Enable/Disable showing of avatars.';
$helptxt['ep_module_top_posters_show_postcount'] = 'Enable/Disable showing of post-counts.';
$helptxt['ep_module_top_posters_num_posters'] = 'Sets how many top posters to show.';
$helptxt['ep_module_custom_code_type'] = 'Sets the type of code you are using for this module.  There are 3 options to choose from, PHP, HTML, or BBC.  Make sure to code in that style for the type that you choose.';
$helptxt['ep_module_custom_code'] = 'In this text area, you should type in your code that you want outputted within the module.  Make sure to code for the correct Code Type that you selected.';
$helptxt['ep_module_staff_list_type'] = 'Choose the style display for users.  Either with usernames or without usernames.';
$helptxt['ep_module_staff_name_type'] = 'Sets what to be displayed for the Staff Title of each member within any of the groups you selected.<br /><br /><strong>Group Name</strong>:  The Staff Title of each member will be identified by the group name that the member is in.<br /><strong>Custom Title</strong>:  The Staff Title of each member will be identified by the Custom Title only that is defined within that members profile.  If there is no Custom Title associated with any members, there will be no Staff Title associated with those members.<br /><strong>Custom Title or Group Name</strong>:  The Staff Title of each member will be identified by the Custom Title if it is not empty, otherwise the Staff Title of these members will be identified by the Group Name.';
$helptxt['ep_module_staff_groups'] = 'Check all groups listed in here to show within this module.  You can move the order in which they are displayed within the module by either clicking on the Up or Down links next to each listed group.';
$helptxt['ep_module_sitemenu_onesm'] = 'Sets whether to allow multiple Submenu expansions for each menu item.  If enabled, all menus will be available for expansion and will keep the current expanded menus at their current state of expanded.  If disabled, will collapse all menus that are expanded when expanding a menu.';

?>