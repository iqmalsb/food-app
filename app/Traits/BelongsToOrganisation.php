<?php

namespace App\Traits;

use App\Models\Organisation;
use Illuminate\Database\Eloquent\Builder;

trait BelongsToOrganisation
{
    public static function bootBelongsToOrganisation()
    {
        static::creating(function ($model) {
            if (!array_key_exists('organisation_id', $model->getAttributes())) {
                if (auth()->check() && auth()->user()->organisation_id) {
                    $model->organisation_id = auth()->user()->organisation_id;
                } elseif (static::getCurrentOrganisationId()) {
                    $model->organisation_id = static::getCurrentOrganisationId();
                }
            }
        });

        static::addGlobalScope('organisation', function (Builder $builder) {
            if (auth()->check()) {
                $user = auth()->user();
                if ($user->role === 'superadmin') {
                    if ($orgId = static::getCurrentOrganisationId()) {
                        $builder->where($builder->getQuery()->from . '.organisation_id', $orgId);
                    }
                    return;
                }
                if ($user->organisation_id) {
                    $builder->where($builder->getQuery()->from . '.organisation_id', $user->organisation_id);
                    return;
                }
            }

            if ($orgId = static::getCurrentOrganisationId()) {
                $builder->where($builder->getQuery()->from . '.organisation_id', $orgId);
            }
        });
    }

    public function organisation()
    {
        return $this->belongsTo(Organisation::class);
    }

    protected static $currentOrganisationId = null;

    public static function setCurrentOrganisationId($id)
    {
        static::$currentOrganisationId = $id;
        session(['current_organisation_id' => $id]);
    }

    public static function getCurrentOrganisationId()
    {
        if (static::$currentOrganisationId !== null) {
            return static::$currentOrganisationId;
        }

        return session('current_organisation_id');
    }
}
