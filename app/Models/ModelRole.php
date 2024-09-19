<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Spatie\Permission\Models\Role;

class ModelRole extends Model
{
    use HasFactory;

    protected $table = 'model_has_roles';

    public $timestamps = false;

    protected $fillable = [
        'role_id',  //from roles table
        'model_type',  //e.g App\User
        'model_id',  //user id from users table
    ];

    public static function assignRole($name, $user_id)
    {
        $role = Role::where('name', $name)->first();

        if (!$role) {
            abort(404, 'Role not found');
        }

        ModelRole::create([
            'role_id' => $role->id,
            'model_type' => 'App\Models\User',
            'model_id' => $user_id
        ]);
    }
}
