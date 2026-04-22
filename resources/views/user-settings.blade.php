@extends('layouts.app')

@section('title', 'User Settings')
@section('page_title', 'User Settings')

@section('styles')
    <link rel="stylesheet" href="{{ asset('assets/css/user-settings.css') }}">
@endsection

@section('content')
<div class="settings-shell">
    @if($canViewAllProfiles)
        <section class="panel-card admin-switcher-panel mb-4">
            <div class="panel-head">
                <div>
                    <p class="panel-kicker">Admin View</p>
                    <h4>Browse employee profile data</h4>
                </div>
                <span class="panel-note">Select any HR, manager, or employee record to review or update it from the same page.</span>
            </div>

            <form method="GET" action="{{ route('user-settings') }}" class="row g-3 align-items-end">
                <div class="col-lg-8">
                    <label class="form-label">Choose User</label>
                    <select name="user_id" class="form-select">
                        @foreach($allUsers as $profileUser)
                            <option value="{{ $profileUser->id }}" {{ $profileUser->id == $user->id ? 'selected' : '' }}>
                                {{ $profileUser->name }} ({{ $profileUser->role }}) - {{ $profileUser->email }}
                            </option>
                        @endforeach
                    </select>
                </div>
                <div class="col-lg-4">
                    <button type="submit" class="btn save-btn w-100">
                        <i class="bi bi-search"></i>
                        Load Profile
                    </button>
                </div>
            </form>
        </section>
    @endif

    <section class="settings-hero">
        <div class="hero-copy">
            <p class="eyebrow">Employee Workspace</p>
            <h3>Keep your profile, role details, and documents organized.</h3>
            <p>Update your employee information in one place so HR records, identity files, and salary-related details stay aligned.</p>
        </div>

        <div class="hero-profile-card">
            <div class="hero-avatar">
                @if($user->photo)
                    <img src="{{ asset('storage/' . $user->photo) }}" alt="Profile Photo">
                @else
                    <span>{{ strtoupper(substr($user->name ?? 'U', 0, 1)) }}</span>
                @endif
            </div>
            <div>
                <span class="meta-label">Signed in as</span>
                <h4>{{ $user->name ?? 'User' }}</h4>
                <p>{{ $user->email ?? '-' }}</p>
                <span class="role-chip">{{ $user->role ?? 'Employee' }}</span>
            </div>
        </div>
    </section>

    @if(session('success'))
        <div class="alert alert-success settings-alert">{{ session('success') }}</div>
    @endif

    @if($errors->any())
        <div class="alert alert-danger settings-alert">
            {{ $errors->first() }}
        </div>
    @endif

    <section class="settings-metrics">
        <article class="metric-card">
            <div class="metric-icon"><i class="bi bi-person-vcard"></i></div>
            <div>
                <span class="meta-label">Employee ID</span>
                <h4>{{ $user->employee_id ?: 'Pending' }}</h4>
            </div>
        </article>
        <article class="metric-card">
            <div class="metric-icon"><i class="bi bi-building"></i></div>
            <div>
                <span class="meta-label">Department</span>
                <h4>{{ $user->department ?: 'Not set' }}</h4>
            </div>
        </article>
        <article class="metric-card">
            <div class="metric-icon"><i class="bi bi-wallet2"></i></div>
            <div>
                <span class="meta-label">Basic Salary</span>
                <h4>{{ $user->basic_salary ? 'Rs. ' . number_format($user->basic_salary, 2) : 'Not set' }}</h4>
            </div>
        </article>
    </section>

    <div class="settings-grid">
        <section class="panel-card form-panel">
            <div class="panel-head">
                <div>
                    
                    <h4>Employee profile details</h4>
                </div>
                
            </div>

            <form method="POST" action="{{ route('user-settings.update') }}" enctype="multipart/form-data" class="settings-form">
                @csrf
                @if($canViewAllProfiles)
                    <input type="hidden" name="target_user_id" value="{{ $user->id }}">
                @endif

                <div class="form-section">
                    <div class="section-title">
                        <h5>Personal Information</h5>
                        
                    </div>

                    <div class="row g-3">
                        <div class="col-md-6">
                            <label class="form-label">Full Name</label>
                            <input type="text" name="name" class="form-control"
                                value="{{ old('name', $user->name) }}" required>
                        </div>

                        <div class="col-md-6">
                            <label class="form-label">Email</label>
                            <input type="email" name="email" class="form-control"
                                value="{{ old('email', $user->email) }}" readonly>
                        </div>

                        <div class="col-md-6">
                            <label class="form-label">Phone</label>
                            <input type="text" name="phone" class="form-control"
                                value="{{ old('phone', $user->phone) }}">
                        </div>

                        <div class="col-md-6">
                            <label class="form-label">Date of Birth</label>
                            <input type="date" name="dob" class="form-control"
                                value="{{ old('dob', $user->dob) }}">
                        </div>

                        <div class="col-12">
                            <label class="form-label">Address</label>
                            <textarea name="address" class="form-control form-textarea" rows="4">{{ old('address', $user->address) }}</textarea>
                        </div>
                    </div>
                </div>

                <div class="form-section">
                    <div class="section-title">
                        <h5>Work Details</h5>
                        
                    </div>

                    <div class="row g-3">
                        <div class="col-md-6">
                            <label class="form-label">Department</label>
                            <input type="text" name="department" class="form-control"
                                value="{{ old('department', $user->department) }}">
                        </div>

                        <div class="col-md-6">
                            <label class="form-label">Designation</label>
                            <input type="text" name="designation" class="form-control"
                                value="{{ old('designation', $user->designation) }}">
                        </div>

                        <div class="col-md-6">
                            <label class="form-label">Employee ID</label>
                            <input type="text" name="employee_id" class="form-control"
                                value="{{ old('employee_id', $user->employee_id) }}">
                        </div>

                        <div class="col-md-6">
                            <label class="form-label">Basic Salary</label>
                            <input type="number" name="basic_salary" class="form-control"
                                value="{{ old('basic_salary', $user->basic_salary) }}">
                        </div>
                    </div>
                </div>

                <div class="form-section">
                    <div class="section-title">
                        <h5>Upload Documents</h5>
                        <p>Add the latest profile photo and official supporting files.</p>
                    </div>

                    <div class="document-grid">
                        <label class="document-upload-card">
                            <span class="document-icon"><i class="bi bi-image"></i></span>
                            <strong>Profile Photo</strong>
                            <small>JPG or PNG up to 2MB</small>
                            <input type="file" name="photo" class="form-control">
                        </label>

                        <label class="document-upload-card">
                            <span class="document-icon"><i class="bi bi-file-earmark-text"></i></span>
                            <strong>Resume</strong>
                            <small>PDF, DOC, or DOCX up to 4MB</small>
                            <input type="file" name="resume" class="form-control">
                        </label>

                        <label class="document-upload-card">
                            <span class="document-icon"><i class="bi bi-shield-check"></i></span>
                            <strong>Aadhaar</strong>
                            <small>PDF or image up to 4MB</small>
                            <input type="file" name="aadhaar" class="form-control">
                        </label>
                    </div>
                </div>

                <div class="form-actions">
                    <button type="submit" class="btn save-btn">
                        <i class="bi bi-floppy"></i>
                        Save Details
                    </button>
                </div>
            </form>
        </section>

        <aside class="panel-card summary-panel">
            <div class="panel-head">
                <div>
                    <p class="panel-kicker">Saved Profile</p>
                    
                </div>
                <span class="panel-note">A quick summary of the information currently saved in the system.</span>
            </div>

            <div class="summary-list">
                <div class="summary-item"><span>Name</span><strong>{{ $user->name ?? '-' }}</strong></div>
                <div class="summary-item"><span>Email</span><strong>{{ $user->email ?? '-' }}</strong></div>
                <div class="summary-item"><span>Phone</span><strong>{{ $user->phone ?? '-' }}</strong></div>
                <div class="summary-item"><span>Date of Birth</span><strong>{{ $user->dob ?? '-' }}</strong></div>
                <div class="summary-item"><span>Address</span><strong>{{ $user->address ?? '-' }}</strong></div>
                <div class="summary-item"><span>Department</span><strong>{{ $user->department ?? '-' }}</strong></div>
                <div class="summary-item"><span>Designation</span><strong>{{ $user->designation ?? '-' }}</strong></div>
                <div class="summary-item"><span>Employee ID</span><strong>{{ $user->employee_id ?? '-' }}</strong></div>
                <div class="summary-item"><span>Basic Salary</span><strong>{{ $user->basic_salary ? 'Rs. ' . number_format($user->basic_salary, 2) : '-' }}</strong></div>
            </div>

            <div class="document-status-grid">
                <div class="document-status-card">
                    <span class="meta-label">Profile Photo</span>
                    @if($user->photo)
                        <img src="{{ asset('storage/' . $user->photo) }}" alt="Profile Photo" class="profile-preview">
                    @else
                        <p>Not uploaded</p>
                    @endif
                </div>

                <div class="document-status-card">
                    <span class="meta-label">Resume</span>
                    @if($user->resume)
                        <a href="{{ asset('storage/' . $user->resume) }}" target="_blank" class="file-link">View Resume</a>
                    @else
                        <p>Not uploaded</p>
                    @endif
                </div>

                <div class="document-status-card">
                    <span class="meta-label">Aadhaar</span>
                    @if($user->aadhaar)
                        <a href="{{ asset('storage/' . $user->aadhaar) }}" target="_blank" class="file-link">View Aadhaar</a>
                    @else
                        <p>Not uploaded</p>
                    @endif
                </div>
            </div>
        </aside>
    </div>
</div>
@endsection
