<?php

namespace Dat0r\Runtime\Entity\Transform;

use Dat0r\Common\Options;
use Dat0r\Runtime\Entity\IEntity;

interface ITransformation
{
    /**
     * Transform the entity value, which is described by the given attributespec,
     * to it's output representation.
     *
     * @param IEntity $entity
     * @param ISpecification $specification
     *
     * @return mixed
     */
    public function apply(IEntity $entity, ISpecification $specification);

    /**
     * Transform an incoming value, which is described by the given attributespec,
     * to it's input (entity compatible) representation and set result on the given entity.
     *
     * @param mixed $input_value
     * @param IEntity $entity
     * @param ISpecification $specification
     *
     * @return void
     */
    public function revert($input_value, IEntity $entity, ISpecification $specification);

    /**
     * @return Options
     */
    public function getOptions();
}
