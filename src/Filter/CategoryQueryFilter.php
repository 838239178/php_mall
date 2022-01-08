<?php


namespace App\Filter;



use ApiPlatform\Core\Bridge\Doctrine\Orm\Filter\AbstractContextAwareFilter;
use ApiPlatform\Core\Bridge\Doctrine\Orm\Filter\FilterInterface;
use ApiPlatform\Core\Bridge\Doctrine\Orm\Util\QueryNameGeneratorInterface;
use Doctrine\ORM\Query\Expr\Join;
use Doctrine\ORM\QueryBuilder;

class CategoryQueryFilter extends AbstractContextAwareFilter
{

    private string $parameterName = "category";

    protected function filterProperty(string $property, $value, QueryBuilder $queryBuilder, QueryNameGeneratorInterface $queryNameGenerator, string $resourceClass, string $operationName = null)
    {
        if ($property != $this->parameterName) return;

        $alias = $queryBuilder->getRootAliases()[0];
        $field = $property;

        $valueParameter = ':'.$queryNameGenerator->generateParameterName($field);
        $aliasedField = sprintf('%s.%s', $alias, $field);
        $association = "App\Entity\Category";
        $queryBuilder
            ->leftJoin($association, "c2", Join::WITH, "c2.categoryId = $aliasedField" )
            ->leftJoin($association, "c3", Join::WITH, "c2.parent = c3.categoryId" )
            ->andWhere("$aliasedField = $valueParameter")
            ->orWhere("c2 = $valueParameter")
            ->orWhere("c3 = $valueParameter")
            ->setParameter($valueParameter, $value);
    }


    public function getDescription(string $resourceClass): array
    {
        return [
            "$this->parameterName" => [
                'property' => null,
                'type' => 'string',
                'is_collection' => false,
                'required' => false,
            ],
        ];
    }
}