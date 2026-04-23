@extends('layouts.app')
@section('title', 'Manage Users')

@section('content')
<div class="section-header">
    <h3 class="section-title">All Users</h3>
    <a href="{{ route('admin.users.create') }}" class="btn btn-primary"><i class="fas fa-plus"></i> Create User</a>
</div>

<div class="card">
    <div class="table-container">
        <table class="table">
            <thead>
                <tr>
                    <th>Name</th>
                    <th>Email</th>
                    <th>Role</th>
                    <th>Status</th>
                    <th>Created At</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                @foreach($users as $user)
                <tr>
                    <td>{{ $user->name }}</td>
                    <td>{{ $user->email }}</td>
                    <td>
                        <span class="badge-status badge-{{ $user->role?->slug ?? 'default' }}">
                            {{ $user->role?->name ?? 'N/A' }}
                        </span>
                    </td>
                    <td>
                        <span class="badge-status {{ $user->is_active ? 'badge-published' : 'badge-cancelled' }}">
                            {{ $user->is_active ? 'Active' : 'Inactive' }}
                        </span>
                    </td>
                    <td>{{ $user->created_at->format('d M Y') }}</td>
                    <td>
                        <div style="display:flex;gap:0.5rem;">
                            <a href="{{ route('admin.users.edit', $user) }}" class="btn btn-outline btn-sm"><i class="fas fa-edit"></i></a>
                            <form action="{{ route('admin.users.toggleStatus', $user) }}" method="POST">
                                @csrf
                                @method('PATCH')
                                <button type="submit" class="btn {{ $user->is_active ? 'btn-danger' : 'btn-success' }} btn-sm" title="{{ $user->is_active ? 'Deactivate' : 'Activate' }}">
                                    <i class="fas {{ $user->is_active ? 'fa-user-slash' : 'fa-user-check' }}"></i>
                                </button>
                            </form>
                        </div>
                    </td>
                </tr>
                @endforeach
            </tbody>
        </table>
    </div>
    <div class="pagination-wrapper">{{ $users->links() }}</div>
</div>
@endsection
