<?php

namespace App\Models\LegalAspects\LegalMatrix;

use Illuminate\Database\Eloquent\Model;
use App\Traits\CompanyTrait;
use Session;

class Law extends Model
{
    use CompanyTrait;

    protected $table = 'sau_lm_laws';

    protected $fillable = [
        'name',
        'law_number',
        'system_apply_id',
        'law_year',
        'law_type_id',
        'description',
        'observations',
        'risk_aspect_id',
        'entity_id',
        'sst_risk_id',
        'repealed',
        'file'
    ];

    public $scope_table_for_company_table = 'sau_lm_company_interest';

    public function lawType()
    {
        return $this->belongsTo(LawType::class);
    }

    public function riskAspect()
    {
        return $this->belongsTo(RiskAspect::class);
    }

    public function entity()
    {
        return $this->belongsTo(Entity::class);
    }

    public function sstRisk()
    {
        return $this->belongsTo(SstRisk::class);
    }

    public function systemApply()
    {
        return $this->belongsTo(SystemApply::class);
    }

    public function articles()
    {
        return $this->hasMany(Article::class, 'law_id')->orderBy('sau_lm_articles.sequence');
    }

    public function scopeCompany($query)
    {
        return $query->where('sau_lm_laws.company_id', Session::get('company_id'));
    }

    public function scopeSystem($query)
    {
        return $query->whereNull('sau_lm_laws.company_id');
    }

    public function scopeAlls($query, $company_id = null)
    {
        if (!$company_id)
            $company_id = Session::get('company_id');

        return $query->where('sau_lm_laws.company_id', $company_id)
                     ->orWhereNull('sau_lm_laws.company_id');
    }

    /**
     * filters checks through the given lawTypes
     * @param  Illuminate\Database\Eloquent\Builder $query
     * @param  array $lawTypes
     * @return Illuminate\Database\Eloquent\Builder
     */
    public function scopeInLawTypes($query, $lawTypes, $typeSearch = 'IN')
    {
        if (COUNT($lawTypes) > 0)
        {
            if ($typeSearch == 'IN')
                $query->whereIn('sau_lm_laws.law_type_id', $lawTypes);

            else if ($typeSearch == 'NOT IN')
                $query->whereNotIn('sau_lm_laws.law_type_id', $lawTypes);
        }

        return $query;
    }

    /**
     * filters checks through the given riskAspects
     * @param  Illuminate\Database\Eloquent\Builder $query
     * @param  array $riskAspects
     * @return Illuminate\Database\Eloquent\Builder
     */
    public function scopeInRiskAspects($query, $riskAspects, $typeSearch = 'IN')
    {
        if (COUNT($riskAspects) > 0)
        {
            if ($typeSearch == 'IN')
                $query->whereIn('sau_lm_laws.risk_aspect_id', $riskAspects);

            else if ($typeSearch == 'NOT IN')
                $query->whereNotIn('sau_lm_laws.risk_aspect_id', $riskAspects);
        }

        return $query;
    }

    /**
     * filters checks through the given entities
     * @param  Illuminate\Database\Eloquent\Builder $query
     * @param  array $entities
     * @return Illuminate\Database\Eloquent\Builder
     */
    public function scopeInEntities($query, $entities, $typeSearch = 'IN')
    {
        if (COUNT($entities) > 0)
        {
            if ($typeSearch == 'IN')
                $query->whereIn('sau_lm_laws.entity_id', $entities);

            else if ($typeSearch == 'NOT IN')
                $query->whereNotIn('sau_lm_laws.entity_id', $entities);
        }

        return $query;
    }

    /**
     * filters checks through the given sstRisks
     * @param  Illuminate\Database\Eloquent\Builder $query
     * @param  array $sstRisks
     * @return Illuminate\Database\Eloquent\Builder
     */
    public function scopeInSstRisks($query, $sstRisks, $typeSearch = 'IN')
    {
        if (COUNT($sstRisks) > 0)
        {
            if ($typeSearch == 'IN')
                $query->whereIn('sau_lm_laws.sst_risk_id', $sstRisks);

            else if ($typeSearch == 'NOT IN')
                $query->whereNotIn('sau_lm_laws.sst_risk_id', $sstRisks);
        }

        return $query;
    }

    /**
     * filters checks through the given systemApply
     * @param  Illuminate\Database\Eloquent\Builder $query
     * @param  array $systemApply
     * @return Illuminate\Database\Eloquent\Builder
     */
    public function scopeInSystemApply($query, $systemApply, $typeSearch = 'IN')
    {
        if (COUNT($systemApply) > 0)
        {
            if ($typeSearch == 'IN')
                $query->whereIn('sau_lm_laws.system_apply_id', $systemApply);

            else if ($typeSearch == 'NOT IN')
                $query->whereNotIn('sau_lm_laws.system_apply_id', $systemApply);
        }

        return $query;
    }

