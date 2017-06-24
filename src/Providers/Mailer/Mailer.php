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

namespace Navegarte\Providers\Mailer;

use Slim\Container;

/**
 * Class Mailer
 *
 * @package Navegarte\Providers\Mailer
 * @author  Vagner Cardoso <vagnercardosoweb@gmail.com>
 */
final class Mailer
{
  /**
   * Retorna o erro ocorrido
   */
  protected $error;
    
    /**
   * @var \Slim\Container
   */
  protected $container;
    
    /**
   * @var \PHPMailer
   */
  protected $mail;
  
  /**
   * Mailer constructor.
   *
   * @param \Slim\Container $container
   */
  public function __construct(Container $container)
  {
    $this->container = $container;
      $this->mail = new \PHPMailer();
    
    /**
     * Configuração dos dados de envio de emails.
     */
    $this->mail->Host = config('mail.host');
    $this->mail->Port = config('mail.port');
    $this->mail->Username = config('mail.username');
    $this->mail->Password = config('mail.password');
    $this->mail->SMTPAuth = config('mail.auth');
    $this->mail->SMTPSecure = config('mail.secure');
  }
  
  /**
   * @param $view
   * @param $data
   * @param $callback
   *
   * @return $this
   */
  public function send($view, $data, $callback)
  {
    $this->config();
    
    $message = new MailerMessage($this->mail);
    
      $message->body($this->container['view.mail']->render("mail/{$view}.twig", ['data' => $data]));
    
    call_user_func($callback, $message);
    
    if (!$this->mail->send()) {
      $this->error = $this->mail->ErrorInfo;
      
      return $this;
    }
    
    $this->error = null;
    
    $this->mail->clearAddresses();
    $this->mail->clearReplyTos();
    $this->mail->clearAllRecipients();
    $this->mail->clearAttachments();
    
    return $this;
  }
  
  /**
   * @return mixed
   */
  public function failed()
  {
    return $this->error;
  }
  
  // ========================================
  // Metodos da classe privados
  // ========================================
  
  /**
   * Configura SMTP e Rementente, Retorno
   */
  private function config()
  {
    /**
     * SMTP Autenticação
     */
    $this->mail->CharSet = config('mail.charset', 'utf-8');
    $this->mail->setLanguage(config('mail.language.name'), config('mail.language.path'));
    $this->mail->isSMTP();
    $this->mail->isHTML(true);
    
    /**
     * Remetente e retorno do email
     */
    $this->mail->From = config('mail.from.mail');
    $this->mail->FromName = config('mail.from.name');
    $this->mail->addReplyTo(config('mail.reply.mail'), config('mail.reply.name'));
  }
}
