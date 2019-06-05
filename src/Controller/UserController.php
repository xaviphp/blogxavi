<?php

namespace App\Controller;

use App\Entity\User;
use App\Form\UserType;
use App\Form\EditUserTypeNoAdmin;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;
use Symfony\Component\Security\Http\Authentication\AuthenticationUtils;
use App\Form\EditUserType;
class UserController extends AbstractController
{
    /**
     * @Route("/user", name="app_user")
     */
    public function index()
    {
        return $this->render('user/index.html.twig', [
            'controller_name' => 'UserController',
        ]);
    }

    /**
     * @Route("user/{id}/edit", name="app_edit_my")
     */
    public function editUser(Request $request, UserPasswordEncoderInterface $passwordEncoder, $id)
    {
        $title = "Edit";
        $user = $this->getDoctrine()->getRepository(User::class)->find($id);
        //create the form
        $form = $this->createForm(EditUserTypeNoAdmin::class, $user);

        $form->handleRequest($request);
        $error = $form->getErrors();

        if ($form->isSubmitted() && $form->isValid()) {
            //encrypt password
            $password = $passwordEncoder->encodePassword($user, $user->getPlainPassword());
            $user->setPassword($password);
            //handle the entities
            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->persist($user);
            $entityManager->flush();
            $this->addFlash(
                'succes', 'User created'
            );
            return $this->redirectToRoute('app_posts');
        }
        //render the form
        return $this->render('admin/edit.html.twig',[
            'error'=>$error,
            'form'=>$form->createView(),
            'title'=>$title
        ]);

    }

    /**
     * @Route("/register",name="app_register")
     */
    public function register (Request $request, UserPasswordEncoderInterface $passwordEncoder){
        $user=new User();
        $user->setRoles(['ROLE_USER']);
        $user->setIsActive(true);
        //create the form
        $form=$this->createForm(UserType::class,$user);

        $form->handleRequest($request);
        $error=$form->getErrors();

        if($form->isSubmitted() && $form->isValid()){
            //encrypt password
            $password=$passwordEncoder->encodePassword($user, $user->getPlainPassword());
            $user->setPassword($password);
            //handle the entities
            $entityManager=$this->getDoctrine()->getManager();
            $entityManager->persist($user);
            $entityManager->flush();
            $this->addFlash(
                'succes', 'User created'
            );
            return $this->redirectToRoute('app_login');
        }

        //render the form
        return $this->render('user/regform.html.twig',[
           'error'=>$error,
           'form'=>$form->createView()
        ]);

    }

    /**
     * @param Request $request
     * @param AuthenticationUtils $authUtils
     * @Route("/login",name="app_login")
     */
    public function login(Request $request, AuthenticationUtils $authUtils){
        $error=$authUtils->getLastAuthenticationError();
        //last username
        $lastUsername=$authUtils->getLastUsername();
        return $this->render('user/login.html.twig',[
            'error'=>$error,
            'last_username'=>$lastUsername
        ]);

        if ($this->getUser()) {
            return $this->redirectToRoute('app_posts');
        }
    }

    /**
     * @Route("/logout",name="app_logout")
     */
    public function logout(){
        $this->redirectToRoute('/');
    }
}
