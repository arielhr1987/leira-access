import {PluginDocumentSettingPanel} from '@wordpress/editor';
import {RadioControl, CheckboxControl, BaseControl} from '@wordpress/components';

/**
 * Leira Access Form Component
 *
 * @param {Object} props - The component props
 * @param {Object} props.meta The meta-data object
 * @param {function|null} props.onChange The meta-data object
 * @return {JSX.Element}
 * @constructor
 */
export const LeiraAccessPanel = ({meta, onChange = null, ...props}) => {
	/**
	 * @var {Object} window.leiraAccessData - The global Leira Access data object
	 * @var {Array} window.leiraAccessData.roles - The available user roles
	 */
	const selected = meta['_leira-access'] || ''; //The selected access option
	const roles = window.leiraAccessData?.roles || []; //The available user roles
	const selectedRoles = meta['_leira-access-roles'] || []; // The selected user roles if selected === 'in'

	/**
	 * Handle change of meta value
	 * @param {string} key - The key updated
	 * @param {string} value - The new value
	 */
	const handleChange = (key, value) => {
		if (onChange) {
			onChange(key, value);
		}
		// if (isBlockPanel) {
		// 	editBlockAttributes(clientId, {meta: {...meta, [key]: value}});
		// } else {
		// 	editPost({meta: {...meta, [key]: value}});
		// }
	};

	/**
	 * Render the component
	 */
	return (
		<PluginDocumentSettingPanel
			name="leira-access-panel"
			title="Access"
			className="leira-access-panel"
		>

			<BaseControl help="Control access to this content by user login status and role."/>

			<RadioControl
				selected={selected}
				onChange={(value) => handleChange('_leira-access', value)}
				options={[
					{label: 'Anyone', value: ''},
					{label: 'No one', value: 'none'},
					{label: 'Logged Out Users', value: 'out'},
					{label: 'Logged In Users', value: 'in'},
				]}
			/>

			{selected === 'in' && (
				<div style={{display: 'flex', flexDirection: 'column', gap: '0.75rem', margin: '.75rem 0 0 1.5rem'}}>
					<strong>Restrict by role:</strong>
					{roles.map((role) => (
						<CheckboxControl
							__nextHasNoMarginBottom={true}
							key={role.value}
							label={role.label}
							checked={selectedRoles.includes(role.value)}
							onChange={(checked) => {
								let newRoles = [];
								if(checked){
									//add to the list
									newRoles = [...selectedRoles, role.value]
								}else{
									//remove from the list
									newRoles = selectedRoles.filter((r) => r !== role.value);
								}
								newRoles = newRoles.filter(Boolean); // Remove empty values
								handleChange('_leira-access-roles', newRoles);
							}}
						/>
					))}
				</div>
			)}
		</PluginDocumentSettingPanel>
	);
};
