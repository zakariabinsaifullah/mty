/**
 * Image — Figure Frame
 *
 * Adds a "Figure Frame" panel to core/image blocks. When enabled, the image
 * gets the Figma "figure" treatment used across the MTY design: a faint 1px
 * border, a dark overlay, four corner ticks and a "FIG. 02" label in the top
 * left corner. The label number is set per image; border, ticks and overlay
 * can each be switched off (e.g. the "Meet James" portrait shows only the
 * label).
 *
 * Editor: classes and the label are applied to the block wrapper here.
 * Front end: mty_render_image_figure_frame() in inc/extensions.php wraps the
 * image in a .mty-fig-frame element and adds the label as real markup.
 */
import { __ } from '@wordpress/i18n';
import { addFilter } from '@wordpress/hooks';
import { InspectorControls } from '@wordpress/block-editor';
import { PanelBody } from '@wordpress/components';
import { createHigherOrderComponent } from '@wordpress/compose';

import { NativeToggleControl, NativeTextControl } from '../../components';

import './style.scss';

const BLOCK_NAME = 'core/image';

/**
 * Builds the modifier classes shared by the editor preview and the front end.
 *
 * @param {Object} attributes Block attributes.
 * @return {string[]} Class names.
 */
const getFrameClasses = attributes => {
    const classes = [];
    if (attributes.figFrameBorder !== false) {
        classes.push('has-fig-border');
    }
    if (attributes.figFrameTicks !== false) {
        classes.push('has-fig-ticks');
    }
    if (attributes.figFrameOverlay !== false) {
        classes.push('has-fig-overlay');
    }
    return classes;
};

/**
 * Add the figure frame attributes to core/image.
 */
addFilter('blocks.registerBlockType', 'mty/image-figure-frame-add-attributes', (settings, name) => {
    if (name !== BLOCK_NAME) {
        return settings;
    }

    return {
        ...settings,
        attributes: {
            ...settings.attributes,
            figFrame: {
                type: 'boolean',
                default: false
            },
            figLabel: {
                type: 'string',
                default: ''
            },
            figFrameBorder: {
                type: 'boolean',
                default: true
            },
            figFrameTicks: {
                type: 'boolean',
                default: true
            },
            figFrameOverlay: {
                type: 'boolean',
                default: true
            }
        }
    };
});

/**
 * Add the "Figure Frame" panel to the core/image settings sidebar.
 */
addFilter(
    'editor.BlockEdit',
    'mty/image-figure-frame-add-inspector-controls',
    createHigherOrderComponent(BlockEdit => {
        return props => {
            const { name, attributes, setAttributes } = props;

            if (name !== BLOCK_NAME) {
                return <BlockEdit {...props} />;
            }

            const { figFrame, figLabel, figFrameBorder, figFrameTicks, figFrameOverlay } = attributes;

            return (
                <>
                    <BlockEdit {...props} />
                    <InspectorControls>
                        <PanelBody title={__('Figure Frame', 'mty')} initialOpen={!!figFrame}>
                            <NativeToggleControl
                                label={__('Enable figure frame', 'mty')}
                                checked={!!figFrame}
                                onChange={value => setAttributes({ figFrame: value })}
                            />
                            {figFrame && (
                                <>
                                    <NativeTextControl
                                        label={__('FIG value', 'mty')}
                                        value={figLabel}
                                        placeholder="02"
                                        help={
                                            figLabel
                                                ? `${__('Shown as', 'mty')} “FIG. ${figLabel.toUpperCase()}”`
                                                : __('Leave empty to hide the FIG label.', 'mty')
                                        }
                                        onChange={value => setAttributes({ figLabel: value })}
                                    />
                                    <NativeToggleControl
                                        label={__('Border', 'mty')}
                                        checked={figFrameBorder !== false}
                                        onChange={value => setAttributes({ figFrameBorder: value })}
                                    />
                                    <NativeToggleControl
                                        label={__('Corner ticks', 'mty')}
                                        checked={figFrameTicks !== false}
                                        onChange={value => setAttributes({ figFrameTicks: value })}
                                    />
                                    <NativeToggleControl
                                        label={__('Dark overlay', 'mty')}
                                        checked={figFrameOverlay !== false}
                                        onChange={value => setAttributes({ figFrameOverlay: value })}
                                    />
                                </>
                            )}
                        </PanelBody>
                    </InspectorControls>
                </>
            );
        };
    })
);

/**
 * Apply the frame classes and label in the editor preview.
 */
addFilter(
    'editor.BlockListBlock',
    'mty/image-figure-frame-add-class',
    createHigherOrderComponent(BlockListBlock => {
        return props => {
            const { name, attributes } = props;

            if (name !== BLOCK_NAME || !attributes.figFrame) {
                return <BlockListBlock {...props} />;
            }

            const classes = [props.className, 'mty-fig-frame-editor', ...getFrameClasses(attributes)].filter(Boolean).join(' ');
            const wrapperProps = { ...props.wrapperProps };
            if (attributes.figLabel) {
                wrapperProps['data-fig-label'] = `FIG. ${attributes.figLabel}`;
            }

            return <BlockListBlock {...props} className={classes} wrapperProps={wrapperProps} />;
        };
    })
);
