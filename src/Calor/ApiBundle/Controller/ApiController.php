<?php

namespace Calor\ApiBundle\Controller;

use FOS\RestBundle\View\View;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use JMS\Serializer\Exception\RuntimeException;
use Symfony\Component\HttpKernel\Exception\HttpException;
use Symfony\Component\HttpFoundation\Request;


class ApiController extends Controller
{
    protected function getEntityManager()
    {
        return $this->getDoctrine()->getManager();
    }
    protected function deserialize($class, Request $request, $format = 'json')
    {
        $serializer = $this->get('serializer');
        $validator = $this->get('validator');

        try {
            $entity = $serializer->deserialize($request->getContent(), $class, $format);
        } catch (RuntimeException $e) {
            return View::create(array('error' => $e->getMessage(), 'status'=>'error'));
        }

        if (count($errors = $validator->validate($entity))) {
            return $errors;
        }
        return $entity;
    }
}
