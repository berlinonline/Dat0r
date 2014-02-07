<?php

namespace Dat0r\Runtime\Field\Type;

use Dat0r\Runtime\Field\Field;
use Dat0r\Runtime\Document\DocumentList;
use Dat0r\Runtime\Validator\Rule\Type\ReferenceRule;
use Dat0r\Runtime\Validator\Rule\RuleList;

class ReferenceField extends Field
{
    const OPT_MAX_REFERENCES = 'max';

    const OPT_REFERENCES = 'references';

    const OPT_MODULE = 'module';

    const OPT_DISPLAY_FIELD = 'display_field';

    const OPT_IDENTITY_FIELD = 'identity_field';

    protected $referenced_modules;

    public function getDefaultValue()
    {
        return DocumentList::create();
    }

    public function getReferencedModules()
    {
        if (!$this->referenced_modules) {
            $this->referenced_modules = array();
            foreach ($this->getOption(self::OPT_REFERENCES) as $reference) {
                $module_class = $reference[self::OPT_MODULE];
                $this->referenced_modules[] = $module_class::getInstance();
            }
        }

        return $this->referenced_modules;
    }

    public function getReferencedModuleByPrefix($prefix)
    {
        foreach ($this->getReferencedModules() as $module) {
            if ($module->getPrefix() === $prefix) {
                return $module;
            }
        }

        return null;
    }

    public function getReferencedModuleByName($name)
    {
        foreach ($this->getReferencedModules() as $module) {
            if ($module->getName() === $name) {
                return $module;
            }
        }

        return null;
    }

    protected function buildValidationRules()
    {
        $rules = new RuleList();
        $rules->push(
            new ReferenceRule(
                'valid-reference',
                array('reference_mappings' => $this->getOption(self::OPT_REFERENCES))
            )
        );

        return $rules;
    }
}
