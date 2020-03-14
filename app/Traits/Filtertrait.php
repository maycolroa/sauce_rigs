<?php

namespace App\Traits;

use Exception;
use App\Models\General\FiltersState;

trait Filtertrait
{
	public function filterDefaultValues($user, $url)
	{
		$filters = FiltersState::select('sau_filters_states.*')
		->where(
			[
				['user_id', $user],
				['url', $url]
			])
		->first();

		$values = [];

		if($filters)
			$values = json_decode($filters->data, true);


		return $values;
	}

}