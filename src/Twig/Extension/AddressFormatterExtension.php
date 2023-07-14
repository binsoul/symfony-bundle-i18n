<?php

declare(strict_types=1);

namespace BinSoul\Symfony\Bundle\I18n\Twig\Extension;

use BinSoul\Common\I18n\Address;
use BinSoul\Common\I18n\AddressFormatter as CommonAddressFormatter;
use BinSoul\Common\I18n\Country;
use BinSoul\Common\I18n\Data\StateData;
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
    private I18nManager $i18nManager;

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
            new TwigFilter(
                'formatAddress',
                function (Address $address, Locale|string|null $locale = null): string {
                    return $this->formatAddress($address, $locale);
                }
            ),
        ];
    }

    /**
     * @return TwigFilter[]
     */
    public function getFunctions(): array
    {
        return [
            new TwigFunction(
                'addressContainerClasses',
                function (Country|string $country, array $options = []): string {
                    return $this->addressContainerClasses($country, $options);
                },
                ['is_safe' => ['html']]
            ),
            new TwigFunction(
                'addressFieldClasses',
                function (string $fieldName, Country|string $country, array $options = []): string {
                    return $this->addressFieldClasses($fieldName, $country, $options);
                },
                ['is_safe' => ['html']]
            ),
        ];
    }

    /**
     * Formats an address.
     *
     * @param Address            $address The address
     * @param Locale|string|null $locale  The locale or null to use the default
     */
    public function formatAddress(Address $address, Locale|string|null $locale = null): string
    {
        return $this->getFormatter($locale)->format($address);
    }

    /**
     * @param Country|string                                                                  $country Country or ISO2 code of the country
     * @param array{'includeFields': array<int, string>, 'excludeFields': array<int, string>} $options
     */
    public function addressContainerClasses(Country|string $country, array $options = []): string
    {
        $layout = $this->getLayout($country instanceof Country ? $country->getIso2() : $country, $options);
        [$numberOfRows, $numberOfColumns] = $this->getDimensions($layout);

        return 'rows-' . $numberOfRows . ' columns-' . $numberOfColumns;
    }

    /**
     * @param string                                                                          $fieldName Name of the field
     * @param Country|string                                                                  $country   Country or ISO2 code of the country
     * @param array{'includeFields': array<int, string>, 'excludeFields': array<int, string>} $options
     */
    public function addressFieldClasses(string $fieldName, Country|string $country, array $options = []): string
    {
        $layout = $this->getLayout($country instanceof Country ? $country->getIso2() : $country, $options);
        [, $numberOfColumns] = $this->getDimensions($layout);

        if (! isset($layout[$fieldName])) {
            return 'invisible';
        }

        [$targetRow, $targetColumn] = $layout[$fieldName];

        $span = 1;
        $rowLayout = [];

        foreach ($layout as $field => $position) {
            if ($position === null) {
                continue;
            }

            [$row, $column] = $position;

            if ($row === $targetRow) {
                $rowLayout[($column - 1)] = $field;
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
     * @param array<string, array{0: int, 1: int}> $layout
     *
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

            [$row, $column] = $position;
            $numberOfRows = max($numberOfRows, $row);
            $numberOfColumns = max($numberOfColumns, $column);
        }

        return [$numberOfRows, $numberOfColumns];
    }

    /**
     * @param array{'includeFields': array<int, string>, 'excludeFields': array<int, string>} $options
     *
     * @return array<string, array{0: int, 1: int}>
     */
    private function getLayout(string $countryCode, array $options): array
    {
        $formatter = $this->getFormatter(null);
        $layoutTemplate = $formatter->generateLayoutTemplate($countryCode);

        $excludedFields = $options['excludeFields'] ?? [];
        $includedFields = $options['includeFields'] ?? [];

        $data = [
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

        $layout = [];

        foreach ($data as $field => $position) {
            if (in_array($field, $excludedFields, true)) {
                continue;
            }

            if ($position === null) {
                continue;
            }

            [$row, $column] = explode(',', $position);

            if (! isset($layout[(int) $row])) {
                $layout[(int) $row] = [];
            }

            $layout[(int) $row][(int) $column] = $field;

            $key = array_search($field, $includedFields, true);

            if ($key !== false) {
                unset($includedFields[$key]);
            }
        }

        ksort($layout);
        $layout = array_values($layout);

        foreach ($includedFields as $field) {
            if ($field === 'state' && StateData::type($countryCode) === null) {
                continue;
            }

            $layout[] = [$field];
        }

        $result = [];

        foreach ($layout as $row => $columns) {
            ksort($columns);

            foreach (array_values($columns) as $column => $field) {
                $result[$field] = [$row + 1, $column + 1];
            }
        }

        return $result;
    }

    /**
     * Returns a address formatter for the given locale.
     */
    private function getFormatter(Locale|string|null $locale): CommonAddressFormatter
    {
        $formatter = $this->i18nManager->getEnvironment()->getAddressFormatter();

        if ($locale === null) {
            return $formatter;
        }

        if (! ($locale instanceof Locale)) {
            $locale = DefaultLocale::fromString($locale);
        }

        return $formatter->withLocale($locale);
    }
}
