<?php

namespace App\Http\Admin\Controller;

use App\Domain\Quizz\Entity\Quizz;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextField;
use EasyCorp\Bundle\EasyAdminBundle\Field\NumberField;
use EasyCorp\Bundle\EasyAdminBundle\Field\BooleanField;
use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractCrudController;
use EasyCorp\Bundle\EasyAdminBundle\Field\AssociationField;
use EasyCorp\Bundle\EasyAdminBundle\Field\FormField;
use EasyCorp\Bundle\EasyAdminBundle\Field\ImageField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextareaField;
use Vich\UploaderBundle\Form\Type\VichImageType;

class QuizzCrudController extends AbstractCrudController
{
    public static function getEntityFqcn(): string
    {
        return Quizz::class;
    }

    public function configureFields(string $pageName): iterable
    {
        return [
            FormField::addPanel('Description')
                ->setHelp('Informations about quizz'),
            TextField::new('name'),
            TextareaField::new('description')->hideOnIndex(),
            TextField::new('slug')->hideOnIndex()
                ->setFormTypeOption('disabled', true),
            TextareaField::new('imageFile')->onlyOnForms()
                ->setFormType(VichImageType::class),
            AssociationField::new('creator')->hideOnIndex()
                ->setFormTypeOption('disabled', true),
            FormField::addPanel('Game')
                ->setHelp('Informations about game'),
            NumberField::new('players'),
            BooleanField::new('isPrivate'),
            NumberField::new('note')->hideOnIndex(),
            AssociationField::new('questions')->hideOnIndex()
        ];
    }
}
