/**
 * WordPress dependencies
 */
import { __ } from '@wordpress/i18n';
import { NativeRangeControl, NativeSelectControl } from '../../../components';

const HoverTransitionControls = ( { attributes, setAttributes } ) => {
    const {
        hoverTransitionDuration,
        hoverTransitionTiming,
        hoverTextColor,
        hoverBackgroundColor,
        hoverBorderColor,
        customHoverTextColor,
        customHoverBackgroundColor,
        customHoverBorderColor
    } = attributes;

    const hasHoverColor =
        customHoverBorderColor ||
        customHoverTextColor ||
        customHoverBackgroundColor ||
        hoverTextColor ||
        hoverBackgroundColor ||
        hoverBorderColor;

    if ( ! hasHoverColor ) {
        return null;
    }

    const timingOptions = [
        { label: __( 'Standard', 'mty' ), value: 'cubic-bezier(0.4, 0, 0.2, 1)' },
        { label: __( 'Ease', 'mty' ), value: 'ease' },
        { label: __( 'Linear', 'mty' ), value: 'linear' },
        { label: __( 'Ease In', 'mty' ), value: 'ease-in' },
        { label: __( 'Ease Out', 'mty' ), value: 'ease-out' },
        { label: __( 'Ease In Out', 'mty' ), value: 'ease-in-out' }
    ];

    return (
        <div
            className="mty-hover-color__transition-controls"
            style={ {
                gridTemplateColumns: 'repeat(2, minmax(0px, 1fr))',
                gap: 'calc(16px)',
                gridColumn: '1 / -1'
            } }
        >
            <NativeRangeControl
                label={ __( 'Transition Duration', 'mty' ) }
                value={ hoverTransitionDuration }
                onChange={ value => setAttributes( { hoverTransitionDuration: value } ) }
                min={ 0 }
                max={ 2000 }
                step={ 50 }
                resetFallbackValue={ 200 }
                help={ __( 'Duration in milliseconds', 'mty' ) }
            />
            <NativeSelectControl
                label={ __( 'Timing Function', 'mty' ) }
                value={ hoverTransitionTiming }
                options={ timingOptions }
                onChange={ value => setAttributes( { hoverTransitionTiming: value } ) }
            />
        </div>
    );
};

export default HoverTransitionControls;
