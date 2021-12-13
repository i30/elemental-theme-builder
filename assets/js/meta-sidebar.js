import {__} from '@wordpress/i18n';
import {registerPlugin} from '@wordpress/plugins';
import {PluginSidebar} from '@wordpress/edit-post';
import {PageAttributesCheck} from '@wordpress/editor';
import {PanelBody} from '@wordpress/components';
import HeaderTemplateSelectBox from './components/HeaderTemplateSelectBox';
import FooterTemplateSelectBox from './components/FooterTemplateSelectBox';

registerPlugin('elemental-theme-builder-plugin', {
    icon: 'screenoptions',
    render: () => (
        <PluginSidebar name="elemental-theme-builder-layout-settings" title={__('Layout Settings', 'elemental-theme-builder')}>
            <PanelBody>
                <p>{__('"Inherit" will display the template assigned for "Entire Site". "Theme Default" will display the template from the current theme.', 'elemental-theme-builder')}</p>
                <HeaderTemplateSelectBox />
                <FooterTemplateSelectBox />
            </PanelBody>
        </PluginSidebar>
    )
});
