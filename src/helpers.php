<?php

use Core\Helpers\Debug;
use Core\Helpers\Str;

if (!function_exists('dd')) {
    /**
     * Debug mode
     *
     * @param mixed $dumps
     *
     * @return void
     */
    function dd(...$dumps)
    {
        foreach ($dumps as $dump) {
            (new Debug())->dump($dump);
        }
        
        die(1);
    }
}

if (!function_exists('env')) {
    /**
     * Get '.env' configuration
     *
     * @param string          $name
     * @param string|int|null $default
     *
     * @return mixed
     */
    function env($name, $default = null)
    {
        $value = getenv($name);
        
        if ($value === false) {
            return $default;
        }
        
        switch (strtolower($value)) {
            case 'true':
            case '(true)':
                return true;
                break;
            case 'false':
            case '(false)':
                return false;
                break;
            case 'empty':
            case '(empty)':
                return '';
                break;
            case 'null':
            case '(null)':
                return null;
                break;
        }
        
        if (strlen($value) > 1 && Str::startsWith($value, '"') && Str::endsWith($value, '"')) {
            return substr($value, 1, -1);
        }
        
        return $value;
    }
}

if (!function_exists('uuid')) {
    /**
     * Gera uma string no padrão uuid
     *
     * @return string
     */
    function uuid()
    {
        return sprintf('%04x%04x-%04x-%04x-%04x-%04x%04x%04x',
            mt_rand(0, 0xffff), mt_rand(0, 0xffff),
            mt_rand(0, 0xffff), mt_rand(0, 0x0C2f) | 0x4000,
            mt_rand(0, 0x3fff) | 0x8000,
            mt_rand(0, 0x2Aff), mt_rand(0, 0xffD3), mt_rand(0, 0xff4B)
        );
    }
}

if (!function_exists('asset')) {
    /**
     * Retorna o path dos assets e versiona
     *
     * @param string $path
     * @param bool   $url
     *
     * @return bool|string
     */
    function asset($path, $url = false)
    {
        if (!Str::startsWith($path, '/')) {
            $path = "/{$path}";
        }
        
        $baseUrl = rtrim(str_ireplace('index.php', '', request()
            ->getUri()
            ->getBasePath()), '/');
        
        if (file_exists(PUBLIC_FOLDER."{$path}")) {
            $version = substr(md5_file(PUBLIC_FOLDER."{$path}"), 0, 15);
            $fullUrl = ($url === true) ? BASE_URL : '';
            
            return "{$fullUrl}{$baseUrl}{$path}?v={$version}";
        }
        
        return false;
    }
}

if (!function_exists('asset_source')) {
    /**
     * @param string|array $path
     *
     * @return bool|string
     */
    function asset_source($path)
    {
        $paths = [];
        
        if (!is_array($path)) {
            $paths = [$path];
        }
        
        $sources = [];
        foreach ($paths as $path) {
            if (!Str::startsWith($path, '/')) {
                $path = "/{$path}";
            }
            
            if (file_exists(PUBLIC_FOLDER."{$path}")) {
                $sources[] = file_get_contents(PUBLIC_FOLDER."{$path}");
            }
        }
        
        return implode('', $sources);
    }
}

if (!function_exists('glob_recursive')) {
    /**
     * Percore os diretórios recursivamente
     * verificando se existe o `pattern` passado
     *
     * @param string $pattern
     * @param int    $flags
     *
     * @return array
     */
    function glob_recursive($pattern, $flags = 0)
    {
        $files = glob($pattern, $flags);
        
        foreach (glob(dirname($pattern).'/*', GLOB_ONLYDIR | GLOB_NOSORT) as $dir) {
            $files = array_merge($files, glob_recursive($dir.'/'.basename($pattern), $flags));
        }
        
        return $files;
    }
}

