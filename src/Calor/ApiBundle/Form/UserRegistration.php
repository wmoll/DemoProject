<?php

namespace Calor\ApiBundle\Form;

use Calor\ApiBundle\Entity\User;
use Symfony\Component\Validator\Constraints as Assert;
use JMS\Serializer\Annotation\Type;

class UserRegistration
{
    /**
     * @Assert\NotBlank
     * @Assert\Regex("/^\w+$/")
     * @Assert\Length(min = "2", max = "50")
     * @Type("string")
     * @var string
     */
    public $username;

    /**
     * @Assert\NotBlank
     * @Type("string")
     * @Assert\Length(min = "2", max = "50")
     * @var string
     */
    public $displayName;

    /**
     * @Assert\Email
     * @Assert\NotBlank
     * @Type("string")
     * @var string
     */
    public $email;

    /**
     * @Assert\NotBlank
     * @Type("string")
     * @Assert\Length(min = "6", max = "50")
     * @var string
     */
    public $password;

    public function getUser()
    {
        /** @var User $user */
        $user = new User();
        $user->setUsername($this->username);
        $user->setDisplayName($this->displayName);
        $user->setEmail($this->email);
        $user->setPlainPassword($this->password);
        $user->setEnabled(true);
        return $user;
    }
}