<?php

declare(strict_types=1);

namespace BinSoul\Test\Symfony\Bundle\I18n\Form\Helper;

use BinSoul\Common\I18n\Address;
use BinSoul\Common\I18n\AddressFormatter;
use BinSoul\Symfony\Bundle\I18n\Form\Helper\AddressFormBuilder;
use PHPUnit\Framework\MockObject\Stub;
use PHPUnit\Framework\TestCase;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\FormEvent;
use Symfony\Component\Form\FormEvents;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\Validator\Constraints\NotBlank;
use Symfony\Component\Validator\Constraints\Regex;

class AddressFormBuilderTest extends TestCase
{
    private $addressFormatter;

    private $builder;

    protected function setUp(): void
    {
        $this->addressFormatter = $this->createStub(AddressFormatter::class);
        $this->builder = new AddressFormBuilder($this->addressFormatter);
    }

    public function test_modify_form_adds_fields_based_on_usage_template(): void
    {
        $countryCode = 'DE';
        $data = ['countryCode' => $countryCode];

        $usageTemplate = $this->createStub(Address::class);
        $fields = [
            'addressLine1' => 'getAddressLine1',
            'addressLine2' => 'getAddressLine2',
            'addressLine3' => 'getAddressLine3',
            'postalCode' => 'getPostalCode',
            'locality' => 'getLocality',
            'subLocality' => 'getSubLocality',
            'state' => 'getState',
            'sortingCode' => 'getSortingCode',
        ];

        foreach ($fields as $method) {
            $usageTemplate->method($method)->willReturn('optional');
        }

        $this->addressFormatter->method('generateUsageTemplate')->willReturn($usageTemplate);
        $this->addressFormatter->method('generateRegexTemplate')->willReturn($this->createStub(Address::class));
        $this->addressFormatter->method('generateLabelTemplate')->willReturn($this->createStub(Address::class));

        $addedFields = [];
        $formMock = $this->createStub(FormInterface::class);
        $formMock->method('add')->willReturnCallback(
            function ($field) use (&$addedFields, $formMock) {
                $addedFields[] = $field;

                return $formMock;
            }
        );
        $formMock->method('remove')->willReturn($formMock);

        $listeners = [];
        $formBuilder = $this->setupFormBuilder($listeners);

        $this->builder->build($formBuilder);

        $this->trigger($listeners, FormEvents::PRE_SUBMIT, new FormEvent($formMock, $data));

        foreach (array_keys($fields) as $fieldName) {
            $this->assertContains($fieldName, $addedFields, "Field {$fieldName} was not added based on usage template.");
        }
    }

    public function test_modify_form_removes_fields_not_in_usage_template(): void
    {
        $countryCode = 'DE';
        $data = [
            'countryCode' => $countryCode,
            'addressLine1' => 'Old Street 1',
            'addressLine2' => 'Suite 100',
            'addressLine3' => 'c/o Someone',
            'postalCode' => '12345',
            'locality' => 'City',
            'subLocality' => 'District',
            'state' => 'State',
            'sortingCode' => 'SC123',
        ];

        $usageTemplate = $this->createStub(Address::class);
        // All fields are null in usageTemplate

        $this->addressFormatter->method('generateUsageTemplate')->willReturn($usageTemplate);
        $this->addressFormatter->method('generateRegexTemplate')->willReturn($this->createStub(Address::class));
        $this->addressFormatter->method('generateLabelTemplate')->willReturn($this->createStub(Address::class));

        $removedFields = [];
        $formMock = $this->createStub(FormInterface::class);
        $formMock->method('add')->willReturn($formMock);
        $formMock->method('remove')->willReturnCallback(
            function ($field) use (&$removedFields, $formMock) {
                $removedFields[] = $field;

                return $formMock;
            }
        );

        $addressMock = $this->createMock(\BinSoul\Common\I18n\MutableAddress::class);
        $addressMock->expects($this->once())->method('setAddressLine1')->with(null);
        $addressMock->expects($this->once())->method('setAddressLine2')->with(null);
        $addressMock->expects($this->once())->method('setAddressLine3')->with(null);
        $addressMock->expects($this->once())->method('setPostalCode')->with(null);
        $addressMock->expects($this->once())->method('setState')->with(null);
        $addressMock->expects($this->once())->method('setLocality')->with(null);
        $addressMock->expects($this->once())->method('setSubLocality')->with(null);
        $addressMock->expects($this->once())->method('setSortingCode')->with(null);

        $formMock->method('getData')->willReturn($addressMock);

        $listeners = [];
        $formBuilder = $this->setupFormBuilder($listeners);

        $this->builder->build($formBuilder);

        $event = new FormEvent($formMock, $data);
        $this->trigger($listeners, FormEvents::PRE_SUBMIT, $event);

        $expectedFields = [
            'addressLine1',
            'addressLine2',
            'addressLine3',
            'postalCode',
            'state',
            'locality',
            'subLocality',
            'sortingCode',
        ];

        foreach ($expectedFields as $fieldName) {
            $this->assertContains($fieldName, $removedFields, "Field {$fieldName} was not removed.");
        }

        $modifiedData = $event->getData();

        foreach ($expectedFields as $fieldName) {
            $this->assertArrayNotHasKey($fieldName, $modifiedData, "Field {$fieldName} should be removed from data.");
        }

        $this->assertEquals($countryCode, $modifiedData['countryCode']);
    }

