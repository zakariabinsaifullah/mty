/**
 * WordPress dependencies
 */
import { __ } from '@wordpress/i18n';

import {
    RichText,
    BlockControls,
    InspectorControls,
    useBlockProps,
    JustifyToolbar,
    __experimentalUseBorderProps as useBorderProps,
    __experimentalUseColorProps as useColorProps,
    __experimentalGetSpacingClassesAndStyles as useSpacingProps,
    __experimentalGetShadowClassesAndStyles as useShadowProps,
    __experimentalLinkControl as LinkControl
} from '@wordpress/block-editor';
import { link } from '@wordpress/icons';
import {
    PanelBody,
    RangeControl,
    ToolbarButton,
    Popover,
    __experimentalToolsPanel as ToolsPanel, // eslint-disable-line
    __experimentalToolsPanelItem as ToolsPanelItem
} from '@wordpress/components';
import { useState, useEffect } from '@wordpress/element';
import { useSelect } from '@wordpress/data';

import classNames from 'classnames';

/**
 * Internal dependencies
 */
import {
    NativeResponsiveControl,
    NativeToggleControl,
    NativeTextControl,
    NativeIconPicker,
    PanelColorControl,
    NativeSelectControl,
    NativeUnitControl
} from '../../components';

import { RenderIcon } from '../../helpers';

import './editor.scss';

