<?php

namespace App\Traits;

use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\File;
use Carbon\Carbon;
use Exception;
use Session;
use DateTime;
use DB;

trait UtilsTrait
{

    /**
     * converts the specified data into the format that the multiselect
     * needs in order to works
     * NOTE: this new structure only applies for the monterail.github.io/vue-multiselect/ library
     * @param  object $data
     * @param  string $itemRef
     * @return collection
     */
    protected function multiSelectFormat($data, $itemRef = '')
    {
        $newFormartCollection = collect([]);

        if (is_array($data)) {
            collect($data)->each(function ($item, $key) use ($newFormartCollection, $itemRef) {
                $newFormartCollection->push([
                    'name' => $item,
                    'value' => $item
                ]);
            });
        } else {//or collection
            collect($data)->each(function ($item, $key) use ($newFormartCollection, $itemRef) {
                $name = $itemRef ? $item[$itemRef] : $item;

                $newFormartCollection->push([
                    'name' => $key,
                    'value' => $name
                ]);
            });
        }

        return $newFormartCollection;
    }

    /**
     * converts the specified data into the format that the multiselect Group
     * needs in order to works
     * NOTE: this new structure only applies for the monterail.github.io/vue-multiselect/ library
     * @param  array $data
     * @param  string $itemRef
     * @return collection
     */
    protected function multiSelectGroupFormat($data)
    {
        $newFormartCollection = collect([]);

        if (is_array($data)) {
            collect($data)->each(function ($item, $key) use ($newFormartCollection) {

                $children = [];

                foreach ($item as $keyChild => $value)
                {
                    array_push($children, [
                        'name' => $value,
                        'value' => $keyChild
                    ]);
                }

                $newFormartCollection->push([
                    'parent' => $key,
                    'children' => $children
                ]);
            });
        }

        return $newFormartCollection;
    }

    /**
     * converts the specified data into the format that the radio
     * needs in order to works
     * @param  object $data
     * @param  string $itemRef
     * @return collection
     */
    protected function radioFormat($data, $itemRef = '')
    {
        $newFormartCollection = collect([]);

        if (is_array($data)) {
            collect($data)->each(function ($item, $key) use ($newFormartCollection, $itemRef) {
                $newFormartCollection->push([
                    'text' => $item,
                    'value' => $item
                ]);
            });
        } else {//or collection
            collect($data)->each(function ($item, $key) use ($newFormartCollection, $itemRef) {
                $name = $itemRef ? $item[$itemRef] : $item;

                $newFormartCollection->push([
                    'text' => $key,
                    'value' => $name
                ]);
            });
        }

        return $newFormartCollection;
    }

    /**
     * returns the absolute path for talend parameters used in
     * talend jobs execution
     * this methos only must be used to get the cd_path and sh_path
     * parameters
     *
     * this method assumes that the talend is located in storage/talend_jobs
     * @param  string $parameter
     * @return string
     */
    protected function getAbsoluteTalendParameter($parameter)
    {
        $parameter = $this->getValueFromEnvFile($parameter);
        if (!starts_with($parameter, '/')) {
            $parameter = "/$parameter";
        }
        return storage_path("talend_jobs$parameter");
    }

    /**
     * returns the PayU api key located in the .env file
     * @return string
     */
    protected function payuApiKey()
    {
        return $this->getValueFromEnvFile('PAYU_APIKEY');
    }

    /**
     * returns the PayU merchant id located in the .env file
     * @return string
     */
    protected function payuMerchantId()
    {
        return $this->getValueFromEnvFile('PAYU_MERCHANT_ID');
    }

    /**
     * returns the PayU account id located in the .env file
     * @return string
     */
    protected function payuAccountId()
    {
        return $this->getValueFromEnvFile('PAYU_ACCOUNT_ID');
    }

    /**
     * returns the PayU test value located in the .env file
     * @return string
     */
    protected function payuTest()
    {
        return $this->getValueFromEnvFile('PAYU_TEST') == 'is_test';
    }

    /**
     * returns the PayU payment site url located in the .env file
     * @return string
     */
    protected function payuPaymentSiteUrl()
    {
        return $this->getValueFromEnvFile('PAYU_PAYMENT_SITE_URL');
    }

    /**
     * returns the PayU payment site url located in the .env file
     * @return string
     */
    protected function diskStorageFiles()
    {
        return $this->getValueFromEnvFile('DISK_STORAGE_FILES');
    }

    /**
     * returns a value located in the .env file
     * by its key
     *
     * if it is not defined, throws exception
     * @param  string $parameter
     * @return string
     */
    protected function getValueFromEnvFile($parameter)
    {
        $value = env($parameter);
        if (!$value) {
            throw new Exception('Parameter '.$parameter.' not found in .env file');
        }
        return $value;
    }

