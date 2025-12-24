<aside class="sidebar" style="overflow-y: auto;">
    <div class="brand">
        <a href="/erp" style="text-decoration: none; display: flex; align-items: center; justify-content: center; width: 100%;">
            <img src="/assets/casjoe_logo.png" alt="Casjoe Apps" style="height: 40px;">
        </a>
    </div>
    
    <style>
        .submenu { display: none; padding-left: 20px; background: #f9f9f9; }
        .submenu.open { display: block; }
        .nav-link { justify-content: space-between; cursor: pointer; color: #ffa600; font-weight: 500; transition: all 0.2s; border-left: 4px solid transparent; }
        .nav-link:hover { background: #ffe0b2; color: #e65100; } /* Darker Orange text on Light Orange bg */
        .nav-link ion-icon { color: #ffa600; transition: color 0.2s; }
        .nav-link:hover ion-icon { color: #e65100; }
        
        .nav-link ion-icon.arrow { transition: transform 0.2s; color: #ffa600; }
        .nav-link.active-parent ion-icon.arrow { transform: rotate(180deg); }
        .nav-link.active-parent { background: #fff3e0; font-weight: bold; border-left: 4px solid #ffa600; color: #e65100; }
        
        .submenu .nav-link { font-size: 0.9em; padding: 8px 15px; color: #555; font-weight: normal; border-left: none; }
        .submenu .nav-link ion-icon { color: #666; }
        .submenu .nav-link:hover { color: #e65100; background: #eee; }
        .submenu .nav-link:hover ion-icon { color: #e65100; }
        .submenu .nav-link.active { color: #d84315; font-weight: bold; background: #ffe0b2; } /* Active submenu item */
    </style>

    <ul class="nav-menu">
        <li class="nav-header">Main</li>
        <li class="nav-item">
            <a href="/dashboard" class="nav-link">
                <span><ion-icon name="grid-outline"></ion-icon> Dashboard</span>
            </a>
        </li>
        <li class="nav-item">
            <a href="/erp" class="nav-link <?= $_SERVER['REQUEST_URI'] == '/erp' ? 'active' : '' ?>">
                <span><ion-icon name="briefcase-outline"></ion-icon> ERP Overview</span>
            </a>
        </li>

        <?php
        $uri = $_SERVER['REQUEST_URI'];
        
        // HR Paths
        $hr_active = strpos($uri, '/erp/hr') !== false || 
                     strpos($uri, '/erp/employees') !== false || 
                     strpos($uri, '/erp/departments') !== false ||
                     strpos($uri, '/erp/attendance') !== false ||
                     strpos($uri, '/erp/leave') !== false ||
                     strpos($uri, '/erp/payroll') !== false ||
                     strpos($uri, '/erp/performance') !== false ||
                     strpos($uri, '/erp/recruitment') !== false ||
                     strpos($uri, '/erp/training') !== false ||
                     strpos($uri, '/erp/lifecycle') !== false ||
                     strpos($uri, '/erp/promotion') !== false ||
                     strpos($uri, '/erp/resignation') !== false ||
                     strpos($uri, '/erp/termination') !== false;

        // CRM Paths
        $crm_active = strpos($uri, '/erp/crm') !== false || 
                      strpos($uri, '/erp/leads') !== false ||
                      strpos($uri, '/erp/opportunities') !== false ||
                      strpos($uri, '/erp/sales') !== false ||
                      strpos($uri, '/erp/support') !== false;

        // Project Paths
        $proj_active = strpos($uri, '/erp/projects') !== false || 
                       strpos($uri, '/erp/tasks') !== false ||
                       strpos($uri, '/erp/calendar') !== false;

        // Finance Paths
        $fin_active = strpos($uri, '/erp/finance') !== false || 
                      strpos($uri, '/erp/inventory') !== false ||
                      strpos($uri, '/erp/assets') !== false ||
                      strpos($uri, '/erp/transactions') !== false;

        // System Paths
        $sys_active = strpos($uri, '/erp/settings') !== false || 
                      strpos($uri, '/erp/users') !== false ||
                      strpos($uri, '/erp/chat') !== false ||
                      strpos($uri, '/erp/announcements') !== false ||
                      strpos($uri, '/erp/activity') !== false ||
                      strpos($uri, '/erp/roles') !== false ||
                      strpos($uri, '/erp/permissions') !== false;
        
        $role = \App\Core\Auth::user()['role'] ?? '';
        $isAdmin = $role === 'admin';
        $isHr = ($role === 'hr' || $isAdmin);
        ?>

        <?php if ($isHr): ?>
        <!-- Human Resources -->
        <li class="nav-item">
            <div class="nav-link <?= $hr_active ? 'active-parent' : '' ?>" onclick="toggleMenu('hr-menu')">
                <span><ion-icon name="people-outline"></ion-icon> Human Resources</span>
                <ion-icon name="chevron-down-outline" class="arrow"></ion-icon>
            </div>
            <ul class="submenu <?= $hr_active ? 'open' : '' ?>" id="hr-menu">
                <li class="nav-item"><a href="/erp/departments" class="nav-link <?= strpos($uri, '/erp/departments') !== false ? 'active' : '' ?>">Departments</a></li>
                <li class="nav-item"><a href="/erp/hr" class="nav-link <?= (strpos($uri, '/erp/hr') !== false || strpos($uri, '/erp/employees') !== false)  ? 'active' : '' ?>">Employees</a></li>
                <li class="nav-item"><a href="/erp/attendance" class="nav-link <?= strpos($uri, '/erp/attendance') !== false ? 'active' : '' ?>">Attendance</a></li>
                <li class="nav-item"><a href="/erp/leave" class="nav-link <?= strpos($uri, '/erp/leave') !== false ? 'active' : '' ?>">Leave Mgmt</a></li>
                <li class="nav-item"><a href="/erp/payroll" class="nav-link <?= strpos($uri, '/erp/payroll') !== false ? 'active' : '' ?>">Payroll</a></li>
                <li class="nav-item"><a href="/erp/performance" class="nav-link <?= strpos($uri, '/erp/performance') !== false ? 'active' : '' ?>">Performance</a></li>
                <li class="nav-item"><a href="/erp/recruitment" class="nav-link <?= strpos($uri, '/erp/recruitment') !== false ? 'active' : '' ?>">Recruitment</a></li>
                <li class="nav-item"><a href="/erp/training" class="nav-link <?= strpos($uri, '/erp/training') !== false ? 'active' : '' ?>">Training</a></li>
                <li class="nav-item"><a href="/erp/lifecycle/promotion" class="nav-link <?= strpos($uri, '/erp/promotion') !== false ? 'active' : '' ?>">Promotion</a></li>
                <li class="nav-item"><a href="/erp/lifecycle/resignation" class="nav-link <?= strpos($uri, '/erp/resignation') !== false ? 'active' : '' ?>">Resignation</a></li>
                <li class="nav-item"><a href="/erp/lifecycle/termination" class="nav-link <?= strpos($uri, '/erp/termination') !== false ? 'active' : '' ?>">Termination</a></li>
            </ul>
        </li>
        <?php endif; ?>

        <?php if ($isAdmin): ?>
        <!-- CRM & Sales -->
        <li class="nav-item">
            <div class="nav-link <?= $crm_active ? 'active-parent' : '' ?>" onclick="toggleMenu('crm-menu')">
                <span><ion-icon name="people-circle-outline"></ion-icon> CRM & Sales</span>
                <ion-icon name="chevron-down-outline" class="arrow"></ion-icon>
            </div>
            <ul class="submenu <?= $crm_active ? 'open' : '' ?>" id="crm-menu">
                <li class="nav-item"><a href="/erp/crm/pipeline" class="nav-link <?= strpos($uri, '/erp/crm/pipeline') !== false ? 'active' : '' ?>">Pipeline</a></li>
                <li class="nav-item"><a href="/erp/crm" class="nav-link <?= $uri == '/erp/crm' ? 'active' : '' ?>">Clients</a></li>
                <li class="nav-item"><a href="/erp/crm/leads" class="nav-link <?= strpos($uri, '/erp/leads') !== false ? 'active' : '' ?>">Leads</a></li>
                <li class="nav-item"><a href="/erp/crm/opportunities" class="nav-link <?= strpos($uri, '/erp/opportunities') !== false ? 'active' : '' ?>">Opportunities</a></li>
                <li class="nav-item"><a href="/erp/crm/sales" class="nav-link <?= strpos($uri, '/erp/sales') !== false ? 'active' : '' ?>">Sales</a></li>
                <li class="nav-item"><a href="/erp/support" class="nav-link <?= strpos($uri, '/erp/support') !== false ? 'active' : '' ?>">Tickets</a></li>
            </ul>
        </li>
        <?php endif; ?>

        <!-- Projects -->
        <li class="nav-item">
            <div class="nav-link <?= $proj_active ? 'active-parent' : '' ?>" onclick="toggleMenu('project-menu')">
                <span><ion-icon name="folder-outline"></ion-icon> Projects</span>
                <ion-icon name="chevron-down-outline" class="arrow"></ion-icon>
            </div>
            <ul class="submenu <?= $proj_active ? 'open' : '' ?>" id="project-menu">
                <li class="nav-item"><a href="/erp/projects" class="nav-link <?= $uri == '/erp/projects' ? 'active' : '' ?>">Projects</a></li>
                <li class="nav-item"><a href="/erp/tasks" class="nav-link <?= strpos($uri, '/erp/tasks') !== false ? 'active' : '' ?>">Tasks/Board</a></li>
                <li class="nav-item"><a href="/erp/projects/timesheets" class="nav-link <?= strpos($uri, '/erp/projects/timesheets') !== false ? 'active' : '' ?>">Timesheets</a></li>
                <li class="nav-item"><a href="/erp/calendar" class="nav-link <?= strpos($uri, '/erp/calendar') !== false ? 'active' : '' ?>">Calendar</a></li>
            </ul>
        </li>
        
        <li class="nav-item">
            <a href="/erp/my-portal" class="nav-link <?= strpos($uri, '/erp/my-portal') !== false ? 'active-parent' : '' ?>">
                <span><ion-icon name="person-circle-outline"></ion-icon> My Portal</span>
            </a>
        </li>

        <?php if ($isAdmin): ?>
        <!-- Assets & Finance -->
        <li class="nav-item">
            <div class="nav-link <?= $fin_active ? 'active-parent' : '' ?>" onclick="toggleMenu('finance-menu')">
                <span><ion-icon name="wallet-outline"></ion-icon> Finance & Assets</span>
                <ion-icon name="chevron-down-outline" class="arrow"></ion-icon>
            </div>
            <ul class="submenu <?= $fin_active ? 'open' : '' ?>" id="finance-menu">
                <li class="nav-item"><a href="/erp/finance/estimates" class="nav-link <?= strpos($uri, '/erp/finance/estimates') !== false ? 'active' : '' ?>">Estimates</a></li>
                <li class="nav-item"><a href="/erp/finance/expenses" class="nav-link <?= strpos($uri, '/erp/finance/expenses') !== false ? 'active' : '' ?>">Expenses</a></li>
                <li class="nav-item"><a href="/erp/finance/vendors" class="nav-link <?= strpos($uri, '/erp/finance/vendors') !== false ? 'active' : '' ?>">Vendors</a></li>
                <li class="nav-item"><a href="/erp/inventory" class="nav-link <?= strpos($uri, '/erp/inventory') !== false ? 'active' : '' ?>">Stock/Inventory</a></li>
                <li class="nav-item"><a href="/erp/assets" class="nav-link <?= strpos($uri, '/erp/assets') !== false ? 'active' : '' ?>">Office Assets</a></li>
                <li class="nav-item"><a href="/erp/transactions" class="nav-link <?= strpos($uri, '/erp/transactions') !== false ? 'active' : '' ?>">Transactions</a></li>
                <li class="nav-item"><a href="/erp/finance/invoices" class="nav-link <?= strpos($uri, '/erp/finance/invoices') !== false ? 'active' : '' ?>">Invoices</a></li>
                <li class="nav-item"><a href="/erp/finance" class="nav-link <?= $uri == '/erp/finance' ? 'active' : '' ?>">Financial Reports</a></li>
            </ul>
        </li>

        <!-- System -->
        <li class="nav-item">
            <div class="nav-link <?= $sys_active ? 'active-parent' : '' ?>" onclick="toggleMenu('system-menu')">
                <span><ion-icon name="settings-outline"></ion-icon> System</span>
                <ion-icon name="chevron-down-outline" class="arrow"></ion-icon>
            </div>
            <ul class="submenu <?= $sys_active ? 'open' : '' ?>" id="system-menu">
                <li class="nav-item"><a href="/erp/chat" class="nav-link <?= strpos($uri, '/erp/chat') !== false ? 'active' : '' ?>">Team Chat</a></li>
                <li class="nav-item"><a href="/erp/announcements" class="nav-link <?= strpos($uri, '/erp/announcements') !== false ? 'active' : '' ?>">Announcements</a></li>
                <li class="nav-item"><a href="/erp/users" class="nav-link <?= strpos($uri, '/erp/users') !== false ? 'active' : '' ?>">Users</a></li>
                <li class="nav-item"><a href="/erp/settings" class="nav-link <?= strpos($uri, '/erp/settings') !== false ? 'active' : '' ?>">Settings</a></li>
                <li class="nav-item"><a href="/erp/activity" class="nav-link <?= strpos($uri, '/erp/activity') !== false ? 'active' : '' ?>">Activity Log</a></li>
            </ul>
        </li>
        <?php else: ?>
        <!-- Employee Only Links (If checking 'System' group needed for non-admins, e.g. Chat) -->
        <li class="nav-item">
             <a href="/erp/chat" class="nav-link <?= strpos($uri, '/erp/chat') !== false ? 'active' : '' ?>">
                <span><ion-icon name="chatbubbles-outline"></ion-icon> Team Chat</span>
            </a>
        </li>
        <?php endif; ?>
    </ul>

    <script>
        function toggleMenu(id) {
            var menu = document.getElementById(id);
            if (menu.classList.contains('open')) {
                menu.classList.remove('open');
            } else {
                menu.classList.add('open');
            }
        }
        // Auto-open logic is now handled by PHP server-side rendering for "open" class
    </script>
</aside>
