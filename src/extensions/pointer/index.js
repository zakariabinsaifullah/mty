/**
 * Pointer
 *
 * Adds a "Pointer" panel to Group and Kadence Section blocks: a thin white
 * leader line that starts at the block's left or right edge and ends in a dot,
 * pointing at the image next to it (the Figma "callouts", e.g. from the boxed
 * titles in "The claims we take on."). Desktop only (hidden at 1024px and
 * below).
 *
 * Editor: drawn on the block wrapper (::before; core uses ::after for selection outlines) from classes and CSS custom
 * properties set here. Front end: mty_render_pointer() in inc/extensions.php
 * adds the same classes and a .mty-pointer element.
 */
import { __ } from '@wordpress/i18n';
import { addFilter } from '@wordpress/hooks';
import { InspectorControls } from '@wordpress/block-editor';
import { PanelBody } from '@wordpress/components';
import { createHigherOrderComponent } from '@wordpress/compose';

import { NativeToggleControl, NativeToggleGroupControl, NativeRangeControl } from '../../components';

import './style.scss';

const BLOCKS = ['core/group', 'kadence/column'];

const DEFAULTS = {
    pointerDirection: 'right',
    pointerLength: 84,
    pointerPosition: 'middle',
    pointerOffset: 45,
    pointerSize: 'small'
};

/**
 * Classes and custom properties shared by the editor preview and front end.
 *
 * @param {Object} attributes Block attributes.
 * @return {{classes: string[], style: Object}} Pointer classes and CSS vars.
 */
export const getPointerProps = attributes => {
    const direction = attributes.pointerDirection || DEFAULTS.pointerDirection;
    const size = attributes.pointerSize || DEFAULTS.pointerSize;
    const position = attributes.pointerPosition || DEFAULTS.pointerPosition;
    const style = {
        '--mty-pointer-length': `${attributes.pointerLength ?? DEFAULTS.pointerLength}px`
    };
    if (position === 'top') {
        style['--mty-pointer-top'] = `${attributes.pointerOffset ?? DEFAULTS.pointerOffset}px`;
    }
    return {
        classes: ['has-mty-pointer', `is-pointer-${direction}`, `is-pointer-${size}`],
        style
    };
};

addFilter('blocks.registerBlockType', 'mty/pointer-add-attributes', (settings, name) => {
    if (!BLOCKS.includes(name)) {
        return settings;
    }

    return {
        ...settings,
        attributes: {
            ...settings.attributes,
            pointer: { type: 'boolean', default: false },
            pointerDirection: { type: 'string', default: DEFAULTS.pointerDirection },
            pointerLength: { type: 'number', default: DEFAULTS.pointerLength },
            pointerPosition: { type: 'string', default: DEFAULTS.pointerPosition },
            pointerOffset: { type: 'number', default: DEFAULTS.pointerOffset },
            pointerSize: { type: 'string', default: DEFAULTS.pointerSize }
        }
    };
});

addFilter(
    'editor.BlockEdit',
    'mty/pointer-add-inspector-controls',
    createHigherOrderComponent(BlockEdit => {
        return props => {
            const { name, attributes, setAttributes } = props;

            if (!BLOCKS.includes(name)) {
                return <BlockEdit {...props} />;
            }

            const { pointer, pointerDirection, pointerLength, pointerPosition, pointerOffset, pointerSize } = attributes;

            return (
                <>
                    <BlockEdit {...props} />
                    <InspectorControls>
                        <PanelBody title={__('Pointer', 'mty')} initialOpen={!!pointer}>
                            <NativeToggleControl
                                label={__('Enable pointer', 'mty')}
                                help={__('A line with a dot from this block’s edge to the image beside it. Desktop only.', 'mty')}
                                checked={!!pointer}
                                onChange={value => setAttributes({ pointer: value })}
                            />
                            {pointer && (
                                <>
                                    <NativeToggleGroupControl
                                        label={__('Direction', 'mty')}
                                        value={pointerDirection}
                                        options={[
                                            { label: __('Left', 'mty'), value: 'left' },
                                            { label: __('Right', 'mty'), value: 'right' }
                                        ]}
                                        onChange={value => setAttributes({ pointerDirection: value })}
                                    />
                                    <NativeRangeControl
                                        label={__('Length (px)', 'mty')}
                                        value={pointerLength}
                                        min={20}
                                        max={600}
                                        onChange={value => setAttributes({ pointerLength: value })}
                                    />
                                    <NativeToggleGroupControl
                                        label={__('Vertical position', 'mty')}
                                        value={pointerPosition}
                                        options={[
                                            { label: __('Middle', 'mty'), value: 'middle' },
                                            { label: __('From top', 'mty'), value: 'top' }
                                        ]}
                                        onChange={value => setAttributes({ pointerPosition: value })}
                                    />
                                    {pointerPosition === 'top' && (
                                        <NativeRangeControl
                                            label={__('Offset from top (px)', 'mty')}
                                            value={pointerOffset}
                                            min={0}
                                            max={800}
                                            onChange={value => setAttributes({ pointerOffset: value })}
                                        />
                                    )}
                                    <NativeToggleGroupControl
                                        label={__('Size', 'mty')}
                                        value={pointerSize}
                                        options={[
                                            { label: __('Small', 'mty'), value: 'small' },
                                            { label: __('Large', 'mty'), value: 'large' }
                                        ]}
                                        onChange={value => setAttributes({ pointerSize: value })}
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

addFilter(
    'editor.BlockListBlock',
    'mty/pointer-add-class',
    createHigherOrderComponent(BlockListBlock => {
        return props => {
            const { name, attributes } = props;

            if (!BLOCKS.includes(name) || !attributes.pointer) {
                return <BlockListBlock {...props} />;
            }

            const { classes, style } = getPointerProps(attributes);
            const wrapperProps = {
                ...props.wrapperProps,
                style: { ...(props.wrapperProps?.style || {}), ...style }
            };

            return (
                <BlockListBlock
                    {...props}
                    className={[props.className, 'mty-pointer-editor', ...classes].filter(Boolean).join(' ')}
                    wrapperProps={wrapperProps}
                />
            );
        };
    })
);