    /**
     * Returns an array with the multiselect data sent to the controller
     * @param Object $object
     *
     * @return Array
     */
    protected function getDataFromMultiselect($object)
    {
        $data = [];

        foreach($object as $v)
        {
            array_push($data, json_decode($v)->value);
        }

        return $data;
    }

    protected function getValuesForMultiselect($data, $keyRef = 'value')
    {
        return  collect($data)
                ->transform(function ($item, $index) use ($keyRef) {
                    return $item[$keyRef];
                });
    }

    protected function getValuesForMultiselectTags($data, $keyRef = 'value')
    {
        return  collect($data)
                ->transform(function ($item, $index) use ($keyRef) {
                    return trim(str_replace(',', '', $item[$keyRef]));
                })
                ->filter(function ($item, $key) {
                    return $item;
                });
    }

    protected function tagsPrepare($data)
    {
        $item = collect([]);

        if (!empty($data))
            $item = $this->getValuesForMultiselectTags($data, 'name')->unique();

        return $item;
    }

    protected function tagsPrepareImport($data, $serapator = ',')
    {
        $data = explode($serapator, $data);
        $item = collect([]);

        foreach ($data as $value)
        {
            $item->push(trim($value));
        }
        
        $item = $item->unique();

        return $item;
    }

    protected function tagsSave($data, $model, $company_id = null)
    {
        $company_id = $company_id ? $company_id : Session::get('company_id');
        
        foreach ($data as $value)
        {
            $item = $model::where('name', $value);
            $item->company_scope = $company_id;
            $item = $item->first();

            if (!$item)
                $model::create([
                    'name'=>$value,
                    'company_id'=>$company_id
                ]);

            //$model::updateOrCreate(['name'=>$value, 'company_id'=>$company_id], ['name'=>$value, 'company_id'=>$company_id]);
        }
    }

    protected function tagsSaveSystemCompany($data, $model, $company_id = null)
    {
        $company_id = $company_id ? $company_id : Session::get('company_id');
        
        foreach ($data as $value)
        {
            $item = $model::where('name', $value)->where('system', true);
            $item = $item->first();

            if (!$item)
            {
                $item2 = $model::where('name', $value)->where('company_id', $company_id);
                $item2 = $item2->first();

                if (!$item2)
                {
                    $model::create([
                        'name'=>$value,
                        'company_id'=>$company_id
                    ]);
                }
            }
        }
    }

    protected function tagsSaveFields($data, $model, $field_id, $company_id = null)
    {
        $company_id = $company_id ? $company_id : Session::get('company_id');
        
        foreach ($data as $value)
        {
            $item = $model::where('name', $value)->where('field_id', $field_id);
            $item->company_scope = $company_id;
            $item = $item->first();

            if (!$item)
                $model::create([
                    'name'=>$value,
                    'company_id'=>$company_id,
                    'field_id'=>$field_id
                ]);
        }
    }

    /**
     * Check if an array is associative
     *
     * @param Array $array
     * @return Boolean
     */
    protected function is_assoc($array)
    {
        // Keys of the array
        $keys = array_keys($array);

        // If the array keys of the keys match the keys, then the array must
        // not be associative (e.g. the keys array looked like {0:0, 1:1...}).
        return array_keys($keys) !== $keys;
    }

    /**
     * Valid if the date has a valid format
     *
     * @param String $date
     * @param String $format
     * @return Booleam
     */
    function validateDate($date, $format = 'Y-m-d H:i:s')
    {
        $d = DateTime::createFromFormat($format, $date);
        return $d && $d->format($format) == $date;
    }

    public function keywordCheck($key, $defaultValue = '', $user = null)
    {
        if (!$user)
            $user = Auth::user();
            
        return isset($user->keywords[$key]) ? $user->keywords[$key] : $defaultValue;
    }

    public function getKeywordQueue($company_id)
    {
        $keywords = DB::table(DB::raw(
            "(SELECT
                k.name AS name,
                IF (c.display_name IS NULL, k.display_name, c.display_name) AS display_name
            FROM
                sau_keywords k
            LEFT JOIN sau_keyword_company c ON c.keyword_id = k.id AND 
                (
                    c.company_id = $company_id OR c.company_id IS NULL
                )) AS t"
            )
        )
        ->pluck('display_name', 'name');
            
