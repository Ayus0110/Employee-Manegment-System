<!DOCTYPE html>
<html lang="en">
<head>
    <link rel="icon" href="{{ asset('/assets/images/browser_icone.png') }}">
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title', 'EMS')</title>

    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.css" rel="stylesheet">
    <link rel="stylesheet" href="{{ asset('assets/css/app.css') }}">

    @yield('styles')
</head>
<body>

@php
    $role = strtolower(auth()->user()->role ?? '');
    $notifications = auth()->check() ? auth()->user()->notifications()->latest()->take(8)->get() : collect();
    $unreadCount = auth()->check() ? auth()->user()->unreadNotifications()->count() : 0;
    $currentUser = auth()->user();
    $profilePhoto = $currentUser && $currentUser->photo ? asset('storage/' . $currentUser->photo) : null;
    $profileInitial = strtoupper(substr($currentUser->name ?? 'U', 0, 1));
@endphp

<div class="sidebar-backdrop" id="sidebarBackdrop"></div>

<div class="sidebar" id="sidebar">
    <h2>EMS</h2>

    <a href="{{ route('dashboard') }}" class="{{ request()->routeIs('dashboard') ? 'active' : '' }}">
        <i class="bi bi-speedometer2"></i> Dashboard
    </a>

    @if($role == 'admin')
        <a href="{{ route('manage-user') }}" class="{{ request()->routeIs('manage-user') ? 'active' : '' }}">
            <i class="bi bi-people"></i> Manage Users
        </a>
    @endif

    <a href="{{ route('user-settings') }}" class="{{ request()->routeIs('user-settings') ? 'active' : '' }}">
        <i class="bi bi-person-gear"></i> User Setting
    </a>

    @if(in_array($role, ['admin', 'hr']))
        <a href="{{ route('employee-details') }}" class="{{ request()->routeIs('employee-details') ? 'active' : '' }}">
            <i class="bi bi-person-badge"></i> Manage Employees
        </a>
    @endif

    @if($role === 'admin')
        <a href="{{ route('departments') }}" class="{{ request()->routeIs('departments') ? 'active' : '' }}">
            <i class="bi bi-building"></i> Departments
        </a>
    @endif

    <a href="{{ in_array($role, ['admin','hr','manager']) ? route('attendance-admin') : route('attendance-user') }}"
       class="{{ request()->routeIs('attendance-admin') || request()->routeIs('attendance-user') ? 'active' : '' }}">
        <i class="bi bi-calendar-check"></i> Attendance
    </a>

    <a href="{{ in_array($role, ['admin','hr','manager']) ? route('leave-admin') : route('leave-user') }}"
       class="{{ request()->routeIs('leave-admin') || request()->routeIs('leave-user') ? 'active' : '' }}">
        <i class="bi bi-envelope"></i> Leave Management
    </a>

    <a href="{{ in_array($role, ['admin','hr','manager']) ? route('salary-admin') : route('salary-user') }}"
       class="{{ request()->routeIs('salary-admin') || request()->routeIs('salary-user') ? 'active' : '' }}">
        <i class="bi bi-cash-stack"></i> Salary Management
    </a>

    <a href="{{ route('tasks') }}" class="{{ request()->routeIs('tasks') ? 'active' : '' }}">
        <i class="bi bi-list-task"></i> Task Assignment
    </a>

    <a href="{{ route('password.change.form') }}" class="{{ request()->routeIs('password.change.form') ? 'active' : '' }}">
        <i class="bi bi-shield-lock"></i> Change Password
    </a>

    <form action="{{ route('logout') }}" method="POST">
        @csrf
        <button type="submit">
            <i class="bi bi-box-arrow-right"></i> Logout
        </button>
    </form>
</div>