if (!function_exists('object_set')) {
    /**
     * Seta um array para objeto
     *
     * @param array $array
     *
     * @return object
     */
    function object_set($array)
    {
        $object = new stdClass();
        
        if (empty($array)) {
            return $object;
        }
        
        foreach ((array)$array as $key => $value) {
            if (is_array($value)) {
                $object->{$key} = object_set($value);
            } else {
                $object->{$key} = isset($value) ? $value : false;
            }
        }
        
        return $object;
    }
}

if (!function_exists('object_get')) {
    /**
     * Recupera um objeto
     *
     * @param object $object
     * @param string $name
     * @param string $default
     *
     * @return mixed
     */
    function object_get($object, $name, $default = null)
    {
        if (is_null($name) && trim($name) == '') {
            return $object;
        }
        
        foreach (explode('.', $name) as $segment) {
            if (is_object($object) || isset($object->{$segment})) {
                $object = $object->{$segment};
            } else {
                return $default;
            }
        }
        
        return $object;
    }
}

if (!function_exists('object_to_array')) {
    /**
     * Converte o objecto em array
     *
     * @param $object
     *
     * @return array
     */
    function object_to_array($object)
    {
        $array = [];
        
        foreach ((object)$object as $key => $value) {
            if (!isset($value) && trim($value) == '') {
                return $array;
            }
            
            if (is_object($value)) {
                $array[$key] = object_to_array($value);
            } else {
                if (isset($key)) {
                    $array[$key] = $value;
                }
            }
        }
        
        return $array;
    }
}

if (!function_exists('config')) {
    /**
     * Configurações do sistema
     *
     * @param string|null     $name
     * @param string|int|null $default
     *
     * @return mixed
     */
    function config($name = null, $default = null)
    {
        $config = [];
        
        foreach (glob_recursive(APP_FOLDER.'/config/**') as $path) {
            if (mb_strpos($path, '.php') !== false) {
                $file = basename($path, '.php');
                
                $config[$file] = include "{$path}";
            }
        }
        
        if (is_null($name)) {
            return $config;
        }
        
        if (array_key_exists($name, $config)) {
            return $config[$name];
        }
        
        foreach (explode('.', $name) as $key) {
            if (is_array($config) && array_key_exists($key, $config)) {
                $config = $config[$key];
            } else {
                return $default;
            }
        }
        
        return $config;
    }
}

if (!function_exists('logger')) {
    /**
     * Logger do sistemas
     *
     * @param string $message
     * @param array  $context
     * @param string $type
     * @param string $file
     *
     * @return bool|\Monolog\Logger
     */
    function logger($message, array $context = array(), $type = 'info', $file = null)
    {
        if (!is_object(app()->resolve('logger'))) {
            return false;
        }
        
        $type = strtolower($type);
        $type = strtoupper(substr($type, 0, 1)).substr($type, 1);
        
        return app()
            ->resolve('logger', [$file])
            ->{"add{$type}"}($message, $context);
    }
}

if (!function_exists('view')) {
    /**
     * Renderiza a view
     *
     * @param string $view
     * @param array  $array
     * @param int    $code
     *
     * @return mixed
     */
    function view($view, array $array = [], $code = null)
    {
        if (is_object(app()->resolve('view'))) {
            $response = response();
            
            if (!is_null($code)) {
                $response = $response->withStatus($code);
            }
            
            $extension = '.php';
            
            if (config('view.engine') === 'twig') {
                $extension = '.twig';
            } else if ($extension === 'blade') {
                $extension = '.blade.php';
            }
            
            // replace '.' em '/'
            $view = str_replace('.', '/', $view);
            
            return app()
                ->resolve('view')
                ->render($response, $view.$extension, $array);
        }
        
        return false;
    }
}

