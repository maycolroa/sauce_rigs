<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;
use App\Models\General\Keyword;
use App\Models\Administrative\Labels\KeywordCompany;
use DB as DBMaster;

class UpdateLabelsLocationsNewFormat extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        foreach (Keyword::get() as $key => $keyword)
        {
            $keyword->display_name = $this->getValueLocation($keyword);
            $keyword->update();
        }

        DBMaster::beginTransaction();

        foreach (KeywordCompany::withoutGlobalScopes()->get() as $key => $keyword)
        {
            $label = new KeywordCompany();
            $label->keyword_id = $keyword->keyword_id;
            $label->company_id = $keyword->company_id;
            $label->display_name = $keyword->display_name;
            KeywordCompany::withoutGlobalScopes()->where('company_id', $keyword->company_id)->where('keyword_id', $keyword->keyword_id)->delete();
            $label->display_name = $this->getValueLocation($label, 'keyword_id');
            $label->save();
        }

        DBMaster::commit();
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        //
    }

    private function getValueLocation($keyword, $column = "id")
    {
        $label = $keyword->display_name;

        if (in_array($keyword->$column, [1,2,3,4,6,7,8,9]))
        {
            $label = trim(preg_replace('/^((1.)|(2.)|(3.)|(4.))/', "", $label));

            if (in_array($keyword->$column, [1,2]))
                $label = "1. {$label}";

            if (in_array($keyword->$column, [3,4]))
                $label = "2. {$label}";

            if (in_array($keyword->$column, [6,7]))
                $label = "3. {$label}";

            if (in_array($keyword->$column, [8,9]))
                $label = "4. {$label}";
        }

        return $label;
    }
}
