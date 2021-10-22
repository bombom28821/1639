<?php

namespace App\Form;

use App\Entity\Author;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\DateType;
use Symfony\Component\Form\Extension\Core\Type\FileType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class AuthorType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('name', TextType::class, [
                'label' => "Author Name",
                'required' => true,
            ])
            ->add('avatar', FileType::class, [
                'label' => "Author Avatar",
                'data_class' => null,
                'required' => is_null($builder->getData()->getAvatar())

            ])
            ->add('date', DateType::class, [
                'label' => "Author birthday",
                'required' => true,
                'widget' => 'single_text'
            ])
            // ->add('Books')
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Author::class,
        ]);
    }
}
