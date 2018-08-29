<?php

namespace App\Traits;

use App\SecApplication;
use App\User;
use App\UserLog;
use Carbon\Carbon;
use Illuminate\Support\Facades\Session;

trait UserLogTrait
{

    /**
     * creates and inits a user log record related to the
     * specified $user and $session_id
     * @param  App\User   $user
     * @param  string $session_id
     * @param  Carbon|null $now
     * @return void
     */
    protected function createUserLog(User $user, $session_id, Carbon $now = null)
    {
        $user->last_session_id = $session_id;
        $user->save();

        if (!$now) {
            $now = Carbon::now();
        }
        UserLog::create([
            'user_id' => $user->id,
            'login' => $now,
            'session_id' => $user->last_session_id,
            'sec_app_id' => SecApplication::where('name', 'reincorporaciones')->first()->id
        ]);
    }

    /**
     * if a session exists related to the last session id of the user
     * this session is destroyed and the user related to this
     * session will be kicked out
     * @param  App\User $user
     * @return void
     */
    protected function checkRelatedSessions(User $user)
    {
        if ($user->last_session_id) {
            $previous_session = Session::getHandler()->read($user->last_session_id);
            if ($previous_session) {
                Session::getHandler()->destroy($user->last_session_id);
            }
        }
    }

    /**
     * updates the userlog in database related
     * with the last session id of the specified $user
     * @param  App\User   $user
     * @param  Carbon|null $now
     * @return void
     */
    protected function updateUserLogLogout(User $user, Carbon $now = null) {
        if (!$now) {
            $now = Carbon::now();
        }
        UserLog::where([
            ['user_id', $user->id],
            ['session_id', $user->last_session_id]
        ])
        ->whereNull('logout')
        ->latest()
        ->update(['logout' => $now]);
    }
}