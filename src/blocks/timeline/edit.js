/**
 * WordPress dependencies
 */
import { __ } from '@wordpress/i18n';
import { useEffect } from '@wordpress/element';
import { useDispatch, useSelect } from '@wordpress/data';
import { createBlock } from '@wordpress/blocks';
import { InspectorControls, useBlockProps, useInnerBlocksProps, useSettings, store as blockEditorStore } from '@wordpress/block-editor';
import { PanelBody, RangeControl, ToggleControl } from '@wordpress/components';

/**
 * Internal dependencies
 */
import { PanelColorControl } from '../../components';
import { getTimelineStyle } from './save';

import './editor.scss';

const createStep = () => createBlock('mty/timeline-step', {}, [createBlock('core/paragraph')]);

const PRESET = /^var\(--wp--preset--color--([a-z0-9-]+)\)$/;

/**
 * Colour helpers: palette colours are stored as preset variables, so a palette
 * change updates the timeline; the picker still needs the hex to mark the
 * swatch as selected.
 *
 * @param {Array} palette Theme palette.
 */
const colorHelpers = palette => ({
    toStored: value => {
        if (!value) {
            return '';
        }
        const preset = palette.find(item => item.color.toLowerCase() === value.toLowerCase());
        return preset ? `var(--wp--preset--color--${preset.slug})` : value;
    },
    toPicker: value => {
        const match = PRESET.exec(value || '');
        return match ? palette.find(item => item.slug === match[1])?.color : value;
    }
});

export default function Edit({ attributes, setAttributes, clientId }) {
    const { steps, columns, color, activeColor, highlightLine, activeLineColor, gap, contentGap } = attributes;
    const [themePalette] = useSettings('color.palette.theme');
    const { toStored, toPicker } = colorHelpers(themePalette || []);

    const stepCount = useSelect(select => select(blockEditorStore).getBlockCount(clientId), [clientId]);
    const { getBlocks } = useSelect(blockEditorStore);
    const { replaceInnerBlocks } = useDispatch(blockEditorStore);

    // Keep the Steps setting in line with steps added, duplicated or removed by hand.
    useEffect(() => {
        if (stepCount && stepCount !== steps) {
            setAttributes({ steps: stepCount });
        }
    }, [stepCount]); // eslint-disable-line react-hooks/exhaustive-deps

    const updateSteps = count => {
        const next = getBlocks(clientId).slice(0, count);
        while (next.length < count) {
            next.push(createStep());
        }
        replaceInnerBlocks(clientId, next, false);
        setAttributes({ steps: count });
    };

    const blockProps = useBlockProps({ style: getTimelineStyle(attributes) });
    const innerBlocksProps = useInnerBlocksProps(blockProps, {
        allowedBlocks: ['mty/timeline-step'],
        // The first step starts highlighted, as in the design.
        template: Array.from({ length: steps }, (_, index) => ['mty/timeline-step', { isActive: index === 0 }, [['core/paragraph']]]),
        orientation: 'horizontal',
        renderAppender: false
    });

    return (
        <>
            <InspectorControls>
                <PanelBody title={__('Timeline', 'mty')}>
                    <RangeControl
                        __nextHasNoMarginBottom
                        __next40pxDefaultSize
                        label={__('Steps', 'mty')}
                        value={steps}
                        onChange={value => value && updateSteps(value)}
                        min={1}
                        max={12}
                    />
                    <RangeControl
                        __nextHasNoMarginBottom
                        __next40pxDefaultSize
                        label={__('Columns (desktop)', 'mty')}
                        help={__('Tablet and mobile always show one column.', 'mty')}
                        value={columns}
                        onChange={value => setAttributes({ columns: value || 4 })}
                        min={1}
                        max={6}
                    />
                    <RangeControl
                        __nextHasNoMarginBottom
                        __next40pxDefaultSize
                        label={__('Step gap (px)', 'mty')}
                        help={__('Space between steps; the line runs across it.', 'mty')}
                        value={gap}
                        onChange={value => setAttributes({ gap: value ?? 40 })}
                        min={0}
                        max={120}
                    />
                    <RangeControl
                        __nextHasNoMarginBottom
                        __next40pxDefaultSize
                        label={__('Content gap (px)', 'mty')}
                        help={__('Space between the line and the blocks below it.', 'mty')}
                        value={contentGap}
                        onChange={value => setAttributes({ contentGap: value ?? 40 })}
                        min={0}
                        max={120}
                    />
                    <ToggleControl
                        __nextHasNoMarginBottom
                        label={__('Highlight line', 'mty')}
                        help={__('Also draw the highlighted step’s line, as a 3px progress bar. Off: only its dot is coloured.', 'mty')}
                        checked={!!highlightLine}
                        onChange={value => setAttributes({ highlightLine: value })}
                    />
                </PanelBody>
                <PanelColorControl
                    label={__('Timeline Colors', 'mty')}
                    colorSettings={[
                        {
                            label: __('Dot & Line', 'mty'),
                            value: toPicker(color),
                            onChange: value => setAttributes({ color: toStored(value) })
                        },
                        {
                            label: __('Highlighted Dot', 'mty'),
                            value: toPicker(activeColor),
                            onChange: value => setAttributes({ activeColor: toStored(value) })
                        },
                        ...(highlightLine
                            ? [
                                  {
                                      label: __('Highlighted Line', 'mty'),
                                      value: toPicker(activeLineColor),
                                      onChange: value => setAttributes({ activeLineColor: toStored(value) })
                                  }
                              ]
                            : [])
                    ]}
                />
            </InspectorControls>
            <div {...innerBlocksProps} />
        </>
    );
}
