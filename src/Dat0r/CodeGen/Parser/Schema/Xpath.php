<?php

namespace Dat0r\CodeGen\Parser\Schema;

use DOMDocument;
use DOMXpath;
use DOMNode;

/**
 * The Xpath class is a conveniece wrapper around DOMXpath and simple adds a namespace prefix to queries.
 */
class Xpath extends DOMXpath
{
    /**
     * @var string $default_namespace
     */
    protected $document_namespace;

    /**
     * Creates a new xpath instance that will use the given 'namespace_prefix' when querying the given document.
     *
     * @param DOMDocument $document
     */
    public function __construct(DOMDocument $document, $namespace_prefix = null)
    {
        parent::__construct($document);

        $this->initNamespace($document, $namespace_prefix);
    }

    /**
     * Takes an xpath expression and preprends the parser's namespace prefix to each xpath segment.
     * Then it runs the namespaced expression and returns the result.
     * Example: '//state_machines/state_machine' - expands to -> '//prefix:state_machines/prefix:state_machine'
     *
     * @param string $expression Non namespaced xpath expression.
     * @param DOMNode $context Allows to pass a context node that is used for the actual xpath query.
     *
     * @return DOMNodeList
     */
    public function query($expression, DOMNode $context = null, $register_ns = null)
    {
        if($this->hasNamespace()) {
            $search = [ '~/(\w+)~', '~^(\w+)$~' ];
            $replace = [ sprintf('/%s:$1', $this->namespace_prefix), sprintf('%s:$1', $this->namespace_prefix) ];
            $expression = preg_replace($search, $replace, $expression);
        }

        return parent::query($expression, $context, $register_ns);
    }

    /**
     * Get the namespace of the document, if defined.
     *
     * @param DOMDocument $document     Document to query on
     *
     * @return string
     */
    protected function initNamespace(DOMDocument $document, $namespace_prefix = null)
    {
        $this->document_namespace = trim($document->documentElement->namespaceURI);
        $namespace_prefix = trim($namespace_prefix);

        if($this->hasNamespace()) {
            $this->namespace_prefix = empty($namespace_prefix) ? $this->getDefaultNamespacePrefix() : $namespace_prefix;

            $this->registerNamespace(
                $this->namespace_prefix,
                $this->document_namespace
            );
        }
    }

    protected function hasNamespace()
    {
        return !empty($this->document_namespace);
    }

    /**
     * Returns the default namespace prefix to use when running xpath queries.
     *
     * @return string
     */
    protected function getDefaultNamespacePrefix()
    {
        return 'dt';
    }
}
