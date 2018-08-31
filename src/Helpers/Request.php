<?php

/**
 * VCWeb <https://www.vagnercardosoweb.com.br/>
 *
 * @package   VCWeb
 * @author    Vagner Cardoso <vagnercardosoweb@gmail.com>
 * @license   MIT
 *
 * @copyright 2017-2018 Vagner Cardoso
 */

namespace Core\Helpers {
    
    /**
     * Class \Core\Helpers\Request
     *
     * @package Core\Helpers
     * @author  Vagner Cardoso <vagnercardosoweb@gmail.com>
     */
    class Request
    {
        /**
         * @var array
         */
        protected $headers = [];
        
        /**
         * @var array
         */
        protected $options = [];
        
        /**
         * Realiza a requisição
         *
         * @param string $method
         * @param string $endPoint
         * @param array  $params
         *
         * @return array
         * @throws \Exception
         */
        public function create($method, $endPoint, array $params = array())
        {
            try {
                // Cria a requisição
                $response = $this->createCurlRequest($method, $endPoint, $params);
                
                // Trata o retorno
                $result = json_decode($response, true);
                
                if (json_last_error() != JSON_ERROR_NONE) {
                    if ($xml = $this->isValidXML($response)) {
                        $result = json_decode(json_encode($xml), true);
                    } else {
                        // Caso não seja o retorno em `json` e em `xml`
                        // será retornado a resposta completa
                        $result = $response;
                    }
                }
                
                return $result;
            } catch (\Exception $e) {
                $code = $e->getCode();
                
                if (is_string($code)) {
                    $code = 500;
                }
                
                throw new \Exception($e->getMessage(), $code);
            }
        }
        
        /**
         * Metodo get cURL
         *
         * @param string $endPoint
         * @param array  $params
         *
         * @return array
         * @throws \Exception
         */
        public function get($endPoint, array $params = array())
        {
            return $this->create('get', $endPoint, $params);
        }
        
        /**
         * Metodo post cURL
         *
         * @param string $endPoint
         * @param array  $params
         *
         * @return array
         * @throws \Exception
         */
        public function post($endPoint, array $params = array())
        {
            return $this->create('post', $endPoint, $params);
        }
        
        /**
         * Metodo put cURL
         *
         * @param string $endPoint
         * @param array  $params
         *
         * @return array
         * @throws \Exception
         */
        public function put($endPoint, array $params = array())
        {
            return $this->create('put', $endPoint, $params);
        }
        
        /**
         * Metodo delete cURL
         *
         * @param string $endPoint
         * @param array  $params
         *
         * @return array
         * @throws \Exception
         */
        public function delete($endPoint, array $params = array())
        {
            return $this->create('delete', $endPoint, $params);
        }
        
        /**
         * Adiciona as headers (cabeçalhos) para a requisição
         *
         * @param string|array $headers
         *
         * @return \Core\Helpers\Request
         */
        public function setHeaders($headers)
        {
            if (!is_array($headers)) {
                $headers = [$headers];
            }
            
            foreach ($headers as $header) {
                $this->headers[] = $header;
            }
            
            return $this;
        }
        
        /**
         * Recupera as headers (cabeçalhos)
         *
         * @return array
         */
        public function getHeaders()
        {
            // Defaults headers
            $this->headers[] = "User-Agent: VCWeb Create cURL";
            $this->headers[] = "Accept-Charset: utf-8";
            $this->headers[] = "Accept-Language: pt-br;q=0.9,pt-BR";
            $this->headers[] = "Content-Type: application/x-www-form-urlencoded";
            
            return $this->headers;
        }
        
        /**
         * Adiciona as opções para a criação do cURL
         *
         * @param array $options
         *
         * @return \Core\Helpers\Request
         */
        public function setOptions($options)
        {
            foreach ((array)$options as $key => $option) {
                $this->options[$key] = $option;
            }
            
            return $this;
        }
        
        /**
         * Recupera as opções
         *
         * @return array
         */
        public function getOptions()
        {
            return $this->options;
        }
        
        /**
         * Verifica se o XML é válido
         *
         * @param $context
         *
         * @return bool|\SimpleXMLElement|string
         */
        protected function isValidXML($context)
        {
            $context = trim($context);
            
            if (empty($context)) {
                return false;
            }
            
            // Verifica se o documento é em HTML
            if (stripos($context, '<!DOCTYPE html>') !== false) {
                return false;
            }
            
            // Lib XML
            libxml_use_internal_errors(true);
            $context = simplexml_load_string($context);
            $errors = libxml_get_errors();
            libxml_clear_errors();
            
            if (!empty($errors)) {
                return false;
            }
            
            return $context;
        }
        
        /**
         * Cria os fields das requisições simulando o http_build_query()
         *
         * @param array $array
         * @param null  $prefix
         *
         * @return string|array
         */
        protected function httpBuildCurl(array $array, $prefix = null)
        {
            if (!is_array($array)) {
                return $array;
            }
            
            $params = [];
            
            foreach ($array as $key => $value) {
                if (is_null($value)) {
                    continue;
                }
                
                if ($prefix && $key && !is_int($key)) {
                    $key = "{$prefix}[{$key}]";
                } else if ($prefix) {
                    $key = "{$prefix}[]";
                }
                
                if (is_array($value)) {
                    $params[] = $this->httpBuildCurl($value, $key);
                } else {
                    $params[] = $key.'='.urlencode($value);
                }
            }
            
            return implode('&', $params);
        }
        
        /**
         * Inicializa e cria as requisições passadas
         *
         * @param string $method
         * @param string $endPoint
         * @param array  $params
         *
         * @return mixed
         * @throws \Exception()
         */
        protected function createCurlRequest($method, $endPoint, array $params)
        {
            $method = mb_strtoupper($method, 'UTF-8');
            
            // Verifica se a data e array e está passada
            if (is_array($params) && !empty($params)) {
                $params = $this->httpBuildCurl($params);
            } else {
                $params = null;
            }
            
            // Trata a URL se for GET
            if ($method === 'GET') {
                $separator = '?';
                if (strpos($endPoint, '?') !== false) {
                    $separator = '&';
                }
                
                $endPoint .= "{$separator}{$params}";
            }
            
            // Inicializa o cURL
            $curl = curl_init();
            
            // Monta as opções da requisição
            $options = [
                CURLOPT_URL => $endPoint,
                CURLOPT_HTTPHEADER => $this->getHeaders(),
                CURLOPT_RETURNTRANSFER => true,
                CURLOPT_CONNECTTIMEOUT => 30,
                CURLOPT_TIMEOUT => 80,
                CURLOPT_CUSTOMREQUEST => $method,
            ];
            
            // Verifica se não e GET e passa os parametros
            if ($method !== 'GET') {
                $options[CURLOPT_POSTFIELDS] = $params;
            }
            
            // Verifica se a requisição e POST
            if ($method === 'POST') {
                $options[CURLOPT_POST] = true;
            }
            
            // Junta os options default com os passados
            $options = $options + $this->getOptions();
            
            // Passa os options para a requisição
            curl_setopt_array($curl, $options);
            
            // Resultados
            $response = curl_exec($curl);
            $error = curl_error($curl);
            curl_close($curl);
            
            // Verifica se houve erros
            if ($error) {
                return $error;
            }
            
            // Retorna a resposta da requisição
            return $response;
        }
    }
}
