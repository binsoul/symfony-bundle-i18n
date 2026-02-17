<?php

declare(strict_types=1);

namespace BinSoul\Symfony\Bundle\I18n\Validator\Constraints;

use Attribute;

/**
 * Validates Locality / City.
 */
#[Attribute(Attribute::TARGET_PROPERTY | Attribute::TARGET_METHOD)]
class Locality extends AbstractAddressFieldConstraint
{
    public static string $addressField = 'locality';
}
