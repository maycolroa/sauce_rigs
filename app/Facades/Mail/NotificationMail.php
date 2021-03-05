<?php

namespace App\Facades\Mail;

use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\Mail;
use Illuminate\Database\Eloquent\Collection AS CollectionEloquent;
use Illuminate\Support\Collection;
use App\Facades\Mail\NotificationGeneralMail;
use App\Models\Administrative\Users\User;
use App\Models\System\LogMails\LogMail;
use App\Models\General\Module;
use App\Models\System\Licenses\License;
use App\Models\Administrative\Employees\Employee;
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
    private $recipients = [];

    /**
     * Recipients Copy to whom the mail will be sent
     *
     * @var Illuminate\Database\Eloquent\Collection
     * @var App\User
     */
    private $copyHidden = [];

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
     * Arrangement of buttons. Ej: [ ['text'=>'Download', 'url'=>'www.example.com', 'color'=>'green'] ]
     * The color parameter is optional
     * 
     * @var array
     */
    private $buttons = [];

    /**
     * Stores a list. Ej: ['item_1', 'item_2', 'item_3']
     *
     * @var array
     */
    private $list;

    /**
     * Indicates whether the list will be sorted - Default: false
     *
     * @var booleam
     */
    private $list_order;

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
     * @var string
     */
    private $module;

    /**
     * Message that will be placed at the end of the mail
     *
     * @var string
     */
    private $subcopy;

    /**
     * Stores additional data that will be sent to the view
     * They can be accessed as follows: $with->param_# . Where # is the order in which the data was sent
     *
     * @var array
     */
    private $with;

    /**
     * Stores the detail of the action that sends the notification
     *
     * @var String
     */
    private $event;

    /**
     * Store the paths of the files that will be attached
     *
     * @var array
     */
    private $attach = [];

    /**
     * Id of the company
     *
     * @var Integer
     */
    private $company;

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
        if (!$recipients instanceof CollectionEloquent && !$recipients instanceof Collection && !$recipients instanceof User && !$recipients instanceof Employee)
            throw new \Exception('Invalid recipient format');
        
        if ($recipients instanceof CollectionEloquent || $recipients instanceof Collection)
        {
            if ($recipients->isEmpty())
                throw new \Exception('Empty collection');
            
            $recipients = $recipients->filter(function ($value, $key) {
                $email = is_array($value) ? 
                        (isset($value['email']) ? $value['email'] : null) : 
                        (isset($value->email) ? $value->email : null);

                if ($email && 
                    preg_match('/^[_a-zA-Z0-9-]+(.[_a-zA-Z0-9-]+)@[a-zA-Z0-9-]+(.[a-zA-Z0-9-]+)(.[a-zA-Z]{2,4})$/', $email) )
                    return true;
                else 
                    return false;
            });

            if ($recipients->isEmpty())
                throw new \Exception('The collection was empty after filtering the invalid emails');
        }

        if (($recipients instanceof User || $recipients instanceof Employee) && 
                !preg_match('/^[_a-zA-Z0-9-]+(.[_a-zA-Z0-9-]+)*@[a-zA-Z0-9-]+(.[a-zA-Z0-9-]+)*(.[a-zA-Z]{2,4})$/', $recipients->email) )
            throw new \Exception('Incorrect email format');
    
        $this->recipients = $recipients;

        return $this;
    }

    public function copyHidden($recipients)
    {
        //return $this->recipients($recipients, true);

        if (!$recipients instanceof CollectionEloquent && !$recipients instanceof Collection && !$recipients instanceof User && !$recipients instanceof Employee)
            throw new \Exception('Invalid recipient format');
        
        if ($recipients instanceof CollectionEloquent || $recipients instanceof Collection)
        {
            if ($recipients->isEmpty())
                throw new \Exception('Empty collection');
            
            $recipients = $recipients->filter(function ($value, $key) {
                $email = is_array($value) ? 
                        (isset($value['email']) ? $value['email'] : null) : 
                        (isset($value->email) ? $value->email : null);

                if ($email && 
                    preg_match('/^[_a-zA-Z0-9-]+(.[_a-zA-Z0-9-]+)@[a-zA-Z0-9-]+(.[a-zA-Z0-9-]+)(.[a-zA-Z]{2,4})$/', $email) )
                    return true;
                else 
                    return false;
            });

            if ($recipients->isEmpty())
                throw new \Exception('The collection was empty after filtering the invalid emails');
        }

        if (($recipients instanceof User || $recipients instanceof Employee) && 
                !preg_match('/^[_a-zA-Z0-9-]+(.[_a-zA-Z0-9-]+)*@[a-zA-Z0-9-]+(.[a-zA-Z0-9-]+)*(.[a-zA-Z]{2,4})$/', $recipients->email) )
            throw new \Exception('Incorrect email format');

        $this->copyHidden = $recipients;

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
        if (!is_string($message) || $message == '')
            throw new \Exception('The format of the message is incorrect'); 

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
     * @param boolean $order
     * @return $this
     */
    public function list($data, $order = null)
    {
        if (!is_array($data))
            throw new \Exception('The format of the list is incorrect');
        
        if (!is_string($order) && $order != null)
            throw new \Exception('The format of the order is incorrect');

        $this->list = $data;
        $this->list_order = $order;

        return $this;
    }

    /**
     * Process the information that will be shown in the table
     *
     * @param Illuminate\Database\Eloquent\Collection $data
     * @param array $data
     * @return $this
     */
    public function table($data)
    {
        if ($data instanceof Collection && !$data->isEmpty())
        {
            $information = [];

            $headers = array_keys($data->toArray()[0]);
 
            foreach ($data->toArray() as $key => $value)
            {
                $aux = [];

                foreach ($value as $key2 => $value2)
                {
                    array_push($aux, ($value2 ? $value2 : ''));
                }

                array_push($information, $aux);
            }

            $this->generateTable($headers, $information);
        }   
        else if (is_array($data) && !empty($data))
        {
            $information = [];
            $i = 0;
            
            foreach ($data as $key => $value)
            {
                if (is_array($value) && $this->is_assoc($value))
                {
                    if ($i == 0)
                        $headers = array_keys($value);

                    $aux = [];

                    foreach ($value as $key2 => $value2)
                    {
                        array_push($aux, ($value2 ? $value2 : ''));
                    }

                    array_push($information, $aux);
                    $i++;
                }
                else
                    throw new \Exception('Invalid table format');
            }

            $this->generateTable($headers, $information);
        }
        else 
            throw new \Exception('Invalid table format');

        return $this;
    }

    /**
     * Generates the Markdown Table
     *
     * @param array $headers
     * @param array $information
     * @return void
     */
    private function generateTable($headers, $information)
    {
        // create instance of the table builder
        $tableBuilder = new \MaddHatter\MarkdownTable\Builder();

        // add some data
        $tableBuilder
            ->headers($headers) //headers
            ->align(['L']) // set column alignment
            ->rows($information);
        
        $this->table = $tableBuilder;
    }

    /**
     * Edit the module that performs the action
     *
     * @param App\Models\Module $module
     * @param string $module
     * @return $this
     */
    public function module($module)
    {
        if (is_string($module))
        {
            $module = Module::where('name', $module)->first();

            if (!$module)
                throw new \Exception('Module not found');   
        }
        else if (!$module instanceof Module)
            throw new \Exception('Invalid module format');
        
        $this->module = $module;

        return $this;
    }

    /**
     * Set the content that appears at the bottom of the page
     *
     * @param string $subcopy
     * @return $this
     */
    public function subcopy($subcopy)
    {
        if (!is_string($subcopy) || $subcopy == '')
            throw new \Exception('The format of the subcopy is incorrect'); 

        $this->subcopy = $subcopy;

        return $this;
    }

    /**
     * Process the data that will be passed to the view
     * Remaining the names of the parameters as param_ # . Where # is the order in which the data was sent
     * 
     * @param array $data
     * @return $this
     */
    public function with($data)
    {
        if (empty($data) || !$this->is_assoc($data))
            throw new \Exception('The format of the data is incorrect');


        $this->with = $data;
        return $this;
    }

    /**
     * Edit the event
     *
     * @param string $subject
     * @return $this
     */
    public function event($event)
    {
        if (!is_string($event) || $event == '')
            throw new \Exception('The format of the event is incorrect'); 

        $this->event = $event;

        return $this;
    }

    /**
     * Set the id of the company
     *
     * @param Integer $company
     * @return $this
     */
    public function company($company)
    {
        if (!is_numeric($company))
            throw new \Exception('Invalid company format');

        $this->company = $company;

        return $this;
    }

    /**
     * Edit the mail subject
     *
     * @param string $attach
     * @param array $attach
     * @return $this
     */
    public function attach($attach)
    {
        if (is_string($attach) && $attach)
        {
            array_push($this->attach, $attach);
        }
        else if (is_array($attach))
        {
            foreach ($attach as $key => $path)
            {
                if (is_string($path) && $path)
                    array_push($this->attach, $path);
            }
        }

        return $this;
    }

    /**
     * Send the mail
     *
     * @return booleam
     */
    public function send()
    {
        if (empty($this->recipients) && empty($this->copyHidden))
            throw new \Exception('No valid recipient was entered');

        if (empty($this->module))
            throw new \Exception('The id of the module that performed the action was not entered');

        if (empty($this->company))
            throw new \Exception('The id of the company that performed the action was not entered');

        if (!$this->checkLicense())
        {
            $this->restart();
            return false; //No tiene licencia activa para el modulo por lo que se omite el envio del correo
        }

        $logModel = $this->createLog('MENSAJE');

        try { 
            $message = (new NotificationGeneralMail($this->prepareData(), $logModel))
                ->onQueue('emails');

            Mail::to($this->recipients)
            ->bcc($this->copyHidden)
            ->queue($message);
            
            $logModel->update(['message' => $message->render()]);
            $this->restart();
        }
        catch (\Exception $e) {
          \Log::info($e->getMessage());
          $logModel->delete();
            throw new \Exception('An error occurred while sending the mail');
        }

        return $this;
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
        $data->copyHidden = $this->copyHidden;
        $data->view = $this->view;
        $data->subject = $this->subject;
        $data->attach = $this->attach;

        $data->module = $this->module;
        $module = explode('/', $this->module->display_name);
        $data->module->display_name = count($module) > 1 ? $module[1] : $module[0];
        
        if (!empty($this->message))
            $data->message = $this->message;

        if (!empty($this->buttons))
            $data->buttons = $this->buttons;
        
        if (!empty($this->list))
        {
            $data->list = $this->list;
            $data->list_order = $this->list_order;
        }

        if (!empty($this->table))
            $data->table = $this->table;

        if (!empty($this->subcopy))
            $data->subcopy = $this->subcopy;
        
        if (!empty($this->with))
            $data->with = $this->with;

        return $data;
    }

    /**
     * Create a record of the sent email
     *
     * @return void
     */
    private function createLog($body)
    {
        $log = new LogMail();

        if (!empty($this->event))
            $event = $this->event;
        else
        {
            $event = explode("\\", Route::currentRouteAction());
            $event = $event[COUNT($event) - 1];
        }

        if ($this->recipients instanceof User || $this->recipients instanceof Employee)
            $log->recipients = $this->recipients->email;             
        else if ($this->recipients instanceof CollectionEloquent || $this->recipients instanceof Collection)
        {
            $array = [];

            foreach($this->recipients as $item)
            {
                $email = is_array($item) ? 
                        (isset($item['email']) ? $item['email'] : null) : 
                        (isset($item->email) ? $item->email : null);

                array_push($array, $email);
            }       

            $log->recipients = implode(',', $array);
        }

        if ($this->copyHidden instanceof User || $this->copyHidden instanceof Employee)
            $log->copy_hidden = $this->copyHidden->email;   
        else if ($this->copyHidden instanceof CollectionEloquent || $this->copyHidden instanceof Collection)
        {   
            $array_copy = [];

            $this->copyHidden->each(function ($value, $key) use (&$array_copy) {
                $email = is_array($value) ? 
                        (isset($value['email']) ? $value['email'] : null) : 
                        (isset($value->email) ? $value->email : null);

                array_push($array_copy, $email);
            });

            $log->copy_hidden = implode(',', $array_copy);
        }
        
        $log->company_id = $this->company;
        $log->module_id = $this->module->id;
        $log->event = $event;
        $log->subject = $this->subject;
        //$log->message = isset($this->message) ? $this->message : '';
        $log->message = $body;
        $log->created_at = date("Y-m-d H:i:s");
        $log->save();

        return $log;
    }

    /**
     * Check if an array is associative
     *
     * @param array $array
     * @return boolean
     */
    private function is_assoc($array)
    {
        // Keys of the array
        $keys = array_keys($array);

        // If the array keys of the keys match the keys, then the array must
        // not be associative (e.g. the keys array looked like {0:0, 1:1...}).
        return array_keys($keys) !== $keys;
    }

    /**
     * restart the class data
     * @return void
     */
    private function restart()
    {
        $this->recipients = [];
        $this->copyHidden = [];
        $this->view = 'notification';
        $this->subject = 'Notificación';
        $this->message = '';
        $this->buttons = [];
        $this->list = [];
        $this->list_order = '';
        $this->table = [];
        $this->subcopy = '';
        $this->with = [];
        $this->company = '';
        $this->attach = [];
        $this->module = '';
        $this->event = '';

    }

    /**
     * Returns true if the company that sends the mail has an active license 
     * for the module from which the request is made
     *
     * @return Booleam
     */
    private function checkLicense()
    {
        $licenses = License::
              join('sau_license_module', 'sau_license_module.license_id', 'sau_licenses.id')
            ->whereRaw('? BETWEEN started_at AND ended_at', [date('Y-m-d')])
            ->where('sau_license_module.module_id', $this->module->id);

        $licenses->company_scope = $this->company;

        return $licenses->exists();
    }

    /*private function checkHeader()
    {
        $header = null;

        if ($this->module->isMain())
        {
            $header =
        }
        return $header;
    }*/
}
