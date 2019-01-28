<?php

namespace App\Traits;

use Exception;
use Session;

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
            throw new Exception('Parameter not found in .env file');
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

    protected function tagsPrepare($data)
    {
        $item = $this->getValuesForMultiselect($data, 'name')->unique();
        return $item;
    }

    protected function tagsSave($data, $model)
    {
        foreach ($data as $value)
        {
            $item = $model::where('name', $value)->first();

            if (!$item)
                $model::create([
                    'name'=>$value,
                    'company_id'=>Session::get('company_id')
                ]);

            //$model::updateOrCreate(['name'=>$value, 'company_id'=>$company_id], ['name'=>$value, 'company_id'=>$company_id]);
        }
    }
}