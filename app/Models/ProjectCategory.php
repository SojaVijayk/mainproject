<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ProjectCategory extends Model
{
    protected $fillable = ['name', 'code', 'description'];

    protected $table ="project_categories";

    public function subCategories()
    {
        return $this->hasMany(ProjectSubCategory::class);
    }
}