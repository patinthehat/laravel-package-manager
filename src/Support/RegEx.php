<?php

namespace LaravelPackageManager\Support;

/**
 * Handles basic regular expression manipulations.
 */

class RegEx
{

    /**
     * Checks to see if filter is a basic '(keyword)' regular expression.
     * @param string $filter
     * @return boolean
     */
    protected function isBasicCapture($filter)
    {
        return (starts_with($filter, '(') && ends_with($filter, ')'));
    }

    /**
     * Check to see if $filter is a simple match like '(one|two|three)'.
     * @param string $filter
     * @return boolean
     */
    protected function isBasicCaptureWithPipes($filter)
    {
        return ($this->isBasicCapture($filter) && strpos($filter, '|') !== false);
    }

    /**
     * Strip off the leading and trailing characters from $filter.
     * TODO this is too similar to getFilterExpressionOnly, consider merging into one method
     * @param string $filter
     * @return string
     */
    protected function removeBasicCaptureChars($filter)
    {
        return substr($filter, 1, strlen($filter) - 2);
    }

    /**
     * Compiles an array of regular expressions into one expression,
     * to avoid multiple calls trying to match each individual
     * expression.
     *
     * @param array $filters
     * @return string
     */
    public function compileFiltersToRegEx(array $filters)
    {
        $parts = [];

        foreach ($filters as $filter) {
            $expression = $this->getFilterExpressionOnly($filter);

            if ($this->isBasicCaptureWithPipes($expression))
                $expression = $this->removeBasicCaptureChars($expression);
            $parts[] = $expression;
        }
        $result = '/(' . implode('|', $parts) . ')/';

        return $result;
    }

    /**
     * Extract the regular expression, removing the leading and trailing '/' chars.
     * If $filter isn't a regex, just return the value of $filter unmodified.
     * @param string $filter
     * @return string
     */
    protected function getFilterExpressionOnly($filter)
    {
        if (!$this->isRegularExpression($filter))
            return $filter;

        return substr($filter, 1, strlen($filter) - 2);
    }

    /**
     * Checks to see if $filter is a regular expression filter,
     * or just a simple text match filter.
     * @param string $filter
     * @return boolean
     */
    public function isRegularExpression($filter)
    {
        if (strlen($filter) <= 2)
            return false;

        $firstchar = $filter[0];
        $lastchar = $filter[strlen($filter) - 1];

        return (($firstchar == '/') && ($firstchar == $lastchar));
    }

}
