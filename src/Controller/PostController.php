<?php

namespace App\Controller;

use App\Form\Post\PostType;
use App\Form\Post\EditPostType;
use App\Form\EditUserTypeNoAdmin;
use App\Form\Comment\CommentType;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\Request;
use App\Entity\Post;
use App\Entity\Comment;
use App\Entity\User;

class PostController extends AbstractController
{

    /**
     * @Route("/post", name="app_posts")
     */
    public function allPost(){

        $title="Ultimos";
        $posts = $this->getDoctrine()->getRepository(Post::class)->findBy(array(), array('id' => 'DESC'));
        return $this->render('post/index.html.twig', [
            'posts' => $posts,
            'title' => $title
        ]);
    }

    /**
     * @Route("/post/my", name="app_my_posts")
     */
    public function myPost(){

        $title=$this->getUser();
        $title=$title->getUsername();
        $posts = $this->getDoctrine()->getRepository(Post::class)->findBy(array('user' => $this->getUser()), array('id' => 'DESC'));
        return $this->render('post/index.html.twig', [
            'posts' => $posts,
            'title' => $title
        ]);
    }

    /**
     * @Route("/post/new", name="new_post")
     */
    public function newPost(Request $request)
    {
        //Creau nuevo objeto Post
        $post= new Post();
        $post-> setTitle('write a post title');
        $user = $this->getDoctrine()->getRepository(User::class)->find($this->getUser());
        $post->setAuthor($user->getUsername());
        $post->setCreatedAt(new \DateTime());
        $post->setPublishedAt(null);

        //Crear formulario
        $form=$this->createForm(PostType::class, $post);

        //handle the request
        $form->handleRequest($request);
        $error=$form->getErrors();

        if($form->isSubmitted() && $form->isValid()){
            //Capturar los datos
            $post->setUser($this->getUser());
            $entityManager=$this->getDoctrine()->getManager();
            $entityManager->persist($post);
            $entityManager->flush();
            $this->addFlash(
                'succes', 'Post created'
            );
            //Fluir hacia la base de datos
            return $this->redirectToRoute('app_posts');

        }

        //render the form
        return $this->render('post/newpost.html.twig', [
            'error'=>$error,
            'form' => $form->createView()]);
    }

    /**
     * @Route("post/{id}/edit", name="app_post_edit")
     */
    public function editPost(Request $request, $id)
    {
        $title="Edit";
        $post = $this->getDoctrine()->getRepository(Post::class)->find($id);
        //create the form
        $form = $this->createForm(EditPostType::class, $post);
        $post->setModifiedAt(new \DateTime());

        $form->handleRequest($request);
        $error = $form->getErrors();

        if ($form->isSubmitted() && $form->isValid()) {
            //handle the entities
            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->persist($post);
            $entityManager->flush();
            $this->addFlash(
                'succes', 'Post modified'
            );
            return $this->redirectToRoute('app_posts');
        }

        //render the form
        return $this->render('post/edit.html.twig',[
            'error'=>$error,
            'form'=>$form->createView(),
            'title'=>$title
        ]);
    }

    /**
     * @Route("post/{id}", name="app_post_show")
     */
    public function showPost(Request $request, $id)
    {
        //En este controlador se añade la funcion de los comentarios
        $comment = new comment();
        $posts = $this->getDoctrine()->getRepository(Post::class)->find($id);
        $comments= $this->getDoctrine()->getRepository(Comment::class)->findBy(array('post'=> $posts));
        //Create the form
        $form = $this->createForm(CommentType::class, $comment);
        //handle the request
        $form->handleRequest($request);
        $error=$form->getErrors();

        if ($form->isSubmitted() && $form->isValid()) {
            //handle the entities
            $comment->setUser($this->getUser());
            $comment->setPost($posts);
            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->persist($comment);
            $entityManager->flush();
            $this->addFlash(
                'succes', 'add comment'
            );
            return  $this->redirect($this->generateUrl('app_post_show', array('id' => $id)));
        }

        return $this->render('post/showpost.html.twig', [
            'posts' => $posts,
            'comments' => $comments,
            'error'=>$error,
            'form'=>$form->createView()
        ]);
    }

    /**
     * @Route("post/{id}/delete/comment", name="app_post_delete_comment")
     */
    public function deleteComment($id)
    {
        $em = $this->getDoctrine()->getManager();

        $comment = $this->getDoctrine()->getRepository(Comment::class)->find($id);

        $em->remove($comment);
        $em->flush();

        return $this->redirect($this->generateUrl('app_posts'));

    }

    /**
     * @Route("post/{id}/delete", name="app_post_delete")
     */
    public function deletePost($id)
    {
        $em = $this->getDoctrine()->getManager();

        $post = $this->getDoctrine()->getRepository(Post::class)->find($id);

        $em->remove($post);
        $em->flush();

        return $this->redirectToRoute('app_posts');

    }
}
