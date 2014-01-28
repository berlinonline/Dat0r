<?php

namespace Dat0r\CodeGen\ClassBuilder\Common;

class BaseDocumentClassBuilder extends DocumentClassBuilder
{
    protected function getTemplate()
    {
        return 'Document/BaseDocument.twig';
    }

    protected function getPackage()
    {
        return $this->module_schema->getPackage() . '\\Base';
    }

    protected function getParentImplementor()
    {
        $parent_class = $this->module_definition->getDocumentImplementor();
        if (!$parent_class) {
            $parent_class = sprintf('\\%s\\Document', self::NS_DOCUMENT);
        }

        return $parent_class;
    }

    protected function getTemplateVars()
    {
        $document_class_vars = array('fields' => $this->prepareFieldsData());

        return array_merge(parent::getTemplateVars(), $document_class_vars);
    }

    protected function prepareFieldsData()
    {
        $fields_data = array();

        foreach ($this->module_definition->getFields() as $field_definition) {
            $fieldname = $field_definition->getName();
            $fieldname_studlycaps = preg_replace('/(?:^|_)(.?)/e', "strtoupper('$1')", $fieldname);
            $field_getter = 'get' . $fieldname_studlycaps;
            $field_setter = 'set' . $fieldname_studlycaps;

            $fields_data[] = array(
                'name' => $fieldname,
                'description' => $field_definition->getDescription(),
                'setter' => $field_setter,
                'getter' => $field_getter,
                'php_type' => 'mixed' // @todo map to php-type
            );
        }

        return $fields_data;
    }
}
