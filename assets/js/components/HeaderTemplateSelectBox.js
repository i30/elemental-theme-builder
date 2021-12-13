import {__} from '@wordpress/i18n';
import {SelectControl} from '@wordpress/components';
import {withSelect, withDispatch} from '@wordpress/data';

function SelectBox({selectedTemplate, onUpdate}) {
	return (
		<SelectControl
			label={__('Elementor Header Template:', 'elemental-theme-builder')}
			value={selectedTemplate}
			onChange={onUpdate}
			className="editor-page-attributes__template"
			options={elementorSiteBuilderData.headerTemplates}
		/>
	)
}

const SelectedSelectBox = withSelect((select) => {
  return {
    selectedTemplate: select('core/editor').getEditedPostAttribute('meta')['elemental_theme_builder_template_siteheader']
  }
})(SelectBox);

const DispatchedSelectBox = withDispatch((dispatch) => {
    return {
      onUpdate: function(templateSlug) {
        dispatch('core/editor').editPost({
          meta: {
            'elemental_theme_builder_template_siteheader': templateSlug || ''
          }
        });
      }
    }
  }
)(SelectedSelectBox);

export default DispatchedSelectBox;
