<div>
<div class="card">
    <div class="card-header d-flex justify-content-between align-items-center">
        <span>User Management ({{ auth()->user()->role === 'superadmin' ? 'Superadmin' : 'Organisation Admin' }})</span>
        @if ($remainingSeats !== null && $org)
            <span class="badge {{ $remainingSeats <= 0 ? 'bg-danger' : 'bg-success' }} px-3 py-2" style="font-size: 0.85rem;">
                <i class="bi bi-person-fill-lock"></i> {{ $remainingSeats }} / {{ $org->seats_limit }} Seats Remaining
            </span>
        @endif
        <a href="{{ route('home') }}" class="btn btn-sm btn-outline-secondary">Back to Dashboard</a>
    </div>

    <div class="card-body">
        <div class="input-group">
            <input type="text" class="form-control" wire:model.live.debounce.250ms="search" placeholder="Search by name or email...">
            @if($search)
                <button type="button" wire:click="$set('search', '')" class="btn btn-outline-secondary">Clear</button>
            @endif
        </div>
        @if ($remainingSeats !== null && $remainingSeats <= 0 && auth()->user()->role !== 'superadmin')
            <button type="button" class="btn btn-dark mt-3" data-bs-toggle="modal" data-bs-target="#contactSupportModal">
                Add New User
            </button>
        @else
            <a href="{{ route('users.create') }}" class="btn btn-dark mt-3">Add New User</a>
        @endif
    </div>

    <div class="card-body">
        <div class="table-responsive">
            <table class="table table-hover">
                <thead>
                  <tr>
                    <th scope="col">#</th>
                    <th scope="col">Name</th>
                    <th scope="col">Email</th>
                    <th scope="col">Role</th>
                    @if(auth()->user()->role === 'superadmin')
                        <th scope="col">Organisation</th>
                    @endif
                    <th scope="col">Position</th>
                    <th scope="col">Created At</th>
                    <th scope="col">Actions</th>
                  </tr>
                </thead>
                <tbody>
                    @forelse ($users as $user)
                        <tr wire:key="user-{{ $user->id }}">
                            <th scope="row">{{ $loop->iteration + ($users->firstItem() - 1) }}</th>
                            <td>{{ $user->name }}</td>
                            <td>{{ $user->email }}</td>
                            <td>
                                <span class="badge {{ $user->role === 'superadmin' ? 'bg-dark' : ($user->role === 'org_admin' ? 'bg-danger' : ($user->role === 'manager' ? 'bg-warning text-dark' : 'bg-success')) }}">
                                    {{ ucfirst(str_replace('_', ' ', $user->role)) }}
                                </span>
                            </td>
                            @if(auth()->user()->role === 'superadmin')
                                <td>
                                    @if($user->organisation)
                                        <span class="badge bg-secondary">{{ $user->organisation->name }}</span>
                                    @else
                                        <span class="badge bg-dark">Global System</span>
                                    @endif
                                </td>
                            @endif
                            <td>{{ $user->position ?: '-' }}</td>
                            <td>{{ $user->created_at->format('Y-m-d H:i') }}</td>
                            <td>
                                <a href="{{ route('users.show', $user) }}" class="btn btn-sm btn-info">Details</a>
                                @if(auth()->user()->role === 'superadmin' && auth()->id() !== $user->id && $user->role !== 'superadmin')
                                    <form action="{{ route('users.impersonate', $user) }}" method="POST" class="d-inline">
                                        @csrf
                                        <button type="submit" class="btn btn-sm btn-warning text-dark">Simulate</button>
                                    </form>
                                @endif
                                @if(auth()->id() !== $user->id)
                                    <button 
                                        type="button" 
                                        wire:click="deleteUser({{ $user->id }})" 
                                        wire:confirm="Are you sure you want to delete this user?" 
                                        class="btn btn-sm btn-danger">
                                        Delete
                                    </button>
                                @endif
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="{{ auth()->user()->role === 'superadmin' ? 8 : 7 }}" class="text-center text-muted py-4">No users found matching your search.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        
        <div class="d-flex justify-content-center mt-3">
            {{ $users->links() }}
        </div>
    </div>
</div>

