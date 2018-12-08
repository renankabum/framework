<?php

use Core\App;
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

if (!function_exists('onlyNumber')) {
    /**
     * Retorna apenas os números de uma string passada
     *
     * @param string $value
     *
     * @return int|string
     */
    function onlyNumber($value)
    {
        if (!empty($value)) {
            return preg_replace('/[^0-9]/', '', $value);
        }
    }
}

if (!function_exists('env')) {
    /**
     * Recupera a configuração setada no .env
     *
     * @param string $name
     * @param string $default
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
        
        return trim($value);
    }
}

if (!function_exists('asset')) {
    /**
     * Retorna o caminho completo do asset
     *
     * @param string $file
     * @param bool   $baseUrl
     * @param bool   $version
     *
     * @return bool|string
     */
    function asset($file, $baseUrl = false, $version = false)
    {
        if (!Str::startsWith($file, '/')) {
            $file = "/{$file}";
        }
        
        $basePath = rtrim(str_ireplace('index.php', '', App::getInstance()->resolve('request')->getUri()->getBasePath()), '/');
        
        if (file_exists(PUBLIC_FOLDER."{$file}")) {
            $baseUrl = ($baseUrl === true ? BASE_URL : '');
            $version = ($version ? '?v='.substr(md5_file(PUBLIC_FOLDER."{$file}"), 0, 15) : '');
            
            return "{$baseUrl}{$basePath}{$file}{$version}";
        }
        
        return false;
    }
}

