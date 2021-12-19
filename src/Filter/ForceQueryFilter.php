<?php


namespace App\Filter;


use ApiPlatform\Core\Bridge\Doctrine\Orm\Filter\AbstractContextAwareFilter;
use ApiPlatform\Core\Bridge\Doctrine\Orm\Util\QueryNameGeneratorInterface;
use Doctrine\ORM\QueryBuilder;
use Doctrine\Persistence\ManagerRegistry;
use Psr\Log\LoggerInterface;
use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Component\PropertyInfo\Type;
use Symfony\Component\Serializer\NameConverter\NameConverterInterface;

class ForceQueryFilter extends AbstractContextAwareFilter
{

    private array $forceWhere;

    public function __construct(ManagerRegistry $managerRegistry,
                                ?RequestStack $requestStack = null,
                                LoggerInterface $logger = null,
                                array $properties = null,
                                NameConverterInterface $nameConverter = null,
                                array $forceWhere = [])
    {
        parent::__construct($managerRegistry, $requestStack, $logger, $properties, $nameConverter);
        $this->forceWhere = $forceWhere;
    }


    /**
     * @inheritDoc
     */
    protected function filterProperty(string $property, $value, QueryBuilder $queryBuilder, QueryNameGeneratorInterface $queryNameGenerator, string $resourceClass, string $operationName = null)
    {
        $rootAlias = $queryBuilder->getRootAliases()[0];
        foreach ($this->forceWhere as $key=>$value) {
            $queryBuilder->andWhere(sprintf("%s.%s = :forceWhereValue%s", $rootAlias, $key, $key))
                ->setParameter("forceWhereValue".$key, $value);
        }
    }

    /**
     * @inheritDoc
     */
    public function getDescription(string $resourceClass): array
    {
        return [];
    }
}