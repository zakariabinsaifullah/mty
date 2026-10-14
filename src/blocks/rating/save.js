/**
 * WordPress Dependencies
 */
import { useBlockProps } from '@wordpress/block-editor';

/**
 * Internal Dependencies
 */
import { StarRating } from '../../components';

// block save function
const Save = props => {
    const { attributes } = props;
    const { nrPos, enableRating, ratingText, totalRating, blockStyle } = attributes;

    const blockProps = useBlockProps.save({
        style: blockStyle
    });

    return (
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
    );
};

export default Save;
