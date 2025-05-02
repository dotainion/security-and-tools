<?php
namespace tools\infrastructure;

use InvalidArgumentException;
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\SMTP;
use PHPMailer\PHPMailer\Exception;
use tools\module\mail\objects\Attatchment;
use tools\module\mail\objects\Mail;
use Throwable;

class SendMail{
    protected PHPMailer $mail;
    protected ImageHelper $img;

    public function __construct(){
        $this->img = new ImageHelper();

        if(!Env::emailAddress() || !Env::emailPassword()){
            throw new InvalidArgumentException('This feature is not yet configured to send email notifications.');
        }

        $this->mail = new PHPMailer(true);
        //Server settings
        //$this->mail->SMTPDebug = SMTP::DEBUG_SERVER;                      //Enable verbose debug output
        
        $this->mail->isSMTP();                                            //Send using SMTP
        $this->mail->Host       = 'smtp.gmail.com';                     //Set the SMTP server to send through
        $this->mail->SMTPAuth   = true;                                   //Enable SMTP authentication
        $this->mail->Username   = Env::emailAddress();  //'areset0000@gmail.com';                     //SMTP username
        $this->mail->Password   = Env::emailPassword();  //'nmczpulryktsbisr';                               //SMTP password
        $this->mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;            //Enable implicit TLS encryption
        $this->mail->Port       = 587;                                    //TCP port to connect to; use 587 if you have set `SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS`

        //Recipients
        $this->mail->setFrom(Env::emailAddress(), 'Mailer');
        /*$this->mail->addAddress('ellen@example.com');               //Name is optional
        $this->mail->addReplyTo('info@example.com', 'Information');
        $this->mail->addCC('cc@example.com');
        $this->mail->addBCC('bcc@example.com');*/

        //Attachments
        //$this->mail->addAttachment('/var/tmp/file.tar.gz');         //Add attachments
        //$this->mail->addAttachment('/tmp/image.jpg', 'new.jpg');    //Optional name

        //Content
        $this->mail->isHTML(true);        
        //$this->mail->AltBody = 'This is the body in plain text for non-HTML mail clients';
    }

    public function setMail(Mail $mail):self{                          
        $this->mail->Subject = $mail->subject();
        $this->mail->Body    = $mail->body();
        foreach($mail->recipients()->list() as $recip){
            $this->mail->addAddress($recip->recipient());
        }
        $this->img->setAssertionMessage('Invalid embedded image. Unable to send email.');
        foreach($mail->attatchments()->list() as $attatch){
            $this->img->initAndSaveBase64TempImage($attatch);
            $this->mail->addEmbeddedImage($this->img->file(), $attatch->contentKey());
        }
        return $this;
    }

    public function send():void{
        try {
            $this->mail->send();
        }catch(Throwable $e){
            throw new InvalidArgumentException($e->getMessage());
        }
    }
}