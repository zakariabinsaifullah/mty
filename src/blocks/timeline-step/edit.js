/**
 * WordPress dependencies
 */
import { __ } from '@wordpress/i18n';
import { InspectorControls, useBlockProps, useInnerBlocksProps } from '@wordpress/block-editor';
import { PanelBody, ToggleControl } from '@wordpress/components';

import classNames from 'classnames';

export default function Edit({ attributes, setAttributes }) {
    const { isActive } = attributes;

    const blockProps = useBlockProps({ className: classNames({ 'is-active': isActive }) });
    const innerBlocksProps = useInnerBlocksProps({ className: 'mty-timeline-step__content' }, { template: [['core/paragraph']] });

    return (
        <>
            <InspectorControls>
                <PanelBody title={__('Step', 'mty')}>
                    <ToggleControl
                        __nextHasNoMarginBottom
                        label={__('Highlight step', 'mty')}
                        help={__('Draws this step’s dot and line in the active colour.', 'mty')}
                        checked={isActive}
                        onChange={value => setAttributes({ isActive: value })}
                    />
                </PanelBody>
            </InspectorControls>
            <div {...blockProps}>
                <div className="mty-timeline-step__marker" aria-hidden="true" />
                <div {...innerBlocksProps} />
            </div>
        </>
    );
}
