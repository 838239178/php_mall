<?php


namespace App\Extension;


use ApiPlatform\Core\Bridge\Doctrine\Orm\Extension\QueryCollectionExtensionInterface;
use ApiPlatform\Core\Bridge\Doctrine\Orm\Extension\QueryItemExtensionInterface;
use ApiPlatform\Core\Bridge\Doctrine\Orm\Util\QueryNameGeneratorInterface;
use App\Consts\Role;
use Doctrine\ORM\QueryBuilder;
use Psr\Log\LoggerInterface;
use ReflectionClass;
use ReflectionException;
use Symfony\Component\Security\Core\Security;

class CurrentUserExtension implements QueryCollectionExtensionInterface, QueryItemExtensionInterface
{
    private Security $security;
    private LoggerInterface $logger;

    public function __construct(Security $security, LoggerInterface $logger)
    {
        $this->security = $security;
        $this->logger = $logger;
    }

    public function applyToCollection(QueryBuilder $queryBuilder, QueryNameGeneratorInterface $queryNameGenerator, string $resourceClass, string $operationName = null)
    {
        try {
            $this->addWhere($queryBuilder, $resourceClass);
        } catch (ReflectionException $e) {
            $this->logger->error($resourceClass . "reflection fail, " . $e->getMessage());
        }
    }


    public function applyToItem(QueryBuilder $queryBuilder, QueryNameGeneratorInterface $queryNameGenerator, string $resourceClass, array $identifiers, string $operationName = null, array $context = [])
    {
        try {
            $this->addWhere($queryBuilder, $resourceClass);
        } catch (ReflectionException $e) {
            $this->logger->error($resourceClass . " reflection fail, " . $e->getMessage());
        }
    }

    /**
     * @throws ReflectionException
     */
    private function addWhere(QueryBuilder $queryBuilder, string $resourceClass): void
    {
        $user = $this->security->getUser();
        $ref = new ReflectionClass($resourceClass);
        $attrs = $ref->getAttributes(NotLimitUser::class);
        /**
         * 如果使用了 NotLimitUser 注解 或者是管理员 或者 没有user属性就跳过
         */
        if (count($attrs) > 0 || $this->security->isGranted(Role::ADMIN) || !$ref->hasProperty("user") || $user == null) {
            return;
        }

        $rootAlias = $queryBuilder->getRootAliases()[0];
        $queryBuilder->andWhere(sprintf('%s.user = :current_user', $rootAlias));
        $queryBuilder->setParameter('current_user', $user);
    }
}