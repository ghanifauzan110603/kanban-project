<?php

namespace App\Policies;

use App\Models\User;

class TaskPolicy
{
    public function update($user, $task)
    {
        return $user->id == $task->user_id;
    }

    public function delete($user, $task)
    {
        return $user->id == $task->user_id;
    }
}