if (!function_exists('imagem')) {
    /**
     * Cria imagem e redimensina a mesma
     *
     * @param $src
     * @param $dest
     * @param $maxWidth
     * @param $maxHeight
     * @param $quality
     *
     * @return bool
     */
    function imagem($src, $dest, $maxWidth, $maxHeight, $quality)
    {
        $png = false;
        if (file_exists($src) && isset($dest)) {
            // Retorna informação sobre o path do um arquivo
            $destInfo = pathinfo($dest);
            
            // Retorna o tamanho da imagem
            $srcSize = getimagesize($src);
            
            // tamanho de destino $destSize[0] = width, $destSize[1] = height
            $srcRatio = $srcSize[0] / $srcSize[1]; // width/height média
            $destRatio = $maxWidth / $maxHeight;
            
            if ($destRatio > $srcRatio) {
                $destSize[1] = $maxHeight;
                $destSize[0] = $maxHeight * $srcRatio;
            } else {
                $destSize[0] = $maxWidth;
                $destSize[1] = $maxWidth / $srcRatio;
            }
            
            // retifica o arquivo
            if ($destInfo['extension'] == "gif") {
                $dest = substr_replace($dest, 'jpg', -3);
            }
            
            // cria uma imagem com a extensão original
            switch ($srcSize[2]) {
                case 1: //GIF
                    $srcImage = imagecreatefromgif($src);
                    break;
                case 2: //JPEG
                    $srcImage = imagecreatefromjpeg($src);
                    break;
                case 3: //PNG
                    $srcImage = imagecreatefrompng($src);
                    imagesavealpha($srcImage, true);
                    $png = true;
                    break;
                default:
                    return false;
                    break;
            }
            
            // ajusta a cor
            if (function_exists("imagecreatetruecolor")) {
                $destImage = imagecreatetruecolor($destSize[0], $destSize[1]);
            } else {
                $destImage = imagecreate($destSize[0], $destSize[1]);
            }
            
            if (function_exists("imageantialias")) {
                imageantialias($destImage, true);
            }
            
            if ($png) {
                if (substr($dest, -3) == 'png') {
                    imagealphablending($destImage, false);
                    imagesavealpha($destImage, true);
                    $transparent = imagecolorallocatealpha($destImage, 255, 255, 255, 127);
                } else {
                    $white = imagecolorallocate($destImage, 255, 255, 255);
                    imagefilledrectangle($destImage, 0, 0, $destSize[0], $destSize[1], $white);
                }
            }
            
            // copia a figura redimencionando o seu tamanho
            if (function_exists("imagecopyresampled")) {
                imagecopyresampled($destImage, $srcImage, 0, 0, 0, 0, $destSize[0], $destSize[1], $srcSize[0], $srcSize[1]);
            } else {
                imagecopyresized($destImage, $srcImage, 0, 0, 0, 0, $destSize[0], $destSize[1], $srcSize[0], $srcSize[1]);
            }
            
            if (substr($dest, -3) == 'png') {
                imagepng($destImage, $dest);
            } else {
                imagejpeg($destImage, $dest, $quality);
            }
            
            return true;
        } else {
            return false;
        }
    }
}

