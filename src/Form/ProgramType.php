<?php

namespace App\Form;

use App\Entity\Program;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\FileType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class ProgramType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('title',TextType::class,
                ['label'=>'Ajoutez un titre',
                    'label_attr'=>[
                        'class'=>'labelProgram'
                    ],
                    'attr'=>[
                        'placeholder'=>'entrez un titre',
                        'class'=>'inputProgram'
                    ]]
            )
            ->add('synopsis',TextareaType::class,
                ['label'=>'Ajoutez un synopsis',
                    'label_attr'=>[
                        'class'=>'labelProgram'
                    ],
                    'attr'=>[
                        'placeholder'=>'entrez un synopsis',
                        'class'=>'inputProgram'
                    ]]
            )
            ->add('poster',FileType::class,
                ['label'=>'Ajoutez une photo',
                    'label_attr'=>[
                        'class'=>'labelProgram'
                    ],
                    'attr'=>[
                        'placeholder'=>'entrez une photo',
                        'class'=>'inputProgram'
                    ]]
            )
            ->add('category', null, ['choice_label' => 'name'])

        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Program::class,
        ]);
    }
}
