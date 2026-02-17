<?php

declare(strict_types=1);

namespace BinSoul\Symfony\Bundle\I18n\Validator\Constraints;

use Attribute;

/**
 * Validates Postal Code.
 */
#[Attribute(Attribute::TARGET_PROPERTY | Attribute::TARGET_METHOD)]
class PostalCode extends AbstractAddressFieldConstraint
{
    public static string $addressField = 'postalCode';
}
