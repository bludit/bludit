<?php

/**
 * This script was modified to fit in Bludit
*/

/**
 * Fuzz Class
 * kevinfiol\fuzzget
 *
 * @category Class
 * @package  None
 * @author   Kevin Fiol <fiolkevin@gmail.com>
 * @license  https://opensource.org/licenses/MIT  MIT License
 * @link     http://github.com/kevinfiol
 */

class Fuzz
{
    private $_source;
    private $_sourceLen;
    private $_maxResults;
    private $_searchMode;
    private $_useLCS;

    /**
     * Fuzz Object Constructor
     * Initialize private variables
     *
     * @param array   $source     An array of associative arrays
     * @param int     $maxResults The maximum number of results to retrieve upon a search
     * @param int     $searchMode 0 = Levenshtein, 1 = Jaro-Winkler
     * @param boolean $useLCS     Factor in Longest Common Substring in search results
     */
    public function __construct($source, $maxResults, $searchMode, $useLCS)
    {
        $this->_source = $source;
        $this->_sourceLen = count($source);
        $this->_maxResults = max($maxResults, 1);
        $this->_useLCS = $useLCS;

        if ($searchMode < 0 || $searchMode > 1) {
            throw new \Exception('Invalid search mode');
        } else {
            $this->_searchMode = $searchMode;
        }
    }

    /**
     * Search Method
     * Initiate Search
     *
     * @param string $search      Term to search for
     * @param int    $minLCS      (if using LCS) Specify the minimum longest common substring
     * @param int    $maxDistance (if using Levenshtein) Specify the maximum distance allowed
     *
     * @return array $results     Array of associative arrays containing search matches
     */
    public function search($search, $minLCS = null, $maxDistance = null)
    {
        $results = [];
        $scores = [];

        // Nullify these parameters if they are irrelevant to searchMode
        if (!$this->_useLCS) $minLCS = null;
        if ($this->_searchMode != 0) $maxDistance = null;

        // Cycle through result pool
        //for ($i = 0; $i < $this->_sourceLen; $i++) {
	foreach ($this->_source as $pageKey => $data) {
            $allLev = [];
            $allJaros = [];
            $allLCSs = [];

            // Cycle through each object's properties
            foreach ($data as $key => $val) {
                if ($this->_searchMode == 0) {
                    $allLev[] = $this->getLevenshtein(strval($val), $search);
                } elseif ($this->_searchMode == 1) {
                    $allJaros[] = $this->getJaroWinkler(strval($val), $search);
                }

                if ($this->_useLCS) {
                    $allLCSs[] = $this->getLCS(strval($val), $search);
                }
            }

            $lowestLev = $allLev ? min($allLev) : null;
            $highestJaro = $allJaros ? max($allJaros) : null;
            $highestLCS = $allLCSs ? max($allLCSs) : null;

            // Get Score
            if ($this->_searchMode == 0) {
                $score = $lowestLev;
            } else {
                $score = -1 * abs($highestJaro);
            }

            if ($this->_useLCS) {
                $score -= $highestLCS;
            }

            // Append Index of object + Best Score
            if (($maxDistance == null || $lowestLev <= $maxDistance)
                && ($minLCS == null || $highestLCS >= $minLCS)
            ) {
                $scores[$pageKey] = $score;
            }
        }

	// Sort by score
	asort($scores);
        return $scores;
    }

    /**
     * Get Longest Common Substring
     *
     * @param string $source Term to search for
     * @param string $target Target term to search against
     *
     * @return int   $result LCS Score
     */
    public function getLCS($source, $target)
    {
        $suffix = [];
        $result = 0;
        $n = mb_strlen($source, CHARSET);
        $m = mb_strlen($target, CHARSET);

        for ($i = 0; $i <= $n; $i++) {
            for ($j = 0; $j <= $m; $j++) {
                if ($i === 0 || $j === 0) {
                    $suffix[$i][$j] = 0;
                } elseif ($source[$i - 1] == $target[$j - 1]) {
                    $suffix[$i][$j] = $suffix[$i - 1][$j - 1] + 1;
                    $result = max($result, $suffix[$i][$j]);
                } else {
                    $suffix[$i][$j] = 0;
                }
            }
        }

        return $result;
    }

    /**
     * Get Levenshtein Distance
     *
     * @param string $source Term to search for
     * @param string $target Target term to search against
     *
     * @return int   Levenshtein Distance
     */
    public function getLevenshtein($source, $target)
    {
        $matrix = [];
        $n = mb_strlen($source, CHARSET);
        $m = mb_strlen($target, CHARSET);

        if ($n === 0) {
            return $m;
        } elseif ($m === 0) {
            return $n;
        }

        // Initialize First Row
        for ($i = 0; $i <= $n; $i++) {
            $matrix[0][$i] = $i;
        }
        // Initialize First Column
        for ($i = 0; $i <= $m; $i++) {
            $matrix[$i][0] = $i;
        }

        for ($i = 1; $i <= $n; $i++) {
            for ($j = 1; $j <= $m; $j++) {
                if ($source[$i - 1] === $target[$j - 1]) {
                    $cost = 0;
                } else {
                    $cost = 1;
                }

                // Cell immediately above + 1
                $up = $matrix[$j - 1][$i] + 1;
                // Cell immediately to the left + 1
                $left = $matrix[$j][$i - 1] + 1;
                // Cell diagnolly above and to the left + cost
                $upleft = $matrix[$j - 1][$i - 1] + $cost;

                $matrix[$j][$i] = min($up, $left, $upleft);
            }
        }

        return $matrix[$m][$n];
    }

