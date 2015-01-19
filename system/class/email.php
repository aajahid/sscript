<?php
/**
 * User: Abdullah Al Jahid
 * Date: 1/4/13
 * Time: 5:40 PM
 *
 * A simple Email class that simplify sending email using php native MAIL() function.
 * @author Abdullah Al Jahid
 */
class Email
{
    private $to;
    public  $toEmail;                               // Email address Where Email will be sent.
    public  $toName       = false;                  // Name of the reciver

    private $from;
    public  $fromEmail    = 'noreply@foodlve.com'; // Email address where email will sent from.
    public  $fromName     = 'Foodlve';            // Sender Name


    private $replyTo;
    private $template;

    public  $subject;                               // Email Subject
    public  $message;                               // Email Content

    public  $contentType  = 'html';                 // Type of Email. text/html


    public  $useTemplate  = true;
    var     $uself        = 0;
    var     $customTemplateVars;


    /**
     * Set the TO email and name.
     *
     * @param $email
     * @param bool $name
     */
    public function setEmailTo($email,$name=false)
    {
        $this->toEmail = $email;
        if($name)
        {
            $this->toName = $name;
        }
    }



    /**
     * Set the FROM email and name.
     *
     * @param $email
     * @param bool $name
     */
    public function setEmailFrom($email,$name=false)
    {
        $this->fromEmail = $email;
        if($name)
        {
            $this->fromName = $name;
        }
    }


    public function setEmailSubject($subject)
    {
        $this->subject = $subject;
    }


    /**
     * Set the Email message;
     *
     * @param $message
     */
    public function setMessage($message)
    {
        $this->message = $message;
    }



    /**
     * return docType based on $this->type value. It returns either plain text or html doc type
     *
     * @return string
     */
    private function getDocType()
    {
        if ( $this->contentType == 'text' )
        {
            return 'Content-type: text/plain; charset=UTF-8';
        }
        elseif ( $this->contentType == 'html' )
        {
            return 'Content-type: text/html; charset=UTF-8';
        }
    }



    /**
     * Generate the email headers
     *
     * @return string
     */
    private function getHeaders()
    {

        $headersep =  (!isset( $this->uself ) || ($this->uself == 0)) ? "\r\n" : "\n" ;
        $headers   =  'MIME-Version: 1.0' . $headersep;
        $headers  .=  $this->getDocType() . $headersep;
        $headers  .=  'From: '.$this->from . $headersep;

        if ( $this->replyTo ) $headers .= 'Reply-To: '.$this->replyTo . $headersep;

        return $headers;
    }





    /**
     * @return string
     */
    private function getMessage()
    {
        return $this->useTemplate ? $this->getTemplate() : $this->message;
    }




    private function getTemplate()
    {
        $html =
<<<Message

        <body bgcolor="ffffff">
            <div align="center">
                <br>

                <table style="font-family: Georgia, 'Times New Roman', Times, serif; font-size: 12px; width: 580px; border: 10px solid #fafafa;" border="0" cellspacing="0" cellpadding="10" align="center" bgcolor="#FFFFFF">
                  <tbody>
                    <tr>
                      <td style="font-family: Georgia, 'Times New Roman', Times, serif; font-size: 28px; border-top: 1px dashed #CCCCCC; color: #333333;">
                        $this->subject
                      </td>
                    </tr>
                    <tr><td><p></p></td></tr>
                    <tr>
                      <td style="color: #666666;" colspan="2">
                      $this->message
                      </td>
                    </tr>
                  </tbody>
                </table>
            </div>
        </body>

Message;

        return $html;

    }





    /**
     * Finally Send the email using php mail() function
     *
     * @return bool
     */
    public function sendMail()
    {
        $this->to    = !$this->toName ? $this->toEmail : $this->toName.' <'.$this->toEmail.'>';
        $this->from  = !$this->fromName ? $this->fromEmail : $this->fromName.'<'.$this->fromEmail.'>';

        return mail( $this->to, $this->subject, $this->getMessage(), $this->getHeaders() );
    }


}
