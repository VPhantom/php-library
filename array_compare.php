<?php declare(encoding = 'utf-8');

/* vim: set expandtab tabstop=4 shiftwidth=4 softtabstop=4: */

/**
 * Bi-directional comparison of multi-dimensional arrays
 *
 * PHP version 5
 *
 * Discussed at length in the user contributed notes at
 * <http://ca.php.net/manual/en/function.array-diff-assoc.php>
 *
 * LICENSE:
 *
 * This comes from a group discusson and effort on user notes on php.net.  I
 * don't think I can force GPL v3 on this unilaterally.  Therefore, all I can
 * say is use/modify this as you'd like, and if you distribute modified
 * versions, please keep the mention that our effort on php.net got us this
 * far. :-)
 *
 * @category  Library
 * @package   Library
 * @author    Stéphane Lavergne <lis@imars.com>
 * @copyright 2006-2010 Stéphane Lavergne
 * @license   http://www.gnu.org/licenses/gpl-3.0.txt  GNU GPL version 3
 * @link      http://www.imars.com/
 */

/**
 * Crawl through each array to find differences.
 * 
 * This includes differences in type (i.e. "2", 2 and 2.0 differ) as well as
 * bits present on one side but not on the other.  To catch differences,
 * including those of type, you need to use var_dump() on the result;
 * print_r() is too limited.
 *
 * This version includes dwraven's replacement of isset() with
 * array_key_exists(), as well as a new improvement to allow the comparison of
 * non-array arguments.
 *
 * @param mixed $array1 Array 1 (or anything to test with !==)
 * @param mixed $array2 Array 2
 *
 * @return array An array with two elements, one containing what's in your 
 *               first array, but not your second, and the second is vice 
 *               versa.
 */
function array_compare($array1, $array2)
{
    $diff = false;
    if (!is_array($array1) || !is_array($array2)) {
        // We need two arrays, so return non-array comparison.
        if ($array1 !== $array2) {
            $diff = Array($array1, $array2);
        }
        return $diff;
    }
    // Left-to-right
    foreach ($array1 as $key => $value) {
        if (!array_key_exists($key, $array2)) {
            $diff[0][$key] = $value;
        } elseif (is_array($value)) {
            if (!is_array($array2[$key])) {
                $diff[0][$key] = $value;
                $diff[1][$key] = $array2[$key];
            } else {
                $new = array_compare($value, $array2[$key]);
                if ($new !== false) {
                    if (isset($new[0])) {
                        $diff[0][$key] = $new[0];
                    }
                    if (isset($new[1])) {
                        $diff[1][$key] = $new[1];
                    }
                }
            }
        } elseif ($array2[$key] !== $value) {
            $diff[0][$key] = $value;
            $diff[1][$key] = $array2[$key];
        }
    }
    // Right-to-left
    foreach ($array2 as $key => $value) {
        if (!array_key_exists($key, $array1)) {
            $diff[1][$key] = $value;
        }
        // No direct comparsion because matching keys were compared in the
        // left-to-right loop earlier, recursively.
    }
    return $diff;
}

?>
