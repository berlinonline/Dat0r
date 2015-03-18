<?php

namespace Dat0r\CodeGen\Parser\Schema;

use DOMDocument;
use DOMAttr;

/**
 * The Xpath class is a conveniece wrapper around DOMDocument to support xinclude from other directories
 */
class Document extends DOMDocument
{
    public function xinclude($options = NULL)
    {
        $return = parent::xinclude($options);

        // Remove xml:base attribute, auto-appended when xincluding resources with different URI
        $xpath = new Xpath($this);

        $nodes = $xpath->query('//@xml:base', $this);
        foreach ($nodes as $node) {
            $node->ownerElement->removeAttribute($node->nodeName);
        }
        return $return;
    }
}