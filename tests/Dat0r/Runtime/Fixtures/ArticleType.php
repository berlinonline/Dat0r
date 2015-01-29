<?php

namespace Dat0r\Tests\Runtime\Fixtures;

use Dat0r\Common\Options;
use Dat0r\Runtime\EntityType;
use Dat0r\Runtime\Attribute\Text\TextAttribute;
use Dat0r\Runtime\Attribute\TextList\TextListAttribute;
use Dat0r\Runtime\Attribute\Number\NumberAttribute;
use Dat0r\Runtime\Attribute\NumberList\NumberListAttribute;
use Dat0r\Runtime\Attribute\Boolean\BooleanAttribute;
use Dat0r\Runtime\Attribute\EmbeddedEntityList\EmbeddedEntityListAttribute;
use Dat0r\Runtime\Attribute\KeyValueList\KeyValueListAttribute;

class ArticleType extends EntityType
{
    public function __construct()
    {
        parent::__construct(
            'Article',
            array(
                new TextAttribute('headline', array('min' => 4)),
                new TextAttribute('content'),
                new NumberAttribute('click_count'),
                new TextAttribute('author'),
                new TextAttribute('email'),
                new NumberListAttribute('images'),
                new TextListAttribute('keywords'),
                new BooleanAttribute('enabled'),
                new EmbeddedEntityListAttribute(
                    'content_objects',
                    array(
                        EmbeddedEntityListAttribute::OPTION_ENTITY_TYPES => array(ParagraphType::CLASS),
                    )
                ),
                new KeyValueListAttribute(
                    'meta',
                    array(
                        'constraints' => array('value_type' => 'dynamic',),
                    )
                ),
                new EmbeddedEntityListAttribute(
                    'workflow_ticket',
                    array(
                        EmbeddedEntityListAttribute::OPTION_ENTITY_TYPES => array(WorkflowTicketType::CLASS)
                    )
                )
            ),
            new Options(
                array(
                    'foo' => 'bar',
                    'nested' => array(
                        'foo' => 'bar',
                        'blah' => 'blub'
                    )
                )
            )
        );
    }

    protected function getEntityImplementor()
    {
        return Article::CLASS;
    }
}
