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
            new TwigFilter('formatPattern', [$this, 'formatPattern']),
            new TwigFilter('formatTime', [$this, 'formatTime']),
            new TwigFilter('formatTimeWithSeconds', [$this, 'formatTimeWithSeconds']),
            new TwigFilter('formatDate', [$this, 'formatDate']),
            new TwigFilter('formatDateTime', [$this, 'formatDateTime']),
            new TwigFilter('formatDateTimeWithSeconds', [$this, 'formatDateTimeWithSeconds']),
        ];
    }

    /**
     * Formats the the given datetime according to the given pattern.
     *
     * @param DateTimeInterface  $datetime The datetime
     * @param string             $pattern  The pattern for the datetime
     * @param Locale|string|null $locale   The locale or null to use the default
     */
    public function formatPattern(DateTimeInterface $datetime, string $pattern, $locale = null): string
    {
        return $this->getFormatter($locale)->formatPattern($datetime, $pattern);
    }

    /**
     * Formats the given time in the standard format.
     *
     * @param DateTimeInterface  $time   The time
     * @param Locale|string|null $locale The locale or null to use the default
     */
    public function formatTime(DateTimeInterface $time, $locale = null): string
    {
        return $this->getFormatter($locale)->formatTime($time);
    }

    /**
     * Formats the given time in the standard format including seconds.
     *
     * @param DateTimeInterface  $time   The time
     * @param Locale|string|null $locale The locale or null to use the default
     */
    public function formatTimeWithSeconds(DateTimeInterface $time, $locale = null): string
    {
        return $this->getFormatter($locale)->formatTimeWithSeconds($time);
    }

    /**
     * Formats the given date in the standard format.
     *
     * @param DateTimeInterface  $date   The date
     * @param Locale|string|null $locale The locale or null to use the default
     */
    public function formatDate(DateTimeInterface $date, $locale = null): string
    {
        return $this->getFormatter($locale)->formatDate($date);
    }

    /**
     * Formats the given date and time in the standard format.
     *
     * @param DateTimeInterface  $datetime The datetime
     * @param Locale|string|null $locale   The locale or null to use the default
     */
    public function formatDateTime(DateTimeInterface $datetime, $locale = null): string
    {
        return $this->getFormatter($locale)->formatDateTime($datetime);
    }

    /**
     * Formats the given date and time in the standard format including seconds.
     *
     * @param DateTimeInterface  $datetime The datetime
     * @param Locale|string|null $locale   The locale or null to use the default
     */
    public function formatDateTimeWithSeconds(DateTimeInterface $datetime, $locale = null): string
    {
        return $this->getFormatter($locale)->formatDateTimeWithSeconds($datetime);
    }

    /**
     * Returns a formatter for the given locale.
     *
     * @param Locale|string|null $locale
     */
    private function getFormatter($locale): CommonDateTimeFormatter
    {
        $formatter = $this->i18nManager->getEnvironment()->getDateTimeFormatter();

        if ($locale === null) {
            return $formatter;
        }

        if (! ($locale instanceof Locale)) {
            $locale = DefaultLocale::fromString((string) $locale);
        }

        return $formatter->withLocale($locale);
    }
}
