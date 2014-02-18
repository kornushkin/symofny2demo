<?php

namespace Propeller\Bundle\UserBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Acme\DemoBundle\Entity\User;

class DefaultController extends Controller
{
    public function addNewUserAction() {
        $user = new User();

        $form = $this->createFormBuilder($user)
            ->add('username', 'text')
            ->add('password', 'text')
            ->add('add', 'submit')
            ->getForm();

        return $this->render('AcmeDemoBundle:Default:addNewUser.html.twig', array(
            'form' => $form->createView(),
        ));
    }

    public function indexAction($name)
    {
        return $this->render('PropellerUserBundle:Default:index.html.twig', array('name' => $name));
    }
}
