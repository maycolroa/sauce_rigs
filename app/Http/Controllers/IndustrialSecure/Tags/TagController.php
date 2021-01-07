<?php

namespace App\Http\Controllers\IndustrialSecure\Tags;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Models\IndustrialSecure\DangerMatrix\TagsAdministrativeControls;
use App\Models\IndustrialSecure\DangerMatrix\TagsEngineeringControls;
use App\Models\IndustrialSecure\DangerMatrix\TagsEpp;
use App\Models\IndustrialSecure\DangerMatrix\TagsAddFields;
use App\Models\IndustrialSecure\DangerMatrix\TagsPossibleConsequencesDanger;
use App\Models\IndustrialSecure\DangerMatrix\TagsWarningSignage;
use App\Models\IndustrialSecure\DangerMatrix\TagsSubstitution;
use App\Models\IndustrialSecure\DangerMatrix\TagsParticipant;
use App\Models\IndustrialSecure\DangerMatrix\TagsDangerDescription;
use App\Models\IndustrialSecure\DangerMatrix\TagsHistoryChange;

class TagController extends Controller
{
    /**
     * Returns an array for a select type input
     *
     * @param Request $request
     * @return Array
     */

    public function multiselectAddFields(Request $request, $id)
    {
        if($request->has('keyword'))
        {
            $keyword = "%{$request->keyword}%";
            $tags = TagsAddFields::select("id", "name")
                ->where(function ($query) use ($keyword) {
                    $query->orWhere('name', 'like', $keyword);
                })
                ->where('field_id', $id)
                ->take(30)->pluck('id', 'name');

            return $this->respondHttp200([
                'options' => $this->multiSelectFormat($tags)
            ]);
        }
    }

    /**
     * Returns an array for a select type input
     *
     * @param Request $request
     * @return Array 
     */

    public function multiselectAdministrativeControls(Request $request)
    {
        if($request->has('keyword'))
        {
            $keyword = "%{$request->keyword}%";
            $tags = TagsAdministrativeControls::select("id", "name")
                ->where(function ($query) use ($keyword) {
                    $query->orWhere('name', 'like', $keyword);
                })
                ->take(30)->pluck('id', 'name');

            return $this->respondHttp200([
                'options' => $this->multiSelectFormat($tags)
            ]);
        }
    }

    /**
     * Returns an array for a select type input
     *
     * @param Request $request
     * @return Array
     */

    public function multiselectEngineeringControls(Request $request)
    {
        if($request->has('keyword'))
        {
            $keyword = "%{$request->keyword}%";
            $tags = TagsEngineeringControls::select("id", "name")
                ->where(function ($query) use ($keyword) {
                    $query->orWhere('name', 'like', $keyword);
                })
                ->take(30)->pluck('id', 'name');

            return $this->respondHttp200([
                'options' => $this->multiSelectFormat($tags)
            ]);
        }
    }

    /**
     * Returns an array for a select type input
     *
     * @param Request $request
     * @return Array
     */

    public function multiselectEpp(Request $request)
    {
        if($request->has('keyword'))
        {
            $keyword = "%{$request->keyword}%";
            $tags = TagsEpp::select("id", "name")
                ->where(function ($query) use ($keyword) {
                    $query->orWhere('name', 'like', $keyword);
                })
                ->take(30)->pluck('id', 'name');

            return $this->respondHttp200([
                'options' => $this->multiSelectFormat($tags)
            ]);
        }
    }

    /**
     * Returns an array for a select type input
     *
     * @param Request $request
     * @return Array
     */

    public function multiselectPossibleConsequencesDanger(Request $request)
    {
        if($request->has('keyword'))
        {
            $keyword = "%{$request->keyword}%";
            $tags = TagsPossibleConsequencesDanger::select("id", "name")
                ->where(function ($query) use ($keyword) {
                    $query->orWhere('name', 'like', $keyword);
                })
                ->take(30)->pluck('id', 'name');

            return $this->respondHttp200([
                'options' => $this->multiSelectFormat($tags)
            ]);
        }
    }

    /**
     * Returns an array for a select type input
     *
     * @param Request $request
     * @return Array
     */

    public function multiselectWarningSignage(Request $request)
    {
        if($request->has('keyword'))
        {
            $keyword = "%{$request->keyword}%";
            $tags = TagsWarningSignage::select("id", "name")
                ->where(function ($query) use ($keyword) {
                    $query->orWhere('name', 'like', $keyword);
                })
                ->take(30)->pluck('id', 'name');

            return $this->respondHttp200([
                'options' => $this->multiSelectFormat($tags)
            ]);
        }
    }

    /**
     * Returns an array for a select type input
     *
     * @param Request $request
     * @return Array
     */

    public function multiselectSubstitution(Request $request)
    {
        if($request->has('keyword'))
        {
            $keyword = "%{$request->keyword}%";
            $tags = TagsSubstitution::select("id", "name")
                ->where(function ($query) use ($keyword) {
                    $query->orWhere('name', 'like', $keyword);
                })
                ->take(30)->pluck('id', 'name');

            return $this->respondHttp200([
                'options' => $this->multiSelectFormat($tags)
            ]);
        }
    }

    /**
     * Returns an array for a select type input
     *
     * @param Request $request
     * @return Array
     */

    public function multiselectParticipants(Request $request)
    {
        if($request->has('keyword'))
        {
            $keyword = "%{$request->keyword}%";
            $tags = TagsParticipant::select("id", "name")
                ->where(function ($query) use ($keyword) {
                    $query->orWhere('name', 'like', $keyword);
                })
                ->take(30)->pluck('id', 'name');

            return $this->respondHttp200([
                'options' => $this->multiSelectFormat($tags)
            ]);
        }
    }

    public function multiselectDangerDescription(Request $request)
    {
        if($request->has('keyword'))
        {
            $keyword = "%{$request->keyword}%";
            $tags = TagsDangerDescription::select("id", "name")
                ->where(function ($query) use ($keyword) {
                    $query->orWhere('name', 'like', $keyword);
                })
                ->take(30)->pluck('id', 'name');

            return $this->respondHttp200([
                'options' => $this->multiSelectFormat($tags)
            ]);
        }
        else
        {
            $tags = TagsDangerDescription::selectRaw("
                sau_tags_danger_description.id as id,
                sau_tags_danger_description.name as name
            ")->pluck('name', 'name');
        
            return $this->multiSelectFormat($tags);
        }
    }

    public function multiselectHistoryChange(Request $request)
    {
        if($request->has('keyword'))
        {
            $keyword = "%{$request->keyword}%";
            $tags = TagsHistoryChange::select("id", "name")
                ->where(function ($query) use ($keyword) {
                    $query->orWhere('name', 'like', $keyword);
                })
                ->take(30)->pluck('id', 'name');

            return $this->respondHttp200([
                'options' => $this->multiSelectFormat($tags)
            ]);
        }
        else
        {
            $tags = TagsHistoryChange::selectRaw("
                sau_tags_dm_history_change.id as id,
                sau_tags_dm_history_change.name as name
            ")->pluck('name', 'name');
        
            return $this->multiSelectFormat($tags);
        }
    }
}
