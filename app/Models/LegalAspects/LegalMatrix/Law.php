<?php

namespace App\Models\LegalAspects\LegalMatrix;

use Illuminate\Database\Eloquent\Model;
use App\Traits\CompanyTrait;

class Law extends Model
{
    use CompanyTrait;

    protected $table = 'sau_lm_laws';

    protected $fillable = [
        'name',
        'law_number',
        'apply_system',
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

    public function articles()
    {
        return $this->hasMany(Article::class, 'law_id')->orderBy('sau_lm_articles.sequence');
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
     * filters checks through the given applySystem
     * @param  Illuminate\Database\Eloquent\Builder $query
     * @param  array $applySystem
     * @return Illuminate\Database\Eloquent\Builder
     */
    public function scopeInApplySystem($query, $applySystem, $typeSearch = 'IN')
    {
        if (COUNT($applySystem) > 0)
        {
            if ($typeSearch == 'IN')
                $query->whereIn('sau_lm_laws.apply_system', $applySystem);

            else if ($typeSearch == 'NOT IN')
                $query->whereNotIn('sau_lm_laws.apply_system', $applySystem);
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
}