if (!function_exists('asset_source')) {
    /**
     * Printa o conteúdo do asset
     *
     * @param string|array $files
     *
     * @return bool|string
     */
    function asset_source($files)
    {
        $contents = [];
        
        if (!is_array($files)) {
            $files = [$files];
        }
        
        foreach ($files as $file) {
            if (!Str::startsWith($file, '/')) {
                $file = "/{$file}";
            }
            
            if (file_exists(PUBLIC_FOLDER."{$file}")) {
                $contents[] = file_get_contents(PUBLIC_FOLDER."{$file}");
            }
        }
        
        return implode('', $contents);
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
            if (is_file($path) && !is_dir($path)) {
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
     * LoggerProvider do sistemas
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
        if (!is_object(App::getInstance()->resolve('logger'))) {
            return false;
        }
        
        $type = strtolower($type);
        $type = strtoupper(substr($type, 0, 1)).substr($type, 1);
        
        return App::getInstance()->resolve('logger', [$file])->{"add{$type}"}($message, $context);
    }
}

if (!function_exists('view')) {
    /**
     * Renderiza a view
     *
     * @param string $template
     * @param array  $array
     * @param int    $code
     *
     * @return mixed
     */
    function view($template, array $array = [], $code = null)
    {
        $response = App::getInstance()->resolve('response');
        
        if (!empty($code) && is_int($code)) {
            $response = $response->withStatus($code);
        }
        
        return App::getInstance()->resolve('view')->render($response, $template, $array);
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
        return App::getInstance()->resolve('response')->withJson($data, $status, JSON_PRETTY_PRINT);
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
        
        return App::getInstance()->resolve('router')->pathFor(strtolower($name), $data, $queryParams).$hash;
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
        
        // Return location ajax
        if (App::getInstance()->resolve('request')->isXhr()) {
            return json([
                'location' => $uri,
            ]);
        }
        
        // Return redirect
        return App::getInstance()->resolve('response')->withRedirect($uri);
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
     * Converte todos os caracteres aplicáveis em entidades html recursivamente.
     *
     * @param mixed $values
     *
     * @return array
     */
    function entities($values)
    {
        return htmlentities_recursive($values);
    }
}

if (!function_exists('htmlentities_recursive')) {
    /**
     * Converte todos os caracteres aplicáveis em entidades html recursivamente.
     *
     * @param mixed $values
     *
     * @return array
     */
    function htmlentities_recursive($values)
    {
        $params = [];
        
        foreach ((array) $values as $key => $value) {
            if (is_array($value)) {
                $params[$key] = htmlentities_recursive($value);
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
     * Verifica recursivamente um array de dados se existe algum vázio.
     *
     * @param array $data
     *
     * @return bool
     */
    function empty_filter(array $data)
    {
        return empty_recursive($data);
    }
}

if (!function_exists('empty_recursive')) {
    /**
     * Verifica recursivamente um array de dados se existe algum vázio.
     *
     * @param array $data
     *
     * @return bool
     */
    function empty_recursive(array $data)
    {
        if (empty($data)) {
            return true;
        }
        
        foreach ((array) $data as $key => $value) {
            if (is_array($value)) {
                return empty_recursive($value);
            } else {
                if (empty($value) && $value != '0') {
                    return true;
                }
            }
        }
        
        return false;
    }
}

if (!function_exists('input_filter')) {
    /**
     * Filtra e protege todos dados passados
     *
     * @param string|array $params
     *
     * @return array
     */
    function input_filter($params)
    {
        return filter_params($params);
    }
}

if (!function_exists('filter_params')) {
    /**
     * Filtra e protege todos dados passados
     *
     * @param string|array $params
     *
     * @return array
     */
    function filter_params($params)
    {
        $result = [];
        
        foreach ((array) $params as $key => $param) {
            if (is_array($param)) {
                $result[$key] = filter_params($param);
            } else {
                if (is_int($param)) {
                    $filter = FILTER_SANITIZE_NUMBER_INT;
                } else if (is_float($param)) {
                    $filter = FILTER_SANITIZE_NUMBER_FLOAT;
                } else if (is_string($param)) {
                    $filter = FILTER_SANITIZE_STRING;
                } else {
                    $filter = FILTER_DEFAULT;
                }
                
                $result[$key] = addslashes(strip_tags(trim(filter_var($param, $filter))));
            }
        }
        
        return $result;
    }
}

if (!function_exists('input')) {
    /**
     * Recupera todos GET,POST...
     *
     * @param string $name
     *
     * @return mixed
     */
    function input($name = null)
    {
        return params($name);
    }
}

if (!function_exists('params')) {
    /**
     * Recupera todos GET,POST...
     *
     * @param string $name
     *
     * @return mixed
     */
    function params($name = null)
    {
        $params = App::getInstance()->resolve('request')->getParams();
        $params = filter_params($params);
        
        if (empty($name)) {
            return $params;
        }
        
        if (array_key_exists($name, $params)) {
            return $params[$name];
        }
        
        return null;
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
        $current = App::getInstance()->resolve('request')->getUri()->getPath();
        
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
        $current = App::getInstance()->resolve('request')->getUri()->getPath();
        
        return Str::contains($current, $routes);
    }
}

if (!function_exists('is_php_cli')) {
    /**
     * Verifica se está usando o php no terminal
     *
     * @return bool
     */
    function is_php_cli()
    {
        return in_array(PHP_SAPI, ['cli', 'phpdbg']);
    }
}

if (!function_exists('get_code_video_youtube')) {
    /**
     * Recupera o código do vídeo do Youtube
     *
     * @param string $url
     *
     * @return string|bool
     */
    function get_code_video_youtube($url)
    {
        if (strpos($url, 'youtu.be/')) {
            preg_match('/(https:|http:|)(\/\/www\.|\/\/|)(.*?)\/(.{11})/i', $url, $matches);
            
            return $matches[4];
        } else if (strstr($url, "/v/")) {
            $aux = explode("v/", $url);
            $aux2 = explode("?", $aux[1]);
            $cod_youtube = $aux2[0];
            
            return $cod_youtube;
        } else if (strstr($url, "v=")) {
            $aux = explode("v=", $url);
            $aux2 = explode("&", $aux[1]);
            $cod_youtube = $aux2[0];
            
            return $cod_youtube;
        } else if (strstr($url, "/embed/")) {
            $aux = explode("/embed/", $url);
            $cod_youtube = $aux[1];
            
            return $cod_youtube;
        } else if (strstr($url, "be/")) {
            $aux = explode("be/", $url);
            $cod_youtube = $aux[1];
            
            return $cod_youtube;
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

if (!function_exists('organize_upload_multiple_files')) {
    /**
     * Reorganiza o array dos files
     *
     * @param array $files
     *
     * @return array
     */
    function organize_upload_multiple_files($files)
    {
        $newFiles = [];
        $multiple = is_array($files);
        $fileCount = $multiple ? count($files['name']) : 1;
        $fileKeys = array_keys($files);
        
        for ($i = 0; $i < $fileCount; $i++) {
            foreach ($fileKeys as $fileKey) {
                $newFiles[$i][$fileKey] = $multiple ? $files[$fileKey][$i] : $files[$fileKey];
            }
        }
        
        return $newFiles;
    }
}

if (!function_exists('get_upload_max_filesize')) {
    /**
     * Converte o `filesize` máximo configurado
     * para upload de arquivos/images
     *
     * @return float|int
     */
    function get_upload_max_filesize()
    {
        $mb = ini_get('upload_max_filesize');
        $maxFileSize = 0;
        
        if (preg_match('/([0-9])+([a-zA-Z])/', $mb, $matche)) {
            switch ($matche[2]) {
                case 'K':
                case 'KB':
                    $maxFileSize = ($matche[1] * pow(1024, 1));
                    break;
                
                case 'M':
                case 'MB':
                    $maxFileSize = ($matche[1] * pow(1024, 2));
                    break;
                
                case 'G':
                case 'GB':
                    $maxFileSize = ($matche[1] * pow(1024, 3));
                    break;
                
                case 'T':
                case 'TB':
                    $maxFileSize = ($matche[1] * pow(1024, 4));
                    break;
                
                case 'P':
                case 'PB':
                    $maxFileSize = ($matche[1] * pow(1024, 5));
                    break;
            }
        }
        
        return $maxFileSize;
    }
}
