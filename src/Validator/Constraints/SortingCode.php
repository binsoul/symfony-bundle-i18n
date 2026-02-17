<?php

declare(strict_types=1);

namespace BinSoul\Symfony\Bundle\I18n\Validator\Constraints;

use Attribute;

/**
 * Validates Sorting Code.
 */
#[Attribute(Attribute::TARGET_PROPERTY | Attribute::TARGET_METHOD)]
class SortingCode extends AbstractAddressFieldConstraint
{
    public static string $addressField = 'sortingCode';
}
