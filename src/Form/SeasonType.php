<?php

namespace App\Form;

use App\Entity\Season;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\IntegerType;
use Symfony\Component\Form\Extension\Core\Type\NumberType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class SeasonType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('number',IntegerType::class,
                ['label'=>'Numéro de la saison',
                    'label_attr'=>[
                        'class'=>'labelProgram'
                    ],
                    'attr'=>[
                    'placeholder'=>'Le numéro please',
                    'class'=>'inputProgram'
                ]])
            ->add('year',IntegerType::class,
                ['label'=>'Année de création',
                    'label_attr'=>[
                        'class'=>'labelProgram'
                    ],
                    'attr'=>[
                        'placeholder'=>'Entrez l\'année de création',
                        'class'=>'inputProgram'
                    ]])
            ->add('description',TextareaType::class,
                ['label'=>'Ajoutez un synopsis',
                    'label_attr'=>[
                        'class'=>'labelProgram'
                    ],
                    'attr'=>[
                        'placeholder'=>'entrez un synopsis',
                        'class'=>'inputProgram'
                    ]])
            ->add('program', null, ['choice_label' => 'title']
                )
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Season::class,
        ]);
    }
}