    public function test_modify_form_applies_all_fields_visible(): void
    {
        $this->builder->makeAllFieldsVisible();

        $countryCode = 'DE';
        $data = ['countryCode' => $countryCode];

        $usageTemplate = $this->createStub(Address::class);
        $fields = [
            'getAddressLine1' => 'addressLine1',
            'getAddressLine2' => 'addressLine2',
            'getAddressLine3' => 'addressLine3',
            'getPostalCode' => 'postalCode',
            'getLocality' => 'locality',
            'getSubLocality' => 'subLocality',
            'getState' => 'state',
            'getSortingCode' => 'sortingCode',
        ];

        foreach (array_keys($fields) as $method) {
            $usageTemplate->method($method)->willReturn(null);
        }

        $this->addressFormatter->method('generateUsageTemplate')->willReturn($usageTemplate);
        $this->addressFormatter->method('generateRegexTemplate')->willReturn($this->createStub(Address::class));
        $this->addressFormatter->method('generateLabelTemplate')->willReturn($this->createStub(Address::class));

        $addedFields = [];
        $formMock = $this->createStub(FormInterface::class);
        $formMock->method('add')->willReturnCallback(
            function ($field, $type, $options) use (&$addedFields, $formMock) {
                $addedFields[$field] = $options;

                return $formMock;
            }
        );
        $formMock->method('remove')->willReturn($formMock);

        $listeners = [];
        $formBuilder = $this->setupFormBuilder($listeners);

        $this->builder->build($formBuilder);

        $this->trigger($listeners, FormEvents::PRE_SUBMIT, new FormEvent($formMock, $data));

        foreach ($fields as $fieldName) {
            $this->assertArrayHasKey($fieldName, $addedFields, "Field {$fieldName} was not added even though makeAllFieldsVisible() was called.");
        }
    }

    public function test_modify_form_adds_all_regex_constraints(): void
    {
        $countryCode = 'DE';
        $data = ['countryCode' => $countryCode];

        $usageTemplate = $this->createStub(Address::class);
        $fields = [
            'getAddressLine1', 'getAddressLine2', 'getAddressLine3',
            'getPostalCode', 'getLocality', 'getSubLocality',
            'getState', 'getSortingCode',
        ];

        foreach ($fields as $method) {
            $usageTemplate->method($method)->willReturn('optional');
        }

        $regexTemplate = $this->createStub(Address::class);

        foreach ($fields as $method) {
            $regexTemplate->method($method)->willReturn('pattern_' . $method);
        }

        $this->addressFormatter->method('generateUsageTemplate')->willReturn($usageTemplate);
        $this->addressFormatter->method('generateRegexTemplate')->willReturn($regexTemplate);
        $this->addressFormatter->method('generateLabelTemplate')->willReturn($this->createStub(Address::class));

        $addedFields = [];
        $formMock = $this->createStub(FormInterface::class);
        $formMock->method('add')->willReturnCallback(
            function ($field, $type, $options) use (&$addedFields, $formMock) {
                $addedFields[$field] = $options;

                return $formMock;
            }
        );
        $formMock->method('remove')->willReturn($formMock);

        $listeners = [];
        $formBuilder = $this->setupFormBuilder($listeners);

        $this->builder->build($formBuilder);

        $this->trigger($listeners, FormEvents::PRE_SUBMIT, new FormEvent($formMock, $data));

        $expectedFields = [
            'addressLine1' => '/pattern_getAddressLine1/',
            'addressLine2' => '/pattern_getAddressLine2/',
            'addressLine3' => '/pattern_getAddressLine3/',
            'postalCode' => '/pattern_getPostalCode/',
            'locality' => '/pattern_getLocality/',
            'subLocality' => '/pattern_getSubLocality/',
            'state' => '/pattern_getState/',
            'sortingCode' => '/pattern_getSortingCode/',
        ];

        foreach ($expectedFields as $field => $pattern) {
            $this->assertArrayHasKey($field, $addedFields, "Field {$field} was not added.");
            $found = false;

            foreach ($addedFields[$field]['constraints'] as $constraint) {
                if ($constraint instanceof Regex && $constraint->pattern === $pattern) {
                    $found = true;

                    break;
                }
            }
            $this->assertTrue($found, "Regex constraint with pattern {$pattern} not found for field {$field}.");
        }
    }

