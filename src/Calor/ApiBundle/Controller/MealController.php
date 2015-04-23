<?php

namespace Calor\ApiBundle\Controller;

use Calor\ApiBundle\Entity\Meal;
use Calor\ApiBundle\Entity\User;
use Calor\ApiBundle\Form\MealAdd;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use JMS\Serializer\Exception\RuntimeException;
use Symfony\Component\HttpKernel\Exception\HttpException;
use Nelmio\ApiDocBundle\Annotation\ApiDoc;
use FOS\RestBundle\Controller\Annotations\View as RestView;
use FOS\RestBundle\Controller\Annotations\QueryParam;
use FOS\RestBundle\View\View;

/**
 * Meal controller.
 *
 */
class MealController extends ApiController
{

    /**
     * Add new meal
     *
     * **Request Format**
     *
     *      "token":"token"
     *      {
     *          "date": "",
     *          "time": "",
     *          "name": "",
     *          "calories": ""
     *      }
     *
     * **Response Format**
     *
     *      {
     *          "meal": {
     *
     *          }
     *      }
     *
     * @ApiDoc(
     *     section="Meals"
     * )
     *
     * @QueryParam(name="date", nullable=false, description="Date")
     * @QueryParam(name="time", nullable=false, description="Time")
     * @QueryParam(name="name", nullable=false, description="Name")
     * @QueryParam(name="calories", nullable=false, description="Calories")
     */
    public function postAction($token, Request $request)
    {
        $em = $this->getEntityManager();

        /** @var mealAdd $mealAdd */
        $mealAdd = $this->deserialize('Calor\ApiBundle\Form\MealAdd', $request);
        if ($mealAdd instanceof MealAdd === false) {
            return View::create(array('errors' => $mealAdd, 'status'=>'error'));
        }

        if(($user = $em->getRepository("CalorApiBundle:Session")->isTokenValid($token))=== false){
            return View::create(array('errors' => 'Token not valid', 'status'=>'error'));
        }

        $meal = $mealAdd->getMeal();


        $meal->setUser($user);
        $em->persist($meal);
        $em->flush();

        return View::create(array('meal' => $meal));

    }

    /**
     * Update Meal
     *
     * **Request Format**
     *
     *      {
     *          "token": "Cbm3zGm8V5IYAQ1KWPkyLpjpGOt6UgPR-kgKhHG_bC8"
     *      }
     *
     * **Response Format**
     *
     *      {
     *          "token": "Cbm3zGm8V5IYAQ1KWPkyLpjpGOt6UgPR-kgKhHG_bC8"
     *      }
     *
     * @ApiDoc(
     *     section="Meals"
     * )
     *
     * @QueryParam(name="date", nullable=false, description="Date")
     * @QueryParam(name="time", nullable=false, description="Time")
     * @QueryParam(name="name", nullable=false, description="Name")
     * @QueryParam(name="calories", nullable=false, description="Calories")
     */
    public function putAction($token, $id, Request $request)
    {

        $em = $this->getEntityManager();

        /** @var mealAdd $mealAdd */
        $mealAdd = $this->deserialize('Calor\ApiBundle\Form\MealAdd', $request);
        if ($mealAdd instanceof MealAdd === false) {
            return View::create(array('errors' => $mealAdd, 'status'=>'error'));
        }

        /** @var User $user */
        if(($user = $em->getRepository("CalorApiBundle:Session")->isTokenValid($token))=== false){
            return View::create(array('errors' => 'Token not valid', 'status'=>'error'));
        }

        $meal = $em->getRepository("CalorApiBundle:Meal")->findOneBy(array('id'=>$id, 'user'=>$user->getId()));

        if($meal === null){
            return View::create(array('errors' => 'Meal not found', 'status'=>'error'));
        }

        $meal = $mealAdd->updateMeal($meal);
        $em->persist($meal);
        $em->flush();

        return View::create(array('meal' => $meal));

    }


    /**
     * Get a meal's list
     *
     * **Request Url**
     *
     *      /Cbm3zGm8V5IYAQ1KWPkyLpjpGOt6UgPR-kgKhHG_bC8/1/25
     *
     * **Response Format**
     *
     *      {
     *      "total": 13,
     *      "page": "1",
     *      "list": [
     *          {
     *              "date": "2001-02-02",
     *              "time": "10:33:33",
     *              "name": "123",
     *              "calories": 25
     *          }...
     *
     * @ApiDoc(
     *     section="Meals",
     *     resource=true
     * )
     * @QueryParam(name="dateFrom", nullable=false, description="Date from")
     * @QueryParam(name="dateTo", nullable=false, description="Date to")
     * @QueryParam(name="timeFrom", nullable=false, description="Time from")
     * @QueryParam(name="timeTo", nullable=false, description="Time to")
     * @QueryParam(name="name", nullable=false, description="Name")
     */
    public function getListAction($token, $page=1, $perPage=25, Request $request)
    {
        $em = $this->getEntityManager();

        /** @var User $user */
        if(($user = $em->getRepository("CalorApiBundle:Session")->isTokenValid($token))=== false){
            return View::create(array('errors' => 'Token not valid', 'status'=>'error'));
        }

        $filter = array('user' => $user);

        if($request->get('dateFrom')!== null){
            $filter['date'] = array($request->get('dateFrom'), $request->get('dateTo', (new \DateTime('now'))->format('Y-m-d')));
        }

        if($request->get('timeFrom')!== null){
            $filter['time'] = array($request->get('timeFrom'), $request->get('timeTo', '23:59:59'));
        }

        if($request->get('name')!== null){$filter['name'] = $request->get('name');}

        $mealsList = $em->getRepository('CalorApiBundle:Meal')->getList($filter, $page, $perPage);


        return $mealsList;
    }


    /**
     * Delete a Meal
     *
     * @ApiDoc(
     *     section="Meals"
     * )
     */
    public function deleteAction($token, $id)

    {


        $em = $this->getEntityManager();

        /** @var User $user */
        if(($user = $em->getRepository("CalorApiBundle:Session")->isTokenValid($token))=== false){
            return View::create(array('errors' => 'Token not valid', 'status'=>'error'));
        }

        $meal = $em->getRepository("CalorApiBundle:Meal")->findOneBy(array('id'=>$id, 'user'=>$user->getId()));


        if($meal === null){
            return View::create(array('errors' => 'Meal not found', 'status'=>'error'));
        }


        $em->remove($meal);
        $em->flush();
    }
}
