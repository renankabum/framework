<?php

/**
 * VCWeb <https://www.vagnercardosoweb.com.br/>
 *
 * @package   VCWeb
 * @author    Vagner Cardoso <vagnercardosoweb@gmail.com>
 * @license   MIT
 *
 * @copyright 2017-2017 Vagner Cardoso
 */

namespace Navegarte\Helpers;

use ArrayAccess;

/**
 * Class Arr
 *
 * @package Navegarte\Helpers
 * @author  Vagner Cardoso <vagnercardosoweb@gmail.com>
 */
class Arr
{
    /**
     * @param mixed $array
     *
     * @return bool
     */
    public static function accessible(array $array)
    {
        return is_array($array) || $array instanceof ArrayAccess;
    }
    
    /**
     * Add an element to an array using "dot" notation if it doesn't exist.
     *
     * @param  array  $array
     * @param  string $key
     * @param  mixed  $value
     *
     * @return array
     */
    public static function add(array $array, $key, $value)
    {
        if (is_null(static::get($array, $key))) {
            static::set($array, $key, $value);
        }
        
        return $array;
    }
    
    /**
     * @param array $array
     * @param int   $mode
     *
     * @return int
     */
    public static function count(array $array, $mode = COUNT_NORMAL)
    {
        return count($array, $mode);
    }
    
    /**
     * Collapse an array of arrays into a single array.
     *
     * @param  array $array
     *
     * @return array
     */
    public static function collapse(array $array)
    {
        $results = [];
        
        foreach ($array as $values) {
            if (!is_array($values)) {
                continue;
            }
            
            $results = array_merge($results, $values);
        }
        
        return $results;
    }
    
    /**
     * Cross join the given arrays, returning all possible permutations.
     *
     * @param array|\array[] ...$arrays
     *
     * @return array
     */
    public static function crossJoin(array ...$arrays)
    {
        return array_reduce(
            $arrays,
            function ($results, $array) {
                return static::collapse(
                    array_map(
                        function ($parent) use ($array) {
                            return array_map(
                                function ($item) use ($parent) {
                                    return array_merge($parent, [$item]);
                                },
                                $array
                            );
                        },
                        $results
                    )
                );
            },
            [[]]
        );
    }
    
    /**
     * Divide an array into two arrays. One with keys and the other with values.
     *
     * @param  array $array
     *
     * @return array
     */
    public static function divide(array $array)
    {
        return [array_keys($array), array_values($array)];
    }
    
    /**
     * Flatten a multi-dimensional associative array with dots.
     *
     * @param  array  $array
     * @param  string $prepend
     *
     * @return array
     */
    public static function dot(array $array, $prepend = '')
    {
        $results = [];
        
        foreach ($array as $key => $value) {
            if (is_array($value) && !empty($value)) {
                $results = array_merge($results, static::dot($value, $prepend . $key . '.'));
            } else {
                $results[$prepend . $key] = $value;
            }
        }
        
        return $results;
    }
    
    /**
     * Get all of the given array except for a specified array of items.
     *
     * @param  array        $array
     * @param  array|string $keys
     *
     * @return array
     */
    public static function except(array $array, $keys)
    {
        static::forget($array, $keys);
        
        return $array;
    }
    
    /**
     * @param  \ArrayAccess|array $array
     * @param  string|int         $key
     *
     * @return bool
     */
    public static function exists(array $array, $key)
    {
        if ($array instanceof ArrayAccess) {
            return $array->offsetExists($key);
        }
        
        return array_key_exists($key, $array);
    }
    
    /**
     * @param  \ArrayAccess|array $array
     * @param  string|array       $keys
     *
     * @return bool
     */
    public static function has(array $array, $keys)
    {
        if (is_null($keys)) {
            return false;
        }
        
        $keys = (array)$keys;
        
        if (!$array) {
            return false;
        }
        
        if ($keys === []) {
            return false;
        }
        
        foreach ($keys as $key) {
            $subKeyArray = $array;
            
            if (static::exists($array, $key)) {
                continue;
            }
            
            foreach (explode('.', $key) as $segment) {
                if (static::accessible($subKeyArray) && static::exists($subKeyArray, $segment)) {
                    $subKeyArray = $subKeyArray[$segment];
                } else {
                    return false;
                }
            }
        }
        
        return true;
    }
    
    /**
     * @param \ArrayAccess|array $array
     * @param string             $key
     * @param mixed|null         $default
     *
     * @return mixed
     */
    public static function get(array $array, $key, $default = null)
    {
        if (!static::accessible($array)) {
            return $default;
        }
        
        if (is_null($key)) {
            return $array;
        }
        
        foreach (explode('.', $key) as $segment) {
            if (static::accessible($array) && static::exists($array, $segment)) {
                $array = $array[$segment];
            } else {
                return $default;
            }
        }
        
        return $array;
    }
    
    /**
     * @param array  $array
     * @param string $key
     * @param mixed  $value
     *
     * @return array
     */
    public static function set(array &$array, $key, $value)
    {
        if (is_null($key)) {
            $array = $value;
        }
        
        $keys = explode('.', $key);
        
        while (count($keys) > 1) {
            $key = array_shift($keys);
            
            if (!isset($array[$key]) || !is_array($array[$key])) {
                $array[$key] = [];
            }
            
            $array = &$array[$key];
        }
        
        $array[array_shift($keys)] = $value;
        
        return $array;
    }
    