    public function test_modify_form_adds_not_blank_constraints_for_all_required_fields(): void
    {
        $countryCode = 'DE';
        $data = ['countryCode' => $countryCode];

        $usageTemplate = $this->createStub(Address::class);
        $fields = [
            'getAddressLine1' => 'addressLine1',
            'getAddressLine2' => 'addressLine2',
            'getAddressLine3' => 'addressLine3',
            'getPostalCode' => 'postalCode',
            'getLocality' => 'locality',
            'getSubLocality' => 'subLocality',
            'getState' => 'state',
            'getSortingCode' => 'sortingCode',
        ];

        foreach (array_keys($fields) as $method) {
            $usageTemplate->method($method)->willReturn('required');
        }

        $this->addressFormatter->method('generateUsageTemplate')->willReturn($usageTemplate);
        $this->addressFormatter->method('generateRegexTemplate')->willReturn($this->createStub(Address::class));
        $this->addressFormatter->method('generateLabelTemplate')->willReturn($this->createStub(Address::class));

        $addedFields = [];
        $formMock = $this->createStub(FormInterface::class);
        $formMock->method('add')->willReturnCallback(
            function ($field, $type, $options) use (&$addedFields, $formMock) {
                $addedFields[$field] = $options;

                return $formMock;
            }
        );
        $formMock->method('remove')->willReturn($formMock);

        $listeners = [];
        $formBuilder = $this->setupFormBuilder($listeners);

        $this->builder->build($formBuilder);

        $this->trigger($listeners, FormEvents::PRE_SUBMIT, new FormEvent($formMock, $data));

        foreach ($fields as $fieldName) {
            $this->assertArrayHasKey($fieldName, $addedFields, "Field {$fieldName} was not added.");
            $this->assertTrue($addedFields[$fieldName]['required'] ?? false, "Field {$fieldName} should be required.");
            $foundNotBlank = false;

            foreach ($addedFields[$fieldName]['constraints'] as $constraint) {
                if ($constraint instanceof NotBlank) {
                    $foundNotBlank = true;

                    break;
                }
            }
            $this->assertTrue($foundNotBlank, "NotBlank constraint not found for field {$fieldName}.");
        }
    }

    public function test_modify_form_disables_all_fields(): void
    {
        $this->builder->makeAllFieldsDisabled();

        $countryCode = 'DE';
        $data = ['countryCode' => $countryCode];

        $usageTemplate = $this->createStub(Address::class);
        $fields = [
            'addressLine1' => 'getAddressLine1',
            'addressLine2' => 'getAddressLine2',
            'addressLine3' => 'getAddressLine3',
            'postalCode' => 'getPostalCode',
            'locality' => 'getLocality',
            'subLocality' => 'getSubLocality',
            'state' => 'getState',
            'sortingCode' => 'getSortingCode',
        ];

        foreach ($fields as $method) {
            $usageTemplate->method($method)->willReturn('optional');
        }

        $this->addressFormatter->method('generateUsageTemplate')->willReturn($usageTemplate);
        $this->addressFormatter->method('generateRegexTemplate')->willReturn($this->createStub(Address::class));
        $this->addressFormatter->method('generateLabelTemplate')->willReturn($this->createStub(Address::class));

        $addedFields = [];
        $formMock = $this->createStub(FormInterface::class);
        $formMock->method('add')->willReturnCallback(
            function ($field, $type, $options) use (&$addedFields, $formMock) {
                $addedFields[$field] = $options;

                return $formMock;
            }
        );
        $formMock->method('remove')->willReturn($formMock);

        $listeners = [];
        $formBuilder = $this->setupFormBuilder($listeners);

        $this->builder->build($formBuilder);

        $this->trigger($listeners, FormEvents::PRE_SUBMIT, new FormEvent($formMock, $data));

        foreach (array_keys($fields) as $fieldName) {
            $this->assertArrayHasKey($fieldName, $addedFields, "Field {$fieldName} was not added.");
            $this->assertTrue($addedFields[$fieldName]['disabled'] ?? false, "Field {$fieldName} should be disabled.");
        }
    }

