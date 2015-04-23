<?php

namespace Calor\ApiBundle\Form;

use Symfony\Component\Validator\Constraints as Assert;
use JMS\Serializer\Annotation\Type;

class UserLogin
{
    /**
     * @Assert\NotBlank
     * @Assert\Regex("/^\w+$/")
     * @Type("string")
     * @var string
     */
    public $username;

    /**
     * @Assert\NotBlank
     * @Type("string")
     * @var string
     */
    public $password;

}