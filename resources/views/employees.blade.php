@extends('layouts.app')

@section('title', 'Employees')

@section('page-title', 'Employees')

@section('breadcrumb')
    <li class="breadcrumb-item"><a href="#">Home</a></li>
    <li class="breadcrumb-item active">Employees</li>
@endsection

@section('content')
    <div class="row">
        <div class="col-12">
            <!-- Filters Card -->
            <div class="card card-primary">
                <div class="card-header">
                    <h3 class="card-title">
                        <i class="fas fa-filter"></i> Filters
                    </h3>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-3">
                            <div class="form-group">
                                <label>Department</label>
                                <select class="form-control" id="filterDepartment">
                                    <option value="">All Departments</option>
                                </select>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="form-group">
                                <label>Search by Name</label>
                                <input type="text" class="form-control" id="filterName" placeholder="Employee name...">
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="form-group">
                                <label>Search by Email</label>
                                <input type="text" class="form-control" id="filterEmail" placeholder="Email...">
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="form-group">
                                <label>Salary Range</label>
                                <input type="text" id="salaryRangeSlider" class="form-control" readonly>
                                <div class="d-flex justify-content-between mt-2">
                                    <small class="text-muted">Min: <span id="salaryMinDisplay">0</span></small>
                                    <small class="text-muted">Max: <span id="salaryMaxDisplay">100,000</span></small>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-12">
                            <button type="button" class="btn btn-primary" id="applyFiltersBtn">
                                <span class="spinner-border spinner-border-sm d-none" role="status" id="filterSpinner"></span>
                                <i class="fas fa-search" id="filterIcon"></i>
                                <span id="filterText">Apply Filters</span>
                            </button>
                            <button type="button" class="btn btn-secondary" id="clearFiltersBtn">
                                <i class="fas fa-times"></i> Clear Filters
                            </button>
                        </div>
                    </div>
                </div>
            </div>

            <div class="card">
                <div class="card-header">
                    <h3 class="card-title">Employees List</h3>
                    <div class="card-tools" id="empActionButtons">
                        <button type="button" class="btn btn-primary btn-sm" data-toggle="modal" data-target="#createEmployeeModal" id="addEmpBtn" style="display: none;">
                            <i class="fas fa-plus"></i> Add Employee
                        </button>
                    </div>
                </div>
                <!-- /.card-header -->
                <div class="card-body table-responsive p-0">
                    <table class="table table-hover text-nowrap">
                        <thead>
                            <tr>
                                <th>ID</th>
                                <th>Name</th>
                                <th>Email</th>
                                <th>Department</th>
                                <th>Salary</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody id="employeesTableBody">
                            <tr>
                                <td colspan="6" class="text-center">
                                    <div class="spinner-border text-primary" role="status">
                                        <span class="sr-only">Loading...</span>
                                    </div>
                                </td>
                            </tr>
                        </tbody>
                    </table>
                </div>
                <!-- /.card-body -->
                <div class="card-footer clearfix">
                    <ul class="pagination pagination-sm m-0 float-right" id="pagination">
                    </ul>
                </div>
            </div>
            <!-- /.card -->
        </div>
    </div>

    <!-- Create Employee Modal -->
    <div class="modal fade" id="createEmployeeModal" tabindex="-1" role="dialog">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Create New Employee</h5>
                    <button type="button" class="close" data-dismiss="modal">
                        <span>&times;</span>
                    </button>
                </div>
                <form id="createEmployeeForm">
                    <div class="modal-body">
                        <div class="form-group">
                            <label for="employeeName">Name <span class="text-danger">*</span></label>
                            <input type="text" class="form-control" id="employeeName" name="name" required>
                            <div class="invalid-feedback"></div>
                        </div>
                        <div class="form-group">
                            <label for="employeeEmail">Email <span class="text-danger">*</span></label>
                            <input type="email" class="form-control" id="employeeEmail" name="email" required>
                            <div class="invalid-feedback"></div>
                        </div>
                        <div class="form-group">
                            <label for="employeeDepartment">Department <span class="text-danger">*</span></label>
                            <select class="form-control" id="employeeDepartment" name="department_id" required>
                                <option value="">Select Department</option>
                            </select>
                            <div class="invalid-feedback"></div>
                        </div>
                        <div class="form-group">
                            <label for="employeeSalary">Salary <span class="text-danger">*</span></label>
                            <input type="number" class="form-control" id="employeeSalary" name="salary" min="0" step="0.01" required>
                            <div class="invalid-feedback"></div>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-dismiss="modal" id="closeEmployeeBtn">Close</button>
                        <button type="submit" class="btn btn-primary" id="createEmployeeBtn">
                            <span class="spinner-border spinner-border-sm d-none" role="status" id="createEmpSpinner"></span>
                            <span id="createEmpText">Create Employee</span>
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
@endsection

