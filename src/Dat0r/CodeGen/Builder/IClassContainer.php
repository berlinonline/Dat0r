<?php

namespace Dat0r\CodeGen\Builder;

interface IClassContainer
{
    public function getFilename();

    public function getNamespace();

    public function getPackage();

    public function getClassname();

    public function getSourceCode();
}
