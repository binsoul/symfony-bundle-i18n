<?php

declare(strict_types=1);

namespace BinSoul\Symfony\Bundle\I18n\Translation;

use BinSoul\Common\I18n\DefaultLocale;
use BinSoul\Common\I18n\DefaultTranslatedMessage;
use BinSoul\Common\I18n\DefaultTranslator;
use BinSoul\Common\I18n\Locale;
use BinSoul\Common\I18n\TranslatedMessage;
use InvalidArgumentException;
use Locale as IntlLocale;
use Symfony\Component\Translation\TranslatorBagInterface;
use Symfony\Contracts\Translation\LocaleAwareInterface;
use Symfony\Contracts\Translation\TranslatorInterface;

/**
 * Implements the {@see \BinSoul\Common\I18n\Translator Translator} interface using the Symfony translation component.
 */
class Translator extends DefaultTranslator
{
    private readonly TranslatorInterface $translator;

    /**
     * Constructs an instance of this class.
     *
     * @param TranslatorInterface|TranslatorBagInterface|LocaleAwareInterface $translator
     */
    public function __construct($translator, ?Locale $locale = null)
    {
        parent::__construct($locale ?? DefaultLocale::fromString(IntlLocale::getDefault()));

        if (! $translator instanceof TranslatorInterface || ! $translator instanceof TranslatorBagInterface || ! $translator instanceof LocaleAwareInterface) {
            throw new InvalidArgumentException(sprintf('The Translator "%s" must implement TranslatorInterface, TranslatorBagInterface and LocaleAwareInterface.', $translator::class));
        }

        $this->translator = $translator;
    }

    public function translate($key, array $parameters = [], ?string $domain = null): TranslatedMessage
    {
        /** @var DefaultTranslatedMessage $message */
        $message = parent::translate($key, $parameters, $domain);

        $parameters = array_merge($message->getParameters() ?? [], $parameters);
        $quantity = $message->getQuantity();

        if ($quantity !== null) {
            $parameters['quantity'] = $quantity;
            $parameters['%count%'] = $quantity;
        }

        $translation = $this->translator->trans($message->getKey(), $parameters, $message->getDomain(), $this->locale->getCode('_'));

        return new DefaultTranslatedMessage($message->getDecoratedMessage(), $translation, $this->locale);
    }
}
