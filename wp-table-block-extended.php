<?php
/**
 * WP Table Block Extended
 *
 * @package           WTBE
 * @author            Firas Codes
 * @copyright         2024 Firas Codes
 * @license           GPL-2.0-or-later
 *
 * @wordpress-plugin
 * Plugin Name:       WP Table Block Extended
 * Plugin URI:        https://github.com/firasdeveloper/wp-table-block-extended
 * Description:       Extends the WordPress core table block with custom styling options including header colors, sticky columns, cell widths, and CTA buttons.
 * Version:           1.0.0
 * Requires at least: 6.0
 * Requires PHP:      8.1
 * Author:            Firas Codes
 * Author URI:        https://www.firascodes.ca
 * Text Domain:       wp-table-block-extended
 * License:           GPL v2 or later
 * License URI:       https://www.gnu.org/licenses/gpl-2.0.html
 * Update URI:        https://github.com/firasdeveloper/wp-table-block-extended
 */

declare(strict_types=1);

namespace WTBE;

// Prevent direct access.
defined('ABSPATH') || exit;

// Plugin constants.
const VERSION     = '1.0.0';
const MIN_PHP     = '8.1';
const MIN_WP      = '6.0';
const PLUGIN_FILE = __FILE__;

// Define path constants.
define('WTBE\PLUGIN_DIR', plugin_dir_path(__FILE__));
define('WTBE\PLUGIN_URL', plugin_dir_url(__FILE__));

/**
 * Check PHP version and display notice if incompatible.
 *
 * @return bool True if PHP version is compatible.
 */
function check_php_version(): bool {
	if (version_compare(PHP_VERSION, MIN_PHP, '<')) {
		add_action('admin_notices', __NAMESPACE__ . '\\php_version_notice');
		return false;
	}
	return true;
}

/**
 * Display PHP version incompatibility notice.
 *
 * @return void
 */
function php_version_notice(): void {
	$message = sprintf(
		/* translators: 1: Plugin name, 2: Required PHP version, 3: Current PHP version */
		esc_html__('%1$s requires PHP %2$s or higher. You are running PHP %3$s.', 'wp-table-block-extended'),
		'<strong>WP Table Block Extended</strong>',
		MIN_PHP,
		PHP_VERSION
	);

	printf('<div class="notice notice-error"><p>%s</p></div>', wp_kses_post($message));
}

/**
 * Initialize the plugin.
 *
 * Handles autoloading with two approaches:
 *
 * 1. COMPOSER (Optional) - If you run `composer install`, the vendor/autoload.php
 *    will be used. This also enables dev tools like PHPCS and PHPStan for:
 *    - Static analysis: Catches bugs without running code (type errors, null issues)
 *    - Code style: Enforces WordPress Coding Standards automatically
 *
 * 2. MANUAL AUTOLOADER (Default) - If Composer is not installed, the plugin uses
 *    its own PSR-4 autoloader below. The plugin works perfectly without Composer.
 *
 * WHY COMPOSER IS OPTIONAL:
 * - WordPress core doesn't use Composer
 * - This plugin has no external PHP dependencies
 * - The manual autoloader provides the same PSR-4 functionality
 * - Composer is only needed if you want dev tools (linting, static analysis)
 *
 * FOR DEVELOPERS:
 * - Run `composer install` to enable PHPCS and PHPStan
 * - Run `composer phpcs` to check code style
 * - Run `composer phpstan` to run static analysis
 *
 * FOR USERS:
 * - Just upload the plugin and activate - no Composer needed!
 *
 * @return void
 */
function init(): void {
	if (! check_php_version()) {
		return;
	}

	/*
	 * Autoloader priority:
	 * 1. Use Composer's autoloader if available (for developers who ran `composer install`)
	 * 2. Fall back to manual PSR-4 autoloader (works out of the box for all users)
	 */
	$composer_autoloader = PLUGIN_DIR . 'vendor/autoload.php';

	if (file_exists($composer_autoloader)) {
		require_once $composer_autoloader;
	} else {
		spl_autoload_register(__NAMESPACE__ . '\\autoloader');
	}

	// Boot the plugin.
	Plugin::instance()->init();
}

/**
 * PSR-4 compliant autoloader.
 *
 * This manual autoloader follows the PSR-4 standard, which maps namespaces to directories:
 * - Namespace: WTBE\Shortcodes\CTA
 * - File path: src/Shortcodes/CTA.php
 *
 * This allows the plugin to work without Composer while still using modern
 * PHP practices like namespaces and autoloading.
 *
 * WHY PSR-4?
 * - Industry standard for PHP autoloading
 * - No need to manually require/include files
 * - Classes are loaded only when needed (better performance)
 * - Matches Composer's autoloading behavior
 *
 * @param string $class The fully-qualified class name (e.g., 'WTBE\Shortcodes\CTA').
 *
 * @return void
 */
function autoloader(string $class): void {
	// Only handle classes in our namespace (WTBE\).
	$prefix   = __NAMESPACE__ . '\\';
	$base_dir = PLUGIN_DIR . 'src/';

	$len = strlen($prefix);

	// Check if the class uses our namespace prefix.
	if (strncmp($prefix, $class, $len) !== 0) {
		return; // Not our class, let other autoloaders handle it.
	}

	// Convert namespace to file path: WTBE\Shortcodes\CTA -> src/Shortcodes/CTA.php
	$relative_class = substr($class, $len);
	$file           = $base_dir . str_replace('\\', '/', $relative_class) . '.php';

	// Require the file if it exists.
	if (file_exists($file)) {
		require_once $file;
	}
}

/**
 * Run on plugin activation.
 *
 * @return void
 */
function activate(): void {
	if (! check_php_version()) {
		deactivate_plugins(plugin_basename(PLUGIN_FILE));
		wp_die(
			esc_html(
				sprintf(
					/* translators: %s: Required PHP version */
					__('WP Table Block Extended requires PHP %s or higher.', 'wp-table-block-extended'),
					MIN_PHP
				)
			),
			'Plugin Activation Error',
			['back_link' => true]
		);
	}

	flush_rewrite_rules();
}

/**
 * Run on plugin deactivation.
 *
 * @return void
 */
function deactivate(): void {
	flush_rewrite_rules();
}

// Register activation/deactivation hooks.
register_activation_hook(__FILE__, __NAMESPACE__ . '\\activate');
register_deactivation_hook(__FILE__, __NAMESPACE__ . '\\deactivate');

// Initialize the plugin on plugins_loaded.
add_action('plugins_loaded', __NAMESPACE__ . '\\init');
