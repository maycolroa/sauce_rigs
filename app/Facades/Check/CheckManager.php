<?php

namespace App\Facades\Check;

use App\Models\Administrative\Users\User;
use App\Models\Administrative\Employees\Employee;
use App\Models\PreventiveOccupationalMedicine\Reinstatements\Check;
use App\Models\PreventiveOccupationalMedicine\Reinstatements\Cie10Code;
use App\Models\PreventiveOccupationalMedicine\Reinstatements\Tracing;
use App\Models\PreventiveOccupationalMedicine\Reinstatements\CheckFile;
use App\Http\Requests\PreventiveOccupationalMedicine\Reinstatements\CheckRequest;
use App\Rules\Reinstatements\EndRecommendationsBePresent;
use App\Rules\Reinstatements\StartRecomendation;
use App\Rules\Reinstatements\MonitoringRecomendation;
use App\Rules\Reinstatements\ProcessOriginDoneMustBePresent;
use App\Rules\Reinstatements\ProcessPclDoneMustBePresent;
use App\Rules\Reinstatements\RequiredIfHasRecommendations;
use App\Rules\Reinstatements\RequiredIfHasRestrictions;
use App\Rules\Reinstatements\RequiredIfInProcessIsNo;
use App\Rules\Reinstatements\EndRestrictionsBePresent;
use App\Rules\Reinstatements\RequiredIfHasIncapacitated;
use App\Traits\ConfigurableFormTrait;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;
use Carbon\Carbon;
use ReflectionClass;
use Exception;
use DB;

class CheckManager
{
    use ConfigurableFormTrait;

    protected $formModel;

    function __construct()
    {
        $this->formModel = $this->getFormModel('form_check');
    }

    /**
     * saves the medicalMonitorings and attaches them to the specified $check
     * @param  Check $check
     * @param  array $laborMonitorings
     * @param  boolean $clearMonitorings
     * @return boolean
     */
    public function saveMedicalMonitoring(Check $check, $medicalMonitorings, $clearMonitorings = false)
    {
        return $this->saveMonitoring('MedicalMonitoring', $check, $medicalMonitorings, $clearMonitorings);
    }

    /**
     * saves the laborMonitorings and attaches them to the specified $check
     * @param  Check $check
     * @param  array $laborMonitorings
     * @param  boolean $clearMonitorings
     * @return boolean
     */
    public function saveLaborMonitoring(Check $check, $laborMonitorings, $clearMonitorings = false)
    {
        return $this->saveMonitoring('LaborMonitoring', $check, $laborMonitorings, $clearMonitorings);
    }

    /**
     * saves a $new_tracing to the specified check and attaches it to the madeByUser
     * @param  Check  $check
     * @param  string $tracingDescription
     * @param  User   $madeByUser
     * @return boolean
     */
    public function saveTracing($class, Check $check, $tracingDescription, User $madeByUser, $tracingsToUpdate = [])
    {
        try
        {
            $this->handleTracingUpdates($class, $madeByUser, $tracingsToUpdate);

            if (!$tracingDescription)
                return true;

            foreach ($tracingDescription as $tracing)
            {
                $tracing = json_decode($tracing);

                $newTracing = (new ReflectionClass($class))->newInstanceArgs([[
                    'description' => $tracing->description
                ]]);

                $newTracing->check_id = $check->id;
                $newTracing->user_id = $madeByUser->id;
                $newTracing->save();
            }

            return true;

        } catch (Exception $e) {
            $this->handleError($e);
            return false;
        }
    }

    /**
     * updates old tracings if necessary
     * @param  array  $tracingsToUpdate
     * @return void
     */
    public function handleTracingUpdates($class, User $madeByUser, $tracingsToUpdate = [])
    {
        if (!is_array($tracingsToUpdate))
            return;

        foreach ($tracingsToUpdate as $tracing)
        {
            $tracing = json_decode($tracing);

            $oldTracing = (new ReflectionClass($class))->newInstanceArgs([])->where('id', $tracing->id)->first();

            if (!$oldTracing)
                continue;

            if ($tracing->description != $oldTracing->description)  
            {
                $oldTracing->update([
                    'description' => $tracing->description,
                    'user_id' => $madeByUser->id
                ]);
            }
        }
    }