    /**
     * Return the first element in an array passing a given truth test.
     *
     * @param  array         $array
     * @param  callable|null $callback
     * @param  mixed         $default
     *
     * @return mixed
     */
    public static function first(array $array, callable $callback = null, $default = null)
    {
        if (is_null($callback)) {
            if (empty($array)) {
                return $default;
            }
            
            foreach ($array as $item) {
                return $item;
            }
        }
        
        foreach ($array as $key => $value) {
            if (call_user_func($callback, $value, $key)) {
                return $value;
            }
        }
        
        return $default;
    }
    
    /**
     * Return the last element in an array passing a given truth test.
     *
     * @param  array         $array
     * @param  callable|null $callback
     * @param  mixed         $default
     *
     * @return mixed
     */
    public static function last(array $array, callable $callback = null, $default = null)
    {
        if (is_null($callback)) {
            return empty($array) ? $default : end($array);
        }
        
        return static::first(array_reverse($array, true), $callback, $default);
    }
    
    /**
     * Flatten a multi-dimensional array into a single level.
     *
     * @param  array $array
     * @param  int   $depth
     *
     * @return array
     */
    public static function flatten(array $array, $depth = INF)
    {
        return array_reduce(
            $array,
            function ($result, $item) use ($depth) {
                if (!is_array($item)) {
                    return array_merge($result, [$item]);
                } elseif ($depth === 1) {
                    return array_merge($result, array_values($item));
                } else {
                    return array_merge($result, static::flatten($item, $depth - 1));
                }
            },
            []
        );
    }
    
    /**
     * @param  array        $array
     * @param  array|string $keys
     *
     * @return void
     */
    public static function forget(array &$array, $keys)
    {
        $original = &$array;
        
        $keys = (array)$keys;
        
        if (count($keys) === 0) {
            return;
        }
        
        foreach ($keys as $key) {
            if (static::exists($array, $key)) {
                unset($array[$key]);
                continue;
            }
            
            $parts = explode('.', $key);
            
            $array = &$original;
            
            while (count($parts) > 1) {
                $part = array_shift($parts);
                
                if (isset($array[$part]) && is_array($array[$part])) {
                    $array = &$array[$part];
                } else {
                    continue 2;
                }
            }
            
            unset($array[array_shift($parts)]);
        }
    }
    
    /**
     * Determines if an array is associative.
     *
     * An array is "associative" if it doesn't have sequential numerical keys beginning with zero.
     *
     * @param  array $array
     *
     * @return bool
     */
    public static function isAssoc(array $array)
    {
        $keys = array_keys($array);
        
        return array_keys($keys) !== $keys;
    }
    
    /**
     * Get a subset of the items from the given array.
     *
     * @param  array        $array
     * @param  array|string $keys
     *
     * @return array
     */
    public static function only(array $array, $keys)
    {
        return array_intersect_key($array, array_flip((array)$keys));
    }
    
    /**
     * Explode the "value" and "key" arguments passed to "pluck".
     *
     * @param  string|array      $value
     * @param  string|array|null $key
     *
     * @return array
     */
    protected static function explodePluckParameters($value, $key)
    {
        $value = is_string($value) ? explode('.', $value) : $value;
        
        $key = is_null($key) || is_array($key) ? $key : explode('.', $key);
        
        return [$value, $key];
    }
    
    /**
     * Push an item onto the beginning of an array.
     *
     * @param  array $array
     * @param  mixed $value
     * @param  mixed $key
     *
     * @return array
     */
    public static function prepend(array $array, $value, $key = null)
    {
        if (is_null($key)) {
            array_unshift($array, $value);
        } else {
            $array = [$key => $value] + $array;
        }
        
        return $array;
    }
    
    /**
     * @param  array  $array
     * @param  string $key
     * @param  mixed  $default
     *
     * @return mixed
     */
    public static function pull(array &$array, $key, $default = null)
    {
        $value = static::get($array, $key, $default);
        
        static::forget($array, $key);
        
        return $value;
    }
    
    /**
     * @param  array $array
     *
     * @return mixed
     */
    public static function random(array $array)
    {
        return $array[array_rand($array)];
    }
    
    /**
     * @param  array $array
     *
     * @return array
     */
    public static function shuffle(array $array)
    {
        shuffle($array);
        
        return $array;
    }
    
    /**
     * @param  array $array
     *
     * @return array
     */
    public static function sortRecursive(array $array)
    {
        foreach ($array as &$value) {
            if (is_array($value)) {
                $value = static::sortRecursive($value);
            }
        }
        
        if (static::isAssoc($array)) {
            ksort($array);
        } else {
            sort($array);
        }
        
        return $array;
    }
    
    /**
     * @param  array    $array
     * @param  callable $callback
     *
     * @return array
     */
    public static function where(array $array, callable $callback)
    {
        return array_filter($array, $callback, ARRAY_FILTER_USE_BOTH);
    }
    
    /**
     * @param  mixed $value
     *
     * @return array
     */
    public static function wrap($value)
    {
        return !is_array($value) ? [$value] : $value;
    }
}
