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
            [
                new UuidAttribute('uuid', $this),
                new TextAttribute('headline', $this, [ TextAttribute::OPTION_MIN_LENGTH => 4 ]),
                new TextAttribute('content', $this),
                new IntegerAttribute('click_count', $this),
                new FloatAttribute('float', $this),
                new TextAttribute('author', $this),
                new TextAttribute('email', $this),
                new TimestampAttribute(
                    'birthday',
                    $this,
                    [
                        TimestampAttribute::OPTION_DEFAULT_VALUE => '2015-01-29T09:18:28.534429+00:00'
                    ]
                ),
                new IntegerListAttribute('images', $this),
                new TextListAttribute('keywords', $this),
                new BooleanAttribute('enabled', $this),
                new EmbeddedEntityListAttribute(
                    'content_objects',
                    $this,
                    [
                        EmbeddedEntityListAttribute::OPTION_ENTITY_TYPES => [ ParagraphType::CLASS ],
                    ]
                ),
                new KeyValueListAttribute(
                    'meta',
                    $this,
                    [
                        KeyValueListAttribute::OPTION_VALUE_TYPE => KeyValueListAttribute::VALUE_TYPE_SCALAR,
                    ]
                ),
                new EmbeddedEntityListAttribute(
                    'workflow_state',
                    $this,
                    [
                        EmbeddedEntityListAttribute::OPTION_ENTITY_TYPES => [ WorkflowStateType::CLASS ]
                    ]
                )
            ],
            new Options(
                [
                    'foo' => 'bar',
                    'nested' => [
                        'foo' => 'bar',
                        'blah' => 'blub'
                    ]
                ]
            )
        );
    }

    public function getEntityImplementor()
    {
        return Article::CLASS;
    }
}
