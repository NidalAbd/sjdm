<!-- resources/views/layouts/footer/contact_us.blade.php -->
<div class="contact-us-section">
    <h5 class="mb-4">Contact Us</h5>

    <!-- Contact Information -->
    <p class="text-white">
        We would love to hear from you! If you have any questions, suggestions, or feedback, please feel free to reach out to us through the contact details below.
    </p>
    <p class="text-white">
        <strong>Email:</strong> <a href="mailto:info@example.com" class="text-white text-decoration-none">info@example.com</a>
    </p>
    <p class="text-white">
        <strong>Phone:</strong> <a href="tel:+1234567890" class="text-white text-decoration-none">+123 456 7890</a>
    </p>
    <p class="text-white">
        <strong>Address:</strong> 1234 Street Name, City, Country
    </p>

    <!-- Social Media Links -->
    <div class="social-icons mt-3">
        <a href="https://facebook.com" class="text-white me-3" target="_blank"><i class="fab fa-facebook-f"></i></a>
        <a href="https://twitter.com" class="text-white me-3" target="_blank"><i class="fab fa-twitter"></i></a>
        <a href="https://linkedin.com" class="text-white me-3" target="_blank"><i class="fab fa-linkedin-in"></i></a>
        <a href="https://instagram.com" class="text-white" target="_blank"><i class="fab fa-instagram"></i></a>
    </div>

    <!-- Optional: Contact Form -->
    <form action="{{ url('/contact') }}" method="POST" class="mt-4">
        @csrf
        <div class="form-group">
            <label for="name" class="text-white">Name</label>
            <input type="text" class="form-control" id="name" name="name" placeholder="Your Name" required>
        </div>
        <div class="form-group">
            <label for="email" class="text-white">Email</label>
            <input type="email" class="form-control" id="email" name="email" placeholder="Your Email" required>
        </div>
        <div class="form-group">
            <label for="message" class="text-white">Message</label>
            <textarea class="form-control" id="message" name="message" rows="3" placeholder="Your Message" required></textarea>
        </div>
        <button type="submit" class="btn btn-primary mt-3">Send Message</button>
    </form>
</div>
