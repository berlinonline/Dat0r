<?php

namespace Dat0r\Runtime\Field;

use Dat0r\Runtime\Document\DocumentList;

/**
 * Concrete implementation of the Field base class.
 * Stuff in here is dedicated to handling references to documents.
 *
 * @copyright BerlinOnline Stadtportal GmbH & Co. KG
 * @author Thorsten Schmitt-Rink <tschmittrink@gmail.com>
 */
class ReferenceField extends Field
{
    const OPT_MAX_REFERENCES = 'max';

    const OPT_REFERENCES = 'references';

    const OPT_MODULE = 'module';

    const OPT_DISPLAY_FIELD = 'display_field';

    const OPT_IDENTITY_FIELD = 'identity_field';

    public function getDefaultValue()
    {
        return DocumentList::create();
    }

    public function getReferencedModules()
    {
        $referenced_modules = array();

        foreach ($this->getOption(self::OPT_REFERENCES) as $reference) {
            $moduleClass = $reference[self::OPT_MODULE];
            $referenced_modules[] = $moduleClass::getInstance();
        }

        return $referenced_modules;
    }
}