    public function test_modify_form_translates_labels_for_all_fields(): void
    {
        $this->builder->withLabelTranslator(
            function ($field, $label) {
                return 'Translated ' . $label;
            }
        );

        $countryCode = 'DE';
        $data = ['countryCode' => $countryCode];

        $usageTemplate = $this->createStub(Address::class);
        $labelTemplate = $this->createStub(Address::class);

        $fields = [
            'addressLine1' => 'getAddressLine1',
            'addressLine2' => 'getAddressLine2',
            'addressLine3' => 'getAddressLine3',
            'postalCode' => 'getPostalCode',
            'locality' => 'getLocality',
            'subLocality' => 'getSubLocality',
            'state' => 'getState',
            'sortingCode' => 'getSortingCode',
        ];

        foreach ($fields as $fieldName => $method) {
            $usageTemplate->method($method)->willReturn('optional');
            $labelTemplate->method($method)->willReturn('LBL_' . strtoupper($fieldName));
        }

        $this->addressFormatter->method('generateUsageTemplate')->willReturn($usageTemplate);
        $this->addressFormatter->method('generateRegexTemplate')->willReturn($this->createStub(Address::class));
        $this->addressFormatter->method('generateLabelTemplate')->willReturn($labelTemplate);

        $addedFields = [];
        $formMock = $this->createStub(FormInterface::class);
        $formMock->method('add')->willReturnCallback(
            function ($field, $type, $options) use (&$addedFields, $formMock) {
                $addedFields[$field] = $options;

                return $formMock;
            }
        );
        $formMock->method('remove')->willReturn($formMock);

        $listeners = [];
        $formBuilder = $this->setupFormBuilder($listeners);

        $this->builder->build($formBuilder);

        $this->trigger($listeners, FormEvents::PRE_SUBMIT, new FormEvent($formMock, $data));

        foreach ($fields as $fieldName => $method) {
            $this->assertArrayHasKey($fieldName, $addedFields, "Field {$fieldName} was not added.");
            $this->assertEquals('Translated LBL_' . strtoupper($fieldName), $addedFields[$fieldName]['label'], "Label for field {$fieldName} was not translated correctly.");
        }
    }

    public function test_modify_form_uses_country_code_from_object_if_missing_in_data(): void
    {
        $countryCode = 'FR';
        $data = []; // No countryCode here

        $object = $this->createStub(\BinSoul\Common\I18n\MutableAddress::class);
        $object->method('getCountryCode')->willReturn($countryCode);

        $formMock = $this->createStub(FormInterface::class);
        $formMock->method('getData')->willReturn($object);
        $formMock->method('add')->willReturn($formMock);
        $formMock->method('remove')->willReturn($formMock);

        $calledCountryCode = null;
        $this->addressFormatter->method('generateUsageTemplate')->willReturnCallback(
            function ($code) use (&$calledCountryCode) {
                $calledCountryCode = $code;

                return $this->createStub(Address::class);
            }
        );
        $this->addressFormatter->method('generateRegexTemplate')->willReturn($this->createStub(Address::class));
        $this->addressFormatter->method('generateLabelTemplate')->willReturn($this->createStub(Address::class));

        $listeners = [];
        $formBuilder = $this->setupFormBuilder($listeners);

        $this->builder->build($formBuilder);

        $this->trigger($listeners, FormEvents::PRE_SUBMIT, new FormEvent($formMock, $data));

        $this->assertEquals($countryCode, $calledCountryCode, 'AddressFormatter should be called with the country code from the address object.');
    }

    public function test_modify_form_uses_choice_type_with_codes_for_states_if_use_code_is_true(): void
    {
        $countryCode = 'CA'; // Canada uses codes
        $data = ['countryCode' => $countryCode];

        $usageTemplate = $this->createStub(Address::class);
        $usageTemplate->method('getState')->willReturn('optional');

        $this->addressFormatter->method('generateUsageTemplate')->willReturn($usageTemplate);
        $this->addressFormatter->method('generateRegexTemplate')->willReturn($this->createStub(Address::class));
        $this->addressFormatter->method('generateLabelTemplate')->willReturn($this->createStub(Address::class));

        $stateOptions = null;
        $stateType = null;
        $formMock = $this->createStub(FormInterface::class);
        $formMock->method('add')->willReturnCallback(
            function ($field, $type, $options) use (&$stateOptions, &$stateType, $formMock) {
                if ($field === 'state') {
                    $stateType = $type;
                    $stateOptions = $options;
                }

                return $formMock;
            }
        );
        $formMock->method('remove')->willReturn($formMock);

        $listeners = [];
        $formBuilder = $this->setupFormBuilder($listeners);

        $this->builder->build($formBuilder);

        $this->trigger($listeners, FormEvents::PRE_SUBMIT, new FormEvent($formMock, $data));

        $this->assertEquals(ChoiceType::class, $stateType);
        $this->assertArrayHasKey('choices', $stateOptions);
        // Alberta code is 'AB'
        $this->assertEquals('AB', $stateOptions['choices']['Alberta']);
    }

