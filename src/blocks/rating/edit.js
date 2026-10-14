/**
 * WordPress Dependencies
 */
import { useBlockProps } from '@wordpress/block-editor';
import { useEffect } from '@wordpress/element';

/**
 * Internal Dependencies
 */
import Inspector from './inspector';
import { StarRating } from '../../components';

// block edit function
const Edit = props => {
    const { attributes, setAttributes, isSelected } = props;
    const { nrPos, enableRating, ratingText, totalRating, ratingSize, ratingNsize, ratingColor, nuRatColor, alignment } = attributes;

    const cssCustomProperties = {
        ...(ratingSize && { '--rating-size': `${ratingSize}px` }),
        ...(ratingNsize && { '--value-size': `${ratingNsize}px` }),
        ...(ratingColor && { '--rating-color': ratingColor }),
        ...(nuRatColor && { '--numeric-color': nuRatColor }),
        ...(alignment && { '--rating-align': alignment })
    };

    const blockProps = useBlockProps({
        style: cssCustomProperties
    });

    useEffect(() => {
        setAttributes({
            blockStyle: cssCustomProperties
        });
    }, [ratingSize, ratingNsize, ratingColor, nuRatColor, alignment]);

    return (
        <>
            {isSelected && <Inspector {...props} />}
            <div {...blockProps}>
                <div className={`rating-container ${nrPos}`}>
                    <StarRating total={totalRating} rating={parseFloat(ratingText) || 0} />
                    {enableRating && (
                        <div className="numeric-rating">
                            <span className="rating-text">{ratingText}</span>
                        </div>
                    )}
                </div>
            </div>
        </>
    );
};

export default Edit;
