<?php


namespace App\Filter;


use ApiPlatform\Core\Serializer\Filter\FilterInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Serializer\Normalizer\AbstractNormalizer;

class SingleGroupFilter implements FilterInterface
{
    private $overrideDefaultGroups;
    private $parameterName;
    private $whitelist;

    public function __construct(string $parameterName = 'group', bool $overrideDefaultGroups = true, array $whitelist = null)
    {
        $this->overrideDefaultGroups = $overrideDefaultGroups;
        $this->parameterName = $parameterName;
        $this->whitelist = $whitelist;
    }

    /**
     * {@inheritdoc}
     */
    public function apply(Request $request, bool $normalization, array $attributes, array &$context)
    {
        if (key_exists($this->parameterName, $commonAttribute = $request->attributes->get('_api_filters', []))) {
            $group = $commonAttribute[$this->parameterName];
        } else {
            $group = $request->query->get($this->parameterName) ?? null;
        }

        if ($group === null || is_array($group)) {
            return;
        }

        $groups = [$group];

        if (null !== $this->whitelist) {
            $groups = array_intersect($this->whitelist, $groups);
        }

        if (!$this->overrideDefaultGroups && isset($context[AbstractNormalizer::GROUPS])) {
            $groups = array_merge((array) $context[AbstractNormalizer::GROUPS], $groups);
        }

        $context[AbstractNormalizer::GROUPS] = $groups;
    }

    /**
     * {@inheritdoc}
     */
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