<div class="main-content">
    <div class="topbar d-flex justify-content-between align-items-center">
        <div class="topbar-main">
            <button type="button" class="mobile-menu-btn" id="mobileMenuToggle" aria-label="Toggle menu">
                <i class="bi bi-list"></i>
            </button>
            <h2 class="mb-0">@yield('page_title', 'Dashboard')</h2>
        </div>
        <div class="topbar-right">
            <div class="notification-dropdown">
                <button type="button" class="notification-toggle" id="notificationToggle">
                    <i class="bi bi-bell"></i>
                    @if($unreadCount > 0)
                        <span class="notification-count">{{ $unreadCount > 9 ? '9+' : $unreadCount }}</span>
                    @endif
                </button>

                <div class="notification-menu" id="notificationMenu">
                    <div class="notification-head">
                        <h6>Notifications</h6>
                        @if($notifications->count())
                            <form action="{{ route('notifications.read-all') }}" method="POST">
                                @csrf
                                <button type="submit">Mark all read</button>
                            </form>
                        @endif
                    </div>

                    @forelse($notifications as $notification)
                        <form action="{{ route('notifications.read', $notification->id) }}" method="POST" class="notification-item-form">
                            @csrf
                            <button type="submit" class="notification-item-btn">
                                <span class="notification-item {{ is_null($notification->read_at) ? 'unread' : '' }}">
                                <strong>{{ $notification->data['title'] ?? 'Notification' }}</strong>
                                <p>{{ $notification->data['message'] ?? '' }}</p>
                                <small>{{ $notification->created_at->diffForHumans() }}</small>
                                </span>
                            </button>
                        </form>
                    @empty
                        <div class="notification-empty">
                            No notifications yet.
                        </div>
                    @endforelse
                </div>
            </div>

            <div class="profile-dropdown">
                <button type="button" class="profile-toggle" id="profileToggle">
                    <span class="profile-avatar">
                        @if($profilePhoto)
                            <img src="{{ $profilePhoto }}" alt="{{ $currentUser->name }}">
                        @else
                            {{ $profileInitial }}
                        @endif
                    </span>
                    <span class="profile-text">
                        <strong>{{ $currentUser->name ?? 'Admin' }}</strong>
                        <span>{{ $currentUser->role ?? 'Admin' }}</span>
                    </span>
                    <i class="bi bi-chevron-down"></i>
                </button>

                <div class="profile-menu" id="profileMenu">
                    <div class="profile-card-head">
                        <span class="profile-avatar-lg">
                            @if($profilePhoto)
                                <img src="{{ $profilePhoto }}" alt="{{ $currentUser->name }}">
                            @else
                                {{ $profileInitial }}
                            @endif
                        </span>
                        <div>
                            <h6>{{ $currentUser->name ?? 'Admin' }}</h6>
                            <p>{{ $currentUser->email ?? '-' }}</p>
                        </div>
                    </div>

                    <div class="profile-details">
                        <div class="profile-detail">
                            <span>Role</span>
                            <strong>{{ $currentUser->role ?? '-' }}</strong>
                        </div>
                        <div class="profile-detail">
                            <span>Phone</span>
                            <strong>{{ $currentUser->phone ?? '-' }}</strong>
                        </div>
                        <div class="profile-detail">
                            <span>Department</span>
                            <strong>{{ $currentUser->department ?? '-' }}</strong>
                        </div>
                        <div class="profile-detail">
                            <span>Designation</span>
                            <strong>{{ $currentUser->designation ?? '-' }}</strong>
                        </div>
                        <div class="profile-detail">
                            <span>Employee ID</span>
                            <strong>{{ $currentUser->employee_id ?? '-' }}</strong>
                        </div>
                    </div>

                    <div class="profile-actions">
                        <a href="{{ route('user-settings') }}" class="profile-primary-link">View Profile</a>
                        <a href="{{ route('password.change.form') }}" class="profile-secondary-link">Change Password</a>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="content-box">
        @yield('content')
    </div>
</div>

@yield('scripts')
<script src="{{ asset('assets/js/app-layout.js') }}"></script>
</body>
</html>
