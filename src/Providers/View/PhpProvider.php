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

namespace Navegarte\Providers\View;

use Navegarte\Contracts\ViewAbstract;
use Psr\Http\Message\ResponseInterface;

/**
 * Class PhpProvider
 *
 * @package Navegarte\Providers\View
 * @author  Vagner Cardoso <vagnercardosoweb@gmail.com>
 */
final class PhpProvider extends ViewAbstract
{
    /**
     * Register new view provider
     *
     * @return mixed
     */
    public function register()
    {
        return 'Render PHP ainda não implementado...';
    }
    
    /**
     * @param \Psr\Http\Message\ResponseInterface $response
     * @param string                              $template
     * @param array                               $data
     *
     * @return \Psr\Http\Message\ResponseInterface
     */
    public function render(ResponseInterface $response, $template, array $data = [])
    {
        $output = $this->fetch($template, $data);
        $response->getBody()->write($output);
        
        return $response;
    }
    
    /**
     * @param string $template
     * @param array  $data
     */
    private function fetch(string $template, array $data = [])
    {
    }
}
