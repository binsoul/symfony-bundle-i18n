<?php

declare(strict_types=1);

namespace BinSoul\Symfony\Bundle\I18n\Translation;

use Symfony\Bundle\FrameworkBundle\Translation\Translator as BaseTranslator;

/**
 * Replaces the original Symfony FrameworkBundle translator.
 */
class DatabaseTranslator extends BaseTranslator
{
    public function addResource(string $format, $resource, string $locale, ?string $domain = null): void
    {
        parent::addResource($format, $resource, $locale, $domain);

        if ($domain !== null && $domain !== 'messages') {
            // add an additional resource to trigger the database loader
            parent::addResource('db', $domain . '.' . $locale . '.db', $locale, $domain);
        }
    }
}