        return $keywords;
    }

    public function formatDateToSave($date)
    {
        if ($date)
            $date = (Carbon::createFromFormat('D M d Y', $date))->format('Y-m-d');

        return $date;
    }

    public function formatDateToForm($date)
    {
        if ($date)
            $date = (Carbon::createFromFormat('Y-m-d', $date))->format('D M d Y');

        return $date;
    }

    public function formatDatetimeToBetweenFilter($dates)
    {
        $dates = explode('/', $dates);

        $result = [];

        if (COUNT($dates) == 2)
        {
            array_push($result, (Carbon::createFromFormat('D M d Y', $dates[0]))->format('Y-m-d 00:00:00'));
            array_push($result, (Carbon::createFromFormat('D M d Y', $dates[1]))->format('Y-m-d 23:59:59'));
        }

        return $result;
    }

    public function isThot()
    {
        if (strpos(url()->current(), 'bi.thotstrategy') === FALSE) {
            return false;
    }

        return true;
    }

    public function makeDirectory($directory)
    {
        if (!File::exists($directory))
        {
            File::makeDirectory($directory, 0777, true);
            chmod($directory, 0777);
        }
    }

    public function sanear_string($string)
    {
     
        $string = trim($string);
     
        $string = str_replace(
            array('á', 'à', 'ä', 'â', 'ª', 'Á', 'À', 'Â', 'Ä'),
            array('a', 'a', 'a', 'a', 'a', 'A', 'A', 'A', 'A'),
            $string
        );
     
        $string = str_replace(
            array('é', 'è', 'ë', 'ê', 'É', 'È', 'Ê', 'Ë'),
            array('e', 'e', 'e', 'e', 'E', 'E', 'E', 'E'),
            $string
        );
     
        $string = str_replace(
            array('í', 'ì', 'ï', 'î', 'Í', 'Ì', 'Ï', 'Î'),
            array('i', 'i', 'i', 'i', 'I', 'I', 'I', 'I'),
            $string
        );
     
        $string = str_replace(
            array('ó', 'ò', 'ö', 'ô', 'Ó', 'Ò', 'Ö', 'Ô'),
            array('o', 'o', 'o', 'o', 'O', 'O', 'O', 'O'),
            $string
        );
     
        $string = str_replace(
            array('ú', 'ù', 'ü', 'û', 'Ú', 'Ù', 'Û', 'Ü'),
            array('u', 'u', 'u', 'u', 'U', 'U', 'U', 'U'),
            $string
        );
     
        $string = str_replace(
            array('ñ', 'Ñ', 'ç', 'Ç'),
            array('n', 'N', 'c', 'C',),
            $string
        );
     
        //Esta parte se encarga de eliminar cualquier caracter extraño
        $string = str_replace(
            array("\\", "¨", "º", "-", "~",
                 "#", "@", "|", "!", "\"",
                 "·", "$", "%", "&", "/",
                 "(", ")", "?", "'", "¡",
                 "¿", "[", "^", "<code>", "]",
                 "+", "}", "{", "¨", "´",
                 ">", "< ", ";", ",", ":",
                 "."),
            '',
            $string
        );
     
     
        return $string;
    }

    public function verifyEmailFormat($email)
    {
        if (preg_match('/^[_a-zA-Z0-9-]+(.[_a-zA-Z0-9-]+)*@[a-zA-Z0-9-]+(.[a-zA-Z0-9-]+)*(.[a-zA-Z]{2,4})$/', $email))
            return true;
        else
            false;
    }

    protected function validEmail($email)
    {
        $email_matches = array();

        $from_regex   = '[a-zA-Z0-9_,\s\-\.\+\^!#\$%&*+\/\=\?\`\|\{\}~\']+';
        $user_regex   = '[a-zA-Z0-9_\-\.\+\^!#\$%&*+\/\=\?\`\|\{\}~\']+';
        $domain_regex = '(?:(?:[a-zA-Z0-9]|[a-zA-Z0-9][a-zA-Z0-9\-]*[a-zA-Z0-9])\.?)+';
        $ipv4_regex   = '[0-9]{1,3}(\.[0-9]{1,3}){3}';
        $ipv6_regex   = '[0-9a-fA-F]{1,4}(\:[0-9a-fA-F]{1,4}){7}';

        preg_match("/^$from_regex\s\<(($user_regex)@($domain_regex|(\[($ipv4_regex|$ipv6_regex)\])))\>$/", $email, $matches_2822);
        preg_match("/^($user_regex)@($domain_regex|(\[($ipv4_regex|$ipv6_regex)\]))$/", $email, $matches_normal);

        // Check for valid email as per RFC 2822 spec.
        if (empty($matches_normal) && !empty($matches_2822) && !empty($matches_2822[3])) {
            $email_matches['from_name'] = $matches_2822[0];
            $email_matches['full_email'] = $matches_2822[1];
            $email_matches['email_name'] = $matches_2822[2];
            $email_matches['domain'] = $matches_2822[3];
        }

        // Check for valid email as per RFC 822 spec.
        if (empty($matches_2822) && !empty($matches_normal) && !empty($matches_normal[2])) {
            $email_matches['from_name'] = '';
            $email_matches['full_email'] = $matches_normal[0];
            $email_matches['email_name'] = $matches_normal[1];
            $email_matches['domain'] = $matches_normal[2];
        }

        if (substr($email, -1) == '.')
            return 0;

        if (!empty($email_matches))
            return 1;
        else
            return 0;
    }
}