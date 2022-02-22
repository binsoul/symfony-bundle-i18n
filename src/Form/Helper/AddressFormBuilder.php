<?php

declare(strict_types=1);

namespace BinSoul\Symfony\Bundle\I18n\Form\Helper;

use BinSoul\Common\I18n\AddressFormatter;
use BinSoul\Common\I18n\MutableAddress;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\FormEvent;
use Symfony\Component\Form\FormEvents;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\Validator\Constraints\NotBlank;
use Symfony\Component\Validator\Constraints\Regex;

/**
 * Generates all possible form fields depending on the country of an address.
 */
class AddressFormBuilder
{
    private AddressFormatter $addressFormatter;

    private ?string $defaultCountry = null;

    /**
     * @var callable|null
     */
    private $labelTranslator;

    private array $countryOptions = [
        'field' => 'countryCode',
        'type' => ChoiceType::class,
        'attr' => [
            'label' => 'Country',
            'required' => true,
            'attr' => [
                'autocomplete' => 'country',
            ],
        ],
    ];

    private array $addressLine1Options = [
        'field' => 'addressLine1',
        'type' => TextType::class,
        'attr' => [
            'label' => 'Address line 1',
            'required' => false,
            'attr' => [
                'autocomplete' => 'address-line1',
            ],
        ],
    ];

    private array $addressLine2Options = [
        'enabled' => true,
        'field' => 'addressLine2',
        'type' => TextType::class,
        'attr' => [
            'label' => 'Address line 2',
            'required' => false,
            'attr' => [
                'autocomplete' => 'address-line2',
            ],
        ],
    ];

    private array $addressLine3Options = [
        'enabled' => true,
        'field' => 'addressLine3',
        'type' => TextType::class,
        'attr' => [
            'label' => 'Address line 3',
            'required' => false,
            'attr' => [
                'autocomplete' => 'address-line3',
            ],
        ],
    ];

    private array $postalCodeOptions = [
        'field' => 'postalCode',
        'type' => TextType::class,
        'attr' => [
            'label' => 'Postal code',
            'required' => false,
            'attr' => [
                'autocomplete' => 'postal-code',
            ],
        ],
    ];

    private array $stateOptions = [
        'field' => 'state',
        'type' => TextType::class,
        'attr' => [
            'label' => 'State',
            'required' => false,
            'attr' => [
                'autocomplete' => 'address-level1',
            ],
        ],
    ];

    private array $localityOptions = [
        'field' => 'locality',
        'type' => TextType::class,
        'attr' => [
            'label' => 'Locality',
            'required' => false,
            'attr' => [
                'autocomplete' => 'address-level2',
            ],
        ],
    ];

    private array $subLocalityOptions = [
        'field' => 'subLocality',
        'type' => TextType::class,
        'attr' => [
            'label' => 'Sub locality',
            'required' => false,
            'attr' => [
                'autocomplete' => 'address-level3',
            ],
        ],
    ];

    private array $sortingCodeOptions = [
        'field' => 'sortingCode',
        'type' => TextType::class,
        'attr' => [
            'label' => 'Sorting code',
            'required' => false,
            'attr' => [
                'autocomplete' => 'sorting-code',
            ],
        ],
    ];

    public function __construct(AddressFormatter $addressFormatter)
    {
        $this->addressFormatter = $addressFormatter;
    }

    public function withDefaultCountry(string $countryCode): self
    {
        $this->defaultCountry = $countryCode;

        return $this;
    }

    public function withLabelTranslator(callable $translator): self
    {
        $this->labelTranslator = $translator;

        return $this;
    }

    public function withCountry(string $fieldName, $fieldType, array $fieldOptions): self
    {
        $this->countryOptions['field'] = $fieldName;
        $this->countryOptions['type'] = $fieldType;

        $fieldOptions['required'] = true;

        if (count($fieldOptions['constraints'] ?? []) === 0) {
            $fieldOptions['constraints'] = [new NotBlank()];
        }

        $this->countryOptions['attr'] = $this->merge($this->countryOptions['attr'], $fieldOptions);

        return $this;
    }

    public function withAddressLine1(string $fieldName, $fieldType, array $fieldOptions): self
    {
        $this->addressLine1Options['field'] = $fieldName;
        $this->addressLine1Options['type'] = $fieldType;
        $this->addressLine1Options['attr'] = $this->merge($this->addressLine1Options['attr'], $fieldOptions);

        return $this;
    }

