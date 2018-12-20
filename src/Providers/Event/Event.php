<?php

/**
 * VCWeb Networks <https://www.vagnercardosoweb.com.br/>
 *
 * @package   VCWeb Networks
 * @author    Vagner Cardoso <vagnercardosoweb@gmail.com>
 * @license   MIT
 *
 * @copyright 2017-2018 Vagner Cardoso
 */

namespace Core\Providers\Event {
    
    /**
     * Class Event
     *
     * @package Core\Providers\Event
     * @author Vagner Cardoso <vagnercardosoweb@gmail.com>
     */
    class Event
    {
        /**
         * @var Event
         */
        protected static $instance = null;
        
        /**
         * @var array
         */
        protected $events = [];
        
        /**
         * @return Event
         */
        public static function getInstance()
        {
            if (empty(self::$instance)) {
                self::$instance = new self();
            }
            
            return self::$instance;
        }
        
        /**
         * @param string $event
         * @param \Closure $callable
         * @param int $priority
         */
        public function on($event, \Closure $callable, $priority = 10)
        {
            $event = (string) $event;
            $priority = (int) $priority;
            
            if (!isset($this->events[$event])) {
                $this->events[$event] = [[]];
            }
            
            if (is_callable($callable)) {
                $this->events[$event][$priority][] = $callable;
            }
        }
        
        /**
         * @param string $event
         * @param mixed ... (Opcional) Argument(s)
         *
         * @return void
         */
        public function emit($event)
        {
            $event = (string) $event;
            
            if (!isset($this->events[$event])) {
                $this->events[$event] = [[]];
            }
            
            if (!empty($this->events[$event])) {
                if (count($this->events[$event]) > 1) {
                    ksort($this->events[$event]);
                }
                
                $arguments = func_get_args();
                array_shift($arguments);
                
                foreach ($this->events[$event] as $priority) {
                    if (!empty($priority)) {
                        foreach ($priority as $callable) {
                            call_user_func_array($callable, $arguments);
                        }
                    }
                }
            }
        }
        
        /**
         * @param string $event
         *
         * @return array|mixed|null
         */
        public function events($event = null)
        {
            $event = (string) $event;
            
            if (!empty($event)) {
                return isset($this->events[$event])
                    ? $this->events[$event]
                    : null;
            }
            
            return $this->events;
        }
        
        /**
         * @param string $event
         */
        public function clear($event = null)
        {
            $event = (string) $event;
            
            if (!empty($event) && isset($this->events[$event])) {
                $this->events[$event] = [[]];
            } else {
                foreach ($this->events as $key => $value) {
                    $this->events[$key] = [[]];
                }
            }
        }
    }
}
