@extends('layouts.app')

@section('title', 'Departments')

@section('page-title', 'Departments')

@section('breadcrumb')
    <li class="breadcrumb-item"><a href="#">Home</a></li>
    <li class="breadcrumb-item active">Departments</li>
@endsection

@section('content')
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header">
                    <h3 class="card-title">Departments List</h3>
                    <div class="card-tools" id="deptActionButtons">
                        <button type="button" class="btn btn-primary btn-sm" data-toggle="modal" data-target="#createDepartmentModal" id="addDeptBtn" style="display: none;">
                            <i class="fas fa-plus"></i> Add Department
                        </button>
                    </div>
                </div>
                <!-- /.card-header -->
                <div class="card-body table-responsive p-0">
                    <div class="row p-3">
                        <div class="col-md-4">
                            <div class="input-group">
                                <input type="text" id="searchInput" name="search" class="form-control" placeholder="Search by name...">
                                <div class="input-group-append">
                                    <button type="button" id="searchBtn" class="btn btn-default">
                                        <span class="spinner-border spinner-border-sm d-none" role="status" id="searchSpinner"></span>
                                        <i class="fas fa-search" id="searchIcon"></i>
                                        <span id="searchText">Search</span>
                                    </button>
                                </div>
                            </div>
                        </div>
                    </div>
                    <table class="table table-hover text-nowrap">
                        <thead>
                            <tr>
                                <th>ID</th>
                                <th>Department Name</th>
                                <th>Employee Count</th>
                                <th>Created At</th>
                            </tr>
                        </thead>
                        <tbody id="departmentsTableBody">
                            <tr>
                                <td colspan="4" class="text-center">
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

    <!-- Create Department Modal -->
    <div class="modal fade" id="createDepartmentModal" tabindex="-1" role="dialog">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Create New Department</h5>
                    <button type="button" class="close" data-dismiss="modal">
                        <span>&times;</span>
                    </button>
                </div>
                <form id="createDepartmentForm">
                    <div class="modal-body">
                        <div class="form-group">
                            <label for="departmentName">Department Name <span class="text-danger">*</span></label>
                            <input type="text" class="form-control" id="departmentName" name="name" required>
                            <div class="invalid-feedback"></div>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-dismiss="modal" id="closeDepartmentBtn">Close</button>
                        <button type="submit" class="btn btn-primary" id="createDepartmentBtn">
                            <span class="spinner-border spinner-border-sm d-none" role="status" id="createDeptSpinner"></span>
                            <span id="createDeptText">Create Department</span>
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
        let searchName = '';

        // Fetch departments
        function fetchDepartments(page = 1, name = '') {
            let url = '/api/departments?page=' + page;
            if (name) {
                url += '&name=' + encodeURIComponent(name);
            }

            let loadingText = name ? 'Searching departments...' : 'Loading departments...';
            $('#departmentsTableBody').html('<tr><td colspan="4" class="text-center"><div class="spinner-border text-primary" role="status"></div><div class="mt-2">' + loadingText + '</div></td></tr>');

            $.ajax({
                url: url,
                type: 'GET',
                headers: {
                    'Authorization': 'Bearer ' + getToken(),
                    'Accept': 'application/json'
                },
                success: function(response) {
                    // Reset search button
                    $('#searchSpinner').addClass('d-none');
                    $('#searchIcon').removeClass('d-none');
                    $('#searchText').text('Search');
                    $('#searchBtn').prop('disabled', false);

                    if (response.status && response.data) {
                        displayDepartments(response.data);
                        displayPagination(response.pagination);
                        currentPage = response.pagination.current_page;
                    } else {
                        $('#departmentsTableBody').html('<tr><td colspan="4" class="text-center text-danger">No departments found</td></tr>');
                    }
                },
                    error: function(xhr) {
                        // Reset search button
                        $('#searchSpinner').addClass('d-none');
                        $('#searchIcon').removeClass('d-none');
                        $('#searchText').text('Search');
                        $('#searchBtn').prop('disabled', false);

                        let errorMsg = 'Error loading departments';
                        if (xhr.responseJSON && xhr.responseJSON.message) {
                            errorMsg = xhr.responseJSON.message;
                        }
                        if (xhr.status === 401) {
                            window.location.href = '/login';
                            return;
                        }
                        $('#departmentsTableBody').html('<tr><td colspan="4" class="text-center text-danger">' + errorMsg + '</td></tr>');
                    }
            });
        }

        // Display departments in table
        function displayDepartments(departments) {
            let html = '';
            if (departments.length === 0) {
                html = '<tr><td colspan="4" class="text-center">No departments found</td></tr>';
            } else {
                departments.forEach(function(dept) {
                    html += '<tr>';
                    html += '<td>' + dept.id + '</td>';
                    html += '<td>' + dept.name + '</td>';
                    html += '<td>' + dept.employees_count + '</td>';
                    html += '<td>' + formatDate(dept.created_at) + '</td>';
                    html += '</tr>';
                });
            }
            $('#departmentsTableBody').html(html);
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

        // Format date
        function formatDate(dateString) {
            if (!dateString) return '-';
            let date = new Date(dateString);
            return date.toLocaleDateString() + ' ' + date.toLocaleTimeString();
        }

        // Get token from localStorage
        function getToken() {
            let token = localStorage.getItem('auth_token');
            if (!token) {
                window.location.href = '/login';
                return '';
            }
            return token;
        }

        // Search button click
        $('#searchBtn').click(function() {
            searchName = $('#searchInput').val().trim();
            // Show loading state
            $('#searchSpinner').removeClass('d-none');
            $('#searchIcon').addClass('d-none');
            $('#searchText').text('Searching...');
            $('#searchBtn').prop('disabled', true);

            fetchDepartments(1, searchName);
        });

        // Search on Enter key
        $('#searchInput').keypress(function(e) {
            if (e.which === 13) {
                searchName = $('#searchInput').val().trim();
                // Show loading state
                $('#searchSpinner').removeClass('d-none');
                $('#searchIcon').addClass('d-none');
                $('#searchText').text('Searching...');
                $('#searchBtn').prop('disabled', true);
                fetchDepartments(1, searchName);
            }
        });

        // Pagination click
        $(document).on('click', '.page-link[data-page]', function(e) {
            e.preventDefault();
            let page = $(this).data('page');
            fetchDepartments(page, searchName);
        });

        // Create department
        $('#createDepartmentForm').submit(function(e) {
            e.preventDefault();
            let name = $('#departmentName').val().trim();

            if (!name) {
                alert('Please enter department name');
                return;
            }

            // Show loading state
            $('#createDeptSpinner').removeClass('d-none');
            $('#createDeptText').text('Creating...');
            $('#createDepartmentBtn').prop('disabled', true);
            $('#closeDepartmentBtn').prop('disabled', true);

            $.ajax({
                url: '/api/departments',
                type: 'POST',
                headers: {
                    'Authorization': 'Bearer ' + getToken(),
                    'X-ROLE': 'admin',
                    'Accept': 'application/json',
                    'Content-Type': 'application/json'
                },
                data: JSON.stringify({
                    name: name
                }),
                success: function(response) {
                    // Reset button state
                    $('#createDeptSpinner').addClass('d-none');
                    $('#createDeptText').text('Create Department');
                    $('#createDepartmentBtn').prop('disabled', false);
                    $('#closeDepartmentBtn').prop('disabled', false);

                    if (response.status) {
                        $('#createDepartmentModal').modal('hide');
                        $('#createDepartmentForm')[0].reset();
                        fetchDepartments(currentPage, searchName);
                        alert('Department created successfully!');
                    }
                },
                error: function(xhr) {
                    // Reset button state
                    $('#createDeptSpinner').addClass('d-none');
                    $('#createDeptText').text('Create Department');
                    $('#createDepartmentBtn').prop('disabled', false);
                    $('#closeDepartmentBtn').prop('disabled', false);

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
                        let errorMsg = 'Error creating department';
                        if (xhr.responseJSON && xhr.responseJSON.message) {
                            errorMsg = xhr.responseJSON.message;
                        }
                        alert(errorMsg);
                    }
                }
            });
        });

        // Reset form when modal is closed
        $('#createDepartmentModal').on('hidden.bs.modal', function() {
            $('#createDepartmentForm')[0].reset();
            $('.invalid-feedback').text('');
            $('.form-control').removeClass('is-invalid');
        });

        // Check admin status and show/hide buttons
        function checkAdminStatus() {
            if (typeof isAdmin === 'function' && isAdmin()) {
                $('#addDeptBtn').show();
            } else {
                $('#addDeptBtn').hide();
            }
        }

        // Initial load
        checkAdminStatus();
        fetchDepartments(1, '');
    });
</script>
@endsection
