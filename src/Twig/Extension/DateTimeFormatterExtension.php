<?php

declare(strict_types=1);

namespace BinSoul\Symfony\Bundle\I18n\Twig\Extension;

use BinSoul\Common\I18n\DateTimeFormatter as CommonDateTimeFormatter;
use BinSoul\Common\I18n\DefaultLocale;
use BinSoul\Common\I18n\Locale;
use BinSoul\Symfony\Bundle\I18n\Formatter\DateTimeFormatter;
use BinSoul\Symfony\Bundle\I18n\I18nManager;
use DateTimeInterface;
use Twig\Extension\AbstractExtension;
use Twig\TwigFilter;

/**
 * Provides integration of the {@see DateTimeFormatter} with Twig.
 */
class DateTimeFormatterExtension extends AbstractExtension
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
                'formatPattern',
                function (DateTimeInterface $datetime, string $pattern, Locale|string|null $locale = null): string {
                    return $this->formatPattern($datetime, $pattern, $locale);
                }
            ),
            new TwigFilter(
                'formatTime',
                function (DateTimeInterface $time, Locale|string|null $locale = null): string {
                    return $this->formatTime($time, $locale);
                }
            ),
            new TwigFilter(
                'formatTimeWithSeconds',
                function (DateTimeInterface $time, Locale|string|null $locale = null): string {
                    return $this->formatTimeWithSeconds($time, $locale);
                }
            ),
            new TwigFilter(
                'formatDate',
                function (DateTimeInterface $date, Locale|string|null $locale = null): string {
                    return $this->formatDate($date, $locale);
                }
            ),
            new TwigFilter(
                'formatDateTime',
                function (DateTimeInterface $datetime, Locale|string|null $locale = null): string {
                    return $this->formatDateTime($datetime, $locale);
                }
            ),
            new TwigFilter(
                'formatDateTimeWithSeconds',
                function (DateTimeInterface $datetime, Locale|string|null $locale = null): string {
                    return $this->formatDateTimeWithSeconds($datetime, $locale);
                }
            ),
        ];
    }

    /**
     * Formats the the given datetime according to the given pattern.
     *
     * @param DateTimeInterface  $datetime The datetime
     * @param string             $pattern  The pattern for the datetime
     * @param Locale|string|null $locale   The locale or null to use the default
     */
    public function formatPattern(DateTimeInterface $datetime, string $pattern, Locale|string|null $locale = null): string
    {
        return $this->getFormatter($locale)->formatPattern($datetime, $pattern);
    }

    /**
     * Formats the given time in the standard format.
     *
     * @param DateTimeInterface  $time   The time
     * @param Locale|string|null $locale The locale or null to use the default
     */
    public function formatTime(DateTimeInterface $time, Locale|string|null $locale = null): string
    {
        return $this->getFormatter($locale)->formatTime($time);
    }

    /**
     * Formats the given time in the standard format including seconds.
     *
     * @param DateTimeInterface  $time   The time
     * @param Locale|string|null $locale The locale or null to use the default
     */
    public function formatTimeWithSeconds(DateTimeInterface $time, Locale|string|null $locale = null): string
    {
        return $this->getFormatter($locale)->formatTimeWithSeconds($time);
    }

    /**
     * Formats the given date in the standard format.
     *
     * @param DateTimeInterface  $date   The date
     * @param Locale|string|null $locale The locale or null to use the default
     */
    public function formatDate(DateTimeInterface $date, Locale|string|null $locale = null): string
    {
        return $this->getFormatter($locale)->formatDate($date);
    }

    /**
     * Formats the given date and time in the standard format.
     *
     * @param DateTimeInterface  $datetime The datetime
     * @param Locale|string|null $locale   The locale or null to use the default
     */
    public function formatDateTime(DateTimeInterface $datetime, Locale|string|null $locale = null): string
    {
        return $this->getFormatter($locale)->formatDateTime($datetime);
    }

    /**
     * Formats the given date and time in the standard format including seconds.
     *
     * @param DateTimeInterface  $datetime The datetime
     * @param Locale|string|null $locale   The locale or null to use the default
     */
    public function formatDateTimeWithSeconds(DateTimeInterface $datetime, Locale|string|null $locale = null): string
    {
        return $this->getFormatter($locale)->formatDateTimeWithSeconds($datetime);
    }

    /**
     * Returns a formatter for the given locale.
     */
    private function getFormatter(Locale|string|null $locale): CommonDateTimeFormatter
    {
        $formatter = $this->i18nManager->getEnvironment()->getDateTimeFormatter();

        if ($locale === null) {
            return $formatter;
        }

        if (! ($locale instanceof Locale)) {
            $locale = DefaultLocale::fromString($locale);
        }

        return $formatter->withLocale($locale);
    }
}
