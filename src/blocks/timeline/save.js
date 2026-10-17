/**
 * WordPress dependencies
 */
import { useBlockProps, useInnerBlocksProps } from '@wordpress/block-editor';

/**
 * Inline custom properties for the timeline wrapper. Defaults are left out so
 * the stylesheet's values (MTY presets, 40px gaps) apply.
 *
 * Colours are stored as preset variables (var(--wp--preset--color--…)) when
 * picked from the palette.
 *
 * @param {Object} attributes Block attributes.
 * @return {Object} Style object.
 */
export const getTimelineStyle = ({ columns, color, activeColor, highlightLine, activeLineColor, gap, contentGap }) => {
    const style = {};

    if (columns && columns !== 4) {
        style['--mty-timeline-columns'] = columns;
    }
    if (color) {
        style['--mty-timeline-color'] = color;
    }
    if (activeColor) {
        style['--mty-timeline-active'] = activeColor;
    }
    // The highlighted step's line becomes a 3px progress bar.
    if (highlightLine) {
        style['--mty-timeline-active-line'] = activeLineColor || 'var(--mty-timeline-active)';
        style['--mty-timeline-active-line-height'] = '3px';
    }
    if (typeof gap === 'number' && gap !== 40) {
        style['--mty-timeline-gap'] = `${gap}px`;
    }
    if (typeof contentGap === 'number' && contentGap !== 40) {
        style['--mty-timeline-content-gap'] = `${contentGap}px`;
    }

    return style;
};

export default function save({ attributes }) {
    const blockProps = useBlockProps.save({ style: getTimelineStyle(attributes) });
    const innerBlocksProps = useInnerBlocksProps.save(blockProps);

    return <div {...innerBlocksProps} />;
}