    /**
     * Get Jaro-Winkler Score
     *
     * @param string $first  String to match
     * @param string $second String to match
     *
     * @return double $jaroWinkler Jaro-Winkler score between 0.0 and 1.0
     */
    public function getJaroWinkler($first, $second)
    {
        $shorter;
        $longer;

        if (mb_strlen($first, CHARSET) > mb_strlen($second, CHARSET)) {
            $longer = mb_strtolower($first, CHARSET);
            $shorter = mb_strtolower($second, CHARSET);
        } else {
            $longer = mb_strtolower($second, CHARSET);
            $shorter = mb_strtolower($first, CHARSET);
        }

        // Get half the length distance of shorter string
        $halfLen = intval((mb_strlen($shorter,CHARSET) / 2) + 1);

        $match1 = $this->_getCharMatch($shorter, $longer, $halfLen);
        $match2 = $this->_getCharMatch($longer, $shorter, $halfLen);

        if ((mb_strlen($match1, CHARSET) == 0 || mb_strlen($match2, CHARSET) == 0)
            || (mb_strlen($match1, CHARSET) != mb_strlen($match2, CHARSET))
        ) {
            return 0.0;
        }

        $trans = $this->_getTranspositions($match1, $match2);

        $distance = (mb_strlen($match1, CHARSET) / mb_strlen($shorter, CHARSET)
            + mb_strlen($match2, CHARSET) / mb_strlen($longer, CHARSET)
            + (mb_strlen($match1, CHARSET) - $trans)
            / mb_strlen($match1, CHARSET)) / 3.0;

        // Apply Winkler Adjustment
        $prefixLen = min(mb_strlen($this->_getPrefix($first, $second),CHARSET), 4);
        $jaroWinkler = round(($distance + (0.1 * $prefixLen * (1.0 - $distance))) * 100.0) / 100.0;

        return $jaroWinkler;
    }

    /**
     * Get Character Matches
     *
     * @param string $first  String to match
     * @param string $second String to match
     * @param int    $limit  Limit of characters to match
     *
     * @return string $common Common substring
     */
    private function _getCharMatch($first, $second, $limit)
    {
        $common = '';
        $copy = $second;
        $firstLen = mb_strlen($first, CHARSET);
        $secondLen = mb_strlen($second, CHARSET);

        for ($i = 0; $i < $firstLen; $i++) {
            $char = $first[$i];
            $found = false;

            for ($j = max(0, $i - $limit); !$found && $j < min($i + $limit, $secondLen); $j++) {
                if ($copy[$j] == $char) {
                    $found = true;
                    $common .= $char;
                    $copy[$j] = '*';
                }
            }
        }

        return $common;
    }

    /**
     * Get Transpositions
     *
     * @param string $first  String to match
     * @param string $second String to match
     *
     * @return int $trans Number of transpositions between strings
     */
    private function _getTranspositions($first, $second)
    {
        $trans = 0;
        $firstLen = mb_strlen($first, CHARSET);

        for ($i = 0; $i < $firstLen; $i++) {
            if ($first[$i] != $second[$i]) {
                $trans += 1;
            }
        }

        $trans /= 2;
        return $trans;
    }

    /**
     * Get Prefix
     *
     * @param string $first  String to match
     * @param string $second String to match
     *
     * @return string Returns substring representing the longest prefix
     */
    private function _getPrefix($first, $second)
    {
        if (mb_strlen($first, CHARSET) == 0 || mb_strlen($second, CHARSET) == 0) {
            return '';
        }

        $index = $this->_getDiffIndex($first, $second);
        if ($index == -1) {
            return $first;
        } elseif ($index == 0) {
            return '';
        } else {
            return mb_substr($first, 0, $index, CHARSET);
        }
    }

    /**
     * Get Difference Index
     *
     * @param string $first  String to match
     * @param string $second String to match
     *
     * @return Return index of first difference
     */
    private function _getDiffIndex($first, $second)
    {
        if ($first == $second) {
            return -1;
        }

        $maxLen = min(mb_strlen($first, CHARSET), mb_strlen($second, CHARSET));
        for ($i = 0; $i < $maxLen; $i++) {
            if ($first[$i] != $second[$i]) {
                return $i;
            }
        }

        return $maxLen;
    }

    /**
     * Print Matrix
     * Utility / Testing function for testing purposes
     *
     * @param array $arr 2-dimensional array representing a matrix
     *
     * @return void
     */
    private function _printMatrix($arr)
    {
        $str = '';
        $width = count($arr[0]);
        $height = count($arr);

        for ($i = 0; $i < $height; $i++) {
            for ($j = 0; $j < $width; $j++) {
                if (!isset($arr[$i][$j])) {
                    $arr[$i][$j] = ' ';
                }

                $str = $str . "[{$arr[$i][$j]}]";

                if ($j === $width - 1) {
                    $str = $str . PHP_EOL;
                }
            }
        }

        print($str);
    }
}
