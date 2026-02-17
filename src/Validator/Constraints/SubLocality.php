<?php

declare(strict_types=1);

namespace BinSoul\Symfony\Bundle\I18n\Validator\Constraints;

use Attribute;

/**
 * Validates Sub-Locality.
 */
#[Attribute(Attribute::TARGET_PROPERTY | Attribute::TARGET_METHOD)]
class SubLocality extends AbstractAddressFieldConstraint
{
    public static string $addressField = 'subLocality';
}
