<?php

namespace App\Http\Form;

use App\Domain\Quizz\Entity\Quizz;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\CollectionType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\UX\Dropzone\Form\DropzoneType;
use Vich\UploaderBundle\Form\Type\VichImageType;

class QuizzType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        switch ($options['flow_step']) {
            case 1:
                $builder
                    ->add('name')
                    ->add('description')
                    ->add('imageFile', DropzoneType::class)
                    ->add('isPrivate')
                    ->add('category')
                ;
                break;

            case 2:
                $builder
                    ->add('players')
                    ->add('questions', CollectionType::class, [
                        'entry_type' => QuestionType::class,
                        'entry_options' => ['label' => false],
                        'allow_add' => true
                    ])
                ;
                break;
        }
    }

    public function getBlockPrefix()
    {
        return 'createQuizz';
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => Quizz::class,
            'translation_domain' => 'form.quizz'
        ]);
    }
}
