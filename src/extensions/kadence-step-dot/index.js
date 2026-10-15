/**
 * Kadence Column — Step Dot
 *
 * Adds an "Add Step Dot" toggle to the advanced inspector controls of
 * kadence/column blocks. When enabled, the `mty-step-dot` class is added to
 * the block's container, which puts a small orange dot on the section's top
 * edge (for numbered step cards). The class is added in the editor here and on
 * the front end by mty_render_kadence_step_dot() in inc/extensions.php.
 */
import { __ } from '@wordpress/i18n';
import { addFilter } from '@wordpress/hooks';
import { InspectorAdvancedControls } from '@wordpress/block-editor';
import { createHigherOrderComponent } from '@wordpress/compose';

import { NativeToggleControl } from '../../components';

import './style.scss';

const BLOCK_NAME = 'kadence/column';
const ATTRIBUTE = 'stepDot';

/**
 * Add the stepDot attribute to kadence/column.
 */
addFilter('blocks.registerBlockType', 'mty/kadence-step-dot-add-attribute', (settings, name) => {
    if (name !== BLOCK_NAME) {
        return settings;
    }

    return {
        ...settings,
        attributes: {
            ...settings.attributes,
            [ATTRIBUTE]: {
                type: 'boolean',
                default: false
            }
        }
    };
});

/**
 * Add the "Add Step Dot" toggle to the kadence/column advanced inspector.
 */
addFilter(
    'editor.BlockEdit',
    'mty/kadence-step-dot-add-inspector-controls',
    createHigherOrderComponent(BlockEdit => {
        return props => {
            const { name, attributes, setAttributes } = props;

            if (name !== BLOCK_NAME) {
                return <BlockEdit {...props} />;
            }

            return (
                <>
                    <BlockEdit {...props} />
                    <InspectorAdvancedControls>
                        <NativeToggleControl
                            label={__('Add Step Dot', 'mty')}
                            checked={!!attributes[ATTRIBUTE]}
                            onChange={value => setAttributes({ [ATTRIBUTE]: value })}
                        />
                    </InspectorAdvancedControls>
                </>
            );
        };
    })
);

/**
 * Apply the `mty-step-dot` class in the editor preview.
 */
addFilter(
    'editor.BlockListBlock',
    'mty/kadence-step-dot-add-class',
    createHigherOrderComponent(BlockListBlock => {
        return props => {
            const { name, attributes } = props;

            if (name !== BLOCK_NAME || !attributes[ATTRIBUTE]) {
                return <BlockListBlock {...props} />;
            }

            const classes = [props.className, 'mty-step-dot'].filter(Boolean).join(' ');

            return <BlockListBlock {...props} className={classes} />;
        };
    })
);
