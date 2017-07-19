<?php

use Core\Helpers\Debug;
use Core\Helpers\Str;

/**
 * Root folder
 *
 * Adicionado para config da pasta raíz
 */
define('ROOT_FOLTER', config('app.root.folder', 'public'));

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
        /* array_map(
           function ($var) {
             (new Debug())->dump($var);
           },
           func_get_args()
         );*/
        
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

if (!function_exists('mix')) {
    /**
     * Usage laravel-mix
     *
     * @param string $path
     *
     * @return mixed
     * @throws \Exception
     */
    function mix($path)
    {
        static $manifest;
    
        if (!Str::startsWith($path, '/')) {
            $path = "/{$path}";
        }
        
        if (!$manifest) {
            if (!file_exists($manifestPath = ROOT . "/" . ROOT_FOLTER . "/mix-manifest.json")) {
                throw new Exception('The mix manifest does not exists.');
            }
    
            $manifest = json_decode(file_get_contents($manifestPath), true);
        }
    
        if (!array_key_exists($path, $manifest)) {
            throw new \Exception("Unable to locate Mix file: {$path}. Please check your webpack.mix.js output paths and try again.");
        }
    
        return $manifest[$path];
    }
}

if (!function_exists('asset')) {
    /**
     * @param string $path
     *
     * @return bool|string
     */
    function asset($path)
    {
        if (!Str::startsWith($path, '/')) {
            $path = "/{$path}";
        }
        
        $baseUrl = rtrim(str_ireplace('index.php', '', request()->getUri()->getBasePath()), '/');
    
        if (file_exists(ROOT . "/" . ROOT_FOLTER . "{$path}")) {
            $version = substr(md5_file(ROOT . "/" . ROOT_FOLTER . "{$path}"), 0, 10);
            
            return "{$baseUrl}{$path}?v={$version}";
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
    
            if (file_exists(ROOT . "/" . ROOT_FOLTER . "{$path}")) {
                $sources[] = file_get_contents(ROOT . "/" . ROOT_FOLTER . "{$path}");
            }
        }
        
        return implode('', $sources);
    }
}

if (!function_exists('config')) {
    /**
     * Get config
     *
     * @param string|null     $name
     * @param string|int|null $default
     *
     * @return mixed
     */
    function config($name = null, $default = null)
    {
        $config = [];
        foreach (glob(ROOT . '/config/*') as $filePath) {
            $fileName = basename($filePath, '.php');
            $config[$fileName] = include "{$filePath}";
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
     * @param string|null $file
     *
     * @return bool|\Monolog\Logger
     * @throws \Exception
     */
    function logger($file = null)
    {
        if (is_object(app()->resolve('logger'))) {
            return app()->resolve('logger', [$file]);
        }
        
        throw new \Exception('Logger configurado incorretamente!');
    }
}

if (!function_exists('view')) {
    /**
     * Rendering view content
     *
     * @param string   $view
     * @param array    $array
     * @param int|null $code
     *
     * @return mixed
     * @throws \Exception
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
            } elseif ($extension === 'blade') {
                $extension = '.blade.php';
            }
    
            // replace '.' em '/'
            $view = str_replace('.', '/', $view);
    
            return app()->resolve('view')->render($response, $view . $extension, $array);
        }
    
        throw new Exception('View configurado incorretamente!');
    }
}

if (!function_exists('imagem')) {
    /**
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
            
            if ($thumbnail_width / $thumbnail_height > $ratio_orig) {
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

if (!function_exists('app')) {
    /**
     * Get instance app
     *
     * @return \Core\App
     */
    function app()
    {
        return Core\App::getInstance();
    }
}
