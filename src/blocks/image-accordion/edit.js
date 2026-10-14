// import editor style
import './editor.scss';

/**
 * WordPress Dependencies
 */
import { useBlockProps, useInnerBlocksProps, BlockControls } from '@wordpress/block-editor';
import { Fragment, useEffect } from '@wordpress/element';
import { __ } from '@wordpress/i18n';
import { ToolbarGroup, ToolbarButton } from '@wordpress/components';
import { plus } from '@wordpress/icons';

/**
 * Internal Dependencies
 */
import Inspector from './inspector';

const TEMPLATE = [
    ['mty/image-accordion-item', { number: '01', title: __('Title 01', 'mty') }],
    ['mty/image-accordion-item', { number: '02', title: __('Title 02', 'mty') }],
    ['mty/image-accordion-item', { number: '03', title: __('Title 03', 'mty') }]
];

// block edit function
const Edit = props => {
    const { attributes, setAttributes, clientId } = props;
    const { itemsGap, contentMaxWidth } = attributes;

    // Only breakpoints with an explicit value are written out; style.scss
    // cascades each unset breakpoint up to the next larger one.
    const cssCustomProperties = {
        ...(itemsGap?.Desktop && { '--dgap': `${itemsGap.Desktop}` }),
        ...(itemsGap?.Tablet && { '--tgap': `${itemsGap.Tablet}` }),
        ...(itemsGap?.Mobile && { '--mgap': `${itemsGap.Mobile}` }),
        ...(contentMaxWidth && { '--content-max-width': `${contentMaxWidth}` })
    };

    useEffect(() => {
        setAttributes({ blockStyle: cssCustomProperties });
    }, [itemsGap, contentMaxWidth]);

    const blockProps = useBlockProps({
        style: cssCustomProperties
    });

    const innerBlockProps = useInnerBlocksProps(
        { className: 'mty-image-accordion' },
        {
            renderAppender: false,
            allowedBlocks: ['mty/image-accordion-item'],
            template: TEMPLATE,
            templateLock: false
        }
    );

    const addItem = () => {
        const childBlocks = wp.data.select('core/block-editor').getBlocks(clientId);
        const newBlock = wp.blocks.createBlock('mty/image-accordion-item', {
            number: String(childBlocks.length + 1).padStart(2, '0'),
            title: __('New Item', 'mty')
        });
        wp.data.dispatch('core/block-editor').insertBlocks(newBlock, childBlocks.length, clientId);
    };

    return (
        <Fragment>
            <BlockControls>
                <ToolbarGroup>
                    <ToolbarButton icon={plus} label={__('Add Item', 'mty')} onClick={addItem} />
                </ToolbarGroup>
            </BlockControls>
            <Inspector {...props} />
            <div {...blockProps}>
                <div {...innerBlockProps} />
            </div>
        </Fragment>
    );
};

export default Edit;
