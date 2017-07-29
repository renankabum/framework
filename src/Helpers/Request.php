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
     * @param array  $data
     *
     * @return array
     */
    public function create($method, $endPoint, array $data = array())
    {
        $method = strtoupper($method);
        if ($method != 'GET') {
            $this->options[CURLOPT_POSTFIELDS] = $data;
        }
        
        if ($method == 'POST') {
            $this->options[CURLOPT_POST] = 1;
        }
        
        if (!empty($data)) {
            $data = $this->http_build_curl($data);
        }
        
        $response = $this->createRequest($method, $endPoint, $data);
        
        return $response;
    }
    
    /**
     * Metodo get cURL
     *
     * @param string $endPoint
     * @param array  $data
     *
     * @return array
     */
    public function get($endPoint, array $data = array())
    {
        return $this->create('get', $endPoint, $data);
    }
    
    /**
     * Metodo post cURL
     *
     * @param string $endPoint
     * @param array  $data
     *
     * @return array
     */
    public function post($endPoint, array $data = array())
    {
        return $this->create('post', $endPoint, $data);
    }
    
    /**
     * Metodo put cURL
     *
     * @param string $endPoint
     * @param array  $data
     *
     * @return array
     */
    public function put($endPoint, array $data = array())
    {
        return $this->create('put', $endPoint, $data);
    }
    
    /**
     * Metodo delete cURL
     *
     * @param string $endPoint
     * @param array  $data
     *
     * @return array
     */
    public function delete($endPoint, array $data = array())
    {
        return $this->create('delete', $endPoint, $data);
    }
    
    /**
     * @param array $headers
     *
     * @return \Core\Helpers\Request
     */
    public function setHeaders($headers)
    {
        foreach ($headers as $header) {
            $this->headers[] = $header;
        }
        
        return $this;
    }
    
    /**
     * @return array
     */
    public function getHeaders()
    {
        return $this->headers;
    }
    
    /**
     * @param array $options
     *
     * @return \Core\Helpers\Request
     */
    public function setOptions($options)
    {
        foreach ($options as $key => $option) {
            $this->options[$key] = $option;
        }
        
        return $this;
    }
    
    /**
     * @return array
     */
    public function getOptions()
    {
        return $this->options;
    }
    
    /**
     * Cria as headers (cabeçalhos) padrões
     * para a requisição
     *
     * @return array
     */
    private function getDefaultHeaders()
    {
        $this->headers[] = "User-Agent: VCWeb Create cURL";
        $this->headers[] = "Accept-Charset: UTF-8";
        $this->headers[] = "Accept-Language: pt-br;q=0.9,pt-BR";
        $this->headers[] = "Accept: application/json";
        $this->headers[] = "Content-Type: application/x-www-form-urlencoded";
        
        return $this->headers;
    }
    
    /**
     * Cria os fields das requisições
     * simulando o http_build_query()
     *
     * @param array $array
     * @param null  $prefix
     *
     * @return string|array
     */
    private function http_build_curl(array $array, $prefix = null)
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
            } elseif ($prefix) {
                $key = "{$prefix}[]";
            }
            
            if (is_array($value)) {
                $params[] = $this->http_build_curl($value, $key);
            } else {
                $params[] = urlencode($key) . '=' . urlencode($value);
            }
        }
        
        return implode('&', $params);
    }
    
    /**
     * Metodo privado da classe para a inicialização do cURL
     *
     * @param string $method
     * @param string $endPoint
     * @param array  $data
     *
     * @return mixed|string
     * @throws \Exception()
     */
    private function createRequest($method, $endPoint, array $data = array())
    {
        /**
         * Inicia o cURL
         */
        $cURL = curl_init();
        
        /**
         * Opções padrões para a requisição
         */
        $options = [
            CURLOPT_URL => $endPoint,
            CURLOPT_RETURNTRANSFER => 1,
            CURLOPT_CONNECTTIMEOUT => 30,
            CURLOPT_TIMEOUT => 80,
            CURLOPT_CUSTOMREQUEST => $method,
            CURLOPT_HTTPHEADER => $this->getDefaultHeaders()
        ];
        
        /**
         * Junta as opções padrões com a setadas.
         */
        $options = array_merge($options, $this->options);
        
        /**
         * Passa as opções para o cURL
         */
        curl_setopt_array($cURL, $options);
        
        $response = curl_exec($cURL);
        $error = curl_error($cURL);
        $code = curl_getinfo($cURL, CURLINFO_HTTP_CODE);
        
        /**
         * Fecha a requisição
         */
        curl_close($cURL);
        
        if ($error) {
            return $error;
        }
        
        if ($code != 200) {
            throw new \Exception($response);
        }
        
        /**
         * Transforma em array o retorno
         */
        $response = json_decode($response, true);
        
        if (json_last_error() != JSON_ERROR_NONE) {
            throw new \Exception($response);
        }
        
        return $response;
    }
}
