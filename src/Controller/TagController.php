<?php
/**
 * Created by PhpStorm.
 * User: linux
 * Date: 02/05/19
 * Time: 20:00
 */

namespace App\Controller;

use App\Entity\Tag;
use App\Form\TagType;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\Request;

class TagController extends AbstractController
{
    /**
     * @Route("/tag", name="app_tag_admin")
     */
    public function alltags(){

        $tags = $this->getDoctrine()->getRepository(Tag::class)->findAll();
        return $this->render('admin/tags.html.twig', [
            'tags' => $tags
        ]);
    }

    /**
     * @Route("/tag/new",name="app_new_tag")
     */
    public function newTag (Request $request){

        $title="Create";
        $tag=new Tag();

        //create the form
        $form=$this->createForm(TagType::class,$tag);

        $form->handleRequest($request);
        $error=$form->getErrors();

        if($form->isSubmitted() && $form->isValid()){
            //handle the entities
            $entityManager=$this->getDoctrine()->getManager();
            $entityManager->persist($tag);
            $entityManager->flush();
            $this->addFlash(
                'succes', 'Tag created'
            );
            return $this->redirectToRoute('app_tag_admin');
        }

        //render the form
        return $this->render('tag/edit.html.twig',[
            'error'=>$error,
            'form'=>$form->createView(),
            'title'=>$title
        ]);

    }

    /**
     * @Route("/tag/{id}/edit",name="app_edit_tag")
     */
    public function editTag (Request $request, $id){

        $title="Edit";
        $tag=$this->getDoctrine()->getRepository(Tag::class)->find($id);

        //create the form
        $form=$this->createForm(TagType::class,$tag);

        $form->handleRequest($request);
        $error=$form->getErrors();

        if($form->isSubmitted() && $form->isValid()){
            //handle the entities
            $entityManager=$this->getDoctrine()->getManager();
            $entityManager->persist($tag);
            $entityManager->flush();
            $this->addFlash(
                'succes', 'Tag edited'
            );
            return $this->redirectToRoute('app_tag_admin');
        }

        //render the form
        return $this->render('tag/edit.html.twig',[
            'error'=>$error,
            'form'=>$form->createView(),
            'title'=>$title
        ]);

    }

    /**
     * @Route("tag/{id}/delete", name="app_tag_delete")
     */
    public function deleteTag($id)
    {
        $em = $this->getDoctrine()->getManager();

        $tag = $this->getDoctrine()->getRepository(Tag::class)->find($id);

        $em->remove($tag);
        $em->flush();

        return $this->redirectToRoute('app_tag_admin');

    }
}