<?php

namespace Dat0r\Runtime\Entity\Transform;

use Dat0r\Common\Options;
use Dat0r\Runtime\Entity\IEntity;

interface ITransformer
{
    /**
     * @param IEntity $entity
     * @param ISpecificationContainer $spec_container
     *
     * @return array
     */
    public function transform(IEntity $entity, ISpecificationContainer $spec_container);

    /**
     * @param array $data
     * @param IEntity $entity
     * @param ISpecificationContainer $spec_container
     *
     * @return void
     */
    public function transformBack(array $data, IEntity $entity, ISpecificationContainer $spec_container);

    /**
     * @return Options
     */
    public function getOptions();
}