export default function Edit(props) {
    const { attributes, setAttributes, className } = props;
    const {
        iconName,
        iconSize,
        customSvgCode,
        iconType,
        strokeWidth,
        justifyContent,
        href,
        linkTarget,
        sizes,
        resMode,
        heading,
        headingTag,
        showTitle,
        listGap,
        titleColor,
        titleSize,
        titleFontFamily,
        iconVerticalAlign,
        iconPosition,
        keepAspectRatio
    } = attributes;

    const fontFamilies = useSelect(select => {
        const settings = select('core/block-editor').getSettings();
        const typography = settings?.typography || settings?.__experimentalFeatures?.typography;
        const fontFamiliesSetting = typography?.fontFamilies;

        if (!fontFamiliesSetting) {
            return [];
        }

        const families = [];
        if (Array.isArray(fontFamiliesSetting)) {
            families.push(...fontFamiliesSetting);
        } else {
            const { theme = [], custom = [], default: defaultFonts = [] } = fontFamiliesSetting;
            families.push(...theme, ...custom, ...defaultFonts);
        }

        return families;
    }, []);

    const fontFamilyOptions = [
        { label: __('Default', 'mty'), value: '' },
        ...fontFamilies.map(f => {
            const value = f.fontFamily || (f.slug ? `var(--wp--preset--font-family--${f.slug})` : f.slug);
            return {
                label: f.name || f.slug || __('Unknown', 'mty'),
                value: value
            };
        })
    ];

    // Default size, and the value each breakpoint falls back to when unset.
    const DEFAULT_ICON_SIZE = 20;

    const resolvedSizes = {
        Desktop: sizes?.Desktop ?? DEFAULT_ICON_SIZE,
        Tablet: sizes?.Tablet ?? sizes?.Desktop ?? DEFAULT_ICON_SIZE,
        Mobile: sizes?.Mobile ?? sizes?.Tablet ?? sizes?.Desktop ?? DEFAULT_ICON_SIZE
    };

    const isInheritedSize = undefined === sizes?.[resMode];

    // Only explicit sizes are written out; style.scss cascades the rest.
    const sizeCustomProperties = {
        ...(sizes?.Desktop && { '--dsize': `${sizes.Desktop}px` }),
        ...(sizes?.Tablet && { '--tsize': `${sizes.Tablet}px` }),
        ...(sizes?.Mobile && { '--msize': `${sizes.Mobile}px` })
    };

    const cssCustomProperties = {
        ...(listGap && { '--list-gap': `${listGap}` }),
        ...(titleColor && { '--title-color': titleColor }),
        ...(titleSize && { '--title-size': `${titleSize}` }),
        ...(titleFontFamily && { '--title-font-family': titleFontFamily })
    };

    useEffect(() => {
        setAttributes({
            blockStyle: cssCustomProperties
        });
    }, [listGap, titleColor, titleSize, titleFontFamily]);

    // states
    const [isEditingURL, setIsEditingURL] = useState(false);
    const [popoverAnchor, setPopoverAnchor] = useState(null);

    const borderProps = useBorderProps(attributes);
    const colorProps = useColorProps(attributes);
    const spacingProps = useSpacingProps(attributes);
    const shadowProps = useShadowProps(attributes);

    const blockProps = useBlockProps({
        style: cssCustomProperties,
        className: classNames(className, {
            [`is-${iconType}`]: iconType,
            [`justify-${justifyContent}`]: justifyContent,
            'has-aspect-ratio': keepAspectRatio
        })
    });

    // With "Icon Position" set to "after", the title comes first.
    const titleFirst = 'after' === iconPosition;
    const title = showTitle && (
        <div className="icon-content">
            <RichText
                tagName={headingTag}
                value={heading}
                onChange={value => setAttributes({ heading: value })}
                placeholder={__('List title...', 'mty')}
                className="icon-heading"
            />
        </div>
    );

    return (
        <>
            <BlockControls group="block">
                <JustifyToolbar
                    allowedControls={['left', 'center', 'right']}
                    value={justifyContent}
                    onChange={value =>
                        setAttributes({
                            justifyContent: value
                        })
                    }
                />
                <ToolbarButton
                    ref={setPopoverAnchor}
                    name="link"
                    icon={link}
                    title={__('Link', 'mty')}
                    onClick={() => setIsEditingURL(true)}
                    isActive={!!href || isEditingURL}
                />
                {isEditingURL && (
                    <Popover
                        anchor={popoverAnchor}
                        onClose={() => setIsEditingURL(false)}
                        placement="bottom"
                        focusOnMount={true}
                        offset={12}
                        className="mty-icon__link-popover"
                        variant="alternate"
                    >
                        <LinkControl
                            value={{
                                url: href,
                                opensInNewTab: linkTarget === '_blank'
                            }}
                            onChange={({ url: newURL = '', opensInNewTab }) => {
                                setAttributes({
                                    href: newURL,
                                    linkTarget: opensInNewTab ? '_blank' : undefined,
                                    linkRel: newURL ? 'nofollow' : undefined,
                                    tagName: 'a'
                                });
                            }}
                            onRemove={() =>
                                setAttributes({
                                    href: undefined,
                                    linkTarget: undefined,
                                    linkRel: undefined,
                                    tagName: 'div'
                                })
                            }
                        />
                    </Popover>
                )}
            </BlockControls>
            <InspectorControls>
                <PanelBody title={__('Settings', 'mty')}>
                    <NativeToggleControl
                        label={__('Add List Title', 'mty')}
                        checked={showTitle}
                        onChange={value => setAttributes({ showTitle: value })}
                    />
                    <NativeIconPicker
                        onIconSelect={(iconName, iconType) => {
                            setAttributes({ iconName, iconType, customSvgCode: undefined });
                        }}
                        onCustomSvgInsert={({ customSvgCode, iconType, strokeWidth }) => {
                            setAttributes({ customSvgCode, iconType, strokeWidth });
                        }}
                        iconName={iconName}
                        customSvgCode={customSvgCode}
                        iconSize={iconSize}
                        strokeWidth={strokeWidth}
                    />
                    <NativeResponsiveControl label={__('Icon Size (px)', 'mty')} props={props}>
                        <RangeControl
                            value={resolvedSizes[resMode]}
                            onChange={value => setAttributes({ sizes: { ...sizes, [resMode]: value } })}
                            min={8}
                            max={256}
                            allowReset
                            help={
                                'Desktop' !== resMode && isInheritedSize
                                    ? __(
                                          'Inherited from the larger screen size. Change it to set a size just for this device.',
                                          'mty'
                                      )
                                    : undefined
                            }
                            __next40pxDefaultSize
                        />
                    </NativeResponsiveControl>
                    {customSvgCode && (
                        <NativeToggleControl
                            label={__('Keep SVG Aspect Ratio', 'mty')}
                            help={__('For wide SVGs such as logos: the icon size sets the height and the width follows the SVG.', 'mty')}
                            checked={keepAspectRatio}
                            onChange={value => setAttributes({ keepAspectRatio: value })}
                        />
                    )}
                </PanelBody>
                {showTitle && (
                    <PanelBody title={__('List Title', 'mty')} initialOpen={false}>
                        <NativeUnitControl
                            label={__('Gap ', 'mty')}
                            value={listGap}
                            onChange={value => setAttributes({ listGap: value })}
                        />
                        <NativeSelectControl
                            label={__('Icon Position', 'mty')}
                            value={iconPosition}
                            onChange={value => setAttributes({ iconPosition: value })}
                            options={[
                                { label: __('Before Title', 'mty'), value: 'before' },
                                { label: __('After Title', 'mty'), value: 'after' }
                            ]}
                        />
                        <NativeSelectControl
                            label={__('Vertical Alignment', 'mty')}
                            value={iconVerticalAlign}
                            onChange={value => setAttributes({ iconVerticalAlign: value })}
                            options={[
                                { label: __('Top', 'mty'), value: 'top' },
                                { label: __('Center', 'mty'), value: 'center' },
                                { label: __('Bottom', 'mty'), value: 'bottom' }
                            ]}
                        />
                        {showTitle && (
                            <>
                                <NativeSelectControl
                                    label={__('Select Tag', 'mty')}
                                    value={headingTag}
                                    onChange={value => setAttributes({ headingTag: value })}
                                    options={[
                                        { label: __('H1', 'mty'), value: 'h1' },
                                        { label: __('H2', 'mty'), value: 'h2' },
                                        { label: __('H3', 'mty'), value: 'h3' },
                                        { label: __('H4', 'mty'), value: 'h4' },
                                        { label: __('H5', 'mty'), value: 'h5' },
                                        { label: __('H6', 'mty'), value: 'h6' },
                                        { label: __('Paragraph', 'mty'), value: 'p' },
                                        { label: __('Div', 'mty'), value: 'div' }
                                    ]}
                                />
                                <NativeTextControl
                                    label={__('Title Text', 'mty')}
                                    value={heading}
                                    onChange={value => setAttributes({ heading: value })}
                                    placeholder={__('List title...', 'mty')}
                                />
                            </>
                        )}
                    </PanelBody>
                )}
            </InspectorControls>
            <InspectorControls group="styles">
                {showTitle && (
                    <ToolsPanel
                        label={__('Title', 'mty')}
                        resetAll={() =>
                            setAttributes({
                                titleSize: undefined,
                                titleColor: undefined
                            })
                        }
                    >
                        <ToolsPanelItem
                            hasValue={() => !!titleSize}
                            label={__('Size', 'mty')}
                            onDeselect={() => {
                                setAttributes({
                                    titleSize: undefined
                                });
                            }}
                            onSelect={() => {}}
                        >
                            <NativeUnitControl
                                label={__('Font Size', 'mty')}
                                value={titleSize}
                                onChange={value => setAttributes({ titleSize: value })}
                            />
                        </ToolsPanelItem>

                        <ToolsPanelItem
                            hasValue={() => !!titleColor}
                            label={__('Color', 'mty')}
                            onDeselect={() => {
                                setAttributes({
                                    titleColor: undefined
                                });
                            }}
                            onSelect={() => {}}
                        >
                            <PanelColorControl
                                label={__('Color', 'mty')}
                                colorSettings={[
                                    {
                                        value: titleColor,
                                        onChange: color => setAttributes({ titleColor: color }),
                                        label: __('Color', 'mty')
                                    }
                                ]}
                            />
                        </ToolsPanelItem>

                        <ToolsPanelItem
                            hasValue={() => !!titleFontFamily}
                            label={__('Font', 'mty')}
                            onDeselect={() => {
                                setAttributes({
                                    titleFontFamily: undefined
                                });
                            }}
                            onSelect={() => {}}
                        >
                            <NativeSelectControl
                                label={__('Font', 'mty')}
                                value={titleFontFamily}
                                onChange={value => setAttributes({ titleFontFamily: value })}
                                options={fontFamilyOptions}
                            />
                        </ToolsPanelItem>
                    </ToolsPanel>
                )}
            </InspectorControls>
            <div {...blockProps}>
                <div
                    className={classNames('mty-icon-block-wrapper', {
                        [`icon-valign-${iconVerticalAlign}`]: iconVerticalAlign
                    })}
                >
                    {titleFirst && title}
                    <div
                        className={classNames('icon-container', colorProps.className, borderProps.className)}
                        style={{
                            ...borderProps.style,
                            ...colorProps.style,
                            ...spacingProps.style,
                            ...shadowProps.style,
                            ...sizeCustomProperties
                        }}
                    >
                        <RenderIcon customSvgCode={customSvgCode} iconName={iconName} size={iconSize} />
                    </div>
                    {!titleFirst && title}
                </div>
            </div>
        </>
    );
}
