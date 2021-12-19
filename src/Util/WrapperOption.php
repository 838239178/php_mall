<?php


namespace App\Util;

use Attribute;

#[Attribute(Attribute::TARGET_PROPERTY)]
class WrapperOption
{
    const DEFAULT = "default";
    const ARRAY = "array";

    public string $type;

    public ?string $elementType;

    /**
     * @param string $type string One of "default", "array"(ArrayCollection) and "SomeClass::class", default value is "default"
     * @param string|null $elementType One of "default" and "SomeClass:class", default is null. It's mandatory required if type="array"
     */
    public function __construct(string $type = "default", ?string $elementType = null)
    {
        $this->type = $type;
        $this->elementType = $elementType;
    }
}