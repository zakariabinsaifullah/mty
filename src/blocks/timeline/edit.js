/**
 * WordPress dependencies
 */
import { __ } from '@wordpress/i18n';
import { useEffect } from '@wordpress/element';
import { useDispatch, useSelect } from '@wordpress/data';
import { createBlock } from '@wordpress/blocks';
import { InspectorControls, useBlockProps, useInnerBlocksProps, store as blockEditorStore } from '@wordpress/block-editor';
import { PanelBody, RangeControl } from '@wordpress/components';

/**
 * Internal dependencies
 */
import { PanelColorControl } from '../../components';
import { getTimelineStyle } from './save';

import './editor.scss';

const createStep = () => createBlock('mty/timeline-step', {}, [createBlock('core/paragraph')]);

export default function Edit({ attributes, setAttributes, clientId }) {
    const { steps, columns, color, activeColor } = attributes;

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
                </PanelBody>
                <PanelColorControl
                    label={__('Timeline Colors', 'mty')}
                    colorSettings={[
                        {
                            label: __('Dot & Line', 'mty'),
                            value: color,
                            onChange: value => setAttributes({ color: value || '#FFFFFF' })
                        },
                        {
                            label: __('Highlighted Step', 'mty'),
                            value: activeColor,
                            onChange: value => setAttributes({ activeColor: value || '#29A852' })
                        }
                    ]}
                />
            </InspectorControls>
            <div {...innerBlocksProps} />
        </>
    );
}
