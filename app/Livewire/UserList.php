<?php

namespace App\Livewire;

use App\Models\User;
use App\Models\Organisation;
use Livewire\Component;
use Livewire\WithPagination;
use Livewire\Attributes\Url;

class UserList extends Component
{
    use WithPagination;

    protected $paginationTheme = 'bootstrap';

    #[Url(keep: true)]
    public $search = '';

    public function updatingSearch()
    {
        $this->resetPage();
    }

    public function deleteUser($userId)
    {
        $user = User::withoutGlobalScopes()->findOrFail($userId);
        $currentUser = auth()->user();

        // Security check
        if ($currentUser->role !== 'superadmin' && $user->organisation_id !== $currentUser->organisation_id) {
            abort(403, 'Unauthorized.');
        }

        // Prevent self deletion
        if ($user->id === $currentUser->id) {
            $this->dispatch('toast', [
                'type' => 'alert-danger',
                'message' => 'You cannot delete your own account.'
            ]);
            return;
        }

        $user->delete();

        $this->dispatch('toast', [
            'type' => 'alert-danger',
            'message' => 'User deleted successfully.'
        ]);
    }

    public function render()
    {
        $currentUser = auth()->user();
        $keyword = trim($this->search);

        if ($currentUser->role === 'superadmin') {
            $orgId = \App\Traits\BelongsToOrganisation::getCurrentOrganisationId();
            if ($orgId) {
                $query = User::withoutGlobalScopes()->where('organisation_id', $orgId);
            } else {
                $query = User::withoutGlobalScopes();
            }
            if ($keyword) {
                $query->where(function ($q) use ($keyword) {
                    $q->where('name', 'LIKE', '%' . $keyword . '%')
                      ->orWhere('email', 'LIKE', '%' . $keyword . '%');
                });
            }
            $users = $query->paginate(10);
        } else {
            $query = User::where('organisation_id', $currentUser->organisation_id);
            if ($keyword) {
                $query->where(function ($q) use ($keyword) {
                    $q->where('name', 'LIKE', '%' . $keyword . '%')
                      ->orWhere('email', 'LIKE', '%' . $keyword . '%');
                });
            }
            $users = $query->paginate(5);
        }

        // Calculate seats limit badge info
        $remainingSeats = null;
        $org = null;
        if ($currentUser->role !== 'superadmin') {
            $org = $currentUser->organisation;
            if ($org) {
                $activeSeats = User::where('organisation_id', $org->id)->count();
                $remainingSeats = max(0, $org->seats_limit - $activeSeats);
            }
        } else {
            $activeOrgId = \App\Traits\BelongsToOrganisation::getCurrentOrganisationId();
            if ($activeOrgId) {
                $org = Organisation::find($activeOrgId);
                if ($org) {
                    $activeSeats = User::withoutGlobalScopes()->where('organisation_id', $org->id)->count();
                    $remainingSeats = max(0, $org->seats_limit - $activeSeats);
                }
            }
        }

        return view('livewire.user-list', [
            'users' => $users,
            'remainingSeats' => $remainingSeats,
            'org' => $org,
        ]);
    }
}
