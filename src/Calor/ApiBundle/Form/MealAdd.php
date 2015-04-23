<?php

namespace Calor\ApiBundle\Form;


use \Calor\ApiBundle\Entity\Meal;
use Symfony\Component\Validator\Constraints as Assert;
use JMS\Serializer\Annotation\Type;

class MealAdd
{
    /**
     * @Assert\NotBlank
     * @Assert\Date
     * @Type("string")
     * @var string
     */
    public $date;

    /**
     * @Assert\NotBlank
     * @Assert\Time
     * @Type("string")
     * @var string
     */
    public $time;

    /**
     * @Assert\NotBlank
     * @Assert\Length(min = "2", max = "50")
     * @Type("string")
     * @var string
     */
    public $name;

    /**
     * @Assert\NotBlank
     * @Assert\Range(min=0, minMessage="Calories must be >= 0")
     * @Type("string")
     * @var string
     */
    public $calories;

    public function getMeal()
    {
        /** @var Meal $meal */
        $meal = new Meal();
        $meal->setName($this->name);
        $meal->setDate( new \DateTime($this->date));
        $meal->setTime( new \DateTime($this->time));
        $meal->setCalories($this->calories);
        return $meal;
    }

    public function updateMeal(Meal $meal){
        $meal->setName($this->name);
        $meal->setDate( new \DateTime($this->date));
        $meal->setTime( new \DateTime($this->time));
        $meal->setCalories($this->calories);
        return $meal;
    }
}