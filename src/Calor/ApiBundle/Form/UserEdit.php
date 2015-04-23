<?php

namespace Calor\ApiBundle\Form;

use Symfony\Component\Validator\Constraints as Assert;
use JMS\Serializer\Annotation\Type;

class UserEdit
{
    /**


     * @Type("string")
     * @var string
     */
    public $displayName;

    /**
     * @Assert\Range(min=0, minMessage="Goal must be >= 0")
     * @Type("string")
     * @var string
     */
    public $target;

}