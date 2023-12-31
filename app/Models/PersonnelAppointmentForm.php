<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\MorphOne;
use Illuminate\Database\Eloquent\SoftDeletes;

class PersonnelAppointmentForm extends Model
{
    public $timestamps = false;
    use HasFactory;use softdeletes;
    protected $table = "personnel_appointment_forms";
    protected $fillable = ["created_at","updated_at","user_id","employee_id","is_accepted","is_refused","inactive","data","i_number"];
    protected $appends = ["data_array"];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class,"user_id");
    }
    public function employee(): BelongsTo
    {
        return $this->belongsTo(Employee::class,"employee_id");
    }
    public function automation(): MorphOne
    {
        return $this->morphOne(Automation::class, 'automationable');
    }
    public function GetDataArrayAttribute(){
        return json_decode($this->data,true);
    }
}
