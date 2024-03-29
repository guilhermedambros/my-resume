<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use App\Skill;
use App\Career;
use App\Education;
use App\User;
use App\Language;
use App\SocialMedia;

class Resume extends Model
{
    use SoftDeletes;

    protected $dates = ['deleted_at', 'created_at', 'updated_at'];
    
    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'id', 'email', 'birth', 'name', 'nationality', 'phone', 'cover_letter', 'user_id'
    ];

    public function skills()
    {
        return $this->belongsToMany(Skill::class);
    }
    public function getSkillIdsAttribute()
    {
        return $this->skills->pluck('id');
    }
    public function languages()
    {
        return $this->belongsToMany(Language::class);
    }
    public function getLanguageIdsAttribute()
    {
        return $this->languages->pluck('id');
    }
    public function careers()
    {
        $order_by = (env('APP_ENV')=='local') ? 'substr(begin, 7, 4)||substr(begin, 4, 2) desc' 
            : 'CONCAT(substr(begin, 7, 4),substr(begin, 4, 2)) desc';
        return $this->hasMany(Career::class)->orderByRaw($order_by);
    }

    public function educations()
    {
        $order_by = (env('APP_ENV')=='local') ? 'substr(begin, 7, 4)||substr(begin, 4, 2) desc' 
        : 'CONCAT(substr(begin, 7, 4),substr(begin, 4, 2)) desc';
        return $this->hasMany(Education::class)->orderByRaw($order_by);
    }

    public function social_medias()
    {
        return $this->hasMany(SocialMedia::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function employee(){
        return (Career::whereNull('end')->where('resume_id', $this->id)->count() > 0) ? true : false;
    }

    public function current_role(){
        return $this->careers->whereNull('end')->pluck( 'role', 'company')->all();
    }
}
