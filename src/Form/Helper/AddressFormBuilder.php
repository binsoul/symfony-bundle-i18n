<?php

declare(strict_types=1);

namespace BinSoul\Symfony\Bundle\I18n\Form\Helper;

use BinSoul\Common\I18n\AddressFormatter;
use BinSoul\Common\I18n\Data\StateData;
use BinSoul\Common\I18n\DefaultAddress;
use BinSoul\Common\I18n\MutableAddress;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\FormEvent;
use Symfony\Component\Form\FormEvents;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\Form\FormTypeInterface;
use Symfony\Component\Validator\Constraints\NotBlank;
use Symfony\Component\Validator\Constraints\Regex;

/**
 * Generates all possible form fields depending on the country of an address.
 */
class AddressFormBuilder
{
    private readonly AddressFormatter $addressFormatter;

    private ?string $defaultCountry = null;

    /**
     * @var callable|null
     */
    private $labelTranslator;

    private bool $allFieldsOptional = false;

    private bool $allFieldsVisible = false;

    private bool $allFieldsDisabled = false;

    /**
     * @var array{message?: ?string, htmlPattern?: ?string, match?: ?bool, normalizer?: ?callable, groups?: array<string>, payload?: mixed, allowNull?: ?bool}
     */
    private array $constraintOptions = [];

    /**
     * @var callable|null
     */
    private $dataProvider;

    /**
     * @var array{enabled: bool, field: string, type: class-string<FormTypeInterface>, attr: array<string, mixed>}
     */
    private array $countryOptions = [
        'enabled' => true,
        'field' => 'countryCode',
        'type' => ChoiceType::class,
        'attr' => [
            'label' => 'Country',
            'required' => false,
            'attr' => [
                'autocomplete' => 'country',
            ],
        ],
    ];

