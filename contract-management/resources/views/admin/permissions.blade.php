@extends('layouts.app')

@section('title', 'Permissions & Roles - Admin')

@section('content')
<div class="container mx-auto px-4 py-8">
    <div class="mb-6">
        <h1 class="text-3xl font-bold text-gray-800">System Permissions & Roles</h1>
        <a href="{{ route('admin.users.index') }}" class="text-blue-600 hover:text-blue-800">← Back to Users</a>
    </div>

    <div class="space-y-6">
        @foreach ($roles as $key => $role)
            <div class="card">
                <div class="card-header" style="background: {{ $key === 'admin' ? '#dc3545' : ($key === 'finance' ? '#17a2b8' : ($key === 'gs_manager' || $key === 'cmd_manager' ? '#28a745' : '#6c757d')) }}; color: white;">
                    <h2 style="color: white; margin: 0;">{{ $role }}</h2>
                    <span class="badge" style="background: rgba(255,255,255,0.2); color: white;">
                        {{ $key === 'admin' ? 'All Departments' : ($key === 'gs_manager' || $key === 'cmd_manager' ? 'All Departments (Read-Only)' : ucfirst(str_replace('_', ' ', $key)) . ' Department') }}
                    </span>
                </div>

                <div style="padding: 1.5rem;">
                    <div style="margin-bottom: 1.5rem;">
                        <p class="text-lg font-semibold text-gray-800 mb-2">Description</p>
                        <p class="text-gray-600">{{ $key === 'admin' ? 'Full system access including user management, contract oversight, and system administration.' : ($key === 'general_service' ? 'Handles contract creation and management for the General Service department.' : ($key === 'cmd' ? 'Manages contracts for the CMD department with full CRUD capabilities.' : ($key === 'finance' ? 'Oversees financial aspects of contracts with comprehensive access.' : 'Supervisory role with read-only access to all contracts and reports.'))) }}</p>
                    </div>

                    <div>
                        <p class="text-lg font-semibold text-gray-800 mb-3">Permissions</p>
                        <ul style="list-style: none; padding: 0;">
                            @if($key === 'admin')
                                <li style="padding: 0.5rem 0 0.5rem 1.5rem; border-left: 3px solid #3498db; margin-bottom: 0.5rem; background: #f8f9fa;">
                                    <span class="text-gray-700">Manage all users (create, edit, activate/deactivate)</span>
                                </li>
                                <li style="padding: 0.5rem 0 0.5rem 1.5rem; border-left: 3px solid #3498db; margin-bottom: 0.5rem; background: #f8f9fa;">
                                    <span class="text-gray-700">View all contracts across all departments</span>
                                </li>
                                <li style="padding: 0.5rem 0 0.5rem 1.5rem; border-left: 3px solid #3498db; margin-bottom: 0.5rem; background: #f8f9fa;">
                                    <span class="text-gray-700">Access system reports and analytics</span>
                                </li>
                                <li style="padding: 0.5rem 0 0.5rem 1.5rem; border-left: 3px solid #3498db; margin-bottom: 0.5rem; background: #f8f9fa;">
                                    <span class="text-gray-700">Cannot create or edit individual contracts</span>
                                </li>
                            @elseif($key === 'general_service')
                                <li style="padding: 0.5rem 0 0.5rem 1.5rem; border-left: 3px solid #3498db; margin-bottom: 0.5rem; background: #f8f9fa;">
                                    <span class="text-gray-700">Create new contracts for General Service department</span>
                                </li>
                                <li style="padding: 0.5rem 0 0.5rem 1.5rem; border-left: 3px solid #3498db; margin-bottom: 0.5rem; background: #f8f9fa;">
                                    <span class="text-gray-700">View all contracts (read-only access)</span>
                                </li>
                                <li style="padding: 0.5rem 0 0.5rem 1.5rem; border-left: 3px solid #3498db; margin-bottom: 0.5rem; background: #f8f9fa;">
                                    <span class="text-gray-700">Edit only contracts created by themselves</span>
                                </li>
                                <li style="padding: 0.5rem 0 0.5rem 1.5rem; border-left: 3px solid #3498db; margin-bottom: 0.5rem; background: #f8f9fa;">
                                    <span class="text-gray-700">Cannot delete contracts</span>
                                </li>
                            @elseif($key === 'cmd')
                                <li style="padding: 0.5rem 0 0.5rem 1.5rem; border-left: 3px solid #3498db; margin-bottom: 0.5rem; background: #f8f9fa;">
                                    <span class="text-gray-700">Create new contracts for CMD department</span>
                                </li>
                                <li style="padding: 0.5rem 0 0.5rem 1.5rem; border-left: 3px solid #3498db; margin-bottom: 0.5rem; background: #f8f9fa;">
                                    <span class="text-gray-700">View all contracts (read-only access)</span>
                                </li>
                                <li style="padding: 0.5rem 0 0.5rem 1.5rem; border-left: 3px solid #3498db; margin-bottom: 0.5rem; background: #f8f9fa;">
                                    <span class="text-gray-700">Full CRUD operations on CMD department contracts</span>
                                </li>
                                <li style="padding: 0.5rem 0 0.5rem 1.5rem; border-left: 3px solid #3498db; margin-bottom: 0.5rem; background: #f8f9fa;">
                                    <span class="text-gray-700">Access department-specific reports</span>
                                </li>
                            @elseif($key === 'finance')
                                <li style="padding: 0.5rem 0 0.5rem 1.5rem; border-left: 3px solid #3498db; margin-bottom: 0.5rem; background: #f8f9fa;">
                                    <span class="text-gray-700">Create new contracts for Finance department</span>
                                </li>
                                <li style="padding: 0.5rem 0 0.5rem 1.5rem; border-left: 3px solid #3498db; margin-bottom: 0.5rem; background: #f8f9fa;">
                                    <span class="text-gray-700">View all contracts across all departments</span>
                                </li>
                                <li style="padding: 0.5rem 0 0.5rem 1.5rem; border-left: 3px solid #3498db; margin-bottom: 0.5rem; background: #f8f9fa;">
                                    <span class="text-gray-700">Full CRUD operations on Finance department contracts</span>
                                </li>
                                <li style="padding: 0.5rem 0 0.5rem 1.5rem; border-left: 3px solid #3498db; margin-bottom: 0.5rem; background: #f8f9fa;">
                                    <span class="text-gray-700">Access comprehensive reports and analytics</span>
                                </li>
                                <li style="padding: 0.5rem 0 0.5rem 1.5rem; border-left: 3px solid #3498db; margin-bottom: 0.5rem; background: #f8f9fa;">
                                    <span class="text-gray-700">Cannot manage users</span>
                                </li>
                            @else
                                <li style="padding: 0.5rem 0 0.5rem 1.5rem; border-left: 3px solid #3498db; margin-bottom: 0.5rem; background: #f8f9fa;">
                                    <span class="text-gray-700">View all contracts across all departments</span>
                                </li>
                                <li style="padding: 0.5rem 0 0.5rem 1.5rem; border-left: 3px solid #3498db; margin-bottom: 0.5rem; background: #f8f9fa;">
                                    <span class="text-gray-700">Access comprehensive reports and analytics</span>
                                </li>
                                <li style="padding: 0.5rem 0 0.5rem 1.5rem; border-left: 3px solid #3498db; margin-bottom: 0.5rem; background: #f8f9fa;">
                                    <span class="text-gray-700">Cannot create, edit, or delete contracts</span>
                                </li>
                                <li style="padding: 0.5rem 0 0.5rem 1.5rem; border-left: 3px solid #3498db; margin-bottom: 0.5rem; background: #f8f9fa;">
                                    <span class="text-gray-700">Cannot manage users</span>
                                </li>
                            @endif
                        </ul>
                    </div>
                </div>
            </div>
        @endforeach
    </div>

    <div class="card mt-8">
        <div class="card-header">
            <h2>Access Matrix</h2>
        </div>
        <div style="overflow-x: auto; padding: 1.5rem;">
            <table style="font-size: 0.875rem;">
                <thead>
                    <tr>
                        <th>Role</th>
                        <th>View All Contracts</th>
                        <th>Create Contracts</th>
                        <th>Edit Contracts</th>
                        <th>Delete Contracts</th>
                        <th>Extend Dates</th>
                        <th>Manage Users</th>
                    </tr>
                </thead>
                <tbody>
                    <tr style="background: #fff3cd;">
                        <td><strong>System Administrator</strong></td>
                        <td style="text-align: center;">✓</td>
                        <td style="text-align: center;">✓</td>
                        <td style="text-align: center;">✓</td>
                        <td style="text-align: center;">✓</td>
                        <td style="text-align: center;">✓</td>
                        <td style="text-align: center;">✓</td>
                    </tr>
                    <tr>
                        <td><strong>Finance Officer</strong></td>
                        <td style="text-align: center;">✓</td>
                        <td style="text-align: center;">✓</td>
                        <td style="text-align: center;">✓</td>
                        <td style="text-align: center;">-</td>
                        <td style="text-align: center;">✓</td>
                        <td style="text-align: center;">-</td>
                    </tr>
                    <tr style="background: #f8f9fa;">
                        <td><strong>GS Manager</strong></td>
                        <td style="text-align: center;">-</td>
                        <td style="text-align: center;">✓</td>
                        <td style="text-align: center;">✓</td>
                        <td style="text-align: center;">-</td>
                        <td style="text-align: center;">✓</td>
                        <td style="text-align: center;">-</td>
                    </tr>
                    <tr>
                        <td><strong>CMD Manager</strong></td>
                        <td style="text-align: center;">-</td>
                        <td style="text-align: center;">✓</td>
                        <td style="text-align: center;">✓</td>
                        <td style="text-align: center;">-</td>
                        <td style="text-align: center;">✓</td>
                        <td style="text-align: center;">-</td>
                    </tr>
                    <tr style="background: #f8f9fa;">
                        <td><strong>GS Officer</strong></td>
                        <td style="text-align: center;">-</td>
                        <td style="text-align: center;">✓</td>
                        <td style="text-align: center;">✓</td>
                        <td style="text-align: center;">-</td>
                        <td style="text-align: center;">✓</td>
                        <td style="text-align: center;">-</td>
                    </tr>
                    <tr>
                        <td><strong>CMD Officer</strong></td>
                        <td style="text-align: center;">-</td>
                        <td style="text-align: center;">✓</td>
                        <td style="text-align: center;">✓</td>
                        <td style="text-align: center;">-</td>
                        <td style="text-align: center;">✓</td>
                        <td style="text-align: center;">-</td>
                    </tr>
                </tbody>
            </table>
        </div>
    </div>
</div>

@endsection
