<?php

namespace App\Policies;

use App\Models\User;
use App\Models\SalesTarget;
use Illuminate\Auth\Access\HandlesAuthorization;

class SalesTargetPolicy
{
    use HandlesAuthorization;

    public function viewAny(User $user)
    {
        return true; // Both admin and manager can view
    }

    public function view(User $user, SalesTarget $salesTarget)
    {
        if ($user->isAdmin()) {
            return true;
        }

        // Manager can only view targets in their scope
        return $salesTarget->salesman->region_id === $user->region_id &&
               $salesTarget->salesman->channel_id === $user->channel_id;
    }

    public function create(User $user)
    {
        return true; // Both admin and manager can create
    }

    public function update(User $user, SalesTarget $salesTarget)
    {
        if ($user->isAdmin()) {
            return true;
        }

        // Manager can only update targets in their scope
        return $salesTarget->salesman->region_id === $user->region_id &&
               $salesTarget->salesman->channel_id === $user->channel_id;
    }

    public function delete(User $user, SalesTarget $salesTarget)
    {
        if ($user->isAdmin()) {
            return true;
        }

        // Manager can only delete targets in their scope
        return $salesTarget->salesman->region_id === $user->region_id &&
               $salesTarget->salesman->channel_id === $user->channel_id;
    }
} 