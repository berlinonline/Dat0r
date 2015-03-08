<?php

namespace Dat0r\Tests\Runtime\Fixtures;

use Dat0r\Common\Options;
use Dat0r\Runtime\Attribute\Boolean\BooleanAttribute;
use Dat0r\Runtime\Attribute\EmbeddedEntityList\EmbeddedEntityListAttribute;
use Dat0r\Runtime\Attribute\Float\FloatAttribute;
use Dat0r\Runtime\Attribute\IntegerList\IntegerListAttribute;
use Dat0r\Runtime\Attribute\Integer\IntegerAttribute;
use Dat0r\Runtime\Attribute\KeyValueList\KeyValueListAttribute;
use Dat0r\Runtime\Attribute\TextList\TextListAttribute;
use Dat0r\Runtime\Attribute\Text\TextAttribute;
use Dat0r\Runtime\Attribute\Timestamp\TimestampAttribute;
use Dat0r\Runtime\Attribute\Uuid\UuidAttribute;
use Dat0r\Runtime\EntityType;

class ArticleType extends EntityType
{
    public function __construct()
    {
        parent::__construct(
            'Article',
            array(
                new UuidAttribute('uuid'),
                new TextAttribute('headline', array(TextAttribute::OPTION_MIN_LENGTH => 4)),
                new TextAttribute('content'),
                new IntegerAttribute('click_count'),
                new FloatAttribute('float'),
                new TextAttribute('author'),
                new TextAttribute('email'),
                new TimestampAttribute('birthday', [
                    TimestampAttribute::OPTION_DEFAULT_VALUE => '2015-01-29T09:18:28.534429+00:00'
                ]),
                new IntegerListAttribute('images'),
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
                        KeyValueListAttribute::OPTION_VALUE_TYPE => KeyValueListAttribute::VALUE_TYPE_SCALAR,
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
