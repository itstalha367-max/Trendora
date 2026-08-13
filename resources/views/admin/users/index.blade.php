@extends('layouts.admin')

@section('title', 'Manage Users')

@section('content')
<style>
    .page-header { animation: slideDown 0.5s ease-out; }
    @keyframes slideDown {
        from { opacity: 0; transform: translateY(-20px); }
        to { opacity: 1; transform: translateY(0); }
    }
    .user-card {
        transition: all 0.3s ease;
        border-radius: 16px;
        border: 1px solid rgba(0,0,0,0.04);
    }
    .user-card:hover {
        transform: translateY(-4px);
        box-shadow: 0 8px 30px rgba(0,0,0,0.08);
    }
    .avatar {
        width: 40px;
        height: 40px;
        border-radius: 50%;
        display: flex;
        align-items: center;
        justify-content: center;
        font-weight: 700;
        font-size: 18px;
        color: #fff;
    }
</style>

<div class="page-header d-flex justify-content-between align-items-center mb-4">
    <div>
        <h2 class="fw-bold mb-0"><i class="fas fa-users me-2 text-primary"></i>Manage Users</h2>
        <p class="text-muted mb-0">View and manage all registered users</p>
    </div>
    <div>
        <span class="badge bg-primary rounded-pill px-3 py-2">
            <i class="fas fa-users me-1"></i>Total: {{ App\Models\User::count() }}
        </span>
    </div>
</div>

@if(session('success'))
    <div class="alert alert-success alert-dismissible fade show border-0 rounded-4">
        <i class="fas fa-check-circle me-2"></i>{{ session('success') }}
        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
    </div>
@endif

@if(session('error'))
    <div class="alert alert-danger alert-dismissible fade show border-0 rounded-4">
        <i class="fas fa-exclamation-circle me-2"></i>{{ session('error') }}
        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
    </div>
@endif

<div class="card border-0 shadow-sm rounded-4">
    <div class="card-body p-0">
        <div style="overflow-x: auto;">
            <div class="table-responsive"><table class="table table-hover mb-0">
                <thead style="background: #0f141e;">
                    <tr>
                        <th style="padding: 15px 20px;">ID</th>
                        <th style="padding: 15px 20px;">User</th>
                        <th style="padding: 15px 20px;">Email</th>
                        <th style="padding: 15px 20px;">Role</th>
                        <th style="padding: 15px 20px;">Orders</th>
                        <th style="padding: 15px 20px;">Joined</th>
                        <th style="padding: 15px 20px; text-align: center;">Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($users as $user)
                    <tr>
                        <td style="padding: 15px 20px;">#{{ $user->id }}</td>
                        <td style="padding: 15px 20px;">
                            <div class="d-flex align-items-center">
                                <div class="avatar me-2" style="background: linear-gradient(135deg, #8b5cf6, #5b7cff);">
                                    {{ substr($user->name, 0, 1) }}
                                </div>
                                <div>
                                    <div class="fw-bold">{{ $user->name }}</div>
                                    @if($user->id === auth()->id())
                                        <span class="badge bg-info text-white">You</span>
                                    @endif
                                </div>
                            </div>
                        </td>
                        <td style="padding: 15px 20px;">{{ $user->email }}</td>
                        <td style="padding: 15px 20px;">
                            <form action="{{ route('admin.users.role', $user->id) }}" method="POST" class="d-inline">
                                @csrf
                                @method('PUT')
                                <select name="role" class="form-control form-control-sm rounded-pill" onchange="this.form.submit()" style="width: auto; display: inline-block; {{ $user->id === auth()->id() ? 'cursor: not-allowed;' : '' }}" {{ $user->id === auth()->id() ? 'disabled' : '' }}>
                                    <option value="user" {{ $user->role == 'user' ? 'selected' : '' }}>User</option>
                                    <option value="admin" {{ $user->role == 'admin' ? 'selected' : '' }}>Admin</option>
                                    <option value="vendor" {{ $user->role == 'vendor' ? 'selected' : '' }}>Vendor</option>
                                </select>
                            </form>
                        </td>
                        <td style="padding: 15px 20px;">
                            <span class="badge bg-primary rounded-pill px-3 py-2">{{ $user->orders->count() }}</span>
                        </td>
                        <td style="padding: 15px 20px;">
                            {{ $user->created_at->format('d M Y') }}
                        </td>
                        <td style="padding: 15px 20px; text-align: center;">
                            <div class="d-flex gap-1 justify-content-center">
                                <a href="{{ route('admin.users.show', $user->id) }}" class="btn btn-sm btn-primary rounded-3" style="width: 32px; height: 32px; display: inline-flex; align-items: center; justify-content: center;">
                                    <i class="fas fa-eye"></i>
                                </a>
                                @if($user->id !== auth()->id())
                                    <form action="{{ route('admin.users.destroy', $user->id) }}" method="POST" class="d-inline">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="btn btn-sm btn-danger rounded-3" style="width: 32px; height: 32px; display: inline-flex; align-items: center; justify-content: center;" onclick="return confirm('Are you sure you want to delete this user?')">
                                            <i class="fas fa-trash"></i>
                                        </button>
                                    </form>
                                @endif
                            </div>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="7" class="text-center py-5">
                            <i class="fas fa-users fa-3x d-block mb-3 text-muted" style="opacity: 0.2;"></i>
                            <h5 class="text-muted">No users found</h5>
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table></div>
        </div>
        @if($users->hasPages())
        <div class="p-3 border-top">{{ $users->links() }}</div>
        @endif
    </div>
</div>
@endsection