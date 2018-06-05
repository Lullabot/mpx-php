<?php

namespace Lullabot\Mpx\DataService;

/**
 * Class Term
 * @package Lullabot\Mpx\DataService
 * @see https://docs.theplatform.com/help/wsf-selecting-objects-by-using-the-q-query-parameter
 */
class Term
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
    const ESCAPE_CHARACTERS = [
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

    /**
     * @var string
     */
    private $value;
    /**
     * @var string
     */
    private $field;

    /**
     * @var string
     */
    private $matchType;

    /**
     * @return string
     */
    public function getValue(): string
    {
        return $this->value;
    }

    /**
     * @param string $value
     *
     * @return Term
     */
    public function setValue(string $value): Term
    {
        $this->value = $value;

        return $this;
    }

    /**
     * @return string
     */
    public function getField(): string
    {
        return $this->field;
    }

    /**
     * @param string $field
     *
     * @return Term
     */
    public function setField(string $field): Term
    {
        $this->field = $field;

        return $this;
    }

    /**
     * @return string
     */
    public function getMatchType(): string
    {
        return $this->matchType;
    }

    /**
     * @param string $matchType
     *
     * @return Term
     */
    public function setMatchType(string $matchType): Term
    {
        if (!isset($this->field)) {
            throw new \LogicException();
        }

        $this->matchType = $matchType;

        return $this;
    }

    /**
     * @return int
     */
    public function getBoost(): int
    {
        return $this->boost;
    }

    /**
     * @param int $boost
     *
     * @return Term
     */
    public function setBoost(int $boost): Term
    {
        $this->boost = $boost;

        return $this;
    }

    /**
     * @return string
     */
    public function getNamespace(): string
    {
        return $this->namespace;
    }

    /**
     * @param string $namespace
     *
     * @return Term
     */
    public function setNamespace(string $namespace): Term
    {
        $this->namespace = $namespace;

        return $this;
    }

    public function isRequired(): bool
    {
        return $this->plusMinus == '+';
    }

    public function require(): Term
    {
        $this->plusMinus = '+';
        return $this;
    }

    public function optional(): Term
    {
        $this->plusMinus = null;
        return $this;
    }

    /**
     * @return bool
     */
    public function isExclude(): bool
    {
        return $this->plusMinus == '-';
    }

    /**
     * @param string $exclude
     *
     * @return Term
     */
    public function exclude(): Term
    {
        $this->plusMinus = '-';

        return $this;
    }

    /**
     * @var int
     */
    private $boost;
    /**
     * @var string
     */
    private $namespace;

    /**
     * @var string
     */
    private $plusMinus;

    public function __construct(string $value, string $field = null, string $namespace = null)
    {
        $this->value = $value;
        $this->field = $field;
        $this->namespace = $namespace;
    }

    public function toQueryParts(): array
    {
        return [
            'q' => [
                (string) $this,
            ],
        ];
    }

    /**
     * @return string
     */
    public function __toString(): string
    {
        $value = "";
        if (isset($this->field)) {
            $field = "";
            if (isset($this->namespace)) {
                $field = $this->namespace . '$';
            }
            $field .= $this->field;

            if (isset($this->matchType)) {
                $field .= '.' . $this->matchType;
            }
            $value = $field . ':';
        }

        if ($this->plusMinus) {
            $value .= $this->plusMinus;
        }

        $value .= '"' . str_replace(array_keys(self::ESCAPE_CHARACTERS), self::ESCAPE_CHARACTERS, $this->value).'"';

        if (isset($this->boost)) {
            $value .= '^' . $this->boost;
        }

        return $value;
    }
}
