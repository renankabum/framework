<?php

/**
 * Core <https://www.vagnercardosoweb.com.br/>
 *
 * @package   Core
 * @author    Vagner Cardoso <vagnercardosoweb@gmail.com>
 * @license   MIT
 *
 * @copyright 2017-2017 Vagner Cardoso
 */

namespace Core\Providers\View;

use Core\Contracts\ViewAbstract;
use Core\Helpers\Arr;
use Psr\Http\Message\ResponseInterface;

/**
 * Class PhpProvider
 *
 * @package Core\Providers\View
 * @author  Vagner Cardoso <vagnercardosoweb@gmail.com>
 */
final class PhpProvider extends ViewAbstract
{
    /**
     * @var array
     */
    protected $var;
    
    /**
     * Register new view provider
     *
     * @return \Core\Providers\View\PhpProvider
     */
    public function register()
    {
        return $this;
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
     * @param string $key
     *
     * @return bool
     */
    public function existsVar($key)
    {
        return Arr::exists($this->var, $key);
    }
    
    /**
     * @return array|bool
     */
    public function getVar($key)
    {
        if ($this->existsVar($key)) {
            return $this->var[$key];
        }
        
        return false;
    }
    
    /**
     * @return array
     */
    public function getVars()
    {
        return $this->var;
    }
    
    /**
     * @param string $key
     * @param mixed  $value
     *
     * @return $this
     *
     */
    public function setVar($key, $value)
    {
        if (!$this->existsVar($key)) {
            $this->var[$key] = $value;
        }
        
        return $this;
    }
    
    /**
     * @param string|array $key
     *
     * @return $this
     */
    public function removeVar($key)
    {
        if ($this->existsVar($key)) {
            Arr::forget($this->var, $key);
        }
        
        return $this;
    }
    
    /**
     * @param string $template
     * @param array  $data
     *
     * @return string
     * @throws \Exception
     * @throws \Throwable
     */
    private function fetch($template, array $data = [])
    {
        $templatePath = rtrim(config('view.path.folder'), '/\\') . "/php/{$template}";
    
        if (isset($data['template'])) {
            throw new \InvalidArgumentException("Duplicate template key found");
        }
    
        if (!is_file($templatePath)) {
            throw new \RuntimeException("View cannot render `$template` because the template does not exist");
        }
    
        $data = array_merge($this->var, $data);
    
        try {
            ob_start();
        
            extract($data);
        
            include $templatePath;
        
            $output = ob_get_clean();
        } catch (\Throwable $e) { // PHP 7+
            ob_end_clean();
            throw $e;
        } catch (\Exception $e) { // PHP < 7
            ob_end_clean();
            throw $e;
        }
    
        return $output;
    }
}
