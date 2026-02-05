/**
 * WP Table Block Extended - Editor Customizations
 *
 * This script extends the core WordPress table block with additional
 * styling options via the block editor's Inspector Controls.
 */

import { __ } from '@wordpress/i18n';
import { InspectorControls } from '@wordpress/block-editor';
import { PanelBody, SelectControl, ToggleControl } from '@wordpress/components';
import { addFilter } from '@wordpress/hooks';

import './editor.scss';

/**
 * Add custom attributes to the core/table block
 *
 * @param {Object} settings - Block settings object
 * @returns {Object} Modified settings
 */
function addTableBlockAttributes(settings) {
	if (settings.name !== 'core/table') {
		return settings;
	}

	// Add custom attribute for header background color
	settings.attributes = {
		...settings.attributes,
		headerBackgroundColor: {
			type: 'string',
			default: 'primary',
		},
	};

	// Remove the default stripes style as we apply it by default
	if (settings.styles) {
		settings.styles = settings.styles.filter(
			(style) => style.name !== 'stripes'
		);
	}

	return settings;
}

// Register the attribute filter
addFilter(
	'blocks.registerBlockType',
	'wtbe/add-table-attributes',
	addTableBlockAttributes
);

/**
 * Custom Table Block Edit component
 * Adds Inspector Controls for table styling options
 */
function TableBlockEdit({ BlockEdit, ...props }) {
	if (props.name !== 'core/table') {
		return <BlockEdit {...props} />;
	}

	const { attributes, setAttributes } = props;

	/**
	 * Toggle a CSS class on/off
	 *
	 * @param {boolean} checked - Whether the toggle is on
	 * @param {string} classToToggle - The class name to toggle
	 */
	function handleClassToggle(checked, classToToggle) {
		let newClassName = attributes?.className?.trim() || '';
		newClassName = newClassName.replace(
			new RegExp(`\\s?${classToToggle}`),
			''
		);

		if (checked) {
			newClassName = `${newClassName} ${classToToggle}`.trim();
		}

		setAttributes({ className: newClassName });
	}

	// Parse current class names to determine toggle states
	let headerBackgroundColor = '';
	let noBorders = false;
	let cellMinWidth = false;
	let freezeFirstColumn = false;
	let centerHeaderText = false;

	// Disable cell minimum width when fixed layout is enabled (they conflict)
	if (attributes.hasFixedLayout) {
		handleClassToggle(false, 'wtbe-cell-min-width');
	}

	if (attributes?.className) {
		const className = attributes.className;

		// Extract header color from class name
		const colorMatch = className.match(/wtbe-header-bg-([^\s]*)/);
		headerBackgroundColor = colorMatch ? colorMatch[1] : '';

		// Check for other toggle classes
		noBorders = className.includes('wtbe-no-borders');
		cellMinWidth = className.includes('wtbe-cell-min-width');
		freezeFirstColumn = className.includes('wtbe-freeze-first-col');
		centerHeaderText = className.includes('wtbe-header-text-center');
	}

	/**
	 * Handle header color selection change
	 *
	 * @param {string} value - Selected color value
	 */
	function handleHeaderColorChange(value) {
		let newClassName = attributes?.className?.trim() || '';
		// Remove existing header color class
		newClassName = newClassName.replace(/\s?wtbe-header-bg-[^\s]*/, '');

		if (value.length > 0) {
			newClassName = `${newClassName} wtbe-header-bg-${value}`.trim();
		}

		setAttributes({ className: newClassName });
	}

	/**
	 * Handle cell min width toggle - disables fixed layout when enabled
	 */
	function handleCellMinWidthToggle(checked) {
		handleClassToggle(checked, 'wtbe-cell-min-width');
		if (checked) {
			setAttributes({ hasFixedLayout: false });
		}
	}

	return (
		<>
			<BlockEdit {...props} />
			<InspectorControls>
				<PanelBody
					title={__('Table Style Options', 'wp-table-block-extended')}
					initialOpen={true}
				>
					<SelectControl
						label={__(
							'Header Background Color',
							'wp-table-block-extended'
						)}
						value={headerBackgroundColor}
						options={[
							{ label: 'Primary (Blue)', value: '' },
							{ label: 'Dark (Black)', value: 'dark' },
							{ label: 'Light (Gray)', value: 'light' },
							{ label: 'Success (Green)', value: 'success' },
							{ label: 'Warning (Orange)', value: 'warning' },
						]}
						onChange={handleHeaderColorChange}
						help={__(
							'Choose the background color for table header cells',
							'wp-table-block-extended'
						)}
					/>
					<ToggleControl
						label={__(
							'Remove Table Borders',
							'wp-table-block-extended'
						)}
						checked={noBorders}
						onChange={(checked) =>
							handleClassToggle(checked, 'wtbe-no-borders')
						}
						help={__(
							'Remove the outer border from the table',
							'wp-table-block-extended'
						)}
					/>
					<ToggleControl
						label={__(
							'Minimum Cell Width (150px)',
							'wp-table-block-extended'
						)}
						checked={cellMinWidth}
						onChange={handleCellMinWidthToggle}
						help={__(
							'Set a minimum width for all cells. Disabled when Fixed Width is on.',
							'wp-table-block-extended'
						)}
					/>
					<ToggleControl
						label={__(
							'Freeze First Column',
							'wp-table-block-extended'
						)}
						checked={freezeFirstColumn}
						onChange={(checked) =>
							handleClassToggle(checked, 'wtbe-freeze-first-col')
						}
						help={__(
							'Keep the first column visible when scrolling horizontally',
							'wp-table-block-extended'
						)}
					/>
					<ToggleControl
						label={__(
							'Center Header Text',
							'wp-table-block-extended'
						)}
						checked={centerHeaderText}
						onChange={(checked) =>
							handleClassToggle(checked, 'wtbe-header-text-center')
						}
						help={__(
							'Center align text in header cells',
							'wp-table-block-extended'
						)}
					/>
				</PanelBody>
			</InspectorControls>
		</>
	);
}

/**
 * Higher-order component to wrap the table block edit
 */
function addCustomControlsToTableBlock(BlockEdit) {
	return (props) => <TableBlockEdit BlockEdit={BlockEdit} {...props} />;
}

// Register the edit filter
addFilter(
	'editor.BlockEdit',
	'wtbe/table-inspector-controls',
	addCustomControlsToTableBlock
);
