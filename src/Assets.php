<?php
/**
 * Assets Handler
 *
 * @package WTBE
 * @since   1.0.0
 */

declare(strict_types=1);

namespace WTBE;

/**
 * Handles enqueueing of scripts and styles.
 *
 * Manages both editor (Gutenberg) and frontend assets,
 * including proper dependency management and versioning.
 *
 * @since 1.0.0
 */
final class Assets {

	/**
	 * Script handle for editor scripts.
	 *
	 * @var string
	 */
	public const EDITOR_SCRIPT_HANDLE = 'wtbe-editor';

	/**
	 * Style handle for editor styles.
	 *
	 * @var string
	 */
	public const EDITOR_STYLE_HANDLE = 'wtbe-editor-style';

	/**
	 * Style handle for frontend styles.
	 *
	 * @var string
	 */
	public const FRONTEND_STYLE_HANDLE = 'wtbe-frontend-style';

	/**
	 * Cached asset file data.
	 *
	 * @var array{dependencies: string[], version: string}|null
	 */
	private ?array $asset_data = null;

	/**
	 * Register hooks for asset enqueueing.
	 *
	 * @since 1.0.0
	 *
	 * @return void
	 */
	public function register(): void {
		add_action('enqueue_block_editor_assets', [$this, 'enqueue_editor_assets']);
		add_action('wp_enqueue_scripts', [$this, 'enqueue_frontend_assets']);
	}

	/**
	 * Enqueue block editor assets.
	 *
	 * Loads JavaScript and CSS required for the Gutenberg editor
	 * customizations of the table block.
	 *
	 * @since 1.0.0
	 *
	 * @return void
	 */
	public function enqueue_editor_assets(): void {
		$asset_data = $this->get_asset_data();

		// Enqueue editor script.
		wp_enqueue_script(
			self::EDITOR_SCRIPT_HANDLE,
			$this->get_asset_url('index.js'),
			$asset_data['dependencies'],
			$asset_data['version'],
			true
		);

		// Set script translations.
		wp_set_script_translations(
			self::EDITOR_SCRIPT_HANDLE,
			'wp-table-block-extended',
			PLUGIN_DIR . 'languages'
		);

		// Enqueue editor styles.
		wp_enqueue_style(
			self::EDITOR_STYLE_HANDLE,
			$this->get_asset_url('index.css'),
			[],
			$asset_data['version']
		);

		/**
		 * Fires after editor assets have been enqueued.
		 *
		 * @since 1.0.0
		 *
		 * @param Assets $assets The assets instance.
		 */
		do_action('wtbe_editor_assets_enqueued', $this);
	}

	/**
	 * Enqueue frontend assets.
	 *
	 * Loads CSS required for table styling on the frontend.
	 *
	 * @since 1.0.0
	 *
	 * @return void
	 */
	public function enqueue_frontend_assets(): void {
		wp_enqueue_style(
			self::FRONTEND_STYLE_HANDLE,
			$this->get_asset_url('style-index.css'),
			[],
			$this->get_version()
		);

		/**
		 * Fires after frontend assets have been enqueued.
		 *
		 * @since 1.0.0
		 *
		 * @param Assets $assets The assets instance.
		 */
		do_action('wtbe_frontend_assets_enqueued', $this);
	}

	/**
	 * Get asset data from the generated asset file.
	 *
	 * Falls back to default dependencies and version if the
	 * asset file doesn't exist (development mode).
	 *
	 * @since 1.0.0
	 *
	 * @return array{dependencies: string[], version: string} Asset data.
	 */
	private function get_asset_data(): array {
		if ($this->asset_data !== null) {
			return $this->asset_data;
		}

		$asset_file = PLUGIN_DIR . 'build/index.asset.php';

		if (file_exists($asset_file)) {
			$this->asset_data = require $asset_file;
		} else {
			$this->asset_data = $this->get_default_asset_data();
		}

		return $this->asset_data;
	}

	/**
	 * Get default asset data for development mode.
	 *
	 * @since 1.0.0
	 *
	 * @return array{dependencies: string[], version: string} Default asset data.
	 */
	private function get_default_asset_data(): array {
		return [
			'dependencies' => [
				'wp-blocks',
				'wp-i18n',
				'wp-element',
				'wp-block-editor',
				'wp-components',
				'wp-hooks',
			],
			'version'      => VERSION,
		];
	}

	/**
	 * Get the full URL for a build asset.
	 *
	 * @since 1.0.0
	 *
	 * @param string $filename The asset filename.
	 *
	 * @return string Full URL to the asset.
	 */
	private function get_asset_url(string $filename): string {
		return PLUGIN_URL . 'build/' . $filename;
	}

	/**
	 * Get the current asset version.
	 *
	 * @since 1.0.0
	 *
	 * @return string Asset version string.
	 */
	private function get_version(): string {
		return $this->get_asset_data()['version'];
	}
}
