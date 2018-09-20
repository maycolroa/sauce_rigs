<?php

namespace App\Facades\Mail;

use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\Mail;
use App\Models\MailInformation;
use App\Mail\NotificationGeneralMail;
use App\Models\LogMail;
use App\Models\Module;
use Route;
use Exception;
use Illuminate\Database\Eloquent\Collection;
use App\User;

class NotificationMail
{
    /**
     * Recipients to whom the mail will be sent
     *
     * @var Illuminate\Database\Eloquent\Collection
     * @var App\User
     */
    private $recipients;

    /**
     * Subject of the mail
     *
     * @var string
     */
    private $subject;

    /**
     * Mail message
     *
     * @var string
     */
    private $message;

    /**
     * Name of the view that will be used in the mail content
     *
     * @var string
     */
    private $view;

    /**
     * Arrangement of buttons
     *
     * @var array
     */
    private $buttons = [];

    /**
     * Stores a list
     *
     * @var array
     */
    private $list;

    private $table;

    /**
     * Module that executes the event
     *
     * @var App\Models\Module
     */
    private $module;

    public function __construct()
    {
 
    }

    /**
     * Edit the recipients of the email
     *
     * @param Illuminate\Database\Eloquent\Collection $recipients
     * @param App\User $recipients
     */

    public function recipients($recipients)
    {
        if (!$recipients instanceof Collection && !$recipients instanceof User)
            throw new \Exception('Invalid recipient format');
        
        $this->recipients = $recipients;

        return $this;
    }

    /**
     * Edit the mail subject
     *
     * @param string $subject
     */
    public function subject($subject)
    {
        if (!is_string($subject) || $subject == '')
            throw new \Exception('The format of the subject is incorrect'); 

        $this->subject = $subject;

        return $this;
    }

    /**
     * Edit the mail message
     *
     * @param string $message
     */
    public function message($message)
    {
        $this->message = $message;

        return $this;
    }
    
    /**
     * Edit the view that will be used in the mail content
     *
     * @param string $view
     */
    public function view($view)
    {
        if (!is_string($view) || $view == '')
            throw new \Exception('The format of the view is incorrect'); 

        $this->view = $view;

        return $this;
    }
    
    /**
     * Edit the buttons that will be available in the mail view
     *
     * @param array $buttons
     */
    public function buttons($buttons)
    {   
        if (!is_array($buttons))
            throw new \Exception('The array of buttons does not comply with the format: array (array ("text" => "Download", "url" => "www.example.com"))');

        foreach($buttons as $button)
        {
            if (!isset($button["text"]) || !isset($button["url"]))
                throw new \Exception('The array of buttons does not comply with the format: array (array ("text" => "Download", "url" => "www.example.com"))');
        }

        $this->buttons = $buttons;

        return $this;
    }
    
    /**
     * Edit the list that will be displayed in the mail
     *
     * @param array $data
     */
    public function list($data)
    {
        if (!is_array($data))
            throw new \Exception('The format of the list is incorrect');

        $this->list = $data;

        return $this;
    }

    /**
     * Edit the module that performs the action
     *
     * @param App\Models\Module $module
     */
    public function module($module)
    {
        if (!$module instanceof Module)
            throw new \Exception('Invalid module format');
        
        $this->module = $module;

        return $this;
    }

    /**
     * Send the mail
     *
     */
    public function send()
    {
        if ($this->recipients instanceof Collection && $this->recipients->isEmpty())
            throw new \Exception(trans('mail.recipient_empty'));
        else if (!$this->recipients instanceof User)
            throw new \Exception(trans('mail.recipient_empty'));
    }

    public static function sendMail(MailInformation $mail)
    {
        if (empty($mail->getRecipients()))
            throw new \Exception(trans('mail.recipient_empty'));

        if (empty($mail->getMessage()))
            throw new \Exception(trans('mail.message_empty'));

        if (empty($mail->getModule()))
            throw new \Exception(trans('mail.module_empty'));

        if (!Module::find($mail->getModule()))
            throw new \Exception(trans('mail.module_not_exist'));

        try { 
            $message = (new NotificationGeneralMail($mail))
                ->onQueue('emails');

            Mail::to($mail->getRecipients())->queue($message);

            $event = explode("\\", Route::currentRouteAction());
            $event = $event[COUNT($event) - 1];

            $log = new LogMail();
            $log->module_id = $mail->getModule();
            $log->event = $event;
            $log->recipients = implode(",", $mail->getRecipients());
            $log->subject = $mail->getSubject();
            $log->message = $mail->getMessage();
            $log->created_at = date("Y-m-d H:i:s");
            $log->save();
        }
        catch (\Exception $e) {
            dd($e);
            throw new \Exception(trans('mail.send_error'));
        }
    }
}