    public function withAddressLine2(string $fieldName, $fieldType, array $fieldOptions): self
    {
        $this->addressLine2Options['enabled'] = true;
        $this->addressLine2Options['field'] = $fieldName;
        $this->addressLine2Options['type'] = $fieldType;
        $this->addressLine2Options['attr'] = $this->merge($this->addressLine1Options['attr'], $fieldOptions);

        return $this;
    }

    public function withoutAddressLine2(): self
    {
        $this->addressLine2Options['enabled'] = false;

        return $this;
    }

    public function withAddressLine3(string $fieldName, $fieldType, array $fieldOptions): self
    {
        $this->addressLine3Options['enabled'] = true;
        $this->addressLine3Options['field'] = $fieldName;
        $this->addressLine3Options['type'] = $fieldType;
        $this->addressLine3Options['attr'] = $this->merge($this->addressLine3Options['attr'], $fieldOptions);

        return $this;
    }

    public function withoutAddressLine3(): self
    {
        $this->addressLine3Options['enabled'] = false;

        return $this;
    }

    public function withPostalCode(string $fieldName, $fieldType, array $fieldOptions): self
    {
        $this->postalCodeOptions['field'] = $fieldName;
        $this->postalCodeOptions['type'] = $fieldType;
        $this->postalCodeOptions['attr'] = $this->merge($this->postalCodeOptions['attr'], $fieldOptions);

        return $this;
    }

    public function withState(string $fieldName, $fieldType, array $fieldOptions): self
    {
        $this->stateOptions['field'] = $fieldName;
        $this->stateOptions['type'] = $fieldType;
        $this->stateOptions['attr'] = $this->merge($this->stateOptions['attr'], $fieldOptions);

        return $this;
    }

    public function withLocality(string $fieldName, $fieldType, array $fieldOptions): self
    {
        $this->localityOptions['field'] = $fieldName;
        $this->localityOptions['type'] = $fieldType;
        $this->localityOptions['attr'] = $this->merge($this->localityOptions['attr'], $fieldOptions);

        return $this;
    }

    public function withSubLocality(string $fieldName, $fieldType, array $fieldOptions): self
    {
        $this->subLocalityOptions['field'] = $fieldName;
        $this->subLocalityOptions['type'] = $fieldType;
        $this->subLocalityOptions['attr'] = $this->merge($this->subLocalityOptions['attr'], $fieldOptions);

        return $this;
    }

    public function withSortingCode(string $fieldName, $fieldType, array $fieldOptions): self
    {
        $this->sortingCodeOptions['field'] = $fieldName;
        $this->sortingCodeOptions['type'] = $fieldType;
        $this->sortingCodeOptions['attr'] = $this->merge($this->sortingCodeOptions['attr'], $fieldOptions);

        return $this;
    }

    public function build(FormBuilderInterface $builder): void
    {
        $builder->add($this->countryOptions['field'], $this->countryOptions['type'], $this->countryOptions['attr']);

        $builder->addEventListener(
            FormEvents::PRE_SET_DATA,
            function (FormEvent $event) {
                $object = $event->getData();
                $data = [];

                $this->modifyForm($event->getForm(), $object, $data);
            }
        );

        $builder->addEventListener(
            FormEvents::PRE_SUBMIT,
            function (FormEvent $event) {
                $object = $event->getForm()->getData();
                $data = $event->getData();
                $event->setData($this->modifyForm($event->getForm(), $object, $data));
            }
        );
    }

