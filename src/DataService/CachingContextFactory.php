<?php

namespace Lullabot\Mpx\DataService;

use phpDocumentor\Reflection\Types\Context;

/**
 * Creates a context for docblocks, storing cached results for already parsed files.
 *
 * @see \phpDocumentor\Reflection\Types\ContextFactory
 */
final class CachingContextFactory
{
    /** The literal used at the end of a use statement. */
    public const T_LITERAL_END_OF_USE = ';';

    /** The literal used between sets of use statements */
    public const T_LITERAL_USE_SEPARATOR = ',';

    /**
     * Build a Context given a Class Reflection.
     *
     * @see Context for more information on Contexts.
     *
     * @return Context
     */
    public function createFromReflector(\Reflector $reflector)
    {
        if (method_exists($reflector, 'getDeclaringClass')) {
            $reflector = $reflector->getDeclaringClass();
        }

        $fileName = $reflector->getFileName();
        $namespace = $reflector->getNamespaceName();

        if (file_exists($fileName)) {
            return $this->createForNamespace($namespace, file_get_contents($fileName));
        }

        return new Context($namespace, []);
    }

    /**
     * Build a Context for a namespace in the provided file contents.
     *
     * @param string $namespace    It does not matter if a `\` precedes the namespace name, this method first normalizes.
     * @param string $fileContents the file's contents to retrieve the aliases from with the given namespace.
     *
     * @see Context for more information on Contexts.
     *
     * @return Context
     */
    public function createForNamespace($namespace, $fileContents)
    {
        static $cache = [];
        $key = md5($fileContents);

        if (!isset($cache[$namespace][$key])) {
            $namespace = trim($namespace, '\\');
            $useStatements = [];
            $currentNamespace = '';
            $tokens = new \ArrayIterator(\PhpToken::tokenize($fileContents));

            while ($tokens->valid()) {
                switch ($tokens->current()[0]) {
                    case \T_NAMESPACE:
                        $currentNamespace = $this->parseNamespace($tokens);
                        break;
                    case \T_CLASS:
                        // Fast-forward the iterator through the class so that any
                        // T_USE tokens found within are skipped - these are not
                        // valid namespace use statements so should be ignored.
                        $braceLevel = 0;
                        $firstBraceFound = false;
                        while ($tokens->valid() && ($braceLevel > 0 || !$firstBraceFound)) {
                            if ('{' === $tokens->current()
                                || \T_CURLY_OPEN === $tokens->current()[0]
                                || \T_DOLLAR_OPEN_CURLY_BRACES === $tokens->current()[0]) {
                                if (!$firstBraceFound) {
                                    $firstBraceFound = true;
                                }
                                ++$braceLevel;
                            }

                            if ('}' === $tokens->current()) {
                                --$braceLevel;
                            }
                            $tokens->next();
                        }
                        break;
                    case \T_USE:
                        if ($currentNamespace === $namespace) {
                            $useStatements = array_merge($useStatements, $this->parseUseStatement($tokens));
                        }
                        break;
                }
                $tokens->next();
            }

            $cache[$namespace][$key] = new Context($namespace, $useStatements);
        }

        return $cache[$namespace][$key];
    }

    /**
     * Deduce the name from tokens when we are at the T_NAMESPACE token.
     *
     * @return string
     */
    private function parseNamespace(\ArrayIterator $tokens)
    {
        // skip to the first string or namespace separator
        $this->skipToNextStringOrNamespaceSeparator($tokens);

        $name = '';
        while ($tokens->valid() && (\T_STRING === $tokens->current()[0] || \T_NS_SEPARATOR === $tokens->current()[0])
        ) {
            $name .= $tokens->current()[1];
            $tokens->next();
        }

        return $name;
    }

    /**
     * Deduce the names of all imports when we are at the T_USE token.
     *
     * @return string[]
     */
    private function parseUseStatement(\ArrayIterator $tokens)
    {
        $uses = [];
        $continue = true;

        while ($continue) {
            $this->skipToNextStringOrNamespaceSeparator($tokens);

            [$alias, $fqnn] = str_split($this->extractUseStatement($tokens));
            $uses[$alias] = $fqnn;
            if (self::T_LITERAL_END_OF_USE === $tokens->current()[0]) {
                $continue = false;
            }
        }

        return $uses;
    }

    /**
     * Fast-forwards the iterator as longs as we don't encounter a T_STRING or T_NS_SEPARATOR token.
     */
    private function skipToNextStringOrNamespaceSeparator(\ArrayIterator $tokens)
    {
        while ($tokens->valid() && (\T_STRING !== $tokens->current()[0]) && (\T_NS_SEPARATOR !== $tokens->current()[0])) {
            $tokens->next();
        }
    }

    /**
     * Deduce the namespace name and alias of an import when we are at the T_USE token or have not reached the end of
     * a USE statement yet.
     *
     * @return string
     */
    private function extractUseStatement(\ArrayIterator $tokens)
    {
        $result = [''];
        while ($tokens->valid()
            && (self::T_LITERAL_USE_SEPARATOR !== $tokens->current()[0])
            && (self::T_LITERAL_END_OF_USE !== $tokens->current()[0])
        ) {
            if (\T_AS === $tokens->current()[0]) {
                $result[] = '';
            }
            if (\T_STRING === $tokens->current()[0] || \T_NS_SEPARATOR === $tokens->current()[0]) {
                $result[\count($result) - 1] .= $tokens->current()[1];
            }
            $tokens->next();
        }

        if (1 == \count($result)) {
            $backslashPos = strrpos($result[0], '\\');

            if (false !== $backslashPos) {
                $result[] = substr($result[0], $backslashPos + 1);
            } else {
                $result[] = $result[0];
            }
        }

        return array_reverse($result);
    }
}