    public function test_modify_form_uses_configured_type_for_states_if_no_names_available(): void
    {
        $countryCode = 'CC'; // Cocos (Keeling) Islands has no state names in StateData
        $data = ['countryCode' => $countryCode];

        $usageTemplate = $this->createStub(Address::class);
        $usageTemplate->method('getState')->willReturn('optional');

        $this->addressFormatter->method('generateUsageTemplate')->willReturn($usageTemplate);
        $this->addressFormatter->method('generateRegexTemplate')->willReturn($this->createStub(Address::class));
        $this->addressFormatter->method('generateLabelTemplate')->willReturn($this->createStub(Address::class));

        $stateType = null;
        $formMock = $this->createStub(FormInterface::class);
        $formMock->method('add')->willReturnCallback(
            function ($field, $type, $options) use (&$stateType, $formMock) {
                if ($field === 'state') {
                    $stateType = $type;
                }

                return $formMock;
            }
        );
        $formMock->method('remove')->willReturn($formMock);

        $listeners = [];
        $formBuilder = $this->setupFormBuilder($listeners);

        $this->builder->build($formBuilder);

        $this->trigger($listeners, FormEvents::PRE_SUBMIT, new FormEvent($formMock, $data));

        // Default type is TextType
        $this->assertEquals(TextType::class, $stateType);
    }

    public function test_modify_form_uses_provided_object_from_data_provider(): void
    {
        $originalObject = $this->createStub(\BinSoul\Common\I18n\MutableAddress::class);
        $providedObject = $this->createStub(\BinSoul\Common\I18n\MutableAddress::class);
        $providedObject->method('getCountryCode')->willReturn('IT');

        $dataProviderCalled = 0;
        $this->builder->withDataProvider(
            function ($object) use (&$dataProviderCalled, $originalObject, $providedObject) {
                $dataProviderCalled++;
                $this->assertSame($originalObject, $object);

                return $providedObject;
            }
        );

        $usageTemplate = $this->createStub(Address::class);
        // We verify indirectly via method().willReturn(), but check the country code in a callback
        $this->addressFormatter->method('generateUsageTemplate')
            ->willReturnCallback(
                function ($countryCode) use ($usageTemplate) {
                    $this->assertEquals('IT', $countryCode, 'AddressFormatter should receive country code from provided object');

                    return $usageTemplate;
                }
            );

        $this->addressFormatter->method('generateRegexTemplate')->willReturn($this->createStub(Address::class));
        $this->addressFormatter->method('generateLabelTemplate')->willReturn($this->createStub(Address::class));

        $formMock = $this->createStub(FormInterface::class);
        $formMock->method('add')->willReturn($formMock);
        $formMock->method('remove')->willReturn($formMock);
        $formMock->method('getData')->willReturn($originalObject);

        $listeners = [];
        $formBuilder = $this->setupFormBuilder($listeners);
        $this->builder->build($formBuilder);

        // Test PRE_SET_DATA
        $this->trigger($listeners, FormEvents::PRE_SET_DATA, new FormEvent($formMock, $originalObject));
        $this->assertEquals(1, $dataProviderCalled, 'DataProvider should be called once during PRE_SET_DATA');

        // Test PRE_SUBMIT
        $this->trigger($listeners, FormEvents::PRE_SUBMIT, new FormEvent($formMock, ['countryCode' => '']));
        $this->assertEquals(2, $dataProviderCalled, 'DataProvider should be called again during PRE_SUBMIT');
    }

