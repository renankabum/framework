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
use Slim\Http\Response;

/**
 * Class BladeProvider
 *
 * @package Navegarte\Providers\BladeProvider
 * @author  Vagner Cardoso <vagnercardosoweb@gmail.com>
 */
final class BladeProvider extends ViewAbstract
{
    /**
     * Get engine template <b>BLADE</b>
     */
    public function register()
    {
        return $this;
    }
    
    /**
     * @param \Slim\Http\Response $response
     * @param string              $template
     * @param array               $data
     *
     * @return \Slim\Http\Response
     */
    public function render(Response $response, $template, array $data = [])
    {
        $output = $this->fetch($template, $data);
        
        $response->getBody()->write($output);
        
        return $response;
    }
    
    /**
     * @param string $template
     * @param array  $data
     *
     * @return mixed
     */
    public function fetch($template, array $data = [])
    {
        if (isset($data['template'])) {
            throw new \InvalidArgumentException("Duplicate template key found");
        }
        
        $viewPaths = '';
        if (is_string(config('view.path.folder'))) {
            $viewPaths = [config('view.path.folder')];
        }
        
        $render = new \Philo\Blade\Blade($viewPaths, config('view.path.compiled') . '/blade');
        
        return $render->view()->make($template, $data)->render();
    }
}