    private function modifyForm(FormInterface $builder, MutableAddress $object, ?array $data): array
    {
        $countryCode = $this->defaultCountry;

        if (trim((string) $data[$this->countryOptions['field']]) !== '') {
            $countryCode = trim((string) $data[$this->countryOptions['field']]);
        } elseif (trim((string) $object->getCountryCode()) !== '') {
            $countryCode = trim((string) $object->getCountryCode());
        }

        $usageTemplate = $this->addressFormatter->generateUsageTemplate($countryCode);
        $regexTemplate = $this->addressFormatter->generateRegexTemplate($countryCode);
        $labelTemplate = $this->addressFormatter->generateLabelTemplate($countryCode);
        $translator = $this->labelTranslator;

        if ($usageTemplate->getAddressLine1()) {
            $attr = $this->addressLine1Options['attr'];
            $constraints = $attr['constraints'] ?? [];

            if ($usageTemplate->getAddressLine1() === 'required') {
                $constraints[] = new NotBlank();
                $attr['required'] = true;
            }

            if ($regexTemplate->getAddressLine1()) {
                $constraints[] = new Regex('/' . $regexTemplate->getAddressLine1() . '/');
            }

            $attr['constraints'] = $constraints;

            if ($translator !== null && $labelTemplate->getAddressLine1()) {
                $attr['label'] = $translator($this->addressLine1Options['field'], $labelTemplate->getAddressLine1());
            }

            $builder->add($this->addressLine1Options['field'], $this->addressLine1Options['type'], $attr);
            $data[$this->addressLine1Options['field']] = $data[$this->addressLine1Options['field']] ?? null;
        } else {
            $builder->remove($this->addressLine1Options['field']);
            $object->setAddressLine1(null);
            unset($data[$this->addressLine1Options['field']]);
        }

        if ($this->addressLine2Options['enabled'] && $usageTemplate->getAddressLine2()) {
            $attr = $this->addressLine2Options['attr'];
            $constraints = $attr['constraints'] ?? [];

            if ($usageTemplate->getAddressLine2() === 'required') {
                $constraints[] = new NotBlank();
                $attr['required'] = true;
            }

            if ($regexTemplate->getAddressLine2()) {
                $constraints[] = new Regex('/' . $regexTemplate->getAddressLine2() . '/');
            }

            $attr['constraints'] = $constraints;

            if ($translator !== null && $labelTemplate->getAddressLine2()) {
                $attr['label'] = $translator($this->addressLine2Options['field'], $labelTemplate->getAddressLine2());
            }

            $builder->add($this->addressLine2Options['field'], $this->addressLine2Options['type'], $attr);
            $data[$this->addressLine2Options['field']] = $data[$this->addressLine2Options['field']] ?? null;
        } else {
            $builder->remove($this->addressLine2Options['field']);
            $object->setAddressLine2(null);
            unset($data[$this->addressLine2Options['field']]);
        }

        if ($this->addressLine3Options['enabled'] && $usageTemplate->getAddressLine3()) {
            $attr = $this->addressLine3Options['attr'];
            $constraints = $attr['constraints'] ?? [];

            if ($usageTemplate->getAddressLine3() === 'required') {
                $constraints[] = new NotBlank();
                $attr['required'] = true;
            }

            if ($regexTemplate->getAddressLine3()) {
                $constraints[] = new Regex('/' . $regexTemplate->getAddressLine3() . '/');
            }

            $attr['constraints'] = $constraints;

            if ($translator !== null && $labelTemplate->getAddressLine3()) {
                $attr['label'] = $translator($this->addressLine3Options['field'], $labelTemplate->getAddressLine3());
            }

            $builder->add($this->addressLine3Options['field'], $this->addressLine3Options['type'], $attr);
            $data[$this->addressLine3Options['field']] = $data[$this->addressLine3Options['field']] ?? null;
        } else {
            $builder->remove($this->addressLine3Options['field']);
            $object->setAddressLine3(null);
            unset($data[$this->addressLine3Options['field']]);
        }

        if ($usageTemplate->getPostalCode()) {
            $attr = $this->postalCodeOptions['attr'];
            $constraints = $attr['constraints'] ?? [];

            if ($usageTemplate->getPostalCode() === 'required') {
                $constraints[] = new NotBlank();
                $attr['required'] = true;
            }

            if ($regexTemplate->getPostalCode()) {
                $constraints[] = new Regex('/' . $regexTemplate->getPostalCode() . '/');
            }

            $attr['constraints'] = $constraints;

            if ($translator !== null && $labelTemplate->getPostalCode()) {
                $attr['label'] = $translator($this->postalCodeOptions['field'], $labelTemplate->getPostalCode());
            }

            $builder->add($this->postalCodeOptions['field'], $this->postalCodeOptions['type'], $attr);
            $data[$this->postalCodeOptions['field']] = $data[$this->postalCodeOptions['field']] ?? null;
        } else {
            $builder->remove($this->postalCodeOptions['field']);
            $object->setPostalCode(null);
            unset($data[$this->postalCodeOptions['field']]);
        }

        if ($usageTemplate->getState()) {
            $attr = $this->stateOptions['attr'];
            $constraints = $attr['constraints'] ?? [];

            if ($usageTemplate->getState() === 'required') {
                $constraints[] = new NotBlank();
                $attr['required'] = true;
            }

            if ($regexTemplate->getState()) {
                $constraints[] = new Regex('/' . $regexTemplate->getState() . '/');
            }

            $attr['constraints'] = $constraints;

            if ($translator !== null && $labelTemplate->getState()) {
                $attr['label'] = $translator($this->stateOptions['field'], $labelTemplate->getState());
            }

            $builder->add($this->stateOptions['field'], $this->stateOptions['type'], $attr);
            $data[$this->stateOptions['field']] = $data[$this->stateOptions['field']] ?? null;
        } else {
            $builder->remove($this->stateOptions['field']);
            $object->setState(null);
            unset($data[$this->stateOptions['field']]);
        }

        if ($usageTemplate->getLocality()) {
            $attr = $this->localityOptions['attr'];
            $constraints = $attr['constraints'] ?? [];

            if ($usageTemplate->getLocality() === 'required') {
                $constraints[] = new NotBlank();
                $attr['required'] = true;
            }

            if ($regexTemplate->getLocality()) {
                $constraints[] = new Regex('/' . $regexTemplate->getLocality() . '/');
            }

            $attr['constraints'] = $constraints;

            if ($translator !== null && $labelTemplate->getLocality()) {
                $attr['label'] = $translator($this->localityOptions['field'], $labelTemplate->getLocality());
            }

            $builder->add($this->localityOptions['field'], $this->localityOptions['type'], $attr);
            $data[$this->localityOptions['field']] = $data[$this->localityOptions['field']] ?? null;
        } else {
            $builder->remove($this->localityOptions['field']);
            $object->setLocality(null);
            unset($data[$this->localityOptions['field']]);
        }

        if ($usageTemplate->getSubLocality()) {
            $attr = $this->subLocalityOptions['attr'];
            $constraints = $attr['constraints'] ?? [];

            if ($usageTemplate->getSubLocality() === 'required') {
                $constraints[] = new NotBlank();
                $attr['required'] = true;
            }

            if ($regexTemplate->getSubLocality()) {
                $constraints[] = new Regex('/' . $regexTemplate->getSubLocality() . '/');
            }

            $attr['constraints'] = $constraints;

            if ($translator !== null && $labelTemplate->getSubLocality()) {
                $attr['label'] = $translator($this->subLocalityOptions['field'], $labelTemplate->getSubLocality());
            }

            $builder->add($this->subLocalityOptions['field'], $this->subLocalityOptions['type'], $attr);
            $data[$this->subLocalityOptions['field']] = $data[$this->subLocalityOptions['field']] ?? null;
        } else {
            $builder->remove($this->subLocalityOptions['field']);
            $object->setSubLocality(null);
            unset($data[$this->subLocalityOptions['field']]);
        }

        if ($usageTemplate->getSortingCode()) {
            $attr = $this->sortingCodeOptions['attr'];
            $constraints = $attr['constraints'] ?? [];

            if ($usageTemplate->getSortingCode() === 'required') {
                $constraints[] = new NotBlank();
                $attr['required'] = true;
            }

            if ($regexTemplate->getSortingCode()) {
                $constraints[] = new Regex('/' . $regexTemplate->getSortingCode() . '/');
            }

            $attr['constraints'] = $constraints;

            if ($translator !== null && $labelTemplate->getSortingCode()) {
                $attr['label'] = $translator($this->sortingCodeOptions['field'], $labelTemplate->getSortingCode());
            }

            $builder->add($this->sortingCodeOptions['field'], $this->sortingCodeOptions['type'], $attr);
            $data[$this->sortingCodeOptions['field']] = $data[$this->sortingCodeOptions['field']] ?? null;
        } else {
            $builder->remove($this->sortingCodeOptions['field']);
            $object->setSortingCode(null);
            unset($data[$this->sortingCodeOptions['field']]);
        }

        return $data;
    }

    private function merge(array $array1, array $array2): array
    {
        $merged = $array1;

        foreach ($array2 as $key => &$value) {
            if (is_array($value) && isset($merged[$key]) && is_array($merged[$key])) {
                $merged[$key] = $this->merge($merged[$key], $value);
            } else {
                $merged[$key] = $value;
            }
        }

        return $merged;
    }
}
