import { __ } from '@wordpress/i18n';
import { InspectorControls } from '@wordpress/block-editor';
import {
    PanelBody,
    __experimentalToolsPanel as ToolsPanel, // eslint-disable-line
    __experimentalToolsPanelItem as ToolsPanelItem // eslint-disable-line
} from '@wordpress/components';
import { arrowLeft, arrowRight, arrowUp, arrowDown } from '@wordpress/icons';

import { NativeToggleGroupControl, NativeRangeControl, NativeToggleControl, NativeTextControl, PanelColorControl } from '../../components';

const Inspector = props => {
    const { attributes, setAttributes } = props;
    const { enableRating, ratingText, totalRating, nrPos, ratingSize, ratingNsize, ratingColor, nuRatColor, alignment } = attributes;

    return (
        <>
            <InspectorControls group="settings">
                <PanelBody title={ __( 'Settings', 'mty' ) } initialOpen={ true }>
                    <NativeToggleControl
                        label={ __( 'Show Numeric Rating', 'mty' ) }
                        checked={ enableRating }
                        onChange={ value => setAttributes( { enableRating: value } ) }
                    />
                    { enableRating && (
                        <NativeToggleGroupControl
                            label={ __( 'Position', 'mty' ) }
                            value={ nrPos }
                            onChange={ value => setAttributes( { nrPos: value } ) }
                            options={ [
                                { value: 'nr_left', label: __( 'Left', 'mty' ), icon: arrowLeft },
                                { value: 'nr_right', label: __( 'Right', 'mty' ), icon: arrowRight },
                                { value: 'nr_top', label: __( 'Top', 'mty' ), icon: arrowUp },
                                { value: 'nr_bottom', label: __( 'Bottom', 'mty' ), icon: arrowDown }
                            ] }
                        />
                    ) }
                    <NativeToggleGroupControl
                        label={ __( 'Alignment', 'mty' ) }
                        value={ alignment }
                        onChange={ value => setAttributes( { alignment: value } ) }
                        options={ [
                            { value: '', label: __( 'Left', 'mty' ) },
                            { value: 'center', label: __( 'Center', 'mty' ) },
                            { value: 'right', label: __( 'Right', 'mty' ) }
                        ] }
                    />
                </PanelBody>
                <PanelBody title={ __( 'Rating', 'mty' ) } initialOpen={ false }>
                    <NativeRangeControl
                        label={ __( 'Total Rating', 'mty' ) }
                        value={ totalRating }
                        onChange={ value => setAttributes( { totalRating: value } ) }
                        min={ 1 }
                        max={ 10 }
                        step={ 1 }
                        resetFallbackValue={ 5 }
                    />
                    <NativeTextControl
                        label={ __( 'Rating', 'mty' ) }
                        value={ ratingText }
                        placeholder="4.5"
                        help={ __( 'Shown as the number rating; the stars fill to the number in it.', 'mty' ) }
                        onChange={ value => setAttributes( { ratingText: value } ) }
                    />
                </PanelBody>
            </InspectorControls>
            <InspectorControls group="styles">
                <ToolsPanel
                    label={ __( 'Rating', 'mty' ) }
                    resetAll={ () =>
                        setAttributes( {
                            ratingSize: undefined,
                            ratingColor: undefined
                        } )
                    }
                >
                    <ToolsPanelItem
                        hasValue={ () => !! ratingSize }
                        label={ __( 'Size', 'mty' ) }
                        onDeselect={ () => {
                            setAttributes( {
                                ratingSize: undefined
                            } );
                        } }
                        onSelect={ () => {} }
                    >
                        <NativeRangeControl
                            label={ __( 'Rating Size', 'mty' ) }
                            value={ ratingSize }
                            onChange={ value => setAttributes( { ratingSize: value } ) }
                            min={ 0 }
                            max={ 100 }
                            step={ 1 }
                        />
                    </ToolsPanelItem>

                    <ToolsPanelItem
                        hasValue={ () => !! ratingColor }
                        label={ __( 'Color', 'mty' ) }
                        onDeselect={ () => {
                            setAttributes( {
                                ratingColor: undefined
                            } );
                        } }
                        onSelect={ () => {} }
                    >
                        <PanelColorControl
                            label={ __( 'Star Color', 'mty' ) }
                            colorSettings={ [
                                {
                                    value: ratingColor,
                                    onChange: color => setAttributes( { ratingColor: color } )
                                }
                            ] }
                        />
                    </ToolsPanelItem>
                </ToolsPanel>
                { enableRating && (
                    <ToolsPanel
                        label={ __( 'Numeric Rating', 'mty' ) }
                        resetAll={ () =>
                            setAttributes( {
                                ratingNsize: undefined,
                                nuRatColor: undefined
                            } )
                        }
                    >
                        <ToolsPanelItem
                            hasValue={ () => !! ratingNsize }
                            label={ __( 'Size', 'mty' ) }
                            onDeselect={ () => {
                                setAttributes( {
                                    ratingNsize: undefined
                                } );
                            } }
                            onSelect={ () => {} }
                        >
                            <NativeRangeControl
                                label={ __( 'Font Size', 'mty' ) }
                                value={ ratingNsize }
                                onChange={ value => setAttributes( { ratingNsize: value } ) }
                                min={ 0 }
                                max={ 100 }
                                step={ 1 }
                            />
                        </ToolsPanelItem>

                        <ToolsPanelItem
                            hasValue={ () => !! nuRatColor }
                            label={ __( 'Color', 'mty' ) }
                            onDeselect={ () => {
                                setAttributes( {
                                    nuRatColor: undefined
                                } );
                            } }
                            onSelect={ () => {} }
                        >
                            <PanelColorControl
                                label={ __( 'Text Color', 'mty' ) }
                                colorSettings={ [
                                    {
                                        value: nuRatColor,
                                        onChange: color => setAttributes( { nuRatColor: color } ),
                                        label: __( 'Color', 'mty' )
                                    }
                                ] }
                            />
                        </ToolsPanelItem>
                    </ToolsPanel>
                ) }
            </InspectorControls>
        </>
    );
};

export default Inspector;
