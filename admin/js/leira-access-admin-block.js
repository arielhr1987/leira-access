"use strict";

(function () {
    var __ = wp.i18n.__;
    var el = wp.element.createElement;

    /**
     * Add spacing control attribute to block.
     *
     * @param {object} settings Current block settings.
     * @param {string} name Name of block.
     *
     * @returns {object} Modified block settings.
     */
    var addSpacingControlAttribute = function (settings, name) {
        // Do nothing if it's another block than our defined ones.
        // if (name == "@core/block") {
        //     return settings;
        // }

        if (settings.attributes) {
            settings.attributes['_leira-access'] = {
                type: 'string',
                default: '',
            };
        }

        return settings;
    };

    wp.hooks.addFilter('blocks.registerBlockType', 'leira-access/attribute/access', addSpacingControlAttribute);

    /**
     * Control for access
     */
    var LeiraAccessControl = wp.compose.withInstanceId(function (options) {

        var value = options.value;
        var roleElements = [];
        var roles = {
            administrator: "Administrator",
            member: "Member",
            editor: "Editor"
        };
        var roles = wp['_leira-access'].roles;

        if (Array.isArray(value) || value === 'in') {
            roleElements.push(el('p', {}, __("Restrict access to a minimum role.")));
            Object.keys(roles).map(function (role) {
                var roleName = roles[role];
                var id = 'inspector-checkbox-control-'.concat(options.instanceId, '-').concat(role);
                var isRoleChecked = false;

                if (Array.isArray(value) && value.includes(role)) {
                    isRoleChecked = true;
                }

                roleElements.push(el(
                    'div',
                    {
                        className: 'components-base-control'
                    },
                    el(
                        'div',
                        {
                            className: 'components-base-control__field',
                        },
                        el(
                            'input',
                            {
                                id: id,
                                className: "components-checkbox-control__input",
                                type: 'checkbox',
                                value: role,
                                label: roleName,
                                checked: isRoleChecked,
                                onChange: function (item) {
                                    options.onChange(item.target)
                                }
                            }
                        ),
                        el(
                            'label',
                            {
                                className: 'components-checkbox-control__label',
                                htmlFor: id
                            },
                            roleName
                        )
                    )
                ));
            });
            value = 'in';
        }

        return el(
            wp.element.Fragment,
            {},
            el(
                wp.components.RadioControl,
                {
                    //label: "User Access",
                    //help: "Access for this block",
                    selected: value,
                    options: [
                        {label: __('Everyone'), value: ''},
                        {label: __('Logged Out Users'), value: 'out'},
                        {label: __('Logged In Users'), value: 'in'}
                    ],
                    onChange: function (option) {
                        //wp.compose.withState.setState({option});
                        options.onChange(option)
                    }
                }
            ),
            el(
                'div',
                {
                    className: 'leira-access-roles'
                },
                roleElements
            )
        )
    });

    /**
     * Create HOC to add access control to inspector controls of block.
     */
    var hocLeiraAccessControl = wp.compose.createHigherOrderComponent(function (BlockEdit) {

        return function (props) {

            var value = '';
            if (props.attributes && props.attributes && props.attributes.hasOwnProperty('_leira-access')) {
                value = props.attributes['_leira-access'];
            }

            return el(
                wp.element.Fragment,
                {},
                el(
                    BlockEdit,
                    props
                ),
                el(
                    wp.editor.InspectorControls,
                    {},
                    el(
                        wp.components.PanelBody,
                        {
                            title: __("Access"),
                            initialOpen: false,
                            className: 'leira-access-container-block'
                        },
                        el(
                            LeiraAccessControl,
                            {
                                value: value,
                                onChange: function (item) {
                                    var access = props.attributes['_leira-access'];

                                    var value = '';
                                    if (item.value) {
                                        //its a role
                                        if (item.checked) {
                                            if (Array.isArray(access)) {
                                                access.push(item.value)

                                            } else {
                                                access = [item.value];
                                            }
                                        } else {
                                            //remove unchecked roles
                                            access = access.filter(function (value, index, arr) {
                                                return value !== item.value;
                                            });
                                        }
                                        access = access.filter(function (item, index) {
                                            return access.indexOf(item) === index;
                                        });
                                        if (!access.length) {
                                            //no roles selected
                                            access = 'in';
                                        }
                                        value = access;
                                    } else {
                                        //its a status ['', 'out', 'in']
                                        value = item;
                                    }

                                    props.setAttributes({
                                        "_leira-access": value
                                    });
                                }
                            }
                        )
                    )
                )
            );
        };
    }, 'hocLeiraAccessControl');

    wp.hooks.addFilter('editor.BlockEdit', 'leira-access/block-leira-access-control', hocLeiraAccessControl);

})();

