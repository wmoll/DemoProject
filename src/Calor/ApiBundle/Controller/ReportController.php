<?php

namespace Calor\ApiBundle\Controller;

use Calor\ApiBundle\Entity\Meal;
use Calor\ApiBundle\Entity\User;
use Calor\ApiBundle\Form\UserLogin;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use JMS\Serializer\Exception\RuntimeException;
use Symfony\Component\HttpFoundation\Session\Session;
use Symfony\Component\HttpKernel\Exception\HttpException;
use Nelmio\ApiDocBundle\Annotation\ApiDoc;
use FOS\RestBundle\Controller\Annotations\View as RestView;
use FOS\RestBundle\Controller\Annotations\QueryParam;
use FOS\RestBundle\View\View;

/**
 * Report controller.
 *
 */
class ReportController extends ApiController
{

    /**
     * Get a Report data
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
     *          "user": {
     *              'id'=>'',
     *              'username'=>'',
     *              'target'=>''
     *          }
     *      }
     *
     * @ApiDoc(
     *     section="Report",
     *     resource=true
     * )
     *
     * @QueryParam(name="dateFrom", description="Date From")
     * @QueryParam(name="dateTo", description="Date to")
     *
     * @QueryParam(name="timeFrom", description="Time from")
     * @QueryParam(name="timeTo", description="Time to")
     */
    public function getAction($token, $mode, Request $request)
    {
        $em = $this->getEntityManager();

        /** @var User $user */
        if(($user = $em->getRepository("CalorApiBundle:Session")->isTokenValid($token))=== false){
            return View::create(array('errors' => 'Token not valid', 'status'=>'error'));
        }

        if(!in_array($mode, array('daily', 'monthly', 'yearly'))){
            return View::create(array('errors' => 'Unknown report mode', 'status'=>'error'));
        }



        $filter = array('user' => $user);

        if($request->get('dateFrom')!== null){
            $filter['date'] = array($request->get('dateFrom'), $request->get('dateTo', (new \DateTime('now'))->format('Y-m-d')));
        }

        if($request->get('timeFrom')!== null){
            $filter['time'] = array($request->get('timeFrom'), $request->get('timeTo', '23:59:59'));
        }


        $report = $em->getRepository('CalorApiBundle:Meal')->getReport($filter, $mode);

        return array('report' => $report);
    }


}
