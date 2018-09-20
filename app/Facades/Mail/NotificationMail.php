<?php

namespace App\Facades\Mail;

use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\Mail;
use Illuminate\Database\Eloquent\Collection;
use App\Mail\NotificationGeneralMail;
use App\User;
use App\Models\LogMail;
use App\Models\Module;
use Route;
use Exception;

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
    private $subject = 'Notificación';

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
    private $view = 'notification';

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

    /**
     * Stores the information of a table
     *
     * @var \MaddHatter\MarkdownTable\Builder
     */
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
     * @return $this
     */

    public function recipients($recipients)
    {
        if (!$recipients instanceof Collection && !$recipients instanceof User)
            throw new \Exception('Invalid recipient format');
        
        if ($recipients instanceof Collection)
        {
            if ($recipients->isEmpty())
                throw new \Exception('Empty collection');
            
            $recipients = $recipients->filter(function ($value, $key) {
                if ($value instanceof User && 
                preg_match('/^[_a-z0-9-]+(.[_a-z0-9-]+)*@[a-z0-9-]+(.[a-z0-9-]+)*(.[a-z]{2,4})$/', $value->email) )
                    return true;
                else 
                    return false;
            });

            if ($recipients->isEmpty())
                throw new \Exception('The collection was empty after filtering the invalid emails');
        }

        if ($recipients instanceof User && 
                !preg_match('/^[_a-z0-9-]+(.[_a-z0-9-]+)*@[a-z0-9-]+(.[a-z0-9-]+)*(.[a-z]{2,4})$/', $recipients->email) )
            throw new \Exception('Incorrect email format');
        
        $this->recipients = $recipients;

        return $this;
    }

    /**
     * Edit the mail subject
     *
     * @param string $subject
     * @return $this
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
     * @return $this
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
     * @return $this
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
     * @return $this
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
     * @return $this
     */
    public function list($data)
    {
        if (!is_array($data))
            throw new \Exception('The format of the list is incorrect');

        $this->list = $data;

        return $this;
    }

    /**
     * Undocumented function
     *
     * @param array $columns
     * @param array $data
     * @return $this
     */
    public function table($columns, $data)
    {
        // create instance of the table builder
        $tableBuilder = new \MaddHatter\MarkdownTable\Builder();

        if (is_array($columns) && COUNT($columns) > 0)
        {
            if (is_array($data) && COUNT($data) > 0)
            {
                // add some data
                $tableBuilder
                    ->headers($columns) //headers
                    //->align(['L','C','R']) // set column alignment
                    ->rows($data);
            }
            else
                throw new \Exception('The format of the information is incorrect');
        }
        else
            throw new \Exception('The format of the columns is incorrect');
        
        $this->table = $tableBuilder;

        return $this;
    }

    /**
     * Edit the module that performs the action
     *
     * @param App\Models\Module $module
     * @return $this
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
     * @return booleam
     * @return $this
     */
    public function send()
    {
        if (empty($this->recipients))
            throw new \Exception('No valid recipient was entered');

        if (empty($this->module))
            throw new \Exception('The id of the module that performed the action was not entered');

        try { 
            $message = (new NotificationGeneralMail($this->prepareData()))
                ->onQueue('emails');

            Mail::to($this->recipients)->queue($message);

            $this->createLog();
        }
        catch (\Exception $e) {
            throw new \Exception('An error occurred while sending the mail');
        }

        return true;
    }

    /**
     * Returns the information needed to build the mail
     *
     * @return stdClass
     */
    private function prepareData()
    {
        $data = new \stdClass();
        $data->recipients = $this->recipients;
        $data->view = $this->view;
        $data->subject = $this->subject;

        if (!empty($this->message))
            $data->message = $this->message;

        if (!empty($this->buttons))
            $data->buttons = $this->buttons;
        
        if (!empty($this->list))
            $data->list = $this->list;

        if (!empty($this->table))
            $data->table = $this->table;

        return $data;
    }

    /**
     * Create a record of the sent email
     *
     * @return void
     */
    private function createLog()
    {
        $log = new LogMail();

        $event = explode("\\", Route::currentRouteAction());
        $event = $event[COUNT($event) - 1];

        if ($this->recipients instanceof User)
            $log->recipients = $this->recipients->email;    
        else if ($this->recipients instanceof Collection)
        {
            $array = [];

            foreach($this->recipients as $item)
            {
                array_push($array, $item->email);
            }

            $log->recipients = implode(',', $array);
        }
        
        $log->module_id = $this->module->id;
        $log->event = $event;
        $log->subject = $this->subject;
        $log->message = $this->message;
        $log->created_at = date("Y-m-d H:i:s");
        $log->save();
    }
}
