<?php

namespace App\Form;

use App\Entity\Author;
use App\Entity\Book;
use App\Entity\Category;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\FileType;
use Symfony\Component\Form\Extension\Core\Type\IntegerType;
use Symfony\Component\Form\Extension\Core\Type\MoneyType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class BookType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('name', TextType::class,[
                'label' => "Book Name",
                'required' => true,
            ])
            ->add('description', TextType::class,[
                'label' => "Description",
            ])
            ->add('cover', FileType::class, [
                'label' => "Book Cover",
                'data_class' => null,
                'required' => is_null($builder->getData()->getCover())
            ])
            ->add('quantity', IntegerType::class, [
                'label' => "Book Quantity",
                'required' => true,
            ])
            ->add('price', MoneyType::class, [
                'label' => "Book Price",
                'required' => true,
                'currency' => "USD",
            ])
            ->add('manufacturer', TextType::class,[
                'label' => "Manufacturer",
                'required' => true,
            ])
            // ->add('orderQuantity')
            ->add('Author', EntityType::class, [
                'label' => "Author",
                'class' => Author::class,
                'choice_label' => "name",
                'multiple' => true,
                'expanded' => true
            ])
            ->add('Category', EntityType::class, [
                'label' => "Category",
                'class' => Category::class,
                'choice_label' => "name",
                'multiple' => false,
                'expanded' =>  false,//true:checkbox, false: drop-down list
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Book::class,
        ]);
    }
}
