<?php

namespace Calor\ApiBundle\Controller;

use Calor\ApiBundle\Entity\Session;
use Calor\ApiBundle\Entity\User;
use Calor\ApiBundle\Entity\UserRepository;
use Calor\ApiBundle\Form\UserEdit;
use Calor\ApiBundle\Form\UserRegistration;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use JMS\Serializer\Exception\RuntimeException;
use Symfony\Component\HttpKernel\Exception\HttpException;
use Nelmio\ApiDocBundle\Annotation\ApiDoc;
use FOS\RestBundle\Controller\Annotations\View as RestView;
use FOS\RestBundle\Controller\Annotations\QueryParam;
use FOS\RestBundle\View\View;

/**
 * User controller.
 *
 */
class UserController extends ApiController
{

    /**
     * Get a User info
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
     *              'displayName'=>'',
     *              'target'=>''
     *          }
     *      }
     *
     * @ApiDoc(
     *     section="Users",
     *     resource=true
     * )
     */
    public function getAction($token)
    {
        $em = $this->getEntityManager();
        /** @var UserRepository $userRepo */
        $userRepo = $em->getRepository('CalorApiBundle:User');
        /** @var User $user */
        $user = $userRepo->getUserByToken($token);

        if ($user === null) {
            return View::create(array('error' => 'Not found', 'status'=>'error'));
        }

        if($em->getRepository("CalorApiBundle:Session")->isTokenValid($token) === false){
            return View::create(array('error' => 'Authentication Timeout', 'status'=>'error'));
        }

        return array('user' => $user);
    }


    /**
     * Register as a user in the application
     *
     * **Request Format**
     *
     *      {
     *          "username": "vladimir.mally",
     *          "email": "vladimir.mally@gmail.com",
     *          "password": "sample-password"
     *      }
     *
     * **Response Headers**
     *
     *      Location: http://example.com/api/v1/{token}
     *
     * @ApiDoc(
     *     section="Users"
     * )
     *
     * @QueryParam(name="displayName", nullable=false, description="Display Name")
     * @QueryParam(name="username", nullable=false, description="User Name")
     * @QueryParam(name="email", nullable=false, description="Email")
     * @QueryParam(name="password", nullable=false, description="Password")
     */
    public function postAction(Request $request)
    {

        $registration = $this->deserialize('Calor\ApiBundle\Form\UserRegistration', $request);
        if ($registration instanceof UserRegistration === false) {
            return View::create(array('errors' => $registration, 'status'=>'error'));
        }
        /** @var UserRegistration $registration */
        $user = $registration->getUser();

        /** @var User $user */
        $userManager = $this->get('fos_user.user_manager');
        $exists = $userManager->findUserBy(array('username' => $user->getUsername()));
        if ($exists instanceof User) {
            return View::create(array('errors'=>array(array("property_path"=>"username", 'message' => 'Username already taken')), 'status'=>'error'));
        }
        $exists = $userManager->findUserBy(array('email' => $user->getEmail()));
        if ($exists instanceof User) {
            return View::create(array('errors'=>array(array("property_path"=>"email", 'message' => 'Email already taken')),'status'=>'error'));
        }

        $userManager->updateUser($user);

        $tokenGenerator = $this->container->get('fos_user.util.token_generator');
        $session = new Session();
        $session->setUser($user);
        $session->setToken($tokenGenerator->generateToken());
        $session->setCreatedAt(new \DateTime());
        $session->setExpiresAt();
        $em = $this->getEntityManager();
        $em->persist($session);
        $em->flush();

        $url = $this->generateUrl(
            'user_get',
            array('token' => $session->getToken()),
            true
        );
        $response = new Response();
        $response->setStatusCode(201);
        $response->headers->set('Location', $url);
        return $response;
    }


    /**
     * Update a User profile
     *
     * **Request Format**
     *
     *      {
     *          "token": "Cbm3zGm8V5IYAQ1KWPkyLpjpGOt6UgPR-kgKhHG_bC8"
     *          "displayName": "vladimir",
     *          "target": "1200"
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
     *     section="Users",
     *     resource=true
     * )
     * @QueryParam(name="displayName", nullable=false, description="Display Name")
     * @QueryParam(name="target", nullable=false, description="User's target")
     */
    public function putAction($token, Request $request)
    {



        $em = $this->getEntityManager();
        /** @var UserRepository $userRepo */
        $userRepo = $em->getRepository('CalorApiBundle:User');
        /** @var User $user */
        $user = $userRepo->getUserByToken($token);

        if ($user === null) {
            return View::create(array('error' => 'Not found', 'status'=>'error'));
        }
        if(!$em->getRepository("CalorApiBundle:Session")->isTokenValid($token)){
            return View::create(array('error' => 'Authentication Timeout', 'status'=>'error'));
        }

        /** @var UserEdit $newUser */
        $newUser = $this->deserialize('Calor\ApiBundle\Form\UserEdit', $request);

        if ($newUser instanceof UserEdit === false) {
            return View::create(array('errors' => $newUser));
        }

        /** @var User $userUpdated */
        $userUpdated = $userRepo->findOneBy(array('id'=>$user['id']));
        //$userUpdated->setDisplayName($newUser->displayName);
        $userUpdated->setTarget($newUser->target);

        $em = $this->getEntityManager();
        $em->persist($userUpdated);
        $em->flush();

       // $user['username'] = $newUser->displayName;
        $user['target'] = $newUser->target;

        return array('user' => $user);
    }

    /**
     * Delete a User
     *
     * @ApiDoc(
     *     section="Users"
     * )
     * @RestView(statusCode=204)
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

        $em = $this->getEntityManager();
        $em->remove($user);
        $em->flush();
    }


}
