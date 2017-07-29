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

namespace Core\Helpers;

/**
 * Class Paginator
 *
 * @package App\Helpers
 * @author  Vagner Cardoso <vagnercardosoweb@gmail.com>
 */
final class Paginator
{
    /**
     * @var int
     */
    protected $total;
    
    /**
     * @var int|null
     */
    protected $limit;
    
    /**
     * @var int|null
     */
    protected $maxLinks;
    
    /**
     * @var string
     */
    protected $link;
    
    /**
     * @var string
     */
    protected $page;
    
    /**
     * @var int|mixed
     */
    protected $number;
    
    /**
     * @var string
     */
    protected $firts;
    
    /**
     * @var string
     */
    protected $last;
    
    /**
     * Paginator constructor.
     *
     * @param int    $total
     * @param int    $limit
     * @param int    $maxLinks
     * @param string $link
     * @param string $page
     */
    public function __construct($total, $link, $limit = null, $maxLinks = null, $page = 'page')
    {
        /**
         * Filtra o page
         */
        $filter = filter_input(INPUT_GET, $page, FILTER_DEFAULT);
        
        /**
         * Inicia os atributos
         */
        $this->total = (int) $total;
        $this->link = (string) $link;
        $this->limit = ((int) $limit ? $limit : 10);
        $this->maxLinks = ((int) $maxLinks ? $maxLinks : 4);
        $this->page = (string) $page;
        $this->number = (isset($filter) ? $filter : 1);
    }
    
    /**
     * Get offset
     *
     * @return int|mixed
     */
    public function offset()
    {
        return ($this->number * $this->limit) - $this->limit;
    }
    
    /**
     * Ger limit
     *
     * @return int|null
     */
    public function limit()
    {
        return $this->limit;
    }
    
    /**
     * Seta primeira página e ultima página
     *
     * @param string $first
     * @param string $last
     *
     * @return $this
     */
    public function setFirstAndLast($first, $last)
    {
        $this->firts = (string) $first;
        $this->last = (string) $last;
        
        return $this;
    }
    
    /**
     * Gera o html da paginação
     *
     * @return string
     */
    public function links()
    {
        $links = '';
        $pages = ceil($this->total / $this->limit);
        $link = $this->link . '?' . $this->page . '=';
        $maxLinks = $this->maxLinks;
        
        if ($this->total > $this->limit) {
            $links .= "<ul class='pagination'>";
            
            if ($this->firts) {
                $links .= "<li><a href='{$link}1'>{$this->firts}</a></li>";
            }
            
            for ($i = $this->number - $maxLinks; $i <= $this->number - 1; $i++) {
                if ($i >= 1) {
                    $links .= "<li><a href='{$link}{$i}'>{$i}</a></li>";
                }
            }
            
            $links .= "<li class='active'><a href='javascript:;'>{$this->number}</a></li>";
            
            for ($i = $this->number + 1; $i <= $this->number + $maxLinks; $i++) {
                if ($i <= $pages) {
                    $links .= "<li><a href='{$link}{$i}'>{$i}</a></li>";
                }
            }
            
            if ($this->last) {
                $links .= "<li><a href='{$link}{$pages}'>{$this->last}</a></li>";
            }
            
            $links .= "</ul>";
        }
        
        return $links;
    }
}