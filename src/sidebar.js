import {registerPlugin} from '@wordpress/plugins';
import {PluginDocumentSettingPanel} from '@wordpress/editor';
import {Panel, PanelBody, RadioControl, CheckboxControl, BaseControl} from '@wordpress/components';
import {useSelect, useDispatch} from '@wordpress/data';
import {useEffect} from '@wordpress/element';
import {PluginSidebar} from '@wordpress/edit-post';
import {LeiraAccessPanel} from './form';
import { Fragment } from '@wordpress/element';
import { InspectorControls, useBlockProps ,InspectorAdvancedControls } from '@wordpress/block-editor';
import { createHigherOrderComponent } from '@wordpress/compose';

/**
 * Leira Access Sidebar Component
 * This component adds a custom sidebar panel in the post editor that allows you to edit block access settings for each block
 *
 * @return {JSX.Element}
 * @constructor
 */
const LeiraAccessSidebar = () => {
	// Get meta value
	const meta = useSelect((select) => select('core/editor').getEditedPostAttribute('meta'));
	const {editPost} = useDispatch('core/editor');

	/**
	 * Render the component
	 */
	return <LeiraAccessPanel
		meta={meta}
		onChange={(key, value) => editPost({meta: {...meta, [key]: value}})}
	/>;
};

registerPlugin('leira-access-sidebar', {render: LeiraAccessSidebar});

// HOC to wrap each block
const withLeiraAccessPanel = createHigherOrderComponent((BlockEdit) => {
	return (props) => {
		const { attributes, setAttributes, isSelected } = props;

		if (!isSelected) return <BlockEdit {...props} />;

		return (
			<>
				<BlockEdit {...props} >
				</BlockEdit>
				<InspectorControls>
					<InspectorAdvancedControls>
						{/* This is the default Advanced panel */}
					</InspectorAdvancedControls>

					<PanelBody title="Access" initialOpen={false}>
						<p style={{ color: '#555d66', fontSize: 13, lineHeight: 1.4 }}>
							Control access to this block by user login status and role.
						</p>
					</PanelBody>
				</InspectorControls>
			</>
		);
	};
}, 'withLeiraAccessPanel');

// Apply HOC to all blocks
wp.hooks.addFilter(
	'editor.BlockEdit',
	'my-plugin/with-leira-access-panel',
	withLeiraAccessPanel
);
