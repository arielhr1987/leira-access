(function ($) {
    'use strict';

    /**
     * All of the code for your admin-facing JavaScript source
     * should reside in this file.
     *
     * Note: It has been assumed you will write jQuery code here, so the
     * $ function reference has been prepared for usage within the scope
     * of this function.
     *
     * This enables you to define handlers, for when the DOM is ready:
     *
     * $(function() {
     *
     * });
     *
     * When the window is loaded:
     *
     * $( window ).load(function() {
     *
     * });
     *
     * ...and/or other possibilities.
     *
     * Ideally, it is not considered best practise to attach more than a
     * single DOM-ready or window-load handler for a particular page.
     * Although scripts in the WordPress core, Plugins and Themes may be
     * practising this, we should strive to set a better example in our own work.
     */

})(jQuery);


// (function (wp) {

// var el = wp.element.createElement;
// var registerPlugin = wp.plugins.registerPlugin;
// var PluginDocumentSettingPanel = wp.editPost.PluginDocumentSettingPanel;
// var PluginSidebar = wp.editPost.PluginSidebar;
// var Text = wp.components.TextControl;
//
// const PluginDocumentSettingPanelDemo = function () {
//
//     return React.createElement(PluginDocumentSettingPanel, {
//         name: "custom-panel1",
//         title: "Custom Panel1",
//         className: "custom-panel1"
//     }, "Custom Panel Contents");
//
//     return el( PluginSidebar,
//         {
//             name: 'my-plugin-sidebar1',
//             icon: 'admin-post',
//             title: 'My plugin sidebar',
//         },
//         'Meta field'
//     );
//
//     return el(PluginDocumentSettingPanel, {
//             name: 'custom panel name',
//             title: 'custom panel title',
//             className: 'custom panel className',
//         },
//         el('div', {className: 'plugin-sidebar-content'})
//     );
//
//
// };
// registerPlugin('plugin-document-setting-panel-demo1', {render: PluginDocumentSettingPanelDemo, icon: 'palmtree'});

// })(window.wp);


// (function (wp) {
//     var Fragment = wp.element.Fragment;
//     var registerPlugin = wp.plugins.registerPlugin;
//     var _wp$editPost = wp.editPost,
//         PluginSidebar = _wp$editPost.PluginSidebar,
//         PluginSidebarMoreMenuItem = _wp$editPost.PluginSidebarMoreMenuItem;
//     var _wp$components = wp.components,
//         PanelRow = _wp$components.PanelRow,
//         PanelBody = _wp$components.PanelBody,
//         ToggleControl = _wp$components.ToggleControl;
//     var _wp$data = wp.data,
//         withSelect = _wp$data.withSelect,
//         withDispatch = _wp$data.withDispatch;
//     var compose = wp.compose.compose;
//
//     function MySidebarPlugin(props) {
//         var myPostMetaKey = props.myPostMetaKey,
//             updateMyPostMetaKey = props.updateMyPostMetaKey;
//         return React.createElement(Fragment, null, React.createElement(PluginSidebarMoreMenuItem, {
//             target: "my-sidebar-plugin",
//             icon: "hammer"
//         }, "mySidebarPlugin"), React.createElement(PluginSidebar, {
//             name: "my-sidebar-plugin",
//             icon: "hammer",
//             title: "My sidebar plugin"
//         }, React.createElement(PanelBody, {
//             opened: true,
//             className: "opened-panel-body",
//             title: "My postmeta panel"
//         }, React.createElement(PanelRow, null, React.createElement(ToggleControl, {
//             label: "My post meta key toggle",
//             checked: myPostMetaKey,
//             onChange: updateMyPostMetaKey
//         })))));
//     }
//
//     var applyWithSelect = withSelect(function (select) {
//         var _select = select('core/editor'),
//             getEditedPostAttribute = _select.getEditedPostAttribute;
//
//         var _getEditedPostAttribu = getEditedPostAttribute('meta'),
//             myPostMetaKey = _getEditedPostAttribu.my_post_meta_key;
//
//         return {
//             myPostMetaKey: myPostMetaKey
//         };
//     });
//     var applyWithDispatch = withDispatch(function (dispatch) {
//         var _dispatch = dispatch('core/editor'),
//             editPost = _dispatch.editPost;
//
//         return {
//             updateMyPostMetaKey: function updateMyPostMetaKey(value) {
//                 editPost({
//                     meta: {
//                         my_post_meta_key: value
//                     }
//                 });
//             }
//         };
//     });
//     registerPlugin('my-sidebar-plugin', {
//         render: compose(applyWithSelect, applyWithDispatch)(MySidebarPlugin)
//     });
// })(window.wp)


