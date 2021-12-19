<?php


namespace App\Form;


use App\Entity\GoodPropKey;
use App\Entity\Product;
use App\Entity\ProductPropKey;
use App\Entity\PropKey;
use App\Repository\ProductRepository;
use Closure;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\EntityManagerInterface;
use JetBrains\PhpStorm\Deprecated;
use Psr\Log\LoggerInterface;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Event\PostSetDataEvent;
use Symfony\Component\Form\Event\PreSetDataEvent;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\FormEvents;
use Symfony\Component\OptionsResolver\OptionsResolver;

class GoodPropKeyType extends AbstractType
{
    private LoggerInterface $logger;

    public function __construct(LoggerInterface $log)
    {
        $this->logger = $log;
    }

    public function configureOptions(OptionsResolver $resolver): void {
        $resolver->setDefault('data_class', GoodPropKey::class);
        $resolver->setDefault('product_provider', "NONE");
        $resolver->addAllowedTypes("product_provider", "callable");
    }

    private function getValidChoices(callable $provider): array {
        /** @var Product $product */
        $product = call_user_func($provider);
        if ($product == null) {
            return ['请先选择商品'=>null];
        }
        /** @var Collection<Propkey> $keys */
        $keys = $product->getPropKeys()->map(fn(ProductPropKey $item)=> $item->getPropKey());
        $choices = [];
        foreach ($keys as $item) {
            $choices[$item->getKeyName()] = $item;
        }
        return $choices;
    }

    #[Deprecated]
    private function getValues(callable $productProvider, PropKey $propKey): array {
        /** @var Product $product */
        $product = call_user_func($productProvider);
        if ($product == null) {
            return ['请先选择商品'=>null];
        }
        $values = explode(",", $product->getPropKeys()
            ->filter(fn(ProductPropKey $pk)=> $pk->getPropKey()->getKeyId() == $propKey->getKeyId())
            ->first()->getOptionValues());
        $mapValues = [];
        foreach ($values as $value) {
            $mapValues[$value] = $value;
        }
        return $mapValues;
    }

    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        if($options['product_provider'] == null) {
            $this->logger->warning("not found provider");
            return;
        } else {
            $product_provider = $options['product_provider'];
        }
        $builder
            ->add('key', ChoiceType::class, ['choices'=>$this->getValidChoices($product_provider)])
            ->add("value");
    }

}