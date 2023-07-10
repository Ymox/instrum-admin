<?php
namespace App\Twig;

use Twig\Extension\AbstractExtension;

class TwigEnum extends AbstractExtension
{
    public function getFunctions(): array
    {
        return [
            new \Twig\TwigFunction('enum_cases', [$this, 'getEnumCases']),
            new \Twig\TwigFunction('enum_value', [$this, 'getEnumValue']),
            new \Twig\TwigFunction('enum_values', [$this, 'getEnumValues']),
            new \Twig\TwigFunction('enum_case_from', [$this, 'getEnumCaseFrom']),
            new \Twig\TwigFunction('enum_case_try_from', [$this, 'getEnumCaseTryFrom']),
        ];
    }

    public function getEnumCases(string|\BackedEnum $enum): mixed
    {
        return $enum::cases();
    }

    public function getEnumValues(string|\BackedEnum $enum): mixed
    {
        return array_column($enum::cases(), 'value');
    }

    public function getEnumValue(string|\BackedEnum $enum, string $case): mixed
    {
        return constant($enum . '::' . $case)->value;
    }

    public function getEnumCaseFrom(string|\BackedEnum $enum, int|string $value): mixed
    {
        if (is_string($enum)) {
            $enum = $$enum;
        }

        return $enum::from($value);
    }

    public function getEnumCaseTryFrom(string|\BackedEnum $enum, int|string $value): mixed
    {
        if (is_string($enum)) {
            $enum = $$enum;
        }

        return $enum::tryFrom($value);
    }
}