    public function test_modify_form_makes_all_fields_optional(): void
    {
        $this->builder->makeAllFieldsOptional();

        $countryCode = 'DE';
        $data = ['countryCode' => $countryCode];

        $usageTemplate = $this->createStub(Address::class);
        $fields = [
            'getAddressLine1' => 'addressLine1',
            'getAddressLine2' => 'addressLine2',
            'getAddressLine3' => 'addressLine3',
            'getPostalCode' => 'postalCode',
            'getLocality' => 'locality',
            'getSubLocality' => 'subLocality',
            'getState' => 'state',
            'getSortingCode' => 'sortingCode',
        ];

        foreach (array_keys($fields) as $method) {
            $usageTemplate->method($method)->willReturn('required');
        }

        $this->addressFormatter->method('generateUsageTemplate')->willReturn($usageTemplate);
        $this->addressFormatter->method('generateRegexTemplate')->willReturn($this->createStub(Address::class));
        $this->addressFormatter->method('generateLabelTemplate')->willReturn($this->createStub(Address::class));

        $addedFields = [];
        $formMock = $this->createStub(FormInterface::class);
        $formMock->method('add')->willReturnCallback(
            function ($field, $type, $options) use (&$addedFields, $formMock) {
                $addedFields[$field] = $options;

                return $formMock;
            }
        );
        $formMock->method('remove')->willReturn($formMock);

        $listeners = [];
        // Important: pass $addedFields by reference to setupFormBuilder to catch countryCode field added during build()
        $formBuilder = $this->setupFormBuilder($listeners, $addedFields);

        $this->builder->build($formBuilder);

        $this->trigger($listeners, FormEvents::PRE_SUBMIT, new FormEvent($formMock, $data));

        $this->assertArrayHasKey('countryCode', $addedFields, 'countryCode field was not added.');
        $this->assertFalse($addedFields['countryCode']['required'] ?? false, 'countryCode field should be optional.');

        foreach ($fields as $fieldName) {
            $this->assertArrayHasKey($fieldName, $addedFields, "Field {$fieldName} was not added.");
            $this->assertFalse($addedFields[$fieldName]['required'] ?? false, "Field {$fieldName} should be optional even if required in template.");

            $hasNotBlank = false;

            foreach ($addedFields[$fieldName]['constraints'] ?? [] as $constraint) {
                if ($constraint instanceof NotBlank) {
                    $hasNotBlank = true;

                    break;
                }
            }
            $this->assertFalse($hasNotBlank, "Field {$fieldName} should not have a NotBlank constraint.");
        }
    }

    public function test_with_and_without_methods_configure_fields_correctly(): void
    {
        $customType = ChoiceType::class;
        $customOptions = ['attr' => ['class' => 'custom-class']];

        $this->builder
            ->withCountry('custom_country', $customType, $customOptions)
            ->withAddressLine1('custom_line1', $customType, $customOptions)
            ->withAddressLine2('custom_line2', $customType, $customOptions)
            ->withAddressLine3('custom_line3', $customType, $customOptions)
            ->withPostalCode('custom_postal', $customType, $customOptions)
            ->withLocality('custom_locality', $customType, $customOptions)
            ->withSubLocality('custom_sublocality', $customType, $customOptions)
            ->withState('custom_state', $customType, $customOptions)
            ->withSortingCode('custom_sorting', $customType, $customOptions);

        $countryCode = 'DE';
        $data = ['custom_country' => $countryCode];

        $usageTemplate = $this->createStub(Address::class);
        $fields = [
            'getAddressLine1' => 'custom_line1',
            'getAddressLine2' => 'custom_line2',
            'getAddressLine3' => 'custom_line3',
            'getPostalCode' => 'custom_postal',
            'getLocality' => 'custom_locality',
            'getSubLocality' => 'custom_sublocality',
            'getState' => 'custom_state',
            'getSortingCode' => 'custom_sorting',
        ];

        foreach (array_keys($fields) as $method) {
            $usageTemplate->method($method)->willReturn('optional');
        }

        $this->addressFormatter->method('generateUsageTemplate')->willReturn($usageTemplate);
        $this->addressFormatter->method('generateRegexTemplate')->willReturn($this->createStub(Address::class));
        $this->addressFormatter->method('generateLabelTemplate')->willReturn($this->createStub(Address::class));

        $addedFields = [];
        $formMock = $this->createStub(FormInterface::class);
        $formMock->method('add')->willReturnCallback(
            function ($field, $type, $options) use (&$addedFields, $formMock) {
                $addedFields[$field] = ['type' => $type, 'options' => $options];

                return $formMock;
            }
        );
        $formMock->method('remove')->willReturn($formMock);

        $listeners = [];
        $formBuilder = $this->setupFormBuilder($listeners);

        // Manual capture for the initial 'add' call in build()
        $capturedDuringBuild = [];
        $formBuilder->method('add')->willReturnCallback(
            function ($field, $type, $options) use (&$capturedDuringBuild, $formBuilder) {
                $capturedDuringBuild[$field] = ['type' => $type, 'options' => $options];

                return $formBuilder;
            }
        );

        $this->builder->build($formBuilder);

        $this->trigger($listeners, FormEvents::PRE_SUBMIT, new FormEvent($formMock, $data));

        // Check Country field (added during build)
        $this->assertArrayHasKey('custom_country', $capturedDuringBuild);
        $this->assertEquals($customType, $capturedDuringBuild['custom_country']['type']);
        $this->assertEquals('custom-class', $capturedDuringBuild['custom_country']['options']['attr']['class']);

        // Check other fields (added during modifyForm)
        foreach ($fields as $fieldName) {
            $this->assertArrayHasKey($fieldName, $addedFields);
            $this->assertEquals($customType, $addedFields[$fieldName]['type']);
            $this->assertEquals('custom-class', $addedFields[$fieldName]['options']['attr']['class']);
        }
    }