    /**
     * saves the monitorings and attaches them to the specified check object
     * the monitorings are defined by the $monitoringClass
     *
     * if everything is ok, returns true
     * if not, returns false
     * 
     * @param  String $monitoringClass
     * @param  App\Check $check
     * @param  array  $monitorings
     * @param  boolean $clearMonitorings
     * @return boolean
     */
    public function saveMonitoring($monitoringClass, Check $check, $monitorings, $clearMonitorings = false)
    {
        try
        {
            if ($monitoringClass == 'MedicalMonitoring') {
                $relation = 'medicalMonitorings';
            } else if ($monitoringClass == 'LaborMonitoring') {
                $relation = 'laborMonitorings';
            }

            if ($clearMonitorings)
                $check->$relation()->delete();

            if (!is_array($monitorings))
                return true;

            $monitoringsToSave = [];

            foreach ($monitorings as $monitoring)
            {
                $monitoring = json_decode($monitoring);

                 if ($monitoringClass == 'MedicalMonitoring')
                 {
                    $attrs = [
                        'set_at' => $this->setFormatDate($monitoring->set_at),
                        'conclusion' => $monitoring->conclusion,
                        'has_monitoring_content' => isset($monitoring->has_monitoring_content) ? $monitoring->has_monitoring_content : null
                    ];
                } else if ($monitoringClass == 'LaborMonitoring')
                {
                    $attrs = [
                        'set_at' => $this->setFormatDate($monitoring->set_at),
                        'conclusion' => $monitoring->conclusion,
                        'has_monitoring_content' => isset($monitoring->has_monitoring_content) ? $monitoring->has_monitoring_content : null,
                        'productivity' => isset($monitoring->productivity) ? $monitoring->productivity : null
                    ];
                }

                $newMonitoring = (new ReflectionClass("App\Models\PreventiveOccupationalMedicine\Reinstatements\\$monitoringClass"))->newInstanceArgs([$attrs]);
                array_push($monitoringsToSave, $newMonitoring);
            }

            $check->$relation()->saveMany($monitoringsToSave);

            return true;

        } catch (Exception $e) {
            $this->handleError($e);
            return false;
        }
    }

    public function handleError(Exception $e)
    {
        Log::error("{$e->getMessage()} \n {$e->getTraceAsString()}");
    }

    /**
     * returns an object with the rules for the attributes:
     *
     * process_origin_done_date
     * emitter_origin
     * process_pcl_done_date
     * pcl
     *
     * these values has special rules because each of them has extra validations
     * to more details, see the according rule class
     * 
     * @param  CheckRequest $request
     * @return 
     */
    public function getProcessRules(CheckRequest $request)
    {
        //Default, VivaAir
        $rules = [
            'start_recommendations' => [new RequiredIfHasRecommendations($request->has_recommendations), new StartRecomendation($request->indefinite_recommendations, $request->end_recommendations, $request->has_recommendations)],
            'indefinite_recommendations' => new RequiredIfHasRecommendations($request->has_recommendations),
            'relocated' => new RequiredIfHasRecommendations($request->has_recommendations),
            'origin_recommendations' => new RequiredIfHasRecommendations($request->has_recommendations),

            'restriction_id' => new RequiredIfHasRestrictions($request->has_restrictions),

            'process_origin_done' => new RequiredIfInProcessIsNo($request->in_process_origin, 'calificación de origen'),
            'process_pcl_done' => new RequiredIfInProcessIsNo($request->in_process_pcl, 'PCL'),

            'end_recommendations' => new EndRecommendationsBePresent($request->has_recommendations, $request->indefinite_recommendations),

            'process_origin_done_date' => new ProcessOriginDoneMustBePresent($request->in_process_origin, $request->process_origin_done),
            'emitter_origin' => new ProcessOriginDoneMustBePresent($request->in_process_origin, $request->process_origin_done, true),
            'process_pcl_done_date' => new ProcessPclDoneMustBePresent($request->in_process_pcl, $request->process_pcl_done),
            'pcl' => new ProcessPclDoneMustBePresent($request->in_process_pcl, $request->process_pcl_done, true),
            'entity_rating_pcl' => new ProcessPclDoneMustBePresent($request->in_process_pcl, $request->process_pcl_done, true)
        ];

        if ($this->formModel != 'hptu' && $this->formModel != 'argos')
        {
            $rules = array_merge($rules, [
                'monitoring_recommendations' => [new RequiredIfHasRecommendations($request->has_recommendations), new MonitoringRecomendation($request->indefinite_recommendations, $request->start_recommendations, $request->end_recommendations, $request->has_recommendations)],
            ]);
        }

        if ($this->formModel == 'misionEmpresarial')
        {
            $rules = array_merge($rules, [
                'start_restrictions' => new RequiredIfHasRestrictions($request->has_restrictions),
                'indefinite_restrictions' => new RequiredIfHasRestrictions($request->has_restrictions),
                'end_restrictions' => new EndRestrictionsBePresent($request->has_restrictions, $request->indefinite_restrictions),
                'incapacitated_days' => new RequiredIfHasIncapacitated($request->has_incapacitated),
                'incapacitated_last_extension' => new RequiredIfHasIncapacitated($request->has_incapacitated),
            ]);
        }

        return $rules;
    }

