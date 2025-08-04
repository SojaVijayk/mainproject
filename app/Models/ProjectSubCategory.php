<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ProjectSubCategory extends Model
{
    protected $fillable = ['category_id', 'name', 'code', 'description'];
    protected $table="project_subcategories";

    public function category()
    {
        return $this->belongsTo(ProjectCategory::class);
    }
}