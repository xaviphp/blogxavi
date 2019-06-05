<?php

namespace App\Form;

use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\OptionsResolver\OptionsResolver;

class TagType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('tag', TextType::class, [
                'attr' => ['autofocus' => true],
                'label' => 'Tag',
                'attr' => [
                    'class' => 'form-control'
                ]
            ])
            ->add('publicar', SubmitType::class,
                ['label' => 'Confirmar',
                    'attr' => [
                        'class' => 'form-submit btn btn-success'
                    ]]);
    }

    /**
     * {@inheritdoc}
     */
    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults(['data_class' => 'App\Entity\Tag']);

    }

}