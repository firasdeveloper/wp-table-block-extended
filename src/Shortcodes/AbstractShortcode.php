<?php
/**
 * Abstract Shortcode Base Class
 *
 * @package WTBE\Shortcodes
 * @since   1.0.0
 */

declare(strict_types=1);

namespace WTBE\Shortcodes;

/**
 * Abstract base class for shortcodes.
 *
 * Provides a consistent interface and shared functionality
 * for all shortcode implementations.
 *
 * @since 1.0.0
 */
abstract class AbstractShortcode {

	/**
	 * Get the shortcode tag.
	 *
	 * @since 1.0.0
	 *
	 * @return string Shortcode tag name.
	 */
	abstract public function get_tag(): string;

	/**
	 * Get default attribute values.
	 *
	 * @since 1.0.0
	 *
	 * @return array<string, mixed> Default attributes.
	 */
	abstract protected function get_defaults(): array;

	/**
	 * Render the shortcode output.
	 *
	 * @since 1.0.0
	 *
	 * @param array<string, mixed> $atts    Parsed shortcode attributes.
	 * @param string               $content Shortcode content (if any).
	 *
	 * @return string Rendered HTML output.
	 */
	abstract protected function render(array $atts, string $content): string;

	/**
	 * Register the shortcode with WordPress.
	 *
	 * @since 1.0.0
	 *
	 * @return void
	 */
	public function register(): void {
		add_shortcode($this->get_tag(), [$this, 'handle']);
	}

	/**
	 * Handle the shortcode callback.
	 *
	 * Parses attributes, applies defaults, and calls the render method.
	 *
	 * @since 1.0.0
	 *
	 * @param array<string, string>|string $atts    Raw shortcode attributes.
	 * @param string|null                  $content Shortcode content.
	 *
	 * @return string Rendered shortcode output.
	 */
	public function handle(array|string $atts, ?string $content = null): string {
		// Ensure $atts is an array.
		$atts = is_array($atts) ? $atts : [];

		// Parse attributes with defaults.
		$parsed_atts = shortcode_atts(
			$this->get_defaults(),
			$atts,
			$this->get_tag()
		);

		// Ensure content is a string.
		$content = $content ?? '';

		/**
		 * Filter shortcode attributes before rendering.
		 *
		 * @since 1.0.0
		 *
		 * @param array<string, mixed> $parsed_atts Parsed attributes.
		 * @param string               $tag         Shortcode tag.
		 * @param string               $content     Shortcode content.
		 */
		$parsed_atts = apply_filters(
			'wtbe_shortcode_atts',
			$parsed_atts,
			$this->get_tag(),
			$content
		);

		return $this->render($parsed_atts, $content);
	}

	/**
	 * Convert string boolean values to actual booleans.
	 *
	 * @since 1.0.0
	 *
	 * @param mixed $value The value to convert.
	 *
	 * @return bool The boolean value.
	 */
	protected function to_bool(mixed $value): bool {
		return filter_var($value, FILTER_VALIDATE_BOOLEAN);
	}

	/**
	 * Sanitize a CSS value.
	 *
	 * Validates that a value is a valid CSS dimension (px, em, rem, %).
	 *
	 * @since 1.0.0
	 *
	 * @param string $value    The CSS value to sanitize.
	 * @param string $fallback Fallback value if invalid.
	 *
	 * @return string Sanitized CSS value.
	 */
	protected function sanitize_css_value(string $value, string $fallback = '0'): string {
		// If numeric, assume pixels.
		if (is_numeric($value)) {
			return $value . 'px';
		}

		// Validate CSS dimension pattern.
		if (preg_match('/^(\d+(?:\.\d+)?)(px|em|rem|%)$/', $value)) {
			return $value;
		}

		return $fallback;
	}

	/**
	 * Build HTML attributes string from an array.
	 *
	 * @since 1.0.0
	 *
	 * @param array<string, string|bool|null> $attributes Attributes to build.
	 *
	 * @return string HTML attributes string.
	 */
	protected function build_attributes(array $attributes): string {
		$html_parts = [];

		foreach ($attributes as $name => $value) {
			if ($value === null || $value === false) {
				continue;
			}

			if ($value === true) {
				$html_parts[] = esc_attr($name);
				continue;
			}

			$html_parts[] = sprintf('%s="%s"', esc_attr($name), esc_attr($value));
		}

		return implode(' ', $html_parts);
	}
}
