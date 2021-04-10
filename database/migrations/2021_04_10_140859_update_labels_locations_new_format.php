<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;
use App\Models\General\Keyword;
use App\Models\Administrative\Labels\KeywordCompany;

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

        foreach (KeywordCompany::withoutGlobalScopes()->get() as $key => $keyword)
        {
            $keyword->id = $keyword->keyword_id;
            $keyword->display_name = $this->getValueLocation($keyword);
            $keyword->update();
        }
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

    private function getValueLocation($keyword, $concat = true)
    {
        $label = $keyword->display_name;

        if (in_array($keyword->id, [1,2,3,4,6,7,8,9]))
        {
            $label = trim(preg_replace('/^((1.)|(2.)|(3.)|(4.))/', "", $label));

            if ($concat)
            {
                if (in_array($keyword->id, [1,2]))
                    $label = "1. {$label}";

                if (in_array($keyword->id, [3,4]))
                    $label = "2. {$label}";

                if (in_array($keyword->id, [6,7]))
                    $label = "3. {$label}";

                if (in_array($keyword->id, [8,9]))
                    $label = "4. {$label}";
            }
        }

        return $label;
    }
}