if (!function_exists('imagemTamExato')) {
    /**
     * Cria imagem com tamanho exato
     *
     * @param $imgSrc
     * @param $dest
     * @param $thumbnail_width
     * @param $thumbnail_height
     * @param $quality
     *
     * @return bool
     */
    function imagemTamExato($imgSrc, $dest, $thumbnail_width, $thumbnail_height, $quality)
    {
        if (file_exists($imgSrc) && isset($dest)) {
            $srcSize = getimagesize($imgSrc);
            $destInfo = pathinfo($dest);
            
            // retifica o arquivo
            if ($destInfo['extension'] == "gif") {
                $dest = substr_replace($dest, 'jpg', -3);
            }
            
            list($width_orig, $height_orig) = getimagesize($imgSrc);
            
            $png = false;
            
            switch ($srcSize[2]) {
                case 1: //GIF
                    $myImage = imagecreatefromgif($imgSrc);
                    break;
                case 2: //JPEG
                    $myImage = imagecreatefromjpeg($imgSrc);
                    break;
                case 3: //PNG
                    $myImage = imagecreatefrompng($imgSrc);
                    $png = true;
                    break;
                default:
                    return false;
                    break;
            }
            
            $ratio_orig = $width_orig / $height_orig;
            
            if (($thumbnail_width / $thumbnail_height) > $ratio_orig) {
                $new_height = $thumbnail_width / $ratio_orig;
                $new_width = $thumbnail_width;
            } else {
                $new_width = $thumbnail_height * $ratio_orig;
                $new_height = $thumbnail_height;
            }
            
            $x_mid = $new_width / 2;  //horizontal middle
            $y_mid = $new_height / 2; //vertical middle
            
            $process = imagecreatetruecolor(round($new_width), round($new_height));
            $thumb = imagecreatetruecolor($thumbnail_width, $thumbnail_height);
            
            if ($png) {
                if (substr($dest, -3) == 'png') {
                    imagesavealpha($myImage, true);
                    imagealphablending($process, false);
                    imagesavealpha($process, true);
                    $transparent = imagecolorallocatealpha($process, 255, 255, 255, 127);
                    imagefilledrectangle($process, 0, 0, $new_width, $new_height, $transparent);
                    imagecopyresampled($process, $myImage, 0, 0, 0, 0, $new_width, $new_height, $width_orig, $height_orig);
                    $thumb = $process;
                } else {
                    $white = imagecolorallocate($thumb, 255, 255, 255);
                    imagefilledrectangle($thumb, 0, 0, $thumbnail_width, $thumbnail_width, $white);
                    imagecopyresampled($thumb, $myImage, 0, 0, 0, 0, $new_width, $new_height, $width_orig, $height_orig);
                }
            } else {
                imagecopyresampled($process, $myImage, 0, 0, 0, 0, $new_width, $new_height, $width_orig, $height_orig);
                imagecopyresampled($thumb, $process, 0, 0, ($x_mid - ($thumbnail_width / 2)), ($y_mid - ($thumbnail_height / 2)), $thumbnail_width, $thumbnail_height, $thumbnail_width, $thumbnail_height);
            }
            
            if (substr($dest, -3) == 'png') {
                imagepng($thumb, $dest);
            } else {
                imagejpeg($thumb, $dest, $quality);
            }
            
            return true;
        } else {
            return false;
        }
    }
}

if (!function_exists('json')) {
    /**
     * Retorna a o resultado em JSON
     *
     * @param mixed $data
     * @param int   $status
     *
     * @return \Slim\Http\Response
     */
    function json($data, $status = 200)
    {
        return response()->withJson($data, $status, JSON_PRETTY_PRINT);
    }
}

if (!function_exists('path_for')) {
    /**
     * Cria a URL do Slim3
     *
     * @param string $name
     * @param array  $data
     * @param array  $queryParams
     * @param string $hash
     *
     * @return string
     */
    function path_for($name, array $data = [], array $queryParams = [], $hash = null)
    {
        if (!empty($hash)) {
            $hash = "#{$hash}";
        }
        
        return router()->pathFor(strtolower($name), $data, $queryParams).$hash;
    }
}

if (!function_exists('location')) {
    /**
     * Redireciona para determinada rota
     *
     * @param string $route
     * @param int    $status
     */
    function location($route, $status = 302)
    {
        header("Location: {$route}", true, $status);
        
        exit;
    }
}

if (!function_exists('redirect')) {
    /**
     * Redireciona para determinada rota padrão Slim3
     *
     * @param string $name
     * @param array  $data
     * @param array  $queryParams
     * @param string $hash
     *
     * @return \Slim\Http\Response
     */
    function redirect($name, array $data = [], array $queryParams = [], $hash = null)
    {
        $uri = path_for($name, $data, $queryParams, $hash);
        
        // Verify ajax
        // Return location ajax
        if (request()->isXhr()) {
            return json([
                'location' => $uri,
            ]);
        }
        
        // Return redirect
        return response()->withRedirect($uri);
    }
}