    /**
     * filters checks through the given lawNumbers
     * @param  Illuminate\Database\Eloquent\Builder $query
     * @param  array $lawNumbers
     * @return Illuminate\Database\Eloquent\Builder
     */
    public function scopeInLawNumbers($query, $lawNumbers, $typeSearch = 'IN')
    {
        if (COUNT($lawNumbers) > 0)
        {
            if ($typeSearch == 'IN')
                $query->whereIn('sau_lm_laws.law_number', $lawNumbers);

            else if ($typeSearch == 'NOT IN')
                $query->whereNotIn('sau_lm_laws.law_number', $lawNumbers);
        }

        return $query;
    }

    /**
     * filters checks through the given lawYears
     * @param  Illuminate\Database\Eloquent\Builder $query
     * @param  array $lawYears
     * @return Illuminate\Database\Eloquent\Builder
     */
    public function scopeInLawYears($query, $lawYears, $typeSearch = 'IN')
    {
        if (COUNT($lawYears) > 0)
        {
            if ($typeSearch == 'IN')
                $query->whereIn('sau_lm_laws.law_year', $lawYears);

            else if ($typeSearch == 'NOT IN')
                $query->whereNotIn('sau_lm_laws.law_year', $lawYears);
        }

        return $query;
    }

    /**
     * filters checks through the given repealed
     * @param  Illuminate\Database\Eloquent\Builder $query
     * @param  array $repealed
     * @return Illuminate\Database\Eloquent\Builder
     */
    public function scopeInRepealed($query, $repealed, $typeSearch = 'IN')
    {
        if (COUNT($repealed) > 0)
        {
            if ($typeSearch == 'IN')
                $query->whereIn('sau_lm_laws.repealed', $repealed);

            else if ($typeSearch == 'NOT IN')
                $query->whereNotIn('sau_lm_laws.repealed', $repealed);
        }

        return $query;
    }

    /**
     * filters checks through the given responsibles
     * @param  Illuminate\Database\Eloquent\Builder $query
     * @param  array $responsibles
     * @return Illuminate\Database\Eloquent\Builder
     */
    public function scopeInResponsibles($query, $responsibles, $typeSearch = 'IN')
    {
        if (COUNT($responsibles) > 0)
        {
            if ($typeSearch == 'IN')
                $query->whereIn('sau_lm_articles_fulfillment.responsible', $responsibles);

            else if ($typeSearch == 'NOT IN')
                $query->whereNotIn('sau_lm_articles_fulfillment.responsible', $responsibles);
        }

        return $query;
    }

    /**
     * filters checks through the given states
     * @param  Illuminate\Database\Eloquent\Builder $query
     * @param  array $states
     * @return Illuminate\Database\Eloquent\Builder
     */
    public function scopeInState($query, $states, $typeSearch = 'IN')
    {
        if (COUNT($states) > 0)
        {
            $items = [];

            if ($typeSearch == 'IN')
            {
                foreach ($states as $key => $value)
                {
                    if ($value == 'Sin calificar')
                        array_push($items, '(SUM(IF(sau_lm_articles_fulfillment.fulfillment_value_id IS NOT NULL, 1, 0)) = 0)');

                    else if ($value == 'En proceso')
                        array_push($items, '( SUM(IF(sau_lm_articles_fulfillment.fulfillment_value_id IS NOT NULL, 1, 0)) > 0 AND SUM(IF(sau_lm_articles_fulfillment.fulfillment_value_id IS NULL, 1, 0)) > 0 )');

                    else if ($value == 'Terminada')
                        array_push($items, '( SUM(IF(sau_lm_articles_fulfillment.fulfillment_value_id IS NULL, 1, 0)) = 0 )');
                }

                $query->havingRaw(implode(" OR ", $items));
            }
            else if ($typeSearch == 'NOT IN')
            {
                foreach ($states as $key => $value)
                {
                    if ($value == 'Sin calificar')
                        array_push($items, '(SUM(IF(sau_lm_articles_fulfillment.fulfillment_value_id IS NOT NULL, 1, 0)) != 0)');

                    else if ($value == 'En proceso')
                        array_push($items, '( SUM(IF(sau_lm_articles_fulfillment.fulfillment_value_id IS NOT NULL, 1, 0)) = 0 OR SUM(IF(sau_lm_articles_fulfillment.fulfillment_value_id IS NULL, 1, 0)) = 0 )');

                    else if ($value == 'Terminada')
                        array_push($items, '( SUM(IF(sau_lm_articles_fulfillment.fulfillment_value_id IS NULL, 1, 0)) != 0 )');
                }

                $query->havingRaw(implode(" AND ", $items));
            }
        }

        return $query;
    }
}
