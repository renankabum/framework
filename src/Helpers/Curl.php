<?php

/**
 * VCWeb Networks <https://www.vagnercardosoweb.com.br/>
 *
 * @package   VCWeb Networks
 * @author    Vagner Cardoso <vagnercardosoweb@gmail.com>
 * @license   MIT
 *
 * @copyright 15/04/2019 Vagner Cardoso
 */

namespace Core\Helpers {
    
    /**
     * Class \Core\Helpers\Request
     *
     * @package Core\Helpers
     * @author  Vagner Cardoso <vagnercardosoweb@gmail.com>
     */
    class Request extends Curl
    {
    }
    
    /**
     * Class \Core\Helpers\Curl
     *
     * @package Core\Helpers
     * @author  Vagner Cardoso <vagnercardosoweb@gmail.com>
     */
    class Curl
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
         * Metodo get cURL
         *
         * @param string $endPoint
         * @param array $params
         *
         * @return array
         * @throws \Exception
         */
        public function get($endPoint, $params = [])
        {
            return $this->create('get', $endPoint, $params);
        }
        
        /**
         * Realiza a requisição
         *
         * @param string $method
         * @param string $endPoint
         * @param array $params
         *
         * @return array
         * @throws \Exception
         */
        public function create($method, $endPoint, $params = [])
        {
            try {
                // Cria a requisição
                $response = $this->createRequest($method, $endPoint, $params);
                
                // Verifica se o retorno e json
                if ($json = Helper::checkJson($response)) {
                    return $json;
                }
                
                // Verifica se o retorno é xml
                if ($xml = Helper::checkXml($response)) {
                    if (version_compare(config('app.version.framework'), 'v1.3.0', '<=')) {
                        $xml = json_decode(json_encode($xml), true);
                    }
                    
                    return $xml;
                }
                
                return $response;
            } catch (\Exception $e) {
                throw $e;
            }
        }
        
        /**
         * Inicializa e cria as requisições passadas
         *
         * @param string $method
         * @param string $endPoint
         * @param array|string $params
         *
         * @return mixed
         * @throws \Exception
         */
        protected function createRequest($method, $endPoint, $params)
        {
            $method = mb_strtoupper($method, 'UTF-8');
            
            // Verifica se os parametros foi passado
            if (!empty($params)) {
                // Formato de array
                if (is_array($params)) {
                    $params = Helper::buildQuery($params);
                } else {
                    // Formato de json
                    if (Helper::checkJson($params) && $method !== 'GET') {
                        $this->setHeaders("Content-Type: application/json");
                    }
                }
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
            
            // Passa os options para a requisição
            curl_setopt_array($curl, $options + $this->getOptions());
            
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
        
        /**
         * Recupera as headers (cabeçalhos)
         *
         * @return array
         */
        public function getHeaders()
        {
            // Defaults headers
            $this->headers[] = "User-Agent: VCWeb Networks Curl";
            $this->headers[] = "Accept-Charset: utf-8";
            $this->headers[] = "Accept-Language: pt-br;q=0.9,pt-BR";
            
            // $this->headers[] = "Content-Type: application/x-www-form-urlencoded";
            
            return $this->headers;
        }
        
        /**
         * Adiciona as headers (cabeçalhos) para a requisição
         *
         * @param string|array $headers
         *
         * @return \Core\Helpers\Curl
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
         * Recupera as opções
         *
         * @return array
         */
        public function getOptions()
        {
            return $this->options;
        }
        
        /**
         * Adiciona as opções para a criação do cURL
         *
         * @param array $options
         *
         * @return \Core\Helpers\Curl
         */
        public function setOptions(array $options)
        {
            foreach ((array) $options as $key => $option) {
                $this->options[$key] = $option;
            }
            
            return $this;
        }
        
        /**
         * Metodo post cURL
         *
         * @param string $endPoint
         * @param array|string $params
         *
         * @return array
         * @throws \Exception
         */
        public function post($endPoint, $params = [])
        {
            return $this->create('post', $endPoint, $params);
        }
        
        /**
         * Metodo put cURL
         *
         * @param string $endPoint
         * @param array $params
         *
         * @return array
         * @throws \Exception
         */
        public function put($endPoint, $params = [])
        {
            return $this->create('put', $endPoint, $params);
        }
        
        /**
         * Metodo delete cURL
         *
         * @param string $endPoint
         * @param array $params
         *
         * @return array
         * @throws \Exception
         */
        public function delete($endPoint, $params = [])
        {
            return $this->create('delete', $endPoint, $params);
        }
    }
}