<!-- Contact Support Modal -->
<div class="modal fade" id="contactSupportModal" tabindex="-1" aria-labelledby="contactSupportModalLabel" aria-hidden="true" wire:ignore.self>
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content" style="background-color: var(--surface-glass); border-color: var(--border-app) !important; border-radius: 16px; backdrop-filter: blur(10px);">
            <div class="modal-header border-bottom-0">
                <h5 class="modal-title font-weight-bold" id="contactSupportModalLabel" style="color: var(--text-primary); font-family: 'Outfit', sans-serif;">
                    <i class="bi bi-headset text-primary me-2"></i> Contact Support
                </h5>
                <button type="button" class="btn-close btn-close-white text-white" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            
            <!-- Step 1: Warning Message -->
            <div id="modal-step-warning">
                <div class="modal-body text-center py-4">
                    <div class="mb-3">
                        <i class="bi bi-exclamation-triangle text-warning" style="font-size: 4rem;"></i>
                    </div>
                    <h5 class="font-weight-bold mb-3" style="color: var(--text-primary); font-family: 'Outfit', sans-serif;">Seats Limit Reached</h5>
                    <p class="text-secondary px-3" style="font-size: 0.95rem; line-height: 1.6;">
                        Your organisation has reached its maximum user seats capacity. In order to add more users, you must upgrade your subscription plan.
                    </p>
                </div>
                <div class="modal-footer border-top-0 d-flex justify-content-end gap-2">
                    <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">Close</button>
                    <button type="button" class="btn btn-primary px-4" onclick="showSupportForm()">Contact Support</button>
                </div>
            </div>

            <!-- Step 2: Inquiry Form -->
            <div id="modal-step-form" style="display: none;">
                <form action="{{ route('support.inquiry') }}" method="POST">
                    @csrf
                    <div class="modal-body">
                        <!-- Inquiry Type -->
                        <div class="mb-3">
                            <label for="inquiry_type" class="form-label" style="color: var(--text-secondary);">Inquiry Type</label>
                            <select name="inquiry_type" id="inquiry_type" class="form-select border-primary" required>
                                <option value="upgrade_package" selected>Upgrade Package (Seats Limit Reached)</option>
                                <option value="bug_report">Bug Found</option>
                                <option value="enhancement">Enhancement Request</option>
                                <option value="general_idea">General Idea</option>
                                <option value="other">Other Inquiry</option>
                            </select>
                        </div>

                        <!-- Subject -->
                        <div class="mb-3">
                            <label for="subject" class="form-label" style="color: var(--text-secondary);">Subject</label>
                            <input type="text" name="subject" id="subject" class="form-control" value="Request to Upgrade Seats Limit" required>
                        </div>

                        <!-- Message -->
                        <div class="mb-3">
                            <label for="message" class="form-label" style="color: var(--text-secondary);">Message Details</label>
                            <textarea name="message" id="message" rows="4" class="form-control" placeholder="Describe your request or details here..." required></textarea>
                        </div>
                    </div>
                    
                    <div class="modal-footer border-top-0 d-flex justify-content-end gap-2">
                        <button type="button" class="btn btn-outline-secondary" onclick="showWarningStep()">Back</button>
                        <button type="submit" class="btn btn-primary px-4">Submit Inquiry</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

<script>
    function showSupportForm() {
        document.getElementById('modal-step-warning').style.display = 'none';
        document.getElementById('modal-step-form').style.display = 'block';
    }

    function showWarningStep() {
        document.getElementById('modal-step-warning').style.display = 'block';
        document.getElementById('modal-step-form').style.display = 'none';
    }

    document.addEventListener('DOMContentLoaded', function () {
        const typeSelect = document.getElementById('inquiry_type');
        const subjectInput = document.getElementById('subject');
        
        if (typeSelect && subjectInput) {
            typeSelect.addEventListener('change', function () {
                if (typeSelect.value === 'upgrade_package') {
                    subjectInput.value = 'Request to Upgrade Seats Limit';
                } else if (typeSelect.value === 'bug_report') {
                    subjectInput.value = 'Bug Report: [Title]';
                } else if (typeSelect.value === 'enhancement') {
                    subjectInput.value = 'Enhancement Request: [Title]';
                } else if (typeSelect.value === 'general_idea') {
                    subjectInput.value = 'New Idea: [Title]';
                } else {
                    subjectInput.value = '';
                }
            });
        }

        // Reset modal view back to warning screen on close
        const modalEl = document.getElementById('contactSupportModal');
        if (modalEl) {
            modalEl.addEventListener('hidden.bs.modal', function () {
                showWarningStep();
            });
        }
    });
</script>
</div>