    public function test_without_methods_disable_fields(): void
    {
        // Disable all fields including countryCode.
        $this->builder
            ->withoutCountry()
            ->withoutAddressLine1()
            ->withoutAddressLine2()
            ->withoutAddressLine3()
            ->withoutPostalCode()
            ->withoutLocality()
            ->withoutSubLocality()
            ->withoutState()
            ->withoutSortingCode();

        $countryCode = 'DE';
        $data = ['countryCode' => $countryCode];

        $usageTemplate = $this->createStub(Address::class);
        $fields = [
            'getAddressLine1', 'getAddressLine2', 'getAddressLine3',
            'getPostalCode', 'getLocality', 'getSubLocality',
            'getState', 'getSortingCode',
        ];

        foreach ($fields as $method) {
            $usageTemplate->method($method)->willReturn('optional');
        }

        $this->addressFormatter->method('generateUsageTemplate')->willReturn($usageTemplate);
        $this->addressFormatter->method('generateRegexTemplate')->willReturn($this->createStub(Address::class));
        $this->addressFormatter->method('generateLabelTemplate')->willReturn($this->createStub(Address::class));

        $addedFields = [];
        $formMock = $this->createStub(FormInterface::class);
        $formMock->method('add')->willReturnCallback(
            function ($field) use (&$addedFields, $formMock) {
                $addedFields[] = $field;

                return $formMock;
            }
        );

        $listeners = [];
        $formBuilder = $this->setupFormBuilder($listeners, $addedFields);
        $this->builder->build($formBuilder);

        $this->trigger($listeners, FormEvents::PRE_SUBMIT, new FormEvent($formMock, $data));

        $allFields = [
            'countryCode', 'addressLine1', 'addressLine2', 'addressLine3',
            'postalCode', 'locality', 'subLocality',
            'state', 'sortingCode',
        ];

        foreach ($allFields as $fieldName) {
            $this->assertNotContains($fieldName, $addedFields, "Field {$fieldName} should have been disabled.");
        }
    }

    public function test_modify_form_uses_default_country_if_no_country_code_provided(): void
    {
        $this->builder->withDefaultCountry('US');

        $data = []; // No countryCode
        $object = $this->createStub(\BinSoul\Common\I18n\MutableAddress::class);
        $object->method('getCountryCode')->willReturn(''); // No countryCode in object either

        $formMock = $this->createStub(FormInterface::class);
        $formMock->method('getData')->willReturn($object);
        $formMock->method('add')->willReturn($formMock);
        $formMock->method('remove')->willReturn($formMock);

        $calledCountryCode = null;
        $this->addressFormatter->method('generateUsageTemplate')->willReturnCallback(
            function ($code) use (&$calledCountryCode) {
                $calledCountryCode = $code;

                return $this->createStub(Address::class);
            }
        );
        $this->addressFormatter->method('generateRegexTemplate')->willReturn($this->createStub(Address::class));
        $this->addressFormatter->method('generateLabelTemplate')->willReturn($this->createStub(Address::class));

        $listeners = [];
        $formBuilder = $this->setupFormBuilder($listeners);

        $this->builder->build($formBuilder);

        $this->trigger($listeners, FormEvents::PRE_SUBMIT, new FormEvent($formMock, $data));

        $this->assertEquals('US', $calledCountryCode, 'AddressFormatter should be called with the default country code.');
    }

    public function test_modify_form_respects_without_state_choice(): void
    {
        $this->builder->withoutStateChoice();

        $countryCode = 'CA'; // Canada normally uses ChoiceType
        $data = ['countryCode' => $countryCode];

        $usageTemplate = $this->createStub(Address::class);
        $usageTemplate->method('getState')->willReturn('optional');

        $this->addressFormatter->method('generateUsageTemplate')->willReturn($usageTemplate);
        $this->addressFormatter->method('generateRegexTemplate')->willReturn($this->createStub(Address::class));
        $this->addressFormatter->method('generateLabelTemplate')->willReturn($this->createStub(Address::class));

        $stateType = null;
        $formMock = $this->createStub(FormInterface::class);
        $formMock->method('add')->willReturnCallback(
            function ($field, $type, $options) use (&$stateType, $formMock) {
                if ($field === 'state') {
                    $stateType = $type;
                }

                return $formMock;
            }
        );
        $formMock->method('remove')->willReturn($formMock);

        $listeners = [];
        $formBuilder = $this->setupFormBuilder($listeners);
        $this->builder->build($formBuilder);

        $this->trigger($listeners, FormEvents::PRE_SUBMIT, new FormEvent($formMock, $data));

        // Should use TextType (default) instead of ChoiceType
        $this->assertEquals(TextType::class, $stateType);
    }

