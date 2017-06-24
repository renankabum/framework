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

/**
 * Class Str
 *
 * @package Navegarte\Helpers
 * @author  Vagner Cardoso <vagnercardosoweb@gmail.com>
 */
final class Str
{
    /**
     * @param string $string
     *
     * @return string
     */
    public static function ucfirst($string)
    {
        return static::upper(static::substr($string, 0, 1)) . static::substr($string, 1);
    }
    
    /**
     * @param string $string
     *
     * @return mixed|string
     */
    public static function upper($string)
    {
        return mb_strtoupper($string, 'UTF-8');
    }
    
    /**
     * @param string   $string
     * @param int      $start
     * @param int|null $length
     *
     * @return string
     */
    public static function substr($string, $start, $length = null)
    {
        return mb_substr($string, $start, $length, 'UTF-8');
    }
    
    /**
     * @param string $string
     *
     * @return mixed|string
     */
    public static function title($string)
    {
        return mb_convert_case($string, MB_CASE_TITLE, 'UTF-8');
    }
    
    /**
     * @param string $string
     * @param string $separator
     *
     * @return string
     */
    public static function slug($string, $separator = '-')
    {
        $rules = [];
        $rules['a'] = 'ÀÁÂÃÄÅÆÇÈÉÊËÌÍÎÏÐÑÒÓÔÕÖØÙÚÛÜüÝÞßàáâãäåæçèéêëìíîïðñòóôõöøùúûýýþÿRr"!@#$%&*()_-+={[}]/?;:.,\\\'<>°ºª';
        $rules['b'] = 'aaaaaaaceeeeiiiidnoooooouuuuuybsaaaaaaaceeeeiiiidnoooooouuuyybyRr                                 ';
        $rules['regex'] = '/([^A-Za-z0-9]|-)+/';
        
        $string = static::lower(strtr($string, utf8_decode($rules['a']), $rules['b']));
        $string = preg_replace($rules['regex'], $separator, $string);
        
        return trim($string, $separator);
    }
    
    /**
     * @param string $string
     *
     * @return mixed|string
     */
    public static function lower($string)
    {
        return mb_strtolower($string, 'UTF-8');
    }
    
    /**
     * @param string $string
     * @param int    $limit
     * @param string $end
     *
     * @return string
     */
    public static function limit($string, $limit = 50, $end = '...')
    {
        if (mb_strwidth($string, 'UTF-8') <= $limit) {
            return $string;
        }
        
        return rtrim(mb_strimwidth($string, 0, $limit, '', 'UTF-8')) . $end;
    }
    
    /**
     * @param string $string
     * @param int    $limit
     * @param string $end
     *
     * @return string
     */
    public static function limitChars($string, $limit = 50, $end = '...')
    {
        if (strlen($string) <= $limit) {
            return $string;
        }
        
        return self::substr($string, 0, strrpos(self::substr($string, 0, $limit), ' ')) . $end;
    }
    
    /**
     * @param string $string
     * @param int    $limit
     * @param string $end
     *
     * @return string
     */
    public static function limitWords($string, $limit, $end = '...')
    {
        preg_match('/^\s*+(?:\S++\s*+){1,' . $limit . '}/u', $string, $matches);
        
        if (empty($matches[0]) || static::length($string) === static::length($matches[0])) {
            return $string;
        }
        
        return rtrim($matches[0]) . $end;
    }
    
    /**
     * @param string      $string
     * @param string|null $encoding
     *
     * @return int
     */
    public static function length($string, $encoding = null)
    {
        if ($encoding) {
            return mb_strlen($string, $encoding);
        }
        
        return mb_strlen($string);
    }
    
    /**
     * @param string       $string
     * @param string|array $searches
     *
     * @return bool
     */
    public static function startsWith($string, $searches)
    {
        foreach ((array)$searches as $search) {
            if ($search != '' && substr($string, 0, strlen($search)) === (string)$search) {
                return true;
            }
        }
        
        return false;
    }
    
    /**
     * @param string       $string
     * @param string|array $searches
     *
     * @return bool
     */
    public static function endsWith($string, $searches)
    {
        foreach ((array)$searches as $search) {
            if (substr($string, -strlen($search)) == (string)$search) {
                return true;
            }
        }
        
        return false;
    }
    
    /**
     * @param string       $string
     * @param string|array $searches
     *
     * @return bool
     */
    public static function contains($string, $searches)
    {
        foreach ((array)$searches as $search) {
            if ($search != '' && mb_strpos($string, $search) !== false) {
                return true;
            }
        }
        
        return false;
    }
    
    /**
     * @param string $string
     * @param string $search
     *
     * @return bool|string
     */
    public static function after($string, $search)
    {
        if ($string == '') {
            return $string;
        }
        
        $position = strpos($string, $search);
        
        if ($position === false) {
            return $string;
        }
        
        return substr($string, $position + strlen($search));
    }
    
    /**
     * @param string $string
     * @param string $search
     *
     * @return bool|string
     */
    public static function before($string, $search)
    {
        if ($string == '') {
            return $string;
        }
        
        $position = strpos($string, $search);
        
        if ($position === false) {
            return $string;
        }
        
        return substr($string, 0, $position);
    }
    
    /**
     * @param string $search
     * @param array  $replace
     * @param string $subject
     *
     * @return mixed|string
     */
    public static function replaceArray($search, array $replace, $subject)
    {
        foreach ($replace as $value) {
            $subject = static::replaceFirst($search, $value, $subject);
        }
        
        return $subject;
    }
    
    /**
     * @param string $search
     * @param string $replace
     * @param string $subject
     *
     * @return mixed|string
     */
    public static function replaceFirst($search, $replace, $subject)
    {
        if ($search == '') {
            return $subject;
        }
        
        $position = strpos($subject, $search);
        
        if ($position !== false) {
            return substr_replace($subject, $replace, $position, strlen($search));
        }
        
        return $subject;
    }
    
    /**
     * @param string $search
     * @param string $replace
     * @param string $subject
     *
     * @return mixed|string
     */
    public static function replaceLast($search, $replace, $subject)
    {
        if ($search == '') {
            return $subject;
        }
        
        $position = strrpos($subject, $search);
        
        if ($position !== false) {
            return substr_replace($subject, $replace, $position, strlen($search));
        }
        
        return $subject;
    }
    
    /**
     * @param int $length
     *
     * @return string
     */
    public static function random($length = 16)
    {
        $string = '';
        
        while (($len = strlen($string)) < $length) {
            $size = $length - $len;
            
            $bytes = random_bytes($size);
            
            $string .= substr(str_replace(['/', '+', '='], '', base64_encode($bytes)), 0, $size);
        }
        
        return $string;
    }
}
