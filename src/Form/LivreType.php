<?php

namespace App\Form;

use App\Entity\Livre;
use App\Entity\Genre;
use App\Entity\Auteur;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\Extension\Core\Type\DateType;

class LivreType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('isbn')
            ->add('titre')
            ->add('nombre_pages')
            ->add('date_de_parution', DateType::class, [
                'widget' => 'single_text'
            ])
            ->add('note')
            ->add('auteurs', EntityType::class, [
                'class' => Auteur::class,
                'choice_label' => 'nom_prenom',
                'multiple' => true,
                'by_reference' => false,
            ])
            ->add('genre', EntityType::class, [
                'class' => Genre::class,
                'choice_label' => 'nom',
                'multiple' => true,
            ]);;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Livre::class,
        ]);
    }
}
