<?php

namespace Strukt\Contract;

/**
 * Describes a form definition, its submitted values, and its validation state.
 */
interface FormInterface
{
    /**
     * Lists the field names defined by the form.
     *
     * @return list<string> Field names.
     */
    public function fields(): array;

    /**
     * Reads the validation rules keyed by field name.
     *
     * @return array<string, list<string>|string> Rules keyed by field name.
     */
    public function rules(): array;

    /**
     * Reads one submitted value.
     *
     * @param string $field Field name.
     * @param mixed $default Value returned when the field is undefined.
     * @return mixed Submitted value, or the default when undefined.
     */
    public function value(string $field, mixed $default = null): mixed;

    /**
     * Assigns one submitted value.
     *
     * @param string $field Field name.
     * @param mixed $value Value to assign.
     * @return static This form.
     */
    public function setValue(string $field, mixed $value): static;

    /**
     * Assigns every submitted value at once.
     *
     * @param array<string, mixed> $values Field values keyed by field name.
     * @return static This form.
     */
    public function setValues(array $values): static;

    /**
     * Reads every submitted value.
     *
     * @return array<string, mixed> Field values keyed by field name.
     */
    public function values(): array;

    /**
     * Checks whether the form carries a submitted value.
     *
     * @return bool True once values have been supplied.
     */
    public function isSubmitted(): bool;

    /**
     * Marks the form as submitted with the given values.
     *
     * @param array<string, mixed> $values Field values keyed by field name.
     * @return static This form.
     */
    public function submit(array $values = []): static;

    /**
     * Runs validation and records any failures.
     *
     * @return bool True when every rule passes.
     */
    public function validate(): bool;

    /**
     * Checks whether the last validation run recorded failures.
     *
     * @return bool True when failures are recorded.
     */
    public function hasErrors(): bool;

    /**
     * Reads the recorded validation failures.
     *
     * @return array<string, list<string>> Messages keyed by field name.
     */
    public function errors(): array;

    /**
     * Reads the recorded failures for one field.
     *
     * @param string $field Field name.
     * @return list<string> Messages for the field, or an empty list.
     */
    public function errorsFor(string $field): array;

    /**
     * Discards submitted values, recorded failures, and the submitted flag.
     *
     * @return static This form.
     */
    public function reset(): static;
}
