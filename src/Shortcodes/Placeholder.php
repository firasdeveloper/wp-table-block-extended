<?php
/**
 * Placeholder Shortcode
 *
 * @package WTBE\Shortcodes
 * @since   1.0.0
 */

declare(strict_types=1);

namespace WTBE\Shortcodes;

/**
 * Placeholder shortcode for controlling table cell width.
 *
 * Creates an invisible div that forces a minimum width inside
 * a table cell, useful for controlling column widths in responsive tables.
 *
 * Usage: [wtbe_placeholder width="300px"]
 *
 * @since 1.0.0
 */
final class Placeholder extends AbstractShortcode {

	/**
	 * CSS custom property name for cell padding.
	 *
	 * @var string
	 */
	private const PADDING_VAR = '--wtbe-cell-padding-x';

	/**
	 * Default padding fallback value.
	 *
	 * @var string
	 */
	private const PADDING_FALLBACK = '24px';

	/**
	 * Get the shortcode tag.
	 *
	 * @since 1.0.0
	 *
	 * @return string Shortcode tag name.
	 */
	public function get_tag(): string {
		return 'wtbe_placeholder';
	}

	/**
	 * Get default attribute values.
	 *
	 * @since 1.0.0
	 *
	 * @return array<string, string> Default attributes.
	 */
	protected function get_defaults(): array {
		return [
			'width' => '',
		];
	}

	/**
	 * Render the shortcode output.
	 *
	 * @since 1.0.0
	 *
	 * @param array<string, mixed> $atts    Parsed shortcode attributes.
	 * @param string               $content Shortcode content (unused).
	 *
	 * @return string Rendered HTML output.
	 */
	protected function render(array $atts, string $content): string {
		$width = trim((string) $atts['width']);

		// Validate width is provided.
		if ($width === '') {
			return $this->render_error(
				__('Error: width attribute is required for [wtbe_placeholder]', 'wp-table-block-extended')
			);
		}

		// Sanitize and validate the width value.
		$sanitized_width = $this->sanitize_width($width);

		if ($sanitized_width === null) {
			return $this->render_error(
				__('Error: invalid width value. Use integer with px, rem, or em (e.g., 100px or 5rem)', 'wp-table-block-extended')
			);
		}

		// Calculate width accounting for cell padding.
		$calculated_width = $this->calculate_width($sanitized_width);

		return sprintf(
			'<div style="width:%s;height:0;" aria-hidden="true"></div>',
			esc_attr($calculated_width)
		);
	}

	/**
	 * Sanitize the width attribute value.
	 *
	 * @since 1.0.0
	 *
	 * @param string $width Raw width value.
	 *
	 * @return string|null Sanitized width or null if invalid.
	 */
	private function sanitize_width(string $width): ?string {
		// If numeric, assume pixels.
		if (is_numeric($width)) {
			return $width . 'px';
		}

		// Validate CSS dimension pattern (px, em, rem only).
		if (preg_match('/^(\d+(?:\.\d+)?)(px|em|rem)$/', $width)) {
			return $width;
		}

		return null;
	}

	/**
	 * Calculate the final width accounting for cell padding.
	 *
	 * Uses CSS calc() to subtract the cell padding from the specified width.
	 *
	 * @since 1.0.0
	 *
	 * @param string $width Sanitized width value.
	 *
	 * @return string CSS calc() expression.
	 */
	private function calculate_width(string $width): string {
		return sprintf(
			'calc(%s - (2 * var(%s, %s)))',
			$width,
			self::PADDING_VAR,
			self::PADDING_FALLBACK
		);
	}

	/**
	 * Render an error message.
	 *
	 * @since 1.0.0
	 *
	 * @param string $message Error message to display.
	 *
	 * @return string Rendered error HTML.
	 */
	private function render_error(string $message): string {
		return sprintf(
			'<div style="background-color:#ffb6b6;font-style:italic;padding:4px;border-radius:2px;">%s</div>',
			esc_html($message)
		);
	}
}
