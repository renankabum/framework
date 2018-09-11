<?php

/**
 * VCWeb <https://www.vagnercardosoweb.com.br/>
 *
 * @package   VCWeb
 * @author    Vagner Cardoso <vagnercardosoweb@gmail.com>
 * @license   MIT
 *
 * @copyright 2017-2018 Vagner Cardoso
 */

namespace Core\Providers\Mailer {
    
    use Core\App;
    use Core\Helpers\Obj;
    
    /**
     * Class Mailer
     *
     * @package Core\Providers\Mailer
     * @author  Vagner Cardoso <vagnercardosoweb@gmail.com>
     */
    class Mailer
    {
        /**
         * @var \PHPMailer
         */
        protected $mail;
        
        /**
         * Mailer constructor.
         */
        public function __construct()
        {
            // Instancia do PHPMAILER
            $this->mail = new \PHPMailer();
            
            // Configurações
            $config = Obj::set(config('mail'));
            
            // Debug
            $this->mail->SMTPDebug = $config->debug;
            
            // Charset
            $this->mail->CharSet = $config->charset;
            
            // Linguagem
            $this->mail->setLanguage('pt_br', '');
            
            // Ativar SMTP
            $this->mail->isSMTP();
            
            // Aceitar HTML
            $this->mail->isHTML(true);
            
            // Configuração do servidor
            $this->mail->Host = $config->host;
            $this->mail->Port = $config->port;
            $this->mail->Username = $config->username;
            $this->mail->Password = $config->password;
            
            // Autenticação SMTP
            $this->mail->SMTPAuth = $config->auth;
            
            // Ativar segurança
            $this->mail->SMTPSecure = $config->secure;
            
            // Remetente
            $this->mail->From = $config->from->mail;
            $this->mail->FromName = $config->from->name;
        }
        
        /**
         * Monta e envia o e-mail
         *
         * @param string   $template
         * @param array    $params
         * @param callable $callback
         *
         * @return $this
         * @throws \Exception
         */
        public function send($template, array $params, callable $callback)
        {
            $message = new Message($this->mail);
            
            $message->body(App::getInstance()->resolve('view-mail')->fetch($template, ['data' => $params]));
            
            call_user_func_array($callback, [$message, $params]);
            
            try {
                if (!$this->mail->send()) {
                    throw new \phpmailerException($this->mail->ErrorInfo, E_USER_ERROR);
                }
                
                // Limpa as propriedade do email
                $this->mail->clearAddresses();
                $this->mail->clearAllRecipients();
                $this->mail->clearAttachments();
                $this->mail->clearBCCs();
                $this->mail->clearCCs();
                $this->mail->clearCustomHeaders();
                $this->mail->clearReplyTos();
            } catch (\phpmailerException $e) {
                throw new \Exception("[MAILER'] {$e->getMessage()}", (is_int($e->getCode()) ? $e->getCode() : 500));
            }
            
            return $this;
        }
    }
}