    public function test_modify_form_respects_force_state_display(): void
    {
        $this->builder->forceStateDisplay();

        $countryCode = 'DE';
        $data = ['countryCode' => $countryCode];

        $usageTemplate = $this->createStub(Address::class);
        $usageTemplate->method('getState')->willReturn(null); // Not visible in template

        $this->addressFormatter->method('generateUsageTemplate')->willReturn($usageTemplate);
        $this->addressFormatter->method('generateRegexTemplate')->willReturn($this->createStub(Address::class));
        $this->addressFormatter->method('generateLabelTemplate')->willReturn($this->createStub(Address::class));

        $stateAdded = false;
        $formMock = $this->createStub(FormInterface::class);
        $formMock->method('add')->willReturnCallback(
            function ($field) use (&$stateAdded, $formMock) {
                if ($field === 'state') {
                    $stateAdded = true;
                }

                return $formMock;
            }
        );
        $formMock->method('remove')->willReturn($formMock);

        $listeners = [];
        $formBuilder = $this->setupFormBuilder($listeners);
        $this->builder->build($formBuilder);

        $this->trigger($listeners, FormEvents::PRE_SUBMIT, new FormEvent($formMock, $data));

        $this->assertTrue($stateAdded, 'State field should be added when forceStateDisplay() is called.');
    }

    public function test_modify_form_applies_custom_constraint_options(): void
    {
        $customOptions = [
            'message' => 'Custom Message',
            'groups' => ['custom_group'],
            'payload' => 'custom_payload',
            'htmlPattern' => '.*',
            'match' => false,
            'normalizer' => 'trim',
        ];

        $this->builder->withConstraintOptions($customOptions);

        $countryCode = 'DE';
        $data = ['countryCode' => $countryCode];

        $usageTemplate = $this->createStub(Address::class);
        $usageTemplate->method('getAddressLine1')->willReturn('required');

        $regexTemplate = $this->createStub(Address::class);
        $regexTemplate->method('getAddressLine1')->willReturn('pattern');

        $this->addressFormatter->method('generateUsageTemplate')->willReturn($usageTemplate);
        $this->addressFormatter->method('generateRegexTemplate')->willReturn($regexTemplate);
        $this->addressFormatter->method('generateLabelTemplate')->willReturn($this->createStub(Address::class));

        $addedOptions = [];
        $formMock = $this->createStub(FormInterface::class);
        $formMock->method('add')->willReturnCallback(
            function ($field, $type, $options) use (&$addedOptions, $formMock) {
                if ($field === 'addressLine1') {
                    $addedOptions = $options;
                }

                return $formMock;
            }
        );
        $formMock->method('remove')->willReturn($formMock);

        $listeners = [];
        $formBuilder = $this->setupFormBuilder($listeners);
        $this->builder->build($formBuilder);

        $this->trigger($listeners, FormEvents::PRE_SUBMIT, new FormEvent($formMock, $data));

        $notBlankFound = false;
        $regexFound = false;

        foreach ($addedOptions['constraints'] as $constraint) {
            if ($constraint instanceof NotBlank) {
                $notBlankFound = true;
                $this->assertEquals('Custom Message', $constraint->message);
                $this->assertEquals(['custom_group'], $constraint->groups);
                $this->assertEquals('custom_payload', $constraint->payload);
            } elseif ($constraint instanceof Regex) {
                $regexFound = true;
                $this->assertEquals('Custom Message', $constraint->message);
                $this->assertEquals(['custom_group'], $constraint->groups);
                $this->assertEquals('custom_payload', $constraint->payload);
                $this->assertEquals('.*', $constraint->htmlPattern);
                $this->assertFalse($constraint->match);
            }
        }

        $this->assertTrue($notBlankFound, 'NotBlank constraint not found.');
        $this->assertTrue($regexFound, 'Regex constraint not found.');
    }

    private function setupFormBuilder(&$listeners, &$addedFields = []): FormBuilderInterface&Stub
    {
        $formBuilder = $this->createStub(FormBuilderInterface::class);

        $formBuilder->method('add')->willReturnCallback(
            function ($field, $type, $options) use (&$addedFields, $formBuilder) {
                $addedFields[$field] = $options;

                return $formBuilder;
            }
        );

        $formBuilder->method('addEventListener')->willReturnCallback(
            function ($eventName, $listener) use (&$listeners, $formBuilder) {
                $listeners[$eventName][] = $listener;

                return $formBuilder;
            }
        );

        return $formBuilder;
    }

    private function trigger(array $listeners, string $eventName, FormEvent $event): void
    {
        foreach ($listeners[$eventName] ?? [] as $listener) {
            $listener($event);
        }
    }
}
