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
use Twig\TwigFunction;

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
     * @return TwigFilter[]
     */
    public function getFunctions(): array
    {
        return [
            new TwigFunction('addressContainerClasses', [$this, 'addressContainerClasses'], ['is_safe' => ['html']]),
            new TwigFunction('addressFieldClasses', [$this, 'addressFieldClasses'], ['is_safe' => ['html']]),
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
     * @param string $countryCode ISO2 code of the country
     */
    public function addressContainerClasses(string $countryCode): string
    {
        $layout = $this->getLayout($countryCode);
        [$numberOfRows, $numberOfColumns] = $this->getDimensions($layout);

        return 'rows-' . $numberOfRows . ' columns-' . $numberOfColumns;
    }

    /**
     * @param string $fieldName   Name of the field
     * @param string $countryCode ISO2 code of the country
     */
    public function addressFieldClasses(string $fieldName, string $countryCode): string
    {
        $layout = $this->getLayout($countryCode);
        [, $numberOfColumns] = $this->getDimensions($layout);

        if (! isset($layout[$fieldName])) {
            return 'invisible';
        }

        [$targetRow, $targetColumn] = explode(',', $layout[$fieldName]);
        $span = 1;
        $rowLayout = [];

        foreach ($layout as $field => $position) {
            if ($position === null) {
                continue;
            }

            [$row, $column] = explode(',', $position);

            if ($row === $targetRow) {
                $rowLayout[((int) $column - 1)] = $field;
            }
        }

        if (count($rowLayout) === 1) {
            $span = $numberOfColumns;
        } elseif ($rowLayout[count($rowLayout) - 1] === $fieldName) {
            $span = $numberOfColumns - count($rowLayout) + 1;
        }

        return 'visible row-' . $targetRow . ' column-' . $targetColumn . ' span-' . $span;
    }

    /**
     * @param array<string, string|null> $layout
     * @return array{0: int, 1: int}
     */
    private function getDimensions(array $layout): array
    {
        $numberOfRows = 0;
        $numberOfColumns = 0;

        foreach ($layout as $position) {
            if ($position === null) {
                continue;
            }

            [$row, $column] = explode(',', $position);
            $numberOfRows = max($numberOfRows, (int) $row);
            $numberOfColumns = max($numberOfColumns, (int) $column);
        }

        return [$numberOfRows, $numberOfColumns];
    }

    /**
     * @return array<string, string|null>
     */
    private function getLayout(string $countryCode): array
    {
        $formatter = $this->getFormatter(null);
        $layoutTemplate = $formatter->generateLayoutTemplate($countryCode);

        return [
            'organization' => $layoutTemplate->getOrganization(),
            'namePrefix' => $layoutTemplate->getNamePrefix(),
            'firstName' => $layoutTemplate->getFirstName(),
            'lastName' => $layoutTemplate->getLastName(),
            'addressLine1' => $layoutTemplate->getAddressLine1(),
            'addressLine2' => $layoutTemplate->getAddressLine2(),
            'addressLine3' => $layoutTemplate->getAddressLine3(),
            'sortingCode' => $layoutTemplate->getSortingCode(),
            'postalCode' => $layoutTemplate->getPostalCode(),
            'locality' => $layoutTemplate->getLocality(),
            'subLocality' => $layoutTemplate->getSubLocality(),
            'state' => $layoutTemplate->getState(),
            'countryCode' => $layoutTemplate->getCountryCode(),
        ];
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

        if (! ($locale instanceof Locale)) {
            $locale = DefaultLocale::fromString((string) $locale);
        }

        return $formatter->withLocale($locale);
    }
}
