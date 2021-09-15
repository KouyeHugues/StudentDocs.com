<?php

namespace App\Form;

use App\Entity\Exam;
use App\Entity\School;
use App\Entity\University;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\IntegerType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\FileType;
use Symfony\Component\Validator\Constraints\File;

class ExamType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('name', TextType::class, [ "required" => true])
            ->add('fileName', FileType::class, [
                'label' => 'Fichier de l\'examen',
                // unmapped means that this field is not associated to any entity property
                'mapped' => false,
                'required' => false,
                'constraints' => [
                    new File([
                        'maxSize' => '8192k',
                        'mimeTypes' => [
                            'application/pdf',
                            'application/x-pdf',
                        ],
                        'mimeTypesMessage' => 'Le fichier sélectionné n\'est un document PDF.',
                    ]),
                ],
            ])
            ->add('academicYear', IntegerType::class, [ "required" => true])
            ->add('parentUniversity', EntityType::class, [
                "class" => University::class,
                "choice_label" => "name"
            ])
            ->add('parentSchool', EntityType::class, [
                "class" => School::class,
                "choice_label" => "name"
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => Exam::class,
        ]);
    }
}