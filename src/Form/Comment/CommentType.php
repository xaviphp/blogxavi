<?php

namespace App\Form\Comment;

use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\OptionsResolver\OptionsResolver;
class CommentType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('comment', TextType::class, [
                'attr' => ['autofocus' => true],
                'label' => 'Añade un comentario',
                'attr'=>[
                    'class'=>'form-control'
                ]
            ])
            ->add('publicar', SubmitType::class,
                ['label'=>'Publicar',
                    'attr'=>[
                        'class'=>'form-submit btn btn-success'
                    ]])
        ;
    }

    /**
     * {@inheritdoc}
     */
    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults(['data_class'=>'App\Entity\Comment']);

    }
}
