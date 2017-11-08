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

namespace Core\Providers\Mailer {

    use Psr\Container\ContainerInterface as Container;

    /**
     * Class Mailer
     *
     * @package Core\Providers\Mailer
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
         * @param Container $container
         */
        public function __construct(Container $container)
        {
            $this->container = $container;
            $this->mail = new \PHPMailer();
            $mail = object_set(config('mail'));

            /**
             * SMTP Autenticação
             */
            $this->mail->CharSet = $mail->charset;
            $this->mail->setLanguage($mail->language->name, $mail->language->path);
            $this->mail->isSMTP();
            $this->mail->isHTML(true);

            /**
             * Configuração dos dados de envio de emails.
             */
            $this->mail->Host = $mail->host;
            $this->mail->Port = $mail->port;
            $this->mail->Username = $mail->username;
            $this->mail->Password = $mail->password;
            $this->mail->SMTPAuth = $mail->auth;
            $this->mail->SMTPSecure = $mail->secure;

            /**
             * Remetente e retorno do email
             */
            $this->mail->From = $mail->from->mail;
            $this->mail->FromName = $mail->from->name;
            $this->mail->addReplyTo($mail->reply->mail, $mail->reply->name);
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
            $message = new MailerMessage($this->mail);

            $message->body($this->container['mailView']->render("{$view}.twig", ['data' => $data]));

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
    }
}
