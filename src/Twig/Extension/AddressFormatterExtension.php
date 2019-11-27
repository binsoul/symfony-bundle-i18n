<?php

declare(strict_types=1);

namespace BinSoul\Symfony\Bundle\I18n\Twig\Extension;

use BinSoul\Common\I18n\Address;
use BinSoul\Common\I18n\AddressFormatter as CommonAddressFormatter;
use BinSoul\Common\I18n\DefaultLocale;
use BinSoul\Common\I18n\Locale;
use BinSoul\Symfony\Bundle\I18n\Formatter\AddressFormatter;
use BinSoul\Symfony\Bundle\I18n\I18nManager;
use Twig\Extension\AbstractExtension;
use Twig\TwigFilter;

/**
 * Provides integration of the {@see AddressFormatter} with Twig.
 */
class AddressFormatterExtension extends AbstractExtension
{
    /**
     * @var I18nManager
     */
    private $i18nManager;

    /**
     * Constructs an instance of this class.
     */
    public function __construct(I18nManager $i18nManager)
    {
        $this->i18nManager = $i18nManager;
    }

    /**
     * @return TwigFilter[]
     */
    public function getFilters(): array
    {
        return [
            new TwigFilter('formatAddress', [$this, 'formatAddress']),
        ];
    }

    /**
     * Formats an address.
     *
     * @param Address            $address The address
     * @param Locale|string|null $locale  The locale or null to use the default
     */
    public function formatAddress(Address $address, $locale = null): string
    {
        return $this->getFormatter($locale)->format($address);
    }

    /**
     * Returns a address formatter for the given locale.
     *
     * @param Locale|string|null $locale
     */
    private function getFormatter($locale): CommonAddressFormatter
    {
        $formatter = $this->i18nManager->getEnvironment()->getAddressFormatter();
        if ($locale === null) {
            return $formatter;
        }

        if (!($locale instanceof Locale)) {
            $locale = DefaultLocale::fromString((string) $locale);
        }

        return $formatter->withLocale($locale);
    }
}
