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
 * Session controller.
 *
 */
class SessionController extends ApiController
{

    /**
     * Authorize (log in)
     *
     * **Request Format**
     *
     *      {
     *          "username": "vladimir.mally",
     *          "password": "sample-password"
     *      }
     *
     * **Response Format**
     *
     *      {
     *          "token": "Cbm3zGm8V5IYAQ1KWPkyLpjpGOt6UgPR-kgKhHG_bC8"
     *      }
     *
     * @ApiDoc(
     *     section="Session"
     * )
     *
     * @QueryParam(name="username", nullable=false, description="User Name")
     * @QueryParam(name="password", nullable=false, description="Password")
     */
    public function postAction(Request $request)
    {
        /** @var UserLogin $loginInfo */
        $loginInfo = $this->deserialize('Calor\ApiBundle\Form\UserLogin', $request);

        if ($loginInfo instanceof UserLogin === false) {
            return View::create(array('errors' => $loginInfo, 'status'=>'error'));
        }


        $user_manager = $this->get('fos_user.user_manager');


        /** @var User $user */
        $user = $user_manager->findUserByUsernameOrEmail($loginInfo->username);
        if ($user == null) {
            return View::create(array('errors'=>array(array("property_path"=>"username", 'message' => 'Login not founded')), 'status'=>'error'));
        }


        $factory = $this->get('security.encoder_factory');
        $encoder = $factory->getEncoder($user);

        $bool = ($encoder->isPasswordValid($user->getPassword(), $loginInfo->password, $user->getSalt())) ? true : false;
        if ($bool !== true) {
            return View::create(array('errors'=>array(array("property_path"=>"password", 'message' => 'Password not correct')), 'status'=>'error'));
        }

        $tokenGenerator = $this->container->get('fos_user.util.token_generator');
        $session = new \Calor\ApiBundle\Entity\Session();
        $session->setUser($user);
        $session->setToken($tokenGenerator->generateToken());
        $session->setCreatedAt(new \DateTime());
        $session->setExpiresAt();
        $em = $this->getEntityManager();
        $em->persist($session);
        $em->flush();

        return View::create(array('token' => $session->getToken()));
    }


    /**
     * Update Session
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
     *     section="Session"
     * )
     */
    public function putAction($token)
    {
        $em = $this->getEntityManager();
        if($em->getRepository("CalorApiBundle:Session")->isTokenValid($token)){
            return View::create(array('error' => 'Authentication Timeout', 'status'=>'error'));
        }else{
            return View::create(array('token' => $token));
        }
    }

    /**
     * Delete a session (log out)
     *
     * @ApiDoc(
     *     section="Session"
     * )
     */
    public function deleteAction($token)
    {
        $em = $this->getEntityManager();
        /** @var UserRepository $userRepo */
        $userRepo = $em->getRepository('CalorApiBundle:User');
        /** @var User $user */
        $user = $userRepo->getUserByToken($token);

        if ($user === null) {
            return View::create(array('error' => 'Not found', 'status'=>'error'));
        }
        if($em->getRepository("CalorApiBundle:Session")->isTokenValid($token)){
            return View::create(array('error' => 'Authentication Timeout', 'status'=>'error'));
        }

        $session = $em->getRepository("CalorApiBundle:Session")->findOneBy('token', $token);
        $em = $this->getEntityManager();
        $em->remove($session);
        $em->flush();

        return View::create(array('status' => 'ok'));
    }

}
