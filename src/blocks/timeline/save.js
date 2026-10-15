/**
 * WordPress dependencies
 */
import { useBlockProps, useInnerBlocksProps } from '@wordpress/block-editor';

/**
 * Inline custom properties for the timeline wrapper. Defaults are left out so
 * the stylesheet's values apply.
 *
 * @param {Object} attributes Block attributes.
 * @return {Object} Style object.
 */
export const getTimelineStyle = ({ columns, color, activeColor }) => {
    const style = {};

    if (columns && columns !== 4) {
        style['--mty-timeline-columns'] = columns;
    }
    if (color && color !== '#FFFFFF') {
        style['--mty-timeline-color'] = color;
    }
    if (activeColor && activeColor !== '#29A852') {
        style['--mty-timeline-active'] = activeColor;
    }

    return style;
};

export default function save({ attributes }) {
    const blockProps = useBlockProps.save({ style: getTimelineStyle(attributes) });
    const innerBlocksProps = useInnerBlocksProps.save(blockProps);

    return <div {...innerBlocksProps} />;
}
