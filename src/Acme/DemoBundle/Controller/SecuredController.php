<?php

namespace Acme\DemoBundle\Controller;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Security\Core\SecurityContext;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;
use Acme\DemoBundle\Entity\User;

/**
 * @Route("/demo/secured")
 */
class SecuredController extends Controller
{
    /**
     * @Route("/login", name="_demo_login")
     * @Template()
     */
    public function loginAction(Request $request)
    {
        if ($request->attributes->has(SecurityContext::AUTHENTICATION_ERROR)) {
            $error = $request->attributes->get(SecurityContext::AUTHENTICATION_ERROR);
        } else {
            $error = $request->getSession()->get(SecurityContext::AUTHENTICATION_ERROR);
        }

        return array(
            'last_username' => $request->getSession()->get(SecurityContext::LAST_USERNAME),
            'error'         => $error,
        );
    }

    /**
     * @Route("/login_check", name="_security_check")
     */
    public function securityCheckAction()
    {
        // The security layer will intercept this request
    }

    /**
     * @Route("/logout", name="_demo_logout")
     */
    public function logoutAction()
    {
        // The security layer will intercept this request
    }

    /**
     * @Route("/hello", defaults={"name"="World"}),
     * @Route("/hello/{name}", name="_demo_secured_hello")
     * @Template()
     */
    public function helloAction($name)
    {
        return array('name' => $name);
    }

    /**
     * @Route("/hello/admin/{name}", name="_demo_secured_hello_admin")
     * @Security("is_granted('ROLE_ADMIN')")
     * @Template()
     */
    public function helloadminAction($name)
    {
        return array('name' => $name);
    }

    /**
     * @Route("/addNewUser", name="_demo_add_new_user")
     * @Security("is_granted('ROLE_ADMIN')")
     * @Template()
     */
    public function addNewUserAction(Request $request) {
        $user = new User();

        $form = $this->createFormBuilder($user)
            ->add('username', 'text')
            ->add('password', 'password')
            ->add('add', 'submit')
            ->getForm();

        $form->handleRequest($request);

        if ($form->isValid()) {
            $entity = $form->getData();

            // Encoding password
            $factory = $this->get('security.encoder_factory');
            $encoder = $factory->getEncoder($entity);
            $password = $encoder->encodePassword($entity->getPassword(), $user->getSalt());
            $entity->setPassword($password);

            $em = $this->getDoctrine()->getManager();
            $em->persist($entity);
            $em->flush();
            // perform some action, such as saving the task to the database
            return $this->redirect($this->generateUrl('_welcome'));
        }

        return $this->render('AcmeDemoBundle:Secured:addNewUser.html.twig', array(
            'form' => $form->createView(),
        ));
    }

}
