<?php

require_once TOOLKIT . '/class.datasource.php';
require_once FACE . '/interface.datasource.php';

class AssociationOutput extends DataSource implements iDatasource
{
    public static function getName()
    {
        return __('Association Output');
    }

    public static function getTemplate()
    {

    }

    public function getSource()
    {

    }

    public function settings()
    {

    }

    public static function validate(array &$settings, array &$errors)
    {

    }

    public static function prepare(array $fields, array $parameters, $template) 
    {

    }

/*-------------------------------------------------------------------------
    Editor
-------------------------------------------------------------------------*/

    public static function buildEditor(XMLElement $wrapper, array &$errors = array(), array $settings = null, $handle = null)
    {
        $fieldset = new XMLElement('fieldset');
        $fieldset->setAttribute('class', 'settings association-output');
        $fieldset->setAttribute('data-context', 'sections');
        $fieldset->appendChild(new XMLElement('legend', __('Associated Content')));

        $options = array();
        $sections = SectionManager::fetch();
        foreach ($sections as $section) {
            $section_id = $section->get('id');
            $section_handle = $section->get('handle');
            $associations = SectionManager::fetchParentAssociations($section_id);

            if (!empty($associations)) {
                foreach ($associations as $association) {
                    $elements = array();
                    $fields = FieldManager::fetch(null, $association['parent_section_id']);
                    foreach ($fields as $field) {
                        $elements[] = array($field->get('element_name'), false);
                    }

                    $options[] = array(
                        'label' => FieldManager::fetchHandleFromID($association['child_section_field_id']),
                        'data-label' => $section_id,
                        'options' => $elements
                    );
                }
            }
        }

        $label = Widget::Label(__('Included Associations'));
        $select = Widget::Select('fields[includedassociations]', $options, array('multiple' => 'multiple'));
        $label->appendChild($select);

        $fieldset->appendChild($label);
        $wrapper->appendChild($fieldset);
    }
}

return 'AssociationOutput';
