@extends('adminlte::page')

@section('title', 'SJDM Panel')

@section('content')
    <div id="admin-app"></div>
@stop

@push('css')
    @vite(['resources/css/admin.css'])
@endpush

@push('js')
    @vite(['resources/js/admin.js'])
    <script>
        // Dark mode toggle for AdminLTE
        document.addEventListener('DOMContentLoaded', function() {
            var toggle = document.getElementById('dark-mode-toggle');
            if (toggle) {
                toggle.addEventListener('click', function(e) {
                    e.preventDefault();
                    document.body.classList.toggle('dark-mode');
                    var icon = this.querySelector('i');
                    if (document.body.classList.contains('dark-mode')) {
                        icon.classList.remove('fa-moon');
                        icon.classList.add('fa-sun');
                        localStorage.setItem('adminlte-dark', '1');
                    } else {
                        icon.classList.remove('fa-sun');
                        icon.classList.add('fa-moon');
                        localStorage.setItem('adminlte-dark', '0');
                    }
                });
            }
            // Restore dark mode from localStorage
            if (localStorage.getItem('adminlte-dark') === '1') {
                document.body.classList.add('dark-mode');
                var icon = document.querySelector('#dark-mode-toggle i');
                if (icon) { icon.classList.remove('fa-moon'); icon.classList.add('fa-sun'); }
            }
        });
    </script>
@endpush
