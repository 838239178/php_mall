<?php

namespace App\Form;

use App\Entity\ProductPropKey;
use App\Entity\PropKey;
use Doctrine\DBAL\Types\ArrayType;
use EasyCorp\Bundle\EasyAdminBundle\Form\Filter\Type\EntityFilterType;
use Psr\Log\LoggerInterface;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\CollectionType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class ProductPropKeyType extends AbstractType
{
    private LoggerInterface $logger;

    /**
     * ProductPropKeyType constructor.
     * @param LoggerInterface $logger
     */
    public function __construct(LoggerInterface $logger)
    {
        $this->logger = $logger;
    }


    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('propKey', EntityType::class, [
                'class' => PropKey::class,
                'choice_label' => 'keyName',
            ])
            ->add('optionValues', CollectionType::class, [
                'allow_add' => true,
                'allow_delete' => true,
                'entry_type' => TextType::class,
                'entry_options' => [
                    'label' => false,
                ],
                'label' => false,
            ]);
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefault('data_class', ProductPropKey::class);
    }

}