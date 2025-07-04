<?php

namespace App\Http\Controllers\General;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Models\Administrative\Processes\TagsProcess;

class TagController extends Controller
{
    /**
     * Returns an array for a select type input
     *
     * @param Request $request
     * @return Array
     */

    public function multiselectTypeProcess(Request $request)
    {
        if($request->has('keyword'))
        {
            $keyword = "%{$request->keyword}%";
            $tags = TagsProcess::select("id", "name")
                ->where(function ($query) use ($keyword) {
                    $query->orWhere('name', 'like', $keyword);
                })
                ->orderBy('name')
                ->take(30)->pluck('id', 'name');

            return $this->respondHttp200([
                'options' => $this->multiSelectFormat($tags)
            ]);
        }
        else
        {
            $tags = TagsProcess::selectRaw("
                sau_tags_processes.id as id,
                sau_tags_processes.name as name
            ")
            ->orderBy('name')
            ->pluck('name', 'name');
        
            return $this->multiSelectFormat($tags);
        }
    }

    public function multiselectTypeProcessRiskMatrx(Request $request)
    {
        if($request->has('keyword'))
        {
            $keyword = "%{$request->keyword}%";
            $tags = TagsProcess::select("id", "name")
                ->where(function ($query) use ($keyword) {
                    $query->orWhere('name', 'like', $keyword);
                })
                ->orderBy('name')
                ->take(30)->pluck('id', 'name');

            return $this->respondHttp200([
                'options' => $this->multiSelectFormat($tags)
            ]);
        }
        else
        {
            $tags = TagsProcess::selectRaw("
                sau_tags_processes.id as id,
                sau_tags_processes.name as name
            ")
            ->orderBy('name')
            ->pluck('id', 'name');
        
            return $this->multiSelectFormat($tags);
        }
    }
}
