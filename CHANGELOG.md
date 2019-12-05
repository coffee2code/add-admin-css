# Changelog

## 1.8 _(2019-12-04)_

### Highlights:

* This minor release adds HTML5 compliance when supported by the theme, modernizes and fixes unit tests, and notes compatibility through WP 5.3+.

### Details:

* New: Add HTML5 compliance by omitting `type` attribute when the theme supports 'html5'
* Unit tests:
    * New: Add unit test to ensure plugin is hooked to initialize on `plugins_loaded`
    * Fix: Don't pass argument to plugin object's `add_css()`
    * Fix: Don't expect `type` attribute in `link` tags since they're not HTML5-compliant
    * Fix: Prevent WP from attempting to print the emoji detection script (which isn't built in the develop.svn repo)
    * Change: Update unit test install script and bootstrap to use latest WP unit test repo
* Change: Note compatibility through WP 5.3+
* Change: Update copyright date (2020)

## 1.7 (2019-04-13)_

### Highlights:

* This release adds a recovery mode to disable output of CSS via the plugin (and an admin notice when it is active), improves documentation, updates the plugin framework, notes compatibility through WP 5.1+, drops compatibility with versions of WP older than 4.7, and more documentation and code improvements.

### Details:

* New: Add recovery mode to be able to disable output of CSS via the plugin
    * Add support for `c2c-no-css` query parameter for enabling recovery mode
    * Add support for `C2C_ADD_ADMIN_CSS_DISABLED` constant for enabling recovery mode
    * Display admin notice when recovery mode is active
    * Add `can_show_css()`, `remove_query_param_from_redirects()`, `recovery_mode_notice()`
* Change: Initialize plugin on `plugins_loaded` action instead of on load
* Change: Update plugin framework to 049
    * 049:
    * Correct last arg in call to `add_settings_field()` to be an array
    * Wrap help text for settings in `label` instead of `p`
    * Only use `label` for help text for checkboxes, otherwise use `p`
    * Ensure a `textarea` displays as a block to prevent orphaning of subsequent help text
    * Note compatibility through WP 5.1+
    * Update copyright date (2019)
    * 048:
    * When resetting options, delete the option rather than setting it with default values
    * Prevent double "Settings reset" admin notice upon settings reset
    * 047:
    * Don't save default setting values to database on install
    * Change "Cheatin', huh?" error messages to "Something went wrong.", consistent with WP core
    * Note compatibility through WP 4.9+
    * Drop compatibility with version of WP older than 4.7
* New: Add README.md file
* New: Add CHANGELOG.md file and move all but most recent changelog entries into it
* New: Add FAQ entry describing ways to fix having potentially crippled the admin
* New: Add inline documentation for hooks
* New: Add GitHub link to readme
* Unit tests:
    * New: Add unit tests for `add_css()`
    * New: Add unit test for defaults for settings
    * Change: Improve tests for settings handling
    * Change: Update `set_option()` to accept an array of setting values to use
    * Change: Explicitly set 'twentyseventeen' as the theme to ensure testing against a known theme
    * Change: Invoke `setup_options()` within each test as needed instead of `setUp()`
* Change: Store setting name in constant
* Change: Cast return value of `c2c_add_admin_css_files` filter as an array
* Change: Improve documentation for hooks within readme.txt
* Change: Note compatibility through WP 5.1+
* Change: Drop compatibility with version of WP older than 4.7
* Change: Rename readme.txt section from 'Advanced' to 'Hooks' and provide a better section intro
* Change: Update installation instruction to prefer built-in installer over .zip file
* Change: Update copyright date (2019)
* Change: Update License URI to be HTTPS

## 1.6 _(2017-11-03)_
* New: Add support for CodeMirror (as packaged with WP 4.9)
    * Adds code highlighting, syntax checking, and other features
* Fix: Show admin notifications for settings page
* Change: Update plugin framework to 046
    * 046:
    * Fix `reset_options()` to reference instance variable `$options`.
	* Note compatibility through WP 4.7+.
	* Update copyright date (2017)
    * 045:
    * Ensure `reset_options()` resets values saved in the database.
    * 044:
    * Add `reset_caches()` to clear caches and memoized data. Use it in `reset_options()` and `verify_config()`.
    * Add `verify_options()` with logic extracted from `verify_config()` for initializing default option attributes.
    * Add `add_option()` to add a new option to the plugin's configuration.
    * Add filter 'sanitized_option_names' to allow modifying the list of whitelisted option names.
    * Change: Refactor `get_option_names()`.
    * 043:
    * Disregard invalid lines supplied as part of hash option value.
    * 042:
    * Update `disable_update_check()` to check for HTTP and HTTPS for plugin update check API URL.
    * Translate "Donate" in footer message.
* Change: Update unit test bootstrap
    * Default `WP_TESTS_DIR` to `/tmp/wordpress-tests-lib` rather than erroring out if not defined via environment variable
    * Enable more error output for unit tests
* Change: Note compatibility through WP 4.9+
* Change: Remove support for WordPress older than 4.6
* Change: Update copyright date (2018)

## 1.5 _(2016-04-21)_
* Change: Declare class as final.
* Change: Update plugin framework to 041:
    * For a setting that is of datatype array, ensure its default value is an array.
    * Make `verify_config()` public.
    * Use `<p class="description">` for input field help text instead of custom styled span.
    * Remove output of markup for adding icon to setting page header.
    * Remove styling for .c2c-input-help.
    * Add braces around the few remaining single line conditionals.
* Change: Note compatibility through WP 4.5+.
* Change: Remove 'Domain Path' from plugin header.
* New: Add LICENSE file.

## 1.4 _(2016-01-10)_

## Highlights:

* This release fixes a couple of bugs, adds support for language packs, and has many minor behind-the-scenes changes.

## Details:

* Bugfix: Allow CSS links/files with query args to be enqueued.
* Bugfix: If specified as part of the link, 'ver' query arg takes precedence as script version.
* Add: More unit tests.
* Add: Amend a couple of the FAQ answers to indicate things are possible via hooks rather than suggesting they aren't possible.
* Change: Update plugin framework to 040:
    * Change class name to `c2c_AddAdminCSS_Plugin_040` to be plugin-specific.
    * Set textdomain using a string instead of a variable.
    * Don't load textdomain from file.
    * Change admin page header from 'h2' to 'h1' tag.
    * Add `c2c_plugin_version()`.
    * Formatting improvements to inline docs.
* Change: Add support for language packs:
    * Set textdomain using a string instead of a variable.
    * Remove .pot file and /lang subdirectory.
* Change: Declare class as final.
* Change: Explicitly declare methods in unit tests as public or protected.
* Change: Minor tweak to description.
* Change: Minor improvements to inline docs and test docs.
* Add: Create empty index.php to prevent files from being listed if web server has enabled directory listings.
* Change: Note compatibility through WP 4.4+.
* Change: Remove support for versions of WordPress older than 4.1.
* Change: Update copyright date (2016).

## 1.3.4 _(2015-04-30)_
* Bugfix: Fix line-wrapping display for Firefox and Safari
* Enhancement: Add an additional unit test
* Update: Move 'Advanced' section lower in readme; add inline docs to example code
* Update: Note compatibility through WP 4.2+

## 1.3.3 _(2015-02-21)_
* Bugfix: Revert back to using `dirname(__FILE__)`; `__DIR__` is only PHP 5.3+

## 1.3.2 _(2015-02-16)_
* Update plugin framework to 039
* Add to and improve unit tests
* Explicitly declare class method `activation()` and `uninstall()` static
* Use `__DIR__` instead of `dirname(__FILE__)`
* Various inline code documentation improvements (spacing, punctuation)
* Note compatibility through WP 4.1+
* Update copyright date (2015)
* Regenerate .pot

## 1.3.1 _(2014-08-23)_
* Update plugin framework to 038
* Minor plugin header reformatting
* Minor code reformatting (spacing, bracing)
* Change documentation links to wp.org to be https
* Localize an additional string
* Note compatibility through WP 4.0+
* Regenerate .pot
* Add plugin icon

## 1.3 _(2014-01-03)_
* Add unit tests
* Update plugin framework to 036
* Improve URL path construction
* Use explicit path for `require_once()`
* Add reset() to reset object to its initial state
* Remove `__clone()` and `__wake()` since they are part of framework
* For `options_page_description()`, match method signature of parent class
* Note compatibility through WP 3.8+
* Drop compatibility with versions of WP older than 3.5
* Update copyright date (2014)
* Change donate link
* Minor readme.txt tweaks (mostly spacing)
* Add banner
* Update screenshot

## 1.2
* Move 'Advanced Tips' section from bottom of settings page into contextual help section
* Add `help_tabs_content()` and `contextual_help()`
* Prevent textareas from wrapping lines
* Display fonts properly in textareas
* Change input fields to be displayed as inline_textarea instead of textarea
* Add `instance()` static method for returning/creating singleton instance
* Made static variable 'instance' private
* Add dummy `__clone()` and `__wakeup()`
* Remove support for previously deprecated `c2c_add_admin_css` global
* Remove `c2c_AddAdminCSS()`; only PHP5 constructor is supported now
* Update plugin framework to 035
* Discontinue use of explicit pass-by-reference for objects
* Add check to prevent execution of code if file is directly accessed
* Regenerate .pot
* Re-license as GPLv2 or later (from X11)
* Add 'License' and 'License URI' header tags to readme.txt and plugin file
* Minor documentation improvements
* Note compatibility through WP 3.5+
* Drop compatibility versions of WP older than 3.1
* Update copyright date (2013)
* Minor code reformatting (spacing)
* Remove ending PHP close tag
* Create repo's WP.org assets directory
* Move screenshot into repo's assets directory

## 1.1
* Rename class from `AddAdminCSS` to `c2c_AddAdminCSS`
* Rename filter from `add_admin_css` to `c2c_add_admin_css`
* Rename filter from `add_admin_css_files` to `c2c_add_admin_css_files`
* Update plugin framework to 029
* Save a static version of itself in class variable `$instance`
* Deprecate use of global variable `$c2c_add_admin_css` to store instance
* Explicitly declare all functions as public
* Add `__construct()`, `activation()`, and `uninstall()`
* Note compatibility through WP 3.3+
* Drop compatibility with versions of WP older than 3.0
* Add .pot
* Add 'Domain Path' plugin header
* Minor code formatting changes (spacing)
* Update copyright date (2011)
* Add plugin homepage and author links in description in readme.txt

## 1.0
* Initial release (not publicly released)
