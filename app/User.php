<?php

namespace App;

use Illuminate\Auth\Authenticatable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Auth\Passwords\CanResetPassword;
use Illuminate\Foundation\Auth\Access\Authorizable;
use Illuminate\Contracts\Auth\Authenticatable as AuthenticatableContract;
use Illuminate\Contracts\Auth\Access\Authorizable as AuthorizableContract;
use Illuminate\Contracts\Auth\CanResetPassword as CanResetPasswordContract;

class User extends Model implements AuthenticatableContract,
                                    AuthorizableContract,
                                    CanResetPasswordContract
{
    use Authenticatable, Authorizable, CanResetPassword;

    /**
     * The database table used by the model.
     *
     * @var string
     */
    protected $table = 'users';

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = ['name', 'email', 'password'];

    /**
     * The attributes excluded from the model's JSON form.
     *
     * @var array
     */
    protected $hidden = ['password', 'remember_token'];

    public function microposts()
    {
    	return $this->hasMany(Micropost::class);
    }

	/**
	 * ユーザーがフォローしているUserを取得
	 *
	 * @return \Illuminate\Database\Eloquent\Relations\BelongsToMany
	 */
    public function followings()
    {
    	return $this->belongsToMany(User::class, 'user_follow', 'user_id', 'follow_id')->withTimestamps();
    }

	/**
	 * ユーザーをフォローしているUserを取得
	 *
	 * @return \Illuminate\Database\Eloquent\Relations\BelongsToMany
	 */
    public function followers()
    {
    	return $this->belongsToMany(User::class, 'user_follow', 'follow_id', 'user_id')->withTimestamps();
    }

	/**
	 * ユーザーフォロー
	 *
	 * @param $user_id
	 * @return bool
	 */
    public function follow($user_id)
    {

    	// 自分自身ではないかの確認
    	$exist = $this->isFollowing($user_id);

    	$its_me = $this->id == $user_id;

    	if ($exist || $its_me) {
    		// 既にフォローしていれば何もしない
    		return false;

	    } else {

    		// 未フォローであればフォローする
		    $this->followings()->attach($user_id);

		    return true;
	    }
    }

	/**
	 * フォローを外す
	 *
	 * @param $user_id
	 * @return bool
	 */
    public function unfollow($user_id)
    {
    	// 既にフォローしているかの確認
	    $exist = $this->isFollowing($user_id);
	    // 自分自身ではないかの確認
	    $its_me = $this->id == $user_id;

	    if ($exist && !$its_me) {
	    	// フォローしていればフォローを外す
		    $this->followings()->detach($user_id);
		    return true;

	    } else {
	    	// 未フォローであれば何もしない
		    return false;
	    }
    }

	/**
	 * フォローしているかどうか
	 *
	 * @param $user_id
	 * @return mixed
	 */
    public function isFollowing($user_id)
    {
    	return $this->followings()->where('follow_id', $user_id)->exists();
    }

	/**
	 * タイムライン取得用
	 *
	 * @return mixed
	 */
    public function feedMicroposts()
    {
    	$follow_user_ids = $this->followings()->lists('users.id')->toArray();
    	$follow_user_ids[] = $this->id;
    	return Micropost::whereIn('user_id', $follow_user_ids);
    }
}
