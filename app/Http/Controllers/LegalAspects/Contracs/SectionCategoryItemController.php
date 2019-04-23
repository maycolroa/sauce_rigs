<?php

namespace App\Http\Controllers\LegalAspects\Contracs;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Models\LegalAspects\Contracts\SectionCategoryItems;
use Session;

class SectionCategoryItemController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        return view('application');
    }

    /**
     * Returns an array for a select type input
     *
     * @param Request $request
     * @return Array
     */
    public function multiselect(Request $request)
    {
        if($request->has('keyword'))
        {
            $keyword = "%{$request->keyword}%";
            $items = SectionCategoryItems::select("id", "item_name AS name")
                ->where(function ($query) use ($keyword) {
                    $query->orWhere('item_name', 'like', $keyword);
                })
                ->take(30)->pluck('id', 'name');

            return $this->respondHttp200([
                'options' => $this->multiSelectFormat($items)
            ]);
        }
        else
        {
            $items = SectionCategoryItems::selectRaw("
                sau_ct_section_category_items.id AS id,
                sau_ct_section_category_items.item_name AS name
            ")->pluck('id', 'name');
        
            return $this->multiSelectFormat($items);
        }
    }
}