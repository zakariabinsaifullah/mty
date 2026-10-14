/**
 * WordPress dependencies
 */
import { InspectorControls } from '@wordpress/block-editor';
import { PanelBody } from '@wordpress/components';
import { __ } from '@wordpress/i18n';

/**
 * Internal dependencies
 */
import { NativeToggleControl, NativeSelectControl, NativeResponsiveControl, NativeUnitControl } from '../../components';

const HEADING_TAGS = [
    { label: __('H1', 'mty'), value: 'h1' },
    { label: __('H2', 'mty'), value: 'h2' },
    { label: __('H3', 'mty'), value: 'h3' },
    { label: __('H4', 'mty'), value: 'h4' },
    { label: __('H5', 'mty'), value: 'h5' },
    { label: __('H6', 'mty'), value: 'h6' },
    { label: __('Paragraph', 'mty'), value: 'p' },
    { label: __('Div', 'mty'), value: 'div' }
];

const Inspector = props => {
    const { attributes, setAttributes } = props;
    const { showTitle, titleTag, itemsGap, contentMaxWidth, resMode } = attributes;

    return (
        <InspectorControls>
            <PanelBody title={__('Settings', 'mty')} initialOpen={true}>
                <NativeToggleControl
                    label={__('Show Title', 'mty')}
                    checked={showTitle}
                    onChange={value => setAttributes({ showTitle: value })}
                />
                {showTitle && (
                    <NativeSelectControl
                        label={__('Select Title Tag', 'mty')}
                        value={titleTag}
                        onChange={value => setAttributes({ titleTag: value })}
                        options={HEADING_TAGS}
                    />
                )}
                <NativeResponsiveControl label={__('Items Gap', 'mty')} props={props}>
                    <NativeUnitControl
                        value={itemsGap?.[resMode]}
                        onChange={value => setAttributes({ itemsGap: { ...itemsGap, [resMode]: value } })}
                    />
                </NativeResponsiveControl>
                <NativeUnitControl
                    label={__('Content Max Width', 'mty')}
                    value={contentMaxWidth}
                    onChange={value => setAttributes({ contentMaxWidth: value })}
                    units={[
                        { label: 'px', value: 'px' },
                        { label: '%', value: '%' },
                        { label: 'em', value: 'em' },
                        { label: 'rem', value: 'rem' }
                    ]}
                />
            </PanelBody>
        </InspectorControls>
    );
};

export default Inspector;
