/**
 * WordPress dependencies
 */
import {
    RichText,
    useBlockProps,
    __experimentalGetBorderClassesAndStyles as getBorderClassesAndStyles,
    __experimentalGetColorClassesAndStyles as getColorClassesAndStyles,
    __experimentalGetSpacingClassesAndStyles as getSpacingClassesAndStyles,
    __experimentalGetShadowClassesAndStyles as getShadowClassesAndStyles
} from '@wordpress/block-editor';
import { Icon } from '@wordpress/components';

/**
 * External Dependencies
 */
import classNames from 'classnames';

/**
 * Internal Dependencies
 */
import { getIconByName } from '../../utils/icons';

// block save function
const save = props => {
    const { attributes } = props;
    const { image, itemStyle, iconName, customSvgCode, number, showTitle, title, titleTag } = attributes;

    const borderProps = getBorderClassesAndStyles(attributes);
    const colorProps = getColorClassesAndStyles(attributes);
    const spacingProps = getSpacingClassesAndStyles(attributes);
    const shadowProps = getShadowClassesAndStyles(attributes);

    const blockProps = useBlockProps.save({
        className: classNames(colorProps.className, borderProps.className, spacingProps.className, shadowProps.className),
        style: {
            ...borderProps.style,
            ...colorProps.style,
            ...spacingProps.style,
            ...shadowProps.style
        }
    });

    const selectedIcon = !customSvgCode && iconName ? getIconByName(iconName) : null;

    return (
        <div {...blockProps}>
            {/*
             * Painted as a background rather than an <img> so the tablet and
             * mobile variants swap by media query — see style.scss. Where the
             * image was described, the role/label pair stands in for its alt.
             */}
            <div className="img" style={itemStyle} {...(image?.alt ? { role: 'img', 'aria-label': image.alt } : {})} />

            <div className="info">
                <div className="item-top">
                    {customSvgCode && <div className="item-icon" aria-hidden="true" dangerouslySetInnerHTML={{ __html: customSvgCode }} />}
                    {selectedIcon && (
                        <div className="item-icon" aria-hidden="true">
                            <Icon icon={selectedIcon.icon} />
                        </div>
                    )}
                    {number && <RichText.Content tagName="span" value={number} className="number" />}
                </div>
                <div className="top-content">
                    {showTitle && <RichText.Content tagName={titleTag || 'h4'} value={title} className="heading" />}
                </div>
            </div>
        </div>
    );
};

export default save;
