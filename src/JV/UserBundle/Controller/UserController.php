<?php

namespace JV\UserBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Request;
use JV\UserBundle\Entity\User;
use JV\UserBundle\Form\UserType;


class UserController extends Controller
{
    public function helloWorldAction()
    {
        return new Response("Hola Mundo!");
    }
    
    public function articleAction($page)
    {
        return new Response("Este es mi artículo número ".$page);
    }
    
    /*
    private function init()
    {
        return $this->getDoctrine()->getManager()->getRepository('JVUserBundle:User');
    }
    */
    
    public function indexAction()
    {
        //$users = $this()->init()->findAll();
        $em = $this->getDoctrine()->getManager()->getRepository('JVUserBundle:User');
        $users = $em->findAll();
        
        /*
        $res = 'Lista de usuarios: <br />';
        
        foreach($users as $user)
        {
            $res .= 'Usuario: ' . $user->getUsername() . ' - Email: ' . $user->getEmail() . '<br />';
        }
        
        return new Response($res);
        */
        
        return $this->render('JVUserBundle:User:index.html.twig', array('users' => $users));
    
    }
    
    public function addAction()
    {
        $user = new User();
        $form = $this->createCreateForm($user);
        
        return $this->render('JVUserBundle:User:add.html.twig', array('form' => $form->createView()));
    }
    
    private function createCreateForm(User $entity)
    {
        $form = $this->createForm(new UserType(), $entity, array(
                'action' => $this->generateUrl('jv_user_create'),
                'method' => 'POST'
            ));
        
        return $form;
    }
    
    public function createAction(Request $request)
    {   
        $user = new User();
        $form = $this->createCreateForm($user);
        $form->handleRequest($request);
        
        if($form->isValid())
        {
            $password = $form->get('password')->getData();
            
            $encoder = $this->container->get('security.password_encoder');
            $encoded = $encoder->encodePassword($user, $password);
            
            $user->setPassword($encoded);
            
            $em = $this->getDoctrine()->getManager();
            $em->persist($user);
            $em->flush();
            
            return $this->redirectToRoute('jv_user_index');
        }
        
        return $this->render('JVUserBundle:User:add.html.twig', array('form' => $form->createView()));
    }
    
    public function viewAction($id)
    {
        
        //$user = init()->find($id);
        $em = $this->getDoctrine()->getManager()->getRepository('JVUserBundle:User');
        $user = $em->find($id);
        
        // $user = $repository->findOneByUsername($nombre);
        // $user = $repository->findOneById($id);
        
        /*
        $res = 'Usuario: ' . $user->getUsername() . ' con email: ' . $user->getEmail();    
        
        return new Response($res);
        */
        return $this->render('JVUserBundle:User:view.html.twig', array('user' => $user));

    }
}
