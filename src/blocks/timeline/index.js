import { registerBlockType } from '@wordpress/blocks';
import './style.scss';

/**
 * Internal dependencies
 */
import Edit from './edit';
import save from './save';
import metadata from './block.json';

const inlineIcon = (
    <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="currentColor">
        <rect x="2" y="7" width="4" height="4" />
        <rect x="6" y="8.25" width="16" height="1.5" />
        <rect x="2" y="13" width="9" height="1.5" />
        <rect x="2" y="16.5" width="7" height="1.5" />
        <rect x="13" y="13" width="9" height="1.5" />
        <rect x="13" y="16.5" width="7" height="1.5" />
    </svg>
);

registerBlockType(metadata.name, {
    icon: inlineIcon,
    /**
     * @see ./edit.js
     */
    edit: Edit,

    /**
     * @see ./save.js
     */
    save
});