    /**
     * @var array{enabled: bool, field: string, type: class-string<FormTypeInterface>, attr: array<string, mixed>}
     */
    private array $addressLine1Options = [
        'enabled' => true,
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

    /**
     * @var array{enabled: bool, field: string, type: class-string<FormTypeInterface>, attr: array<string, mixed>}
     */
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

    /**
     * @var array{enabled: bool, field: string, type: class-string<FormTypeInterface>, attr: array<string, mixed>}
     */
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

    /**
     * @var array{enabled: bool, field: string, type: class-string<FormTypeInterface>, attr: array<string, mixed>}
     */
    private array $postalCodeOptions = [
        'enabled' => true,
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

    /**
     * @var array{enabled: bool, enableChoice: bool, forceDisplay: bool, field: string, type: class-string<FormTypeInterface>, attr: array<string, mixed>}
     */
    private array $stateOptions = [
        'enabled' => true,
        'enableChoice' => true,
        'forceDisplay' => false,
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

    /**
     * @var array{enabled: bool, field: string, type: class-string<FormTypeInterface>, attr: array<string, mixed>}
     */
    private array $localityOptions = [
        'enabled' => true,
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

    /**
     * @var array{enabled: bool, field: string, type: class-string<FormTypeInterface>, attr: array<string, mixed>}
     */
    private array $subLocalityOptions = [
        'enabled' => true,
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

    /**
     * @var array{enabled: bool, field: string, type: class-string<FormTypeInterface>, attr: array<string, mixed>}
     */
    private array $sortingCodeOptions = [
        'enabled' => true,
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

    /**
     * Marks all fields as optional.
     *
     * @return $this
     */
    public function makeAllFieldsOptional(): self
    {
        $this->allFieldsOptional = true;

        return $this;
    }

    /**
     * Marks all fields as visible except when they are disabled explicitly.
     *
     * @return $this
     */
    public function makeAllFieldsVisible(): self
    {
        $this->allFieldsVisible = true;

        return $this;
    }

    /**
     * Marks all fields as disabled.
     *
     * @return $this
     */
    public function makeAllFieldsDisabled(): self
    {
        $this->allFieldsDisabled = true;

        return $this;
    }

    /**
     * @param array{message?: ?string, htmlPattern?: ?string, match?: ?bool, normalizer?: ?callable, groups?: array<string>, payload?: mixed, allowNull?: ?bool} $constraintOptions
     */
    public function withConstraintOptions(array $constraintOptions): self
    {
        $this->constraintOptions = $constraintOptions;

        return $this;
    }

    public function withDataProvider(callable $dataProvider): self
    {
        $this->dataProvider = $dataProvider;

        return $this;
    }

    /**
     * @param class-string<FormTypeInterface> $fieldType
     * @param array<string, mixed>            $fieldOptions
     */
    public function withCountry(string $fieldName, string $fieldType, array $fieldOptions): self
    {
        $this->countryOptions['field'] = $fieldName;
        $this->countryOptions['type'] = $fieldType;
        $this->countryOptions['attr'] = $this->merge($this->countryOptions['attr'], $fieldOptions);

        return $this;
    }

    public function withoutCountry(): self
    {
        $this->countryOptions['enabled'] = false;

        return $this;
    }

    /**
     * @param class-string<FormTypeInterface> $fieldType
     * @param array<string, mixed>            $fieldOptions
     */
    public function withAddressLine1(string $fieldName, string $fieldType, array $fieldOptions): self
    {
        $this->addressLine1Options['field'] = $fieldName;
        $this->addressLine1Options['type'] = $fieldType;
        $this->addressLine1Options['attr'] = $this->merge($this->addressLine1Options['attr'], $fieldOptions);

        return $this;
    }

    public function withoutAddressLine1(): self
    {
        $this->addressLine1Options['enabled'] = false;

        return $this;
    }

    /**
     * @param class-string<FormTypeInterface> $fieldType
     * @param array<string, mixed>            $fieldOptions
     */
    public function withAddressLine2(string $fieldName, string $fieldType, array $fieldOptions): self
    {
        $this->addressLine2Options['enabled'] = true;
        $this->addressLine2Options['field'] = $fieldName;
        $this->addressLine2Options['type'] = $fieldType;
        $this->addressLine2Options['attr'] = $this->merge($this->addressLine2Options['attr'], $fieldOptions);

        return $this;
    }

    public function withoutAddressLine2(): self
    {
        $this->addressLine2Options['enabled'] = false;

        return $this;
    }

    /**
     * @param class-string<FormTypeInterface> $fieldType
     * @param array<string, mixed>            $fieldOptions
     */
    public function withAddressLine3(string $fieldName, string $fieldType, array $fieldOptions): self
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

    /**
     * @param class-string<FormTypeInterface> $fieldType
     * @param array<string, mixed>            $fieldOptions
     */
    public function withPostalCode(string $fieldName, string $fieldType, array $fieldOptions): self
    {
        $this->postalCodeOptions['field'] = $fieldName;
        $this->postalCodeOptions['type'] = $fieldType;
        $this->postalCodeOptions['attr'] = $this->merge($this->postalCodeOptions['attr'], $fieldOptions);

        return $this;
    }

    public function withoutPostalCode(): self
    {
        $this->postalCodeOptions['enabled'] = false;

        return $this;
    }

    /**
     * @param class-string<FormTypeInterface> $fieldType
     * @param array<string, mixed>            $fieldOptions
     */
    public function withState(string $fieldName, string $fieldType, array $fieldOptions): self
    {
        $this->stateOptions['field'] = $fieldName;
        $this->stateOptions['type'] = $fieldType;
        $this->stateOptions['attr'] = $this->merge($this->stateOptions['attr'], $fieldOptions);

        return $this;
    }

    public function withoutState(): self
    {
        $this->stateOptions['enabled'] = false;

        return $this;
    }

    public function withoutStateChoice(): self
    {
        $this->stateOptions['enableChoice'] = false;

        return $this;
    }

    public function forceStateDisplay(): self
    {
        $this->stateOptions['forceDisplay'] = true;

        return $this;
    }

    /**
     * @param class-string<FormTypeInterface> $fieldType
     * @param array<string, mixed>            $fieldOptions
     */
    public function withLocality(string $fieldName, string $fieldType, array $fieldOptions): self
    {
        $this->localityOptions['field'] = $fieldName;
        $this->localityOptions['type'] = $fieldType;
        $this->localityOptions['attr'] = $this->merge($this->localityOptions['attr'], $fieldOptions);

        return $this;
    }

    public function withoutLocality(): self
    {
        $this->localityOptions['enabled'] = false;

        return $this;
    }

    /**
     * @param class-string<FormTypeInterface> $fieldType
     * @param array<string, mixed>            $fieldOptions
     */
    public function withSubLocality(string $fieldName, string $fieldType, array $fieldOptions): self
    {
        $this->subLocalityOptions['field'] = $fieldName;
        $this->subLocalityOptions['type'] = $fieldType;
        $this->subLocalityOptions['attr'] = $this->merge($this->subLocalityOptions['attr'], $fieldOptions);

        return $this;
    }

    public function withoutSubLocality(): self
    {
        $this->subLocalityOptions['enabled'] = false;

        return $this;
    }

    /**
     * @param class-string<FormTypeInterface> $fieldType
     * @param array<string, mixed>            $fieldOptions
     */
    public function withSortingCode(string $fieldName, string $fieldType, array $fieldOptions): self
    {
        $this->sortingCodeOptions['field'] = $fieldName;
        $this->sortingCodeOptions['type'] = $fieldType;
        $this->sortingCodeOptions['attr'] = $this->merge($this->sortingCodeOptions['attr'], $fieldOptions);

        return $this;
    }

    public function withoutSortingCode(): self
    {
        $this->sortingCodeOptions['enabled'] = false;

        return $this;
    }

    public function build(FormBuilderInterface $builder): void
    {
        $attr = $this->countryOptions['attr'];

        if (! $this->allFieldsOptional) {
            $attr['required'] = true;
            $constraints = (array) ($attr['constraints'] ?? []);
            $constraints[] = new NotBlank(
                null,
                $this->constraintOptions['message'] ?? null,
                $this->constraintOptions['allowNull'] ?? null,
                $this->constraintOptions['normalizer'] ?? null,
                $this->constraintOptions['groups'] ?? null,
                $this->constraintOptions['payload'] ?? null
            );
            $attr['constraints'] = $constraints;
        }

        if ($this->allFieldsDisabled) {
            $attr['disabled'] = true;
        }

        if ($this->countryOptions['enabled']) {
            $builder->add($this->countryOptions['field'], $this->countryOptions['type'], $attr);
        }

        $builder->addEventListener(
            FormEvents::PRE_SET_DATA,
            function (FormEvent $event): void {
                $object = $event->getData();
                $data = [];

                if ($this->dataProvider !== null) {
                    $object = ($this->dataProvider)($object);
                }

                $this->modifyForm($event->getForm(), $object instanceof MutableAddress ? $object : null, $data);
            }
        );

        $builder->addEventListener(
            FormEvents::PRE_SUBMIT,
            function (FormEvent $event): void {
                $object = $event->getForm()->getData();
                $data = $event->getData();

                if ($this->dataProvider !== null) {
                    $object = ($this->dataProvider)($object);
                }

                if (is_array($data)) {
                    $modifiedData = $this->modifyForm($event->getForm(), $object instanceof MutableAddress ? $object : null, $data);
                    $event->setData($modifiedData);
                }
            }
        );
    }

    /**
     * @param FormInterface<mixed>    $builder
     * @param array<array-key, mixed> $data
     *
     * @return array<array-key, mixed>
     */
    private function modifyForm(FormInterface $builder, ?MutableAddress $object, array $data): array
    {
        $countryCode = $this->defaultCountry ?? '';

        $object ??= new DefaultAddress();

        $providedCountryCode = $data[$this->countryOptions['field']] ?? '';
        $providedCountryCode = is_scalar($providedCountryCode) ? trim((string) $providedCountryCode) : '';

        if ($providedCountryCode !== '') {
            $countryCode = $providedCountryCode;
        } elseif (trim((string) $object->getCountryCode()) !== '') {
            $countryCode = trim((string) $object->getCountryCode());
        }

        $usageTemplate = $this->addressFormatter->generateUsageTemplate($countryCode);
        $regexTemplate = $this->addressFormatter->generateRegexTemplate($countryCode);
        $labelTemplate = $this->addressFormatter->generateLabelTemplate($countryCode);
        $translator = $this->labelTranslator;

        $constraintOptions = $this->constraintOptions;

        if ($this->addressLine1Options['enabled'] && ($this->allFieldsVisible || $usageTemplate->getAddressLine1())) {
            $attr = $this->addressLine1Options['attr'];
            $constraints = (array) ($attr['constraints'] ?? []);

            if (! $this->allFieldsOptional && ($usageTemplate->getAddressLine1() === 'required' || $this->addressLine1Options['attr']['required'])) {
                $constraints[] = new NotBlank(
                    null,
                    $constraintOptions['message'] ?? null,
                    $constraintOptions['allowNull'] ?? null,
                    $constraintOptions['normalizer'] ?? null,
                    $constraintOptions['groups'] ?? null,
                    $constraintOptions['payload'] ?? null
                );

                $attr['required'] = true;
            }

            if ($regexTemplate->getAddressLine1()) {
                $constraints[] = new Regex(
                    '/' . $regexTemplate->getAddressLine1() . '/',
                    $constraintOptions['message'] ?? null,
                    $constraintOptions['htmlPattern'] ?? null,
                    $constraintOptions['match'] ?? null,
                    $constraintOptions['normalizer'] ?? null,
                    $constraintOptions['groups'] ?? null,
                    $constraintOptions['payload'] ?? null,
                );
            }

            $attr['constraints'] = $constraints;

            if ($translator !== null && $labelTemplate->getAddressLine1()) {
                $attr['label'] = $translator($this->addressLine1Options['field'], $labelTemplate->getAddressLine1());
            }

            if ($this->allFieldsDisabled) {
                $attr['disabled'] = true;
            }

            $builder->add($this->addressLine1Options['field'], $this->addressLine1Options['type'], $attr);
            $data[$this->addressLine1Options['field']] ??= null;
        } else {
            $builder->remove($this->addressLine1Options['field']);
            $object->setAddressLine1(null);
            unset($data[$this->addressLine1Options['field']]);
        }

        if ($this->addressLine2Options['enabled'] && ($this->allFieldsVisible || $usageTemplate->getAddressLine2())) {
            $attr = $this->addressLine2Options['attr'];
            $constraints = (array) ($attr['constraints'] ?? []);

            if (! $this->allFieldsOptional && ($usageTemplate->getAddressLine2() === 'required' || $this->addressLine2Options['attr']['required'])) {
                $constraints[] = new NotBlank(
                    null,
                    $constraintOptions['message'] ?? null,
                    $constraintOptions['allowNull'] ?? null,
                    $constraintOptions['normalizer'] ?? null,
                    $constraintOptions['groups'] ?? null,
                    $constraintOptions['payload'] ?? null
                );
                $attr['required'] = true;
            }

            if ($regexTemplate->getAddressLine2()) {
                $constraints[] = new Regex(
                    '/' . $regexTemplate->getAddressLine2() . '/',
                    $constraintOptions['message'] ?? null,
                    $constraintOptions['htmlPattern'] ?? null,
                    $constraintOptions['match'] ?? null,
                    $constraintOptions['normalizer'] ?? null,
                    $constraintOptions['groups'] ?? null,
                    $constraintOptions['payload'] ?? null,
                );
            }

            $attr['constraints'] = $constraints;

            if ($translator !== null && $labelTemplate->getAddressLine2()) {
                $attr['label'] = $translator($this->addressLine2Options['field'], $labelTemplate->getAddressLine2());
            }

            if ($this->allFieldsDisabled) {
                $attr['disabled'] = true;
            }

            $builder->add($this->addressLine2Options['field'], $this->addressLine2Options['type'], $attr);
            $data[$this->addressLine2Options['field']] ??= null;
        } else {
            $builder->remove($this->addressLine2Options['field']);
            $object->setAddressLine2(null);
            unset($data[$this->addressLine2Options['field']]);
        }

        if ($this->addressLine3Options['enabled'] && ($this->allFieldsVisible || $usageTemplate->getAddressLine3())) {
            $attr = $this->addressLine3Options['attr'];
            $constraints = (array) ($attr['constraints'] ?? []);

            if (! $this->allFieldsOptional && ($usageTemplate->getAddressLine3() === 'required' || $this->addressLine3Options['attr']['required'])) {
                $constraints[] = new NotBlank(
                    null,
                    $constraintOptions['message'] ?? null,
                    $constraintOptions['allowNull'] ?? null,
                    $constraintOptions['normalizer'] ?? null,
                    $constraintOptions['groups'] ?? null,
                    $constraintOptions['payload'] ?? null
                );
                $attr['required'] = true;
            }

            if ($regexTemplate->getAddressLine3()) {
                $constraints[] = new Regex(
                    '/' . $regexTemplate->getAddressLine3() . '/',
                    $constraintOptions['message'] ?? null,
                    $constraintOptions['htmlPattern'] ?? null,
                    $constraintOptions['match'] ?? null,
                    $constraintOptions['normalizer'] ?? null,
                    $constraintOptions['groups'] ?? null,
                    $constraintOptions['payload'] ?? null,
                );
            }

            $attr['constraints'] = $constraints;

            if ($translator !== null && $labelTemplate->getAddressLine3()) {
                $attr['label'] = $translator($this->addressLine3Options['field'], $labelTemplate->getAddressLine3());
            }

            if ($this->allFieldsDisabled) {
                $attr['disabled'] = true;
            }

            $builder->add($this->addressLine3Options['field'], $this->addressLine3Options['type'], $attr);
            $data[$this->addressLine3Options['field']] ??= null;
        } else {
            $builder->remove($this->addressLine3Options['field']);
            $object->setAddressLine3(null);
            unset($data[$this->addressLine3Options['field']]);
        }

        if ($this->postalCodeOptions['enabled'] && ($this->allFieldsVisible || $usageTemplate->getPostalCode())) {
            $attr = $this->postalCodeOptions['attr'];
            $constraints = (array) ($attr['constraints'] ?? []);

            if (! $this->allFieldsOptional && ($usageTemplate->getPostalCode() === 'required' || $this->postalCodeOptions['attr']['required'])) {
                $constraints[] = new NotBlank(
                    null,
                    $constraintOptions['message'] ?? null,
                    $constraintOptions['allowNull'] ?? null,
                    $constraintOptions['normalizer'] ?? null,
                    $constraintOptions['groups'] ?? null,
                    $constraintOptions['payload'] ?? null
                );
                $attr['required'] = true;
            }

            if ($regexTemplate->getPostalCode()) {
                $constraints[] = new Regex(
                    '/' . $regexTemplate->getPostalCode() . '/',
                    $constraintOptions['message'] ?? null,
                    $constraintOptions['htmlPattern'] ?? null,
                    $constraintOptions['match'] ?? null,
                    $constraintOptions['normalizer'] ?? null,
                    $constraintOptions['groups'] ?? null,
                    $constraintOptions['payload'] ?? null,
                );
            }

            $attr['constraints'] = $constraints;

            if ($translator !== null && $labelTemplate->getPostalCode()) {
                $attr['label'] = $translator($this->postalCodeOptions['field'], $labelTemplate->getPostalCode());
            }

            if ($this->allFieldsDisabled) {
                $attr['disabled'] = true;
            }

            $builder->add($this->postalCodeOptions['field'], $this->postalCodeOptions['type'], $attr);
            $data[$this->postalCodeOptions['field']] ??= null;
        } else {
            $builder->remove($this->postalCodeOptions['field']);
            $object->setPostalCode(null);
            unset($data[$this->postalCodeOptions['field']]);
        }

        $forceState = $this->stateOptions['forceDisplay'] && (StateData::type($countryCode) !== null);

        if ($forceState || ($this->stateOptions['enabled'] && ($this->allFieldsVisible || $usageTemplate->getState()))) {
            $attr = $this->stateOptions['attr'];
            $constraints = (array) ($attr['constraints'] ?? []);

            if (! $this->allFieldsOptional && ($usageTemplate->getState() === 'required' || $this->stateOptions['attr']['required'])) {
                $constraints[] = new NotBlank(
                    null,
                    $constraintOptions['message'] ?? null,
                    $constraintOptions['allowNull'] ?? null,
                    $constraintOptions['normalizer'] ?? null,
                    $constraintOptions['groups'] ?? null,
                    $constraintOptions['payload'] ?? null
                );
                $attr['required'] = true;
            }

            if ($regexTemplate->getState()) {
                $constraints[] = new Regex(
                    '/' . $regexTemplate->getState() . '/',
                    $constraintOptions['message'] ?? null,
                    $constraintOptions['htmlPattern'] ?? null,
                    $constraintOptions['match'] ?? null,
                    $constraintOptions['normalizer'] ?? null,
                    $constraintOptions['groups'] ?? null,
                    $constraintOptions['payload'] ?? null,
                );
            }

            $attr['constraints'] = $constraints;

            if ($translator !== null && $labelTemplate->getState()) {
                $attr['label'] = $translator($this->stateOptions['field'], $labelTemplate->getState());
            }

            if ($this->allFieldsDisabled) {
                $attr['disabled'] = true;
            }

            $names = $this->stateOptions['enableChoice'] ? StateData::names($countryCode) : [];

            if ($names !== []) {
                if (! StateData::useCode($countryCode)) {
                    $attr['choices'] = array_combine($names, $names);
                } else {
                    $attr['choices'] = array_combine($names, StateData::codes($countryCode));
                }

                $builder->add($this->stateOptions['field'], ChoiceType::class, $attr);
            } else {
                $builder->add($this->stateOptions['field'], $this->stateOptions['type'], $attr);
            }

            $data[$this->stateOptions['field']] ??= null;
        } else {
            $builder->remove($this->stateOptions['field']);
            $object->setState(null);
            unset($data[$this->stateOptions['field']]);
        }

        if ($this->localityOptions['enabled'] && ($this->allFieldsVisible || $usageTemplate->getLocality())) {
            $attr = $this->localityOptions['attr'];
            $constraints = (array) ($attr['constraints'] ?? []);

            if (! $this->allFieldsOptional && ($usageTemplate->getLocality() === 'required' || $this->localityOptions['attr']['required'])) {
                $constraints[] = new NotBlank(
                    null,
                    $constraintOptions['message'] ?? null,
                    $constraintOptions['allowNull'] ?? null,
                    $constraintOptions['normalizer'] ?? null,
                    $constraintOptions['groups'] ?? null,
                    $constraintOptions['payload'] ?? null
                );
                $attr['required'] = true;
            }

            if ($regexTemplate->getLocality()) {
                $constraints[] = new Regex(
                    '/' . $regexTemplate->getLocality() . '/',
                    $constraintOptions['message'] ?? null,
                    $constraintOptions['htmlPattern'] ?? null,
                    $constraintOptions['match'] ?? null,
                    $constraintOptions['normalizer'] ?? null,
                    $constraintOptions['groups'] ?? null,
                    $constraintOptions['payload'] ?? null,
                );
            }

            $attr['constraints'] = $constraints;

            if ($translator !== null && $labelTemplate->getLocality()) {
                $attr['label'] = $translator($this->localityOptions['field'], $labelTemplate->getLocality());
            }

            if ($this->allFieldsDisabled) {
                $attr['disabled'] = true;
            }

            $builder->add($this->localityOptions['field'], $this->localityOptions['type'], $attr);
            $data[$this->localityOptions['field']] ??= null;
        } else {
            $builder->remove($this->localityOptions['field']);
            $object->setLocality(null);
            unset($data[$this->localityOptions['field']]);
        }

        if ($this->subLocalityOptions['enabled'] && ($this->allFieldsVisible || $usageTemplate->getSubLocality())) {
            $attr = $this->subLocalityOptions['attr'];
            $constraints = (array) ($attr['constraints'] ?? []);

            if (! $this->allFieldsOptional && ($usageTemplate->getSubLocality() === 'required' || $this->subLocalityOptions['attr']['required'])) {
                $constraints[] = new NotBlank(
                    null,
                    $constraintOptions['message'] ?? null,
                    $constraintOptions['allowNull'] ?? null,
                    $constraintOptions['normalizer'] ?? null,
                    $constraintOptions['groups'] ?? null,
                    $constraintOptions['payload'] ?? null
                );
                $attr['required'] = true;
            }

            if ($regexTemplate->getSubLocality()) {
                $constraints[] = new Regex(
                    '/' . $regexTemplate->getSubLocality() . '/',
                    $constraintOptions['message'] ?? null,
                    $constraintOptions['htmlPattern'] ?? null,
                    $constraintOptions['match'] ?? null,
                    $constraintOptions['normalizer'] ?? null,
                    $constraintOptions['groups'] ?? null,
                    $constraintOptions['payload'] ?? null,
                );
            }

            $attr['constraints'] = $constraints;

            if ($translator !== null && $labelTemplate->getSubLocality()) {
                $attr['label'] = $translator($this->subLocalityOptions['field'], $labelTemplate->getSubLocality());
            }

            if ($this->allFieldsDisabled) {
                $attr['disabled'] = true;
            }

            $builder->add($this->subLocalityOptions['field'], $this->subLocalityOptions['type'], $attr);
            $data[$this->subLocalityOptions['field']] ??= null;
        } else {
            $builder->remove($this->subLocalityOptions['field']);
            $object->setSubLocality(null);
            unset($data[$this->subLocalityOptions['field']]);
        }

        if ($this->sortingCodeOptions['enabled'] && ($this->allFieldsVisible || $usageTemplate->getSortingCode())) {
            $attr = $this->sortingCodeOptions['attr'];
            $constraints = (array) ($attr['constraints'] ?? []);

            if (! $this->allFieldsOptional && ($usageTemplate->getSortingCode() === 'required' || $this->sortingCodeOptions['attr']['required'])) {
                $constraints[] = new NotBlank(
                    null,
                    $constraintOptions['message'] ?? null,
                    $constraintOptions['allowNull'] ?? null,
                    $constraintOptions['normalizer'] ?? null,
                    $constraintOptions['groups'] ?? null,
                    $constraintOptions['payload'] ?? null
                );
                $attr['required'] = true;
            }

            if ($regexTemplate->getSortingCode()) {
                $constraints[] = new Regex(
                    '/' . $regexTemplate->getSortingCode() . '/',
                    $constraintOptions['message'] ?? null,
                    $constraintOptions['htmlPattern'] ?? null,
                    $constraintOptions['match'] ?? null,
                    $constraintOptions['normalizer'] ?? null,
                    $constraintOptions['groups'] ?? null,
                    $constraintOptions['payload'] ?? null,
                );
            }

            $attr['constraints'] = $constraints;

            if ($translator !== null && $labelTemplate->getSortingCode()) {
                $attr['label'] = $translator($this->sortingCodeOptions['field'], $labelTemplate->getSortingCode());
            }

            if ($this->allFieldsDisabled) {
                $attr['disabled'] = true;
            }

            $builder->add($this->sortingCodeOptions['field'], $this->sortingCodeOptions['type'], $attr);
            $data[$this->sortingCodeOptions['field']] ??= null;
        } else {
            $builder->remove($this->sortingCodeOptions['field']);
            $object->setSortingCode(null);
            unset($data[$this->sortingCodeOptions['field']]);
        }

        return $data;
    }

    /**
     * @param array<array-key, mixed> $array1
     * @param array<array-key, mixed> $array2
     *
     * @return array<string, mixed>
     */
    private function merge(array $array1, array $array2): array
    {
        $merged = $this->toMap($array1);

        foreach ($this->toMap($array2) as $key => $value) {
            if (is_array($value) && isset($merged[$key]) && is_array($merged[$key])) {
                $merged[$key] = $this->merge($merged[$key], $value);
            } else {
                $merged[$key] = $value;
            }
        }

        return $merged;
    }

    /**
     * @param array<array-key, mixed> $value
     *
     * @return array<string, mixed>
     */
    private function toMap(array $value): array
    {
        $result = [];

        foreach ($value as $key => $item) {
            $result[(string) $key] = $item;
        }

        return $result;
    }
}