    /**
     * according to the $valuesToUpdate parameter,
     * object that contains all attributes with their values to update a check
     *
     * this method calls another methods that checks these each value in order to define
     * if some of the attributes of the check must be null
     * 
     * @param  CheckRequest $request
     * @param integer $secCompanyId
     * @return object
     */
    public function checkNullAttrs(CheckRequest $request, $secCompanyId)
    {
        $valuesToUpdate = $request->all();
        $valuesToUpdate = $this->checkNullRecommendations($valuesToUpdate, $valuesToUpdate['has_recommendations'], $valuesToUpdate['indefinite_recommendations']);
        $valuesToUpdate = $this->checkNullEmitterOrigin($valuesToUpdate, $valuesToUpdate['in_process_origin'], $valuesToUpdate['process_origin_done']);
        $valuesToUpdate = $this->checkNullPcl($valuesToUpdate, $valuesToUpdate['in_process_pcl'], $valuesToUpdate['process_pcl_done']);

        $valuesToUpdate = $this->checkFiles($request, $secCompanyId, $valuesToUpdate, 'process_origin_file', 'process_origin_file_name');
        $valuesToUpdate = $this->checkFiles($request, $secCompanyId, $valuesToUpdate, 'process_pcl_file', 'process_pcl_file_name');

        $valuesToUpdate = $this->checkFormatDate($valuesToUpdate);

        return $valuesToUpdate;
    }

    /**
     * checks if any files are attached to the check
     * if any, moves the files to the process_files folder
     * and make the relation in the database
     * @param  CheckRequest $request
     * @param integer $secCompanyId
     * @param  object $valuesToUpdate
     * @param  strign $fileAttr
     * @param  strign $filenameAttr
     * @return object
     */
    public function checkFiles(CheckRequest $request, $secCompanyId, $valuesToUpdate, $fileAttr, $filenameAttr)
    {
        if ($request->hasFile($fileAttr))
        {
            $filesRelativeFolder = "public/preventiveOccupationalMedicine/reinstatements/files/{$secCompanyId}";
            $directory = storage_path("app/{$filesRelativeFolder}");

            if (!File::exists($directory))
                File::makeDirectory($directory);
            
            $file = $request->file($fileAttr);
            $serverFilename = Carbon::now()->timestamp . '_' . str_random(15) . ".{$file->getClientOriginalExtension()}";
            $file->storeAs($filesRelativeFolder, $serverFilename);
            $valuesToUpdate[$fileAttr] = $serverFilename;
            $valuesToUpdate[$filenameAttr] = $file->getClientOriginalName();
        }

        return $valuesToUpdate;
    }

    /**
     * according to the $has_recommendations and $indefinite_recommendations parameters,
     * defines if the recommendations fields must be null
     * there fields and defined by $attrs array inside this method
     * 
     * @param  object $valuesToUpdate
     * @param  string $has_recommendations
     * @param  string $indefinite_recommendations
     * @return object
     */
    public function checkNullRecommendations($valuesToUpdate, $has_recommendations, $indefinite_recommendations)
    {
        $attrs = [
            'start_recommendations',
            'end_recommendations',
            'indefinite_recommendations',
            'origin_recommendations',
            'relocated',
            'detail',
            'monitoring_recommendations'
        ];

        $valuesToUpdate = $this->setNullAttrs($valuesToUpdate, $has_recommendations, $attrs);

        if ($has_recommendations == 'SI' && $indefinite_recommendations == 'SI')
            $valuesToUpdate['end_recommendations'] = null;

        return $valuesToUpdate;
    }

