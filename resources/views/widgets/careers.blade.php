<!-- resources/views/layouts/footer/careers.blade.php -->
<div class="careers-section">
    <h5 class="mb-4">Careers</h5>
    <p class="text-white">
        Join the dynamic team at {{ config('app.name') }}! We are always looking for talented and passionate individuals to help us achieve our mission of delivering exceptional services and solutions to our clients.
    </p>
    <p class="text-white">
        At {{ config('app.name') }}, we foster a culture of innovation, collaboration, and personal growth. We believe in investing in our employees, offering competitive salaries, comprehensive benefits, and opportunities for professional development.
    </p>
    <p class="text-white">
        If you're ready to take your career to the next level and be part of a forward-thinking company, we encourage you to explore our current job openings.
    </p>

    <!-- Optional: Add a link to a dedicated Careers page if available -->
    <a href="{{ url('/careers') }}" class="text-white text-decoration-none">View current job openings</a>
</div>