// (function (wp) {
//     var el = wp.element.createElement;
//     var registerPlugin = wp.plugins.registerPlugin;
//     var PluginPostStatusInfo = wp.editPost.PluginPostStatusInfo;
//     var CheckboxControl = wp.components.CheckboxControl;
//
//     var PluginPostStatusInfoTest = function PluginPostStatusInfoTest() {
//         var content = '<div class="leira-access-container">' +
//             '            <p class="">' +
//             'Visible to:            </p>' +
//             '' +
//             '            <input type="hidden" name="leira-access-nonce" value="80b4bbb1fb">' +
//             '' +
//             '            <input type="hidden" class="leira-access-item-id" value="meta-2">' +
//             '' +
//             '            <input type="radio" class="leira-access-status" name="leira-access-status[meta-2]" id="leira-access-for-meta-2" checked="checked" value="">' +
//             '            <label for="leira-access-for-meta-2">' +
//             'Everyone            </label>' +
//             '            <br>' +
//             '' +
//             '            <input type="radio" class="leira-access-status" name="leira-access-status[meta-2]" id="leira-access-out-for-meta-2" value="out">' +
//             '            <label for="leira-access-out-for-meta-2">' +
//             'Logged Out Users            </label>' +
//             '            <br>' +
//             '' +
//             '            <input type="radio" class="leira-access-status" name="leira-access-status[meta-2]" id="leira-access-in-for-meta-2" value="in">' +
//             '            <label for="leira-access-in-for-meta-2">' +
//             'Logged In Users            </label>' +
//             '' +
//             '            <div class="leira-access-roles">' +
//             '                <p class="">' +
//             'Restrict item to a minimum role                </p>' +
//             '' +
//             '                    <input type="checkbox" name="leira-access-role[meta-2][1]" id="leira-access-administrator-for-meta-2" value="administrator">' +
//             '                    <label for="leira-access-administrator-for-meta-2">' +
//             'Administrator                    </label>' +
//             '                    <br>' +
//             '' +
//             '                    <input type="checkbox" name="leira-access-role[meta-2][2]" id="leira-access-editor-for-meta-2" value="editor">' +
//             '                    <label for="leira-access-editor-for-meta-2">' +
//             'Editor                    </label>' +
//             '                    <br>' +
//             '' +
//             '                    <input type="checkbox" name="leira-access-role[meta-2][3]" id="leira-access-author-for-meta-2" value="author">' +
//             '                    <label for="leira-access-author-for-meta-2">' +
//             'Author                    </label>' +
//             '                    <br>' +
//             '' +
//             '                    <input type="checkbox" name="leira-access-role[meta-2][4]" id="leira-access-contributor-for-meta-2" value="contributor">' +
//             '                    <label for="leira-access-contributor-for-meta-2">' +
//             'Contributor                    </label>' +
//             '                    <br>' +
//             '' +
//             '                    <input type="checkbox" name="leira-access-role[meta-2][5]" id="leira-access-subscriber-for-meta-2" value="subscriber">' +
//             '                    <label for="leira-access-subscriber-for-meta-2">' +
//             'Subscriber                    </label>' +
//             '                    <br>' +
//             '' +
//             '' +
//             '            </div>' +
//             '        </div>';
//
//         content = el('div', {
//                 className: 'leira-access-container',
//             },
//             [el('p', {}, 'Visible to:')]);
//         return el(PluginPostStatusInfo, {}, content);
//     };
//
//     registerPlugin('post-status-info-test', {
//         render: PluginPostStatusInfoTest
//     });
// })(window.wp)