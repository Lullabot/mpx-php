<?php

namespace Lullabot\Mpx\DataService\QQuery;

use Lullabot\Mpx\DataService\QueryPartsInterface;

/**
 * A Term used within a Q Query.
 *
 * @see TermGroup
 * @see https://docs.theplatform.com/help/wsf-selecting-objects-by-using-the-q-query-parameter
 */
class Term implements QueryPartsInterface, TermInterface, \Stringable
{
    /**
     * Character sequences that must be escaped from term values.
     *
     * Note that these are not individual characters, but sometimes strings like
     * '&&'.
     *
     * This list matches the upstream documentation, except moving backslash to
     * the beginning of the list.
     *
     * @see https://docs.theplatform.com/help/wsf-selecting-objects-by-using-the-q-query-parameter#tp-toc31
     */
    public const ESCAPE_CHARACTERS = [
        '\\' => '\\\\',
        '+' => '\+',
        '-' => '\-',
        '&&' => '\&&',
        '||' => '\||',
        '!' => '\!',
        '(' => '\(',
        ')' => '\)',
        '{' => '\{',
        '}' => '\{',
        '[' => '\[',
        ']' => '\]',
        '^' => '\^',
        '"' => '\"',
        '~' => '\~',
        '*' => '\*',
        '?' => '\?',
        ':' => '\:',
        ';' => '\;',
    ];

    private ?string $matchType = null;

    private ?bool $wrap = null;

    public function getValue(): string
    {
        return $this->value;
    }

    public function setValue(string $value): self
    {
        $this->value = $value;

        return $this;
    }

    public function getField(): string
    {
        return $this->field;
    }

    public function setField(string $field): self
    {
        $this->field = $field;

        return $this;
    }

    public function getMatchType(): string
    {
        return $this->matchType;
    }

    public function setMatchType(string $matchType): self
    {
        if (!isset($this->field)) {
            throw new \LogicException();
        }

        $this->matchType = $matchType;

        return $this;
    }

    public function getBoost(): int
    {
        return $this->boost;
    }

    public function setBoost(int $boost): self
    {
        $this->boost = $boost;

        return $this;
    }

    public function getNamespace(): string
    {
        return $this->namespace;
    }

    public function setNamespace(string $namespace): self
    {
        $this->namespace = $namespace;

        return $this;
    }

    public function isRequired(): bool
    {
        return '+' == $this->plusMinus;
    }

    public function require(): self
    {
        $this->plusMinus = '+';

        return $this;
    }

    public function optional(): self
    {
        $this->plusMinus = null;

        return $this;
    }

    public function isExclude(): bool
    {
        return '-' == $this->plusMinus;
    }

    public function exclude(): self
    {
        $this->plusMinus = '-';

        return $this;
    }

    private ?int $boost = null;

    private ?string $plusMinus = null;

    public function __construct(private string $value, private ?string $field = null, private ?string $namespace = null)
    {
    }

    public function __toString(): string
    {
        $value = '';
        if ($this->plusMinus) {
            $value .= $this->plusMinus;
        }

        $value = $this->renderField($value);

        $value .= '"'.str_replace(array_keys(self::ESCAPE_CHARACTERS), self::ESCAPE_CHARACTERS, $this->value).'"';

        if (isset($this->boost)) {
            $value .= '^'.$this->boost;
        }

        if ($this->wrap) {
            $value = '('.$value.')';
        }

        return $value;
    }

    public function wrapParenthesis($wrap = true): self
    {
        $this->wrap = $wrap;

        return $this;
    }

    public function toQueryParts(): array
    {
        return [
            'q' => (string) $this,
        ];
    }

    /**
     * Add the field specification to the term string.
     *
     * @param string $value The current term value.
     *
     * @return string The value with the attached field, if set.
     */
    private function renderField(string $value): string
    {
        if (isset($this->field)) {
            $field = '';
            if (isset($this->namespace)) {
                $field = $this->namespace.'$';
            }
            $field .= $this->field;

            if (isset($this->matchType)) {
                $field .= '.'.$this->matchType;
            }
            $value .= $field.':';
        }

        return $value;
    }
}
