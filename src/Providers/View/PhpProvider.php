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

namespace Core\Providers\View;

use Core\Contracts\ViewAbstract;
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
     * @var string
     */
    protected $templatePath;
    
    /**
     * @var string
     */
    protected $templatePhp;
    
    /**
     * @var array
     */
    protected $attributes = [];
    
    /**
     * @var array
     */
    protected $data = [];
    
    /**
     * @var string
     */
    protected $content;
    
    /**
     * Registar o novo provider
     *
     * @return \Core\Providers\View\PhpProvider
     */
    public function register()
    {
        $config = (object) config('view');
    
        $this->templatePath = rtrim($config->path['folder'], '/\\');
        $this->templatePhp = $this->templatePath . '/app.php';
        
        return $this;
    }
    
    /**
     * Renderiza a view
     *
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
     * Método para incluir um novo arquivo no template
     *
     * @param string $pathFile
     *
     * @return $this
     */
    public function includeFile($pathFile)
    {
        $pathFile = (string) "{$this->templatePath}/{$pathFile}.php";
    
        if (file_exists($pathFile)) {
            include "{$pathFile}";
        }
        
        return $this;
    }
    
    /**
     * Método para incluir todas view dentro
     * do template `app.php`
     *
     * @return $this
     */
    public function content()
    {
        include "{$this->content}";
        
        return $this;
    }
    
    /**
     * Adiciona os atributos
     *
     * @param array $attributes
     *
     * @return $this
     */
    public function setAttributes(array $attributes)
    {
        $this->attributes = $attributes;
        
        return $this;
    }
    
    /**
     * Recupera todos atributos passados
     *
     * @return array
     */
    public function getAttributes()
    {
        return $this->attributes;
    }
    
    /**
     * Adiciona um atributo
     *
     * @param mixed $key
     * @param mixed $value
     *
     * @return $this
     */
    public function addAttribute($key, $value)
    {
        $this->attributes[$key] = $value;
        
        return $this;
    }
    
    /**
     * Recupera o atributo
     *
     * @param string $key
     *
     * @return bool|mixed
     */
    public function getAttribute($key)
    {
        if (!isset($this->attributes[$key])) {
            return false;
        }
        
        if (array_key_exists($key, $this->attributes)) {
            return $this->attributes[$key];
        }
        
        return false;
    }
    
    /**
     * Adiciona um atributo ( Padrão Twig Template )
     *
     * @param mixed $key
     * @param mixed $value
     *
     * @return \Core\Providers\View\PhpProvider
     */
    public function addGlobal($key, $value)
    {
        $this->addAttribute($key, $value);
    }
    
    /**
     * Recupera a data, atributos e container da classe
     *
     * @param string $name
     *
     * @return mixed
     */
    public function __get($name)
    {
        $data = array_merge($this->attributes, $this->data);
        
        if (array_key_exists($name, $data)) {
            return $data[$name];
        }
    }
    
    /**
     * Cria o render
     *
     * @param string $template
     * @param array  $data
     *
     * @return string
     * @throws \Exception
     * @throws \Throwable
     */
    private function fetch($template, array $data = [])
    {
        if (!is_file($this->templatePath . '/' . $template)) {
            throw new \RuntimeException("View cannot render `$template` because the template does not exist");
        }
    
        $this->data = $this->attributes + $data;
        
        try {
            ob_start();
    
            extract($this->data);
    
            if (file_exists($this->templatePhp)) {
                $this->content = $this->templatePath . '/' . $template;
        
                include "{$this->templatePhp}";
            } else {
                include "{$this->templatePath}/{$template}";
            }
            
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
