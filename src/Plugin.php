<?php
/**
 * Main Plugin Class
 *
 * @package WTBE
 * @since   1.0.0
 */

declare(strict_types=1);

namespace WTBE;

use WTBE\Shortcodes\CTA;
use WTBE\Shortcodes\Placeholder;

/**
 * Plugin bootstrap class.
 *
 * Implements the Singleton pattern to ensure only one instance
 * of the plugin is loaded at any time.
 *
 * @since 1.0.0
 */
final class Plugin {

	/**
	 * Singleton instance.
	 *
	 * @var self|null
	 */
	private static ?self $instance = null;

	/**
	 * Assets handler instance.
	 *
	 * @var Assets|null
	 */
	private ?Assets $assets = null;

	/**
	 * Array of registered shortcodes.
	 *
	 * @var array<string, object>
	 */
	private array $shortcodes = [];

	/**
	 * Get the singleton instance.
	 *
	 * @since 1.0.0
	 *
	 * @return self Plugin instance.
	 */
	public static function instance(): self {
		if (self::$instance === null) {
			self::$instance = new self();
		}

		return self::$instance;
	}

	/**
	 * Private constructor to prevent direct instantiation.
	 *
	 * @since 1.0.0
	 */
	private function __construct() {
		// Singleton pattern - constructor is private.
	}

	/**
	 * Prevent cloning of the instance.
	 *
	 * @since 1.0.0
	 *
	 * @return void
	 */
	private function __clone(): void {
		// Prevent cloning.
	}

	/**
	 * Prevent unserializing of the instance.
	 *
	 * @since 1.0.0
	 *
	 * @throws \Exception Always throws to prevent unserializing.
	 *
	 * @return void
	 */
	public function __wakeup(): void {
		throw new \Exception('Cannot unserialize singleton.');
	}

	/**
	 * Initialize the plugin.
	 *
	 * Registers all hooks, shortcodes, and initializes components.
	 *
	 * @since 1.0.0
	 *
	 * @return void
	 */
	public function init(): void {
		$this->load_textdomain();
		$this->init_assets();
		$this->register_shortcodes();

		/**
		 * Fires after the plugin has been fully initialized.
		 *
		 * @since 1.0.0
		 *
		 * @param Plugin $plugin The plugin instance.
		 */
		do_action('wtbe_loaded', $this);
	}

	/**
	 * Load plugin text domain for translations.
	 *
	 * @since 1.0.0
	 *
	 * @return void
	 */
	private function load_textdomain(): void {
		load_plugin_textdomain(
			'wp-table-block-extended',
			false,
			dirname(plugin_basename(PLUGIN_FILE)) . '/languages'
		);
	}

	/**
	 * Initialize assets handler.
	 *
	 * @since 1.0.0
	 *
	 * @return void
	 */
	private function init_assets(): void {
		$this->assets = new Assets();
		$this->assets->register();
	}

	/**
	 * Register all shortcodes.
	 *
	 * @since 1.0.0
	 *
	 * @return void
	 */
	private function register_shortcodes(): void {
		$shortcode_classes = [
			'wtbe_placeholder' => Placeholder::class,
			'wtbe_cta'         => CTA::class,
		];

		/**
		 * Filter the registered shortcodes.
		 *
		 * @since 1.0.0
		 *
		 * @param array<string, class-string> $shortcode_classes Shortcode tag => class name pairs.
		 */
		$shortcode_classes = apply_filters('wtbe_shortcodes', $shortcode_classes);

		foreach ($shortcode_classes as $tag => $class) {
			if (class_exists($class)) {
				$this->shortcodes[$tag] = new $class();
				$this->shortcodes[$tag]->register();
			}
		}
	}

	/**
	 * Get the assets handler.
	 *
	 * @since 1.0.0
	 *
	 * @return Assets|null Assets handler instance.
	 */
	public function assets(): ?Assets {
		return $this->assets;
	}

	/**
	 * Get the plugin version.
	 *
	 * @since 1.0.0
	 *
	 * @return string Plugin version.
	 */
	public function version(): string {
		return VERSION;
	}

	/**
	 * Get the plugin directory path.
	 *
	 * @since 1.0.0
	 *
	 * @return string Plugin directory path with trailing slash.
	 */
	public function dir(): string {
		return PLUGIN_DIR;
	}

	/**
	 * Get the plugin URL.
	 *
	 * @since 1.0.0
	 *
	 * @return string Plugin URL with trailing slash.
	 */
	public function url(): string {
		return PLUGIN_URL;
	}
}
