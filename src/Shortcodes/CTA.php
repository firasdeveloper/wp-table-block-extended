<?php
/**
 * CTA (Call-to-Action) Shortcode
 *
 * @package WTBE\Shortcodes
 * @since   1.0.0
 */

declare(strict_types=1);

namespace WTBE\Shortcodes;

/**
 * CTA button shortcode for table cells.
 *
 * Creates a styled call-to-action button/link that can be used
 * inside table cells for actions like "Learn More" or "Sign Up".
 *
 * Usage: [wtbe_cta url="https://example.com" label="Learn More" nofollow="true"]
 *
 * @since 1.0.0
 */
final class CTA extends AbstractShortcode {

	/**
	 * Default CSS class for the CTA button.
	 *
	 * @var string
	 */
	private const DEFAULT_CLASS = 'wtbe-cta';

	/**
	 * Get the shortcode tag.
	 *
	 * @since 1.0.0
	 *
	 * @return string Shortcode tag name.
	 */
	public function get_tag(): string {
		return 'wtbe_cta';
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
			'url'       => '#',
			'label'     => __('Click Here', 'wp-table-block-extended'),
			'newtab'    => 'true',
			'nofollow'  => 'false',
			'sponsored' => 'false',
			'class'     => '',
			'id'        => '',
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
		$url       = esc_url((string) $atts['url']);
		$label     = (string) $atts['label'];
		$newtab    = $this->to_bool($atts['newtab']);
		$nofollow  = $this->to_bool($atts['nofollow']);
		$sponsored = $this->to_bool($atts['sponsored']);
		$class     = trim((string) $atts['class']);
		$id        = trim((string) $atts['id']);

		// Build HTML attributes.
		$attributes = $this->build_link_attributes(
			url: $url,
			newtab: $newtab,
			nofollow: $nofollow,
			sponsored: $sponsored,
			class: $class,
			id: $id
		);

		return sprintf(
			'<a %s>%s</a>',
			$this->build_attributes($attributes),
			esc_html($label)
		);
	}

	/**
	 * Build the link attributes array.
	 *
	 * @since 1.0.0
	 *
	 * @param string $url       Link URL.
	 * @param bool   $newtab    Whether to open in new tab.
	 * @param bool   $nofollow  Whether to add nofollow.
	 * @param bool   $sponsored Whether to add sponsored.
	 * @param string $class     Additional CSS classes.
	 * @param string $id        Element ID.
	 *
	 * @return array<string, string|null> Link attributes.
	 */
	private function build_link_attributes(
		string $url,
		bool $newtab,
		bool $nofollow,
		bool $sponsored,
		string $class,
		string $id
	): array {
		$attributes = [
			'href'   => $url,
			'class'  => $this->build_class_string($class),
			'id'     => $id !== '' ? $id : null,
			'target' => $newtab ? '_blank' : null,
			'rel'    => $this->build_rel_string($newtab, $nofollow, $sponsored),
		];

		return $attributes;
	}

	/**
	 * Build the CSS class string.
	 *
	 * @since 1.0.0
	 *
	 * @param string $additional_class Additional classes to include.
	 *
	 * @return string Combined class string.
	 */
	private function build_class_string(string $additional_class): string {
		$classes = [self::DEFAULT_CLASS];

		if ($additional_class !== '') {
			$classes[] = $additional_class;
		}

		/**
		 * Filter the CTA button CSS classes.
		 *
		 * @since 1.0.0
		 *
		 * @param string[] $classes Array of CSS classes.
		 */
		$classes = apply_filters('wtbe_cta_classes', $classes);

		return implode(' ', array_map('sanitize_html_class', $classes));
	}

	/**
	 * Build the rel attribute string.
	 *
	 * @since 1.0.0
	 *
	 * @param bool $newtab    Whether link opens in new tab.
	 * @param bool $nofollow  Whether to add nofollow.
	 * @param bool $sponsored Whether to add sponsored.
	 *
	 * @return string|null Rel attribute value or null if empty.
	 */
	private function build_rel_string(bool $newtab, bool $nofollow, bool $sponsored): ?string {
		$rel_parts = [];

		if ($nofollow) {
			$rel_parts[] = 'nofollow';
		}

		if ($sponsored) {
			$rel_parts[] = 'sponsored';
		}

		if ($newtab) {
			$rel_parts[] = 'noopener';
			$rel_parts[] = 'noreferrer';
		}

		/**
		 * Filter the CTA button rel attribute parts.
		 *
		 * @since 1.0.0
		 *
		 * @param string[] $rel_parts Array of rel attribute values.
		 * @param bool     $newtab    Whether link opens in new tab.
		 * @param bool     $nofollow  Whether nofollow is enabled.
		 * @param bool     $sponsored Whether sponsored is enabled.
		 */
		$rel_parts = apply_filters('wtbe_cta_rel', $rel_parts, $newtab, $nofollow, $sponsored);

		return $rel_parts !== [] ? implode(' ', $rel_parts) : null;
	}
}
