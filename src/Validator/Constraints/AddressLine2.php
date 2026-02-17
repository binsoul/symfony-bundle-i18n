<?php

declare(strict_types=1);

namespace BinSoul\Symfony\Bundle\I18n\Validator\Constraints;

use Attribute;

/**
 * Validates Address Line 2.
 */
#[Attribute(Attribute::TARGET_PROPERTY | Attribute::TARGET_METHOD)]
class AddressLine2 extends AbstractAddressFieldConstraint
{
    public static string $addressField = 'addressLine2';
}
