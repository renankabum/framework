<?php

/**
 * NAVEGARTE Networks
 *
 * @package   FrontEnd
 * @author    Vagner Cardoso <vagnercardosoweb@gmail.com>
 * @license   MIT
 *
 * @copyright 2017-2017 Vagner Cardoso - NAVEGARTE
 */

namespace Navegarte\Handlers;

use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;
use Slim\Handlers\AbstractHandler;

/**
 * Class ErrorHandler
 *
 * @package Navegarte\Handlers
 * @author  Vagner Cardoso <vagnercardosoweb@gmail.com>
 */
final class ErrorHandler extends AbstractHandler
{
  /** @var bool */
  protected $displayErrorDetails;
  
  /**
   * ErrorHandler constructor.
   *
   * @param bool $displayErrorDetails
   */
  public function __construct($displayErrorDetails)
  {
    $this->displayErrorDetails = $displayErrorDetails;
  }
  
  /**
   * Invoke errors handlers
   *
   * @param \Psr\Http\Message\ServerRequestInterface $request
   * @param \Psr\Http\Message\ResponseInterface      $response
   * @param \Throwable                               $error
   *
   * @return \Psr\Http\Message\ResponseInterface
   */
  public function __invoke(Request $request, Response $response, \Throwable $error)
  {
    // Verify content type
    $contentType = $this->determineContentType($request);
    switch ($contentType) {
      case 'application/json':
        $output = $this->renderJson($error);
        break;
      
      case 'application/xml':
      case 'text/xml':
        $output = $this->renderXML($error);
        break;
      
      case 'text/html':
        $output = $this->renderXML($error);
        break;
      
      default:
        throw new \UnexpectedValueException("Cannot render unknown content type {$contentType}");
    }
    
    // Log error
    if (logger() !== false) {
      //log init
    }
    
    // Output error
    $body = $response->getBody();
    $body->write($output);
    
    return $response->withStatus(500)->withHeader('Content-Type', $contentType)->withBody($body);
  }
  
  /**
   * Render error as JSON
   *
   * @param \Throwable $error
   *
   * @return string
   */
  private function renderJson(\Throwable $error)
  {
    $json = ['message' => config('client.name', 'Web') . ' Application Error'];
    
    if ($this->displayErrorDetails) {
      $json['error'] = [];
      
      do {
        $json['error'][] = [
          'type' => get_class($error),
          'code' => $error->getCode(),
          'message' => $error->getMessage(),
          'file' => $error->getFile(),
          'line' => $error->getLine(),
          'trace' => explode("\n", $error->getTraceAsString()),
        ];
      } while ($error = $error->getPrevious());
    }
    
    return json_encode($json, JSON_PRETTY_PRINT);
  }
  
  /**
   * Render error as XML
   *
   * @param \Throwable $error
   *
   * @return string
   */
  private function renderXML(\Throwable $error)
  {
    $xml = "<error>\n <message>" . config('client.name', 'Web') . " Application Error</message>\n";
    
    if ($this->displayErrorDetails) {
      do {
        /*$json['error'][] = [
          'type' => get_class($error),
          'code' => $error->getCode(),
          'message' => $error->getMessage(),
          'file' => $error->getFile(),
          'line' => $error->getLine(),
          'trace' => explode("\n", $error->getTraceAsString()),
        ];*/
      } while ($error = $error->getPrevious());
    }
    
    $xml .= '</error>';
    
    return $xml;
  }
  
  /**
   * Render error as HTML
   *
   * @param \Throwable $error
   *
   * @return string
   */
  private function renderHtml(\Throwable $error)
  {
    $output = [];
  
    $output['title'] = config('client.name', 'Web') . ' Application Error';
    
    return view('error/handler', $output, 500);
  }
}
