<?php

namespace App\Http\Controllers\LegalAspects\Contracs;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Models\LegalAspects\Contracts\SectionCategoryItems;
use App\Traits\ContractTrait;

class SectionCategoryItemController extends Controller
{
    use ContractTrait;

    function __construct()
    {
        parent::__construct();
        //$this->middleware("permission:users_d, {$this->company}", ['only' => 'destroy']);
    }

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
        if ($this->user->hasRole('Arrendatario', $this->team) || $this->user->hasRole('Contratista', $this->team))
        {
            $contract = $this->getContractUser($this->user->id);
            $items = $this->getStandardItemsContract($contract);
            $items = COUNT($items) > 0 ? $items->pluck('id', 'item_name') : [];
            return $this->multiSelectFormat($items);
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