    /**
     * according to the $in_process_origin and $process_origin_done parameters,
     * defines if the process origin fields must be null
     * there fields and defined by $attrs array inside this method
     * 
     * @param  object $valuesToUpdate
     * @param  string $in_process_origin
     * @param  string $process_origin_done
     * @return object
     */
    public function checkNullEmitterOrigin($valuesToUpdate, $in_process_origin, $process_origin_done)
    {
        $params = $this->getParametersToSetNullAttrs(
            $in_process_origin,
            $process_origin_done,
            'process_origin_done',
            'process_origin_done_date',
            'emitter_origin'
        );

        return $this->setNullAttrs($valuesToUpdate, $params['yesNoRef'], $params['attrs']);
    }

    /**
     * according to the $in_process_origin and $process_origin_done parameters,
     * defines if the process pcl fields must be null
     * there fields and defined by $attrs array inside this method
     * 
     * @param  object $valuesToUpdate
     * @param  string $in_process_pcl
     * @param  string $process_pcl_done
     * @return object
     */
    public function checkNullPcl($valuesToUpdate, $in_process_pcl, $process_pcl_done)
    {
        $params = $this->getParametersToSetNullAttrs(
            $in_process_pcl,
            $process_pcl_done,
            'process_pcl_done',
            'process_pcl_done_date',
            'pcl',
            'entity_rating_pcl'
        );

        return $this->setNullAttrs($valuesToUpdate, $params['yesNoRef'], $params['attrs']);
    }

    /**
     * this method only must be used to compute parameters to define which fields must be null
     * for the "checkNullPcl" and "checkNullEmitterOrigin" methods.
     *
     * this method is defined because both of that cases (emitter origin and pcl) use the same
     * funcionality to define which fields must be null
     * 
     * @param  string $in_process
     * @param  string $process_done
     * @param  string $done_attr
     * @param  string $date_attr
     * @param  string $main_attr
     * @return object
     */
    public function getParametersToSetNullAttrs($in_process, $process_done, $done_attr, $date_attr, $main_attr, $extra_attr = '')
    {
        $yesNoRef = 'NO';
        $attrs = [];

        if ($in_process == 'SI') {
            $attrs = [
                $done_attr,
                $date_attr
            ];
        } else if ($in_process == 'NO' && $process_done == 'NO') {
            $attrs = [
                $date_attr,
                $main_attr,
            ];
            if ($extra_attr) {
                array_push($attrs, $extra_attr);
            }
        } else {
            $yesNoRef = 'SI';
        }

        return [
            'yesNoRef' => $yesNoRef,
            'attrs' => $attrs
        ];
    }

    /**
     * according to the $yesNoRef parameter, is it is 'NO',
     * sets to null all fields defined in the $attrs field
     * within the $valuesToUpdate object
     * 
     * @param object $valuesToUpdate
     * @param string $yesNoRef
     * @param array $attrs
     * @return object
     */
    public function setNullAttrs($valuesToUpdate, $yesNoRef, $attrs)
    {
        if ($yesNoRef == 'NO')
        {
            foreach ($attrs as $attr)
            {
                $valuesToUpdate[$attr] = null;
            }
        }
        
        return $valuesToUpdate;
    }

    /**
     * according to the $has_recommendations and $indefinite_recommendations parameters,
     * defines if the recommendations fields must be null
     * there fields and defined by $attrs array inside this method
     * 
     * @param  object $valuesToUpdate
     * @return object
     */
    public function checkFormatDate($valuesToUpdate)
    {
        $attrs = [
            'start_recommendations',
            'end_recommendations',
            'disease_origin_date',
            'monitoring_recommendations',
            'process_origin_done_date',
            'process_pcl_done_date',
            'date_controversy_origin_1',
            'date_controversy_origin_2',
            'date_controversy_pcl_1',
            'date_controversy_pcl_2',
            'start_restrictions',
            'end_restrictions',
            'incapacitated_last_extension',
            'deadline',
            'next_date_tracking',
            'relocated_date'
        ];

        foreach ($attrs as $attr)
        {
            $valuesToUpdate[$attr] = $this->setFormatDate($valuesToUpdate[$attr]);
        }

        return $valuesToUpdate;
    }

