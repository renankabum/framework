<?php

/**
 * VCWeb Networks <https://www.vagnercardosoweb.com.br/>
 *
 * @package   VCWeb Networks
 * @author    Vagner Cardoso <vagnercardosoweb@gmail.com>
 * @license   MIT
 *
 * @copyright 28/04/2017 Vagner Cardoso
 */

namespace Core\Helpers {
    
    /**
     * Class Paginator
     *
     * @package App\Helpers
     * @author  Vagner Cardoso <vagnercardosoweb@gmail.com>
     */
    class Paginator
    {
        /**
         * @var int
         */
        protected $total;
        
        /**
         * @var int
         */
        protected $limit;
        
        /**
         * @var int
         */
        protected $offset;
        
        /**
         * @var int
         */
        protected $pages;
        
        /**
         * @var int
         */
        protected $range;
        
        /**
         * @var string
         */
        protected $link;
        
        /**
         * @var string
         */
        protected $currentPage;
        
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
         * @param int $total
         * @param int $limit
         * @param int $range
         * @param string $link
         * @param string $pageString
         */
        public function __construct($total, $link, $limit = 10, $range = 4, $pageString = 'page')
        {
            // Atributos
            $this->total = (int) $total;
            $this->link = (string) $link;
            $this->limit = (int) ($limit ? $limit : 10);
            $this->range = (int) ($range ? $range : 4);
            
            // Calcula total de páginas
            $this->pages = max((int) ceil($this->total / $this->limit), 1);
            
            // Filter page
            $currentPage = filter_input(INPUT_GET, $pageString, FILTER_DEFAULT);
            $currentPage = ($currentPage > PHP_INT_MAX) ? $this->pages : $currentPage;
            $this->currentPage = (int) (isset($currentPage) ? $currentPage : 1);
            
            // Calcula offset
            $this->offset = ($this->currentPage * $this->limit) - $this->limit;
            
            // Monta o link
            if (strpos($this->link, '?') !== false) {
                $this->link = "{$this->link}&{$pageString}=";
            } else {
                $this->link = "{$this->link}?{$pageString}=";
            }
            
            // Verifica o total de página passadas
            if ($this->offset >= $this->total) {
                header("Location: {$this->link}{$this->pages}", true, 301);
            }
        }
        
        /**
         * @return int
         */
        public function total()
        {
            return $this->total;
        }
        
        /**
         * @return int
         */
        public function limit()
        {
            return $this->limit;
        }
        
        /**
         * @return int
         */
        public function offset()
        {
            return $this->offset;
        }
        
        /**
         * @return int
         */
        public function pages()
        {
            return $this->pages;
        }
        
        /**
         * @return int
         */
        public function range()
        {
            return $this->range;
        }
        
        /**
         * @return string
         */
        public function link()
        {
            return $this->link;
        }
        
        /**
         * @return string
         */
        public function currentPage()
        {
            return $this->currentPage;
        }
        
        /**
         * @return bool
         */
        public function prev()
        {
            return ($this->currentPage > 1);
        }
        
        /**
         * @return bool
         */
        public function next()
        {
            return ($this->pages > $this->currentPage);
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
         * @param string $classCss
         *
         * @return string
         */
        public function links($classCss = 'pagination')
        {
            $html = '';
            
            // Cria html da paginação
            if ($this->total > $this->limit) {
                $html .= "<ul class='{$classCss}'>";
                $html .= $this->before();
                $html .= "<li class='active'><a href='javascript:void(0);'>{$this->currentPage}</a></li>";
                $html .= $this->after();
                $html .= "</ul>";
            }
            
            return $html;
        }
        
        /**
         * @return string
         */
        protected function before()
        {
            $html = '';
            
            if ($this->firts) {
                $html .= "<li><a href='{$this->link}1'>{$this->firts}</a></li>";
            }
            
            for ($i = $this->currentPage - $this->range; $i <= $this->currentPage - 1; $i++) {
                if ($i >= 1) {
                    $html .= "<li><a href='{$this->link}{$i}'>{$i}</a></li>";
                }
            }
            
            return $html;
        }
        
        /**
         * @return string
         */
        protected function after()
        {
            $html = '';
            
            for ($i = $this->currentPage + 1; $i <= $this->currentPage + $this->range; $i++) {
                if ($i <= $this->pages) {
                    $html .= "<li><a href='{$this->link}{$i}'>{$i}</a></li>";
                }
            }
            
            if ($this->last) {
                $html .= "<li><a href='{$this->link}{$this->pages}'>{$this->last}</a></li>";
            }
            
            return $html;
        }
    }
}
