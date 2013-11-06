<?php

namespace Dat0r\CodeGen\Config;

interface IConfig
{
    const DEPLOY_COPY = 'copy';

    const DEPLOY_MOVE = 'move';

    public function getCacheDir();

    public function getDeployDir();

    public function getDeployMethod();

    public function getPluginSettings();

    public function validate();
}