@section('scripts')
<script>
    $(document).ready(function() {
        let currentPage = 1;
        let filters = {
            name: '',
            email: '',
            department_id: '',
            salary_range: ''
        };
        let salarySliderMin = 0;
        let salarySliderMax = 100000; // Default max, will be updated based on data

        // Get token from localStorage
        function getToken() {
            let token = localStorage.getItem('auth_token');
            if (!token) {
                window.location.href = '/login';
                return '';
            }
            return token;
        }

        // Initialize salary range slider
        function initializeSalarySlider() {
            // Calculate step based on max value (smaller step for smaller ranges)
            let step = salarySliderMax > 10000 ? 1000 : (salarySliderMax > 1000 ? 100 : 10);
            
            $('#salaryRangeSlider').ionRangeSlider({
                type: 'double',
                min: 0,
                max: salarySliderMax,
                from: 0,
                to: salarySliderMax,
                step: step,
                grid: true,
                grid_num: 5,
                onFinish: function(data) {
                    $('#salaryMinDisplay').text(data.from.toLocaleString());
                    $('#salaryMaxDisplay').text(data.to.toLocaleString());
                },
                onUpdate: function(data) {
                    $('#salaryMinDisplay').text(data.from.toLocaleString());
                    $('#salaryMaxDisplay').text(data.to.toLocaleString());
                }
            });
            // Set initial display values
            $('#salaryMinDisplay').text('0');
            $('#salaryMaxDisplay').text(salarySliderMax.toLocaleString());
        }

        // Fetch max salary from database
        function fetchMaxSalary() {
            $.ajax({
                url: '/api/employees/max-salary',
                type: 'GET',
                headers: {
                    'Authorization': 'Bearer ' + getToken(),
                    'Accept': 'application/json'
                },
                success: function(response) {
                    if (response.status && response.data && response.data.max_salary) {
                        let maxSalary = parseFloat(response.data.max_salary);
                        
                        // Set slider max to max_salary * 2 (e.g., if max is 600, slider max is 1200)
                        if (maxSalary > 0) {
                            salarySliderMax = maxSalary * 2;
                        } else {
                            // If no employees, use default
                            salarySliderMax = 100000;
                        }
                        
                        // Update slider if it's already initialized
                        let slider = $('#salaryRangeSlider').data('ionRangeSlider');
                        if (slider) {
                            slider.update({
                                min: 0,
                                max: salarySliderMax,
                                from: 0,
                                to: salarySliderMax
                            });
                            $('#salaryMinDisplay').text('0');
                            $('#salaryMaxDisplay').text(salarySliderMax.toLocaleString());
                        } else {
                            // Initialize slider with new max
                            initializeSalarySlider();
                        }
                    } else {
                        // If no max salary data, use default
                        salarySliderMax = 100000;
                        initializeSalarySlider();
                    }
                },
                error: function(xhr) {
                    console.error('Error loading max salary');
                    // Use default if API fails
                    salarySliderMax = 100000;
                    initializeSalarySlider();
                }
            });
        }

        // Fetch all departments for dropdowns
        function fetchAllDepartments() {
            let allDepartments = [];
            let currentPage = 1;
            let lastPage = 1;

            function fetchPage(page) {
                $.ajax({
                    url: '/api/departments?page=' + page,
                    type: 'GET',
                    headers: {
                        'Authorization': 'Bearer ' + getToken(),
                        'Accept': 'application/json'
                    },
                    success: function(response) {
                        if (response.status && response.data) {
                            allDepartments = allDepartments.concat(response.data);
                            lastPage = response.pagination.last_page;

                            if (page < lastPage) {
                                fetchPage(page + 1);
                            } else {
                                // All departments loaded, populate dropdowns
                                let html = '<option value="">Select Department</option>';
                                let filterHtml = '<option value="">All Departments</option>';
                                allDepartments.forEach(function(dept) {
                                    html += '<option value="' + dept.id + '">' + dept.name + '</option>';
                                    filterHtml += '<option value="' + dept.id + '">' + dept.name + '</option>';
                                });
                                $('#employeeDepartment').html(html);
                                $('#filterDepartment').html(filterHtml);
                            }
                        }
                    },
                    error: function(xhr) {
                        console.error('Error loading departments');
                    }
                });
            }

            fetchPage(1);
        }

        // Fetch employees with filters and update salary slider max
        function fetchEmployees(page = 1) {
            let url = '/api/employees?page=' + page;

            // Add filters to URL
            if (filters.name) {
                url += '&name=' + encodeURIComponent(filters.name);
            }
            if (filters.email) {
                url += '&email=' + encodeURIComponent(filters.email);
            }
            if (filters.department_id) {
                url += '&department_id=' + encodeURIComponent(filters.department_id);
            }
            if (filters.salary_range) {
                url += '&salary_range=' + encodeURIComponent(filters.salary_range);
            }

            let loadingText = 'Loading employees...';
            if (filters.name || filters.email || filters.department_id || filters.salary_range) {
                loadingText = 'Searching employees...';
            }
            $('#employeesTableBody').html('<tr><td colspan="6" class="text-center"><div class="spinner-border text-primary" role="status"></div><div class="mt-2">' + loadingText + '</div></td></tr>');

            $.ajax({
                url: url,
                type: 'GET',
                headers: {
                    'Authorization': 'Bearer ' + getToken(),
                    'Accept': 'application/json'
                },
                success: function(response) {
                    // Reset filter button
                    $('#filterSpinner').addClass('d-none');
                    $('#filterIcon').removeClass('d-none');
                    $('#filterText').text('Apply Filters');
                    $('#applyFiltersBtn').prop('disabled', false);

                    if (response.status && response.data) {
                        displayEmployees(response.data);
                        displayPagination(response.pagination);
                        currentPage = response.pagination.current_page;

                        // Note: Max salary is now set from database on page load
                    } else {
                        $('#employeesTableBody').html('<tr><td colspan="6" class="text-center text-danger">No employees found</td></tr>');
                    }
                },
                error: function(xhr) {
                    // Reset filter button
                    $('#filterSpinner').addClass('d-none');
                    $('#filterIcon').removeClass('d-none');
                    $('#filterText').text('Apply Filters');
                    $('#applyFiltersBtn').prop('disabled', false);

                    let errorMsg = 'Error loading employees';
                    if (xhr.responseJSON && xhr.responseJSON.message) {
                        errorMsg = xhr.responseJSON.message;
                    }
                    if (xhr.status === 401) {
                        window.location.href = '/login';
                        return;
                    }
                    $('#employeesTableBody').html('<tr><td colspan="6" class="text-center text-danger">' + errorMsg + '</td></tr>');
                }
            });
        }

        // Display employees in table
        function displayEmployees(employees) {
            let html = '';
            if (employees.length === 0) {
                html = '<tr><td colspan="6" class="text-center">No employees found</td></tr>';
            } else {
                employees.forEach(function(emp) {
                    html += '<tr>';
                    html += '<td>' + emp.id + '</td>';
                    html += '<td>' + emp.name + '</td>';
                    html += '<td>' + emp.email + '</td>';
                    // Display department name if available, otherwise show department_id
                    let deptName = (emp.department && emp.department.name) ? emp.department.name : (emp.department_id || 'N/A');
                    html += '<td>' + deptName + '</td>';
                    html += '<td>' + formatCurrency(emp.salary) + '</td>';
                    html += '<td>';
                    // Only show delete button if user is admin
                    if (typeof isAdmin === 'function' && isAdmin()) {
                        html += '<button class="btn btn-sm btn-danger" onclick="deleteEmployee(' + emp.id + ', \'' + emp.name + '\')"><i class="fas fa-trash"></i></button>';
                    } else {
                        html += '<span class="text-muted">Not allowed</span>';
                    }
                    html += '</td>';
                    html += '</tr>';
                });
            }
            $('#employeesTableBody').html(html);
        }

        // Display pagination
        function displayPagination(pagination) {
            let html = '';
            let currentPage = pagination.current_page;
            let lastPage = pagination.last_page;

            // Previous button
            if (currentPage > 1) {
                html += '<li class="page-item"><a class="page-link" href="#" data-page="' + (currentPage - 1) + '">&laquo;</a></li>';
            } else {
                html += '<li class="page-item disabled"><a class="page-link" href="#">&laquo;</a></li>';
            }

            // Page numbers
            for (let i = 1; i <= lastPage; i++) {
                if (i === currentPage) {
                    html += '<li class="page-item active"><a class="page-link" href="#">' + i + '</a></li>';
                } else {
                    html += '<li class="page-item"><a class="page-link" href="#" data-page="' + i + '">' + i + '</a></li>';
                }
            }

            // Next button
            if (currentPage < lastPage) {
                html += '<li class="page-item"><a class="page-link" href="#" data-page="' + (currentPage + 1) + '">&raquo;</a></li>';
            } else {
                html += '<li class="page-item disabled"><a class="page-link" href="#">&raquo;</a></li>';
            }

            $('#pagination').html(html);
        }

        // Format number (no currency symbol)
        function formatCurrency(amount) {
            return new Intl.NumberFormat('en-US', {
                minimumFractionDigits: 0,
                maximumFractionDigits: 0
            }).format(amount);
        }

        // Delete employee
        window.deleteEmployee = function(id, name) {
            if (!confirm('Are you sure you want to delete employee "' + name + '"?')) {
                return;
            }

            // Show loading in table
            $('#employeesTableBody').html('<tr><td colspan="6" class="text-center"><div class="spinner-border text-primary" role="status"></div><div class="mt-2">Deleting employee...</div></td></tr>');

            $.ajax({
                url: '/api/employees/' + id,
                type: 'DELETE',
                headers: {
                    'Authorization': 'Bearer ' + getToken(),
                    'X-ROLE': 'admin',
                    'Accept': 'application/json'
                },
                success: function(response) {
                    if (response.status) {
                        fetchEmployees(currentPage);
                        alert('Employee deleted successfully!');
                    }
                },
                error: function(xhr) {
                    let errorMsg = 'Error deleting employee';
                    if (xhr.responseJSON && xhr.responseJSON.message) {
                        errorMsg = xhr.responseJSON.message;
                    }
                    if (xhr.status === 401) {
                        window.location.href = '/login';
                        return;
                    }
                    // Reload employees on error
                    fetchEmployees(currentPage);
                    alert(errorMsg);
                }
            });
        };

        // Apply filters
        $('#applyFiltersBtn').click(function() {
            // Show loading state
            $('#filterSpinner').removeClass('d-none');
            $('#filterIcon').addClass('d-none');
            $('#filterText').text('Searching...');
            $('#applyFiltersBtn').prop('disabled', true);

            filters.name = $('#filterName').val().trim();
            filters.email = $('#filterEmail').val().trim();
            filters.department_id = $('#filterDepartment').val();

            // Get values from range slider
            let slider = $('#salaryRangeSlider').data('ionRangeSlider');
            if (slider) {
                let sliderMin = slider.result.from;
                let sliderMax = slider.result.to;

                // Only set salary_range if values are different from default (meaning user changed them)
                if (sliderMin > 0 || sliderMax < salarySliderMax) {
                    filters.salary_range = sliderMin + '-' + sliderMax;
                } else {
                    filters.salary_range = '';
                }
            } else {
                filters.salary_range = '';
            }

            fetchEmployees(1);
        });

        // Clear filters
        $('#clearFiltersBtn').click(function() {
            $('#filterName').val('');
            $('#filterEmail').val('');
            $('#filterDepartment').val('');

            // Reset salary slider to default values
            let slider = $('#salaryRangeSlider').data('ionRangeSlider');
            if (slider) {
                slider.update({
                    min: 0,
                    max: salarySliderMax,
                    from: 0,
                    to: salarySliderMax
                });
            }
            $('#salaryMinDisplay').text('0');
            $('#salaryMaxDisplay').text(salarySliderMax.toLocaleString());

            filters = {
                name: '',
                email: '',
                department_id: '',
                salary_range: ''
            };
            fetchEmployees(1);
        });

        // Pagination click
        $(document).on('click', '.page-link[data-page]', function(e) {
            e.preventDefault();
            let page = $(this).data('page');
            fetchEmployees(page);
        });

        // Create employee
        $('#createEmployeeForm').submit(function(e) {
            e.preventDefault();
            let name = $('#employeeName').val().trim();
            let email = $('#employeeEmail').val().trim();
            let departmentId = $('#employeeDepartment').val();
            let salary = $('#employeeSalary').val();

            if (!name || !email || !departmentId || !salary) {
                alert('Please fill all required fields');
                return;
            }

            // Show loading state
            $('#createEmpSpinner').removeClass('d-none');
            $('#createEmpText').text('Creating...');
            $('#createEmployeeBtn').prop('disabled', true);
            $('#closeEmployeeBtn').prop('disabled', true);

            $.ajax({
                url: '/api/employees',
                type: 'POST',
                headers: {
                    'Authorization': 'Bearer ' + getToken(),
                    'X-ROLE': 'admin',
                    'Accept': 'application/json',
                    'Content-Type': 'application/json'
                },
                data: JSON.stringify({
                    name: name,
                    email: email,
                    department_id: parseInt(departmentId),
                    salary: parseFloat(salary)
                }),
                success: function(response) {
                    // Reset button state
                    $('#createEmpSpinner').addClass('d-none');
                    $('#createEmpText').text('Create Employee');
                    $('#createEmployeeBtn').prop('disabled', false);
                    $('#closeEmployeeBtn').prop('disabled', false);

                    if (response.status) {
                        $('#createEmployeeModal').modal('hide');
                        $('#createEmployeeForm')[0].reset();
                        // Refresh max salary in case new employee has higher salary
                        fetchMaxSalary();
                        fetchEmployees(currentPage);
                        alert('Employee created successfully!');
                    }
                },
                error: function(xhr) {
                    // Reset button state
                    $('#createEmpSpinner').addClass('d-none');
                    $('#createEmpText').text('Create Employee');
                    $('#createEmployeeBtn').prop('disabled', false);
                    $('#closeEmployeeBtn').prop('disabled', false);

                    if (xhr.status === 401) {
                        window.location.href = '/login';
                        return;
                    }
                    if (xhr.status === 422 && xhr.responseJSON && xhr.responseJSON.errors) {
                        let errors = xhr.responseJSON.errors;
                        let errorMsg = '';
                        for (let field in errors) {
                            errorMsg += errors[field][0] + '\n';
                        }
                        alert(errorMsg);
                    } else {
                        let errorMsg = 'Error creating employee';
                        if (xhr.responseJSON && xhr.responseJSON.message) {
                            errorMsg = xhr.responseJSON.message;
                        }
                        alert(errorMsg);
                    }
                }
            });
        });

        // Reset form when modal is closed
        $('#createEmployeeModal').on('hidden.bs.modal', function() {
            $('#createEmployeeForm')[0].reset();
            $('.invalid-feedback').text('');
            $('.form-control').removeClass('is-invalid');
        });

        // Load departments when modal is opened
        $('#createEmployeeModal').on('show.bs.modal', function() {
            fetchAllDepartments();
        });

        // Check admin status and show/hide buttons
        function checkAdminStatus() {
            if (typeof isAdmin === 'function' && isAdmin()) {
                $('#addEmpBtn').show();
            } else {
                $('#addEmpBtn').hide();
            }
        }

        // Initial load - fetch max salary first, then initialize slider
        checkAdminStatus();
        fetchMaxSalary();
        fetchAllDepartments();
        fetchEmployees(1);
    });
</script>
@endsection
