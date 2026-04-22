<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="icon" href="{{ asset('/assets/images/browser_icone.png') }}">
    <title>EMS</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.css" rel="stylesheet">
    <link rel="stylesheet" href="{{ asset('assets/css/style.css') }}">
</head>

<body class="landing-body">
    <div class="landing-overlay"></div>

    <header class="landing-header">
        <a href="{{ route('home') }}" class="brand-block">
            <img src="{{ asset('/assets/images/ems_project_icone.png') }}" alt="EMS Logo" class="brand-logo">
            <div>
                
                <strong>Employee Management System</strong>
            </div>
        </a>

        <nav class="landing-nav">
            <a href="{{ route('login') }}" class="nav-link ghost-link">Staff Login</a>
            <a href="{{ route('login') }}" class="nav-link primary-link">Get Started</a>
        </nav>
    </header>

    <main class="landing-shell">
        <section class="hero-card">
            <div class="hero-copy">
                <span class="hero-badge">Smart Workforce Operations</span>
                <h1>Manage your people, payroll, attendance, and approvals in one EMS workspace.</h1>
                <p>Built for admins, HR teams, managers, and employees to stay aligned with cleaner records, faster approvals, and a dashboard that keeps daily work organized.</p>

                <div class="hero-actions">
                    <a href="{{ route('login') }}" class="hero-btn primary-btn">
                        <i class="bi bi-box-arrow-in-right"></i>
                        Login to EMS
                    </a>
                    <a href="{{ route('login') }}" class="hero-btn secondary-btn">
                        <i class="bi bi-grid"></i>
                        Open Dashboard
                    </a>
                </div>

                <div class="hero-stats">
                    <article>
                        <strong>Attendance</strong>
                        <span>Track daily presence and monthly records.</span>
                    </article>
                    <article>
                        <strong>Payroll</strong>
                        <span>Manage salary, slips, and payment status.</span>
                    </article>
                    <article>
                        <strong>Leave</strong>
                        <span>Review requests and approvals in one flow.</span>
                    </article>
                </div>
            </div>

            <aside class="hero-panel">
                <div class="panel-top">
                    <span class="panel-chip">Admin Ready</span>
                    <span class="panel-chip soft-chip">HR Friendly</span>
                </div>

                <div class="workflow-list">
                    <div class="workflow-item">
                        <i class="bi bi-people-fill"></i>
                        <div>
                            <strong>People Management</strong>
                            <p>Create users, manage roles, and organize departments.</p>
                        </div>
                    </div>
                    <div class="workflow-item">
                        <i class="bi bi-calendar2-check-fill"></i>
                        <div>
                            <strong>Attendance Control</strong>
                            <p>Mark attendance, review records, and monitor workforce status.</p>
                        </div>
                    </div>
                    <div class="workflow-item">
                        <i class="bi bi-cash-stack"></i>
                        <div>
                            <strong>Payroll Flow</strong>
                            <p>Process salaries, view slips, and track salary status clearly.</p>
                        </div>
                    </div>
                </div>
            </aside>
        </section>

        <section class="feature-grid">
            <article class="feature-card">
                <span class="feature-icon"><i class="bi bi-speedometer2"></i></span>
                <h3>Central Dashboard</h3>
                <p>Monitor employees, departments, users, and schedules from one operational screen.</p>
            </article>
            <article class="feature-card">
                <span class="feature-icon"><i class="bi bi-person-workspace"></i></span>
                <h3>Role Based Access</h3>
                <p>Support admins, HR, managers, and employees with the right pages and actions.</p>
            </article>
            <article class="feature-card">
                <span class="feature-icon"><i class="bi bi-bell-fill"></i></span>
                <h3>Instant Updates</h3>
                <p>Notify staff for leave decisions and salary activity with a cleaner communication flow.</p>
            </article>
        </section>
    </main>
</body>

</html>
