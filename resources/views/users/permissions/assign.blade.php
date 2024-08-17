@extends('layouts.app')

@section('title', 'Assign Permissions')

@section('content_header')
    @include('partials.breadcrumbs')  <!-- Automatically include breadcrumbs -->
    <h1>Assign Permissions to {{ $user->name }}</h1>
@stop

@section('content')
    <div class="row justify-content-center p-0">
        <div class="col-md-12">
            <div class="card">
                <div class="card-body">
                    <form action="{{ route('users.storeAssignPermission', $user->id) }}" method="POST">
                        @csrf
                        <div class="mb-4">
                            <label for="permissions" class="form-label">Permissions</label>
                            <div class="d-flex justify-content-end mb-3">
                                <button type="button" class="btn btn-outline-success btn-sm me-2" id="selectAll">Select All</button>
                                <button type="button" class="btn btn-outline-danger btn-sm" id="unselectAll">Unselect All</button>
                            </div>
                            <div class="accordion" id="permissionsAccordion">
                                @foreach($permissions as $groupName => $groupPermissions)
                                    <div class="accordion-item">
                                        <h2 class="accordion-header" id="heading{{ $groupName }}">
                                            <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#collapse{{ $groupName }}" aria-expanded="false" aria-controls="collapse{{ $groupName }}">
                                                {{ ucfirst($groupName) }} Permissions
                                            </button>
                                        </h2>
                                        <div id="collapse{{ $groupName }}" class="accordion-collapse collapse" aria-labelledby="heading{{ $groupName }}" data-bs-parent="#permissionsAccordion">
                                            <div class="accordion-body">
                                                <div class="d-flex justify-content-end mb-2">
                                                    <button type="button" class="btn btn-outline-success btn-sm me-2 select-group" data-group="{{ $groupName }}">Select Group</button>
                                                    <button type="button" class="btn btn-outline-danger btn-sm unselect-group" data-group="{{ $groupName }}">Unselect Group</button>
                                                </div>
                                                <div class="row">
                                                    @foreach($groupPermissions->chunk(3) as $chunk)
                                                        <div class="col-md-4">
                                                            @foreach($chunk as $permission)
                                                                <div class="form-check mb-2">
                                                                    <input class="form-check-input permission-checkbox" type="checkbox" name="permissions[]" value="{{ $permission->id }}" id="permission_{{ $permission->id }}" data-group="{{ $groupName }}" @if($user->permissions->contains($permission->id)) checked @endif>
                                                                    <label class="form-check-label" for="permission_{{ $permission->id }}">{{ $permission->name }}</label>
                                                                </div>
                                                            @endforeach
                                                        </div>
                                                    @endforeach
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                @endforeach
                            </div>
                        </div>
                        <div class="d-flex justify-content-end">
                            <button type="submit" class="btn btn-primary me-2">Assign Permissions</button>
                            <a href="{{ route('users.index', $user->id) }}" class="btn btn-secondary">Cancel</a>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
@stop

@section('script')
    <script>
        document.addEventListener('DOMContentLoaded', function () {
            // Function to apply dark mode to the accordion
            function applyAccordionDarkMode(isDarkMode) {
                const accordionItems = document.querySelectorAll('.accordion-item');
                accordionItems.forEach(item => {
                    if (isDarkMode) {
                        item.classList.add('bg-dark', 'text-white');
                    } else {
                        item.classList.remove('bg-dark', 'text-white');
                    }
                });
            }

            // Check if dark mode is enabled on page load
            const isDarkMode = document.body.classList.contains('dark-mode');
            applyAccordionDarkMode(isDarkMode);

            // Watch for dark mode changes
            const observer = new MutationObserver(mutations => {
                mutations.forEach(mutation => {
                    if (mutation.attributeName === 'class') {
                        const isDarkMode = document.body.classList.contains('dark-mode');
                        applyAccordionDarkMode(isDarkMode);
                    }
                });
            });

            observer.observe(document.body, { attributes: true });

            // Select/Deselect all permissions
            document.getElementById('selectAll').addEventListener('click', function () {
                document.querySelectorAll('.permission-checkbox').forEach(function (checkbox) {
                    checkbox.checked = true;
                });
            });

            document.getElementById('unselectAll').addEventListener('click', function () {
                document.querySelectorAll('.permission-checkbox').forEach(function (checkbox) {
                    checkbox.checked = false;
                });
            });

            // Select/Deselect all checkboxes in a group
            document.querySelectorAll('.select-group').forEach(function (button) {
                button.addEventListener('click', function () {
                    const groupName = this.getAttribute('data-group');
                    document.querySelectorAll(`.permission-checkbox[data-group="${groupName}"]`).forEach(function (checkbox) {
                        checkbox.checked = true;
                    });
                });
            });

            document.querySelectorAll('.unselect-group').forEach(function (button) {
                button.addEventListener('click', function () {
                    const groupName = this.getAttribute('data-group');
                    document.querySelectorAll(`.permission-checkbox[data-group="${groupName}"]`).forEach(function (checkbox) {
                        checkbox.checked = false;
                    });
                });
            });
        });
    </script>
@endsection
<!-- Bootstrap CSS -->
<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">

<!-- Bootstrap JS (at the end of the body tag) -->
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
