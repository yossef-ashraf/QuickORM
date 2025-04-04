 ```php
<?php

namespace App\Models;

use QuickORM\Src\Model;

class User extends Model
{
    protected $table = 'users';
    protected $primaryKey = 'id';

    protected $fillable = ['name', 'email', 'password', 'age'];
    protected $guarded = ['id', 'created_at', 'updated_at'];
    protected $hidden = ['password'];
    protected $casts = [
        'age' => 'integer',
        'is_active' => 'boolean',
        'last_login' => 'datetime',
        'preferences' => 'json'
    ];

    public function posts()
    {
        return $this->hasMany(Post::class, 'user_id');
    }

    public function profile()
    {
        return $this->hasOne(Profile::class, 'user_id');
    }

    public function roles()
    {
        return $this->belongsToMany(Role::class, 'user_roles', 'user_id', 'role_id');
    }

    public static function findByEmail($email)
    {
        return (new static)->where('email', $email)->first();
    }

    public static function getActiveUsers($minAge = 18)
    {
        return (new static)
            ->where('is_active', true)
            ->where('age', '>=', $minAge)
            ->get();
    }

    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    public function deactivate()
    {
        $this->is_active = false;
        return $this->save();
    }

    public static function searchByName($name)
    {
        return (new static)
            ->where('name', 'LIKE', "%{$name}%")
            ->get();
    }

    public static function getUsersWithRecentPosts($days = 7)
    {
        $date = date('Y-m-d', strtotime("-{$days} days"));
        return (new static)
            ->whereIn('id', function($query) use ($date) {
                $query->select('user_id')
                      ->from('posts')
                      ->where('created_at', '>=', $date);
            })
            ->get();
    }

    public static function getTopUsersByPostCount($limit = 10)
    {
        return (new static)
            ->select('users.*', $this->pdo->raw('COUNT(posts.id) as post_count'))
            ->leftJoin('posts', 'users.id', '=', 'posts.user_id')
            ->groupBy('users.id')
            ->orderBy('post_count', 'DESC')
            ->limit($limit)
            ->get();
    }

    protected static function boot()
    {
        parent::boot();

        static::creating(function ($user) {
            $user->password = password_hash($user->password, PASSWORD_DEFAULT);
        });
    }
}
```