if (!function_exists('__')) {
    /**
     *  Converte todas as entidades HTML para os seus caracteres
     *
     * @param string $value
     *
     * @return string
     */
    function __($value)
    {
        return html_entity_decode($value, ENT_QUOTES, 'UTF-8');
    }
}

if (!function_exists('entities')) {
    /**
     * @param mixed $values
     *
     * @return array
     */
    function entities($values)
    {
        $params = [];
        
        foreach ((array)$values as $key => $value) {
            if (is_array($value)) {
                $params[$key] = entities($value);
            } else {
                if (is_string($value)) {
                    $value = htmlentities($value, ENT_QUOTES, 'UTF-8', false);
                }
                
                $params[$key] = $value;
            }
        }
        
        return $params;
    }
}

if (!function_exists('empty_filter')) {
    /**
     * @param array $data
     *
     * @return bool
     */
    function empty_filter(array $data)
    {
        if (empty($data)) {
            return true;
        }
        
        foreach ((array)$data as $key => $value) {
            if (is_array($value)) {
                return empty_filter($value);
            } else {
                if (empty($value)) {
                    return true;
                }
            }
        }
        
        return false;
    }
}

if (!function_exists('input_filter')) {
    /**
     * @param array $data
     *
     * @return array
     */
    function input_filter(array $data)
    {
        $request = [];
        
        foreach ((array)$data as $key => $value) {
            if (is_array($value)) {
                $request[$key] = input_filter($value);
            } else {
                if (is_int($value)) {
                    $filter = FILTER_SANITIZE_NUMBER_INT;
                } else if (is_float($value)) {
                    $filter = FILTER_SANITIZE_NUMBER_FLOAT;
                } else if (is_string($value)) {
                    $filter = FILTER_SANITIZE_STRING;
                } else {
                    $filter = FILTER_DEFAULT;
                }
                
                $request[$key] = addslashes(strip_tags(trim(filter_var($value, $filter))));
            }
        }
        
        return $request;
    }
}

if (!function_exists('input')) {
    /**
     * @param string $name
     *
     * @return mixed
     */
    function input($name = null)
    {
        $data = request()->getParams();
        $data = input_filter($data);
        
        if (empty($name)) {
            return $data;
        }
        
        if (array_key_exists($name, $data)) {
            return $data[$name];
        }
        
        return null;
    }
}

if (!function_exists('request')) {
    /**
     * Get instance request
     *
     * @return \Psr\Http\Message\ServerRequestInterface|\Slim\Http\Request
     */
    function request()
    {
        return app()->resolve('request');
    }
}

if (!function_exists('response')) {
    /**
     * Get instance response
     *
     * @return \Psr\Http\Message\ResponseInterface|\Slim\Http\Response
     */
    function response()
    {
        return app()->resolve('response');
    }
}

if (!function_exists('router')) {
    /**
     * Get instance router
     *
     * @return \Slim\Router
     */
    function router()
    {
        return app()->resolve('router');
    }
}

if (!function_exists('is_route')) {
    /**
     * Verifica se está em determinada rota
     *
     * @param string $route
     * @param string $active
     *
     * @return bool|string
     */
    function is_route($route, $active = null)
    {
        $current = request()
            ->getUri()
            ->getPath();
        
        if (substr($current, 0, 1) !== '/') {
            $current = "/{$current}";
        }
        
        if (path_for($route) === $current) {
            if (!empty($active)) {
                return 'active';
            }
            
            return true;
        }
        
        return false;
    }
}

if (!function_exists('has_route')) {
    /**
     * Verifica se está entre as rotas passadas
     *
     * @param mixed $routes
     *
     * @return bool
     */
    function has_route($routes)
    {
        $current = request()
            ->getUri()
            ->getPath();
        
        foreach ((array)$routes as $name) {
            if ($name !== '' && mb_strpos($current, $name) !== false) {
                return true;
            }
        }
        
        return false;
    }
}

if (!function_exists('app')) {
    /**
     * Get instance app
     *
     * @return \Core\App
     */
    function app()
    {
        return \Core\App::getInstance();
    }
}