    /**
     * Undocumented function
     *
     * @param String $value
     * @return void
     */
    private function setFormatDate($value)
    {
        if ($value)
            $value = (Carbon::createFromFormat('D M d Y', $value))->format('Y-m-d');

        return $value;
    }

    public function saveFiles(Check $check, $request, User $madeByUser)
    {
        $directory = "preventiveOccupationalMedicine/reinstatements/files/{$check->company_id}";
            
        try
        {
            if ($request->has('files') && COUNT($request->files) > 0)
            {
                $files_names_delete = [];

                foreach ($request->get('files') as $keyF => $file) 
                {
                    $create_file = true;

                    if (isset($file['id']))
                    {
                        $fileUpload = CheckFile::findOrFail($file['id']);

                        if ($file['old_name'] == $file['file'])
                            $create_file = false;
                        else
                            array_push($files_names_delete, $file['old_name']);
                    }
                    else
                    {
                        $fileUpload = new CheckFile();
                        $fileUpload->check_id = $check->id;
                        $fileUpload->user_id = $madeByUser->id;
                    }

                    if ($create_file)
                    {
                        $file_tmp = $file['file'];
                        $nameFile = base64_encode($madeByUser->id . now() . rand(1,10000) . $keyF) .'.'. $file_tmp->extension();
                        $file_tmp->storeAs($directory, $nameFile, 'public');
                        $fileUpload->file = $nameFile;
                        $fileUpload->file_name = $file_tmp->getClientOriginalName();
                    }

                    if (!$fileUpload->save())
                        return false;
                }

                //Borrar archivos reemplazados
                foreach ($files_names_delete as $keyf => $file)
                {
                    Storage::disk('public')->delete($directory."/".$file);
                }
            }

            return true;

        } catch (Exception $e) {
            $this->handleError($e);
            return false;
        }
    }

    public function getTracingOthers($employee_id, $check_id, $table)
    {
        $checks = Check::select(
                "{$table}.id AS id",
                "{$table}.description AS descripcion",
                "{$table}.updated_at AS updated_at",
                'sau_users.name AS name',
                DB::raw('CONCAT(sau_reinc_cie10_codes.code, " - ", sau_reinc_cie10_codes.description) AS diagnostico'),
                'sau_reinc_checks.disease_origin AS disease_origin'
            )
            ->join('sau_employees', 'sau_employees.id', 'sau_reinc_checks.employee_id')
            ->join($table, "{$table}.check_id", 'sau_reinc_checks.id' )
            ->join('sau_users', 'sau_users.id', "{$table}.user_id")
            ->join('sau_reinc_cie10_codes', 'sau_reinc_cie10_codes.id', 'sau_reinc_checks.cie10_code_id')
            ->isOpen()
            ->where('sau_reinc_checks.employee_id', $employee_id)
            ->where("{$table}.check_id", '<>', $check_id)
            ->orderBy("{$table}.created_at", 'desc')
            ->get();

        $oldTracings = [];

        foreach ($checks as $check) {

            array_push($oldTracings, [

                'id' => $check->id,
                'description' => $check->descripcion,
                'made_by' => $check->name,
                'updated_at' => $check->updated_at->toDateString(),
                'disease_origin' => $check->disease_origin,
                'diagnosis' => $check->diagnostico

            ]);
        }

        return $oldTracings;
    }

    public function deleteData($check, $data)
    {
        $directory = "preventiveOccupationalMedicine/reinstatements/files/{$check->company_id}";

        if (COUNT($data['files']) > 0)
        {
            foreach ($data['files'] as $keyF => $file)
            {
                $file_delete = CheckFile::find($file);

                if ($file_delete)
                {
                    Storage::disk('public')->delete($directory."/".$file_delete->file);
                    $file_delete->delete();
                }
            }
        }
    }
    
}