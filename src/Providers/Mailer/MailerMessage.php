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
    
    /**
     * Class MailerMessage
     *
     * @package Core\Providers\Mailer
     * @author  Vagner Cardoso <vagnercardosoweb@gmail.com>
     */
    final class MailerMessage
    {
        /**
         * @var \PHPMailer
         */
        protected $mail;
        
        /**
         * MailerMessage constructor.
         *
         * @param \PHPMailer $mail
         */
        public function __construct(\PHPMailer $mail)
        {
            $this->mail = $mail;
        }
        
        /**
         * Adicionar quem está enviando o email
         *
         * @param string      $address
         * @param null|string $name
         *
         * @return $this
         */
        public function from($address, $name = null)
        {
            $this->mail->From = $address;
            $this->mail->FromName = $name;
            
            return $this;
        }
        
        /**
         * Adicionar a quem vai a resposta se for respondido o email
         *
         * @param string      $address
         * @param null|string $name
         *
         * @return $this
         */
        public function reply($address, $name = null)
        {
            $this->mail->addReplyTo($address, $name);
            
            return $this;
        }
        
        /**
         * Adiciona para quem vai enviar o email.
         *
         * @param string      $address
         * @param null|string $name
         *
         * @return $this
         */
        public function to($address, $name = null)
        {
            $this->mail->addAddress($address, $name);
            
            return $this;
        }
        
        /**
         * Adiciona o titulo do email
         *
         * @param string $subject
         *
         * @return $this
         */
        public function subject($subject)
        {
            $this->mail->Subject = $subject;
            
            return $this;
        }
        
        /**
         * Se existir arquivo, adiciona o arquivo.
         * os path dos arquivos devem ser passado como array ou o path direto
         *
         * @param string $path
         * @param string $name
         *
         * @return $this
         */
        public function addFile($path, $name = '')
        {
            $this->mail->addAttachment($path, $name);
            
            return $this;
        }
        
        /**
         * Corpo da mensagem
         *
         * @param $body
         */
        public function body($body)
        {
            $this->mail->Body = $body;
        }
    }
}
