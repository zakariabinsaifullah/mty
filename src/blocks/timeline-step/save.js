/**
 * WordPress dependencies
 */
import { useBlockProps, useInnerBlocksProps } from '@wordpress/block-editor';

import classNames from 'classnames';

export default function save({ attributes }) {
    const blockProps = useBlockProps.save({ className: classNames({ 'is-active': attributes.isActive }) });
    const innerBlocksProps = useInnerBlocksProps.save({ className: 'mty-timeline-step__content' });

    return (
        <div {...blockProps}>
            <div className="mty-timeline-step__marker" aria-hidden="true" />
            <div {...innerBlocksProps} />
        </div>
    );
}
