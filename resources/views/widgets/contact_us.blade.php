<!-- resources/views/layouts/footer/contact_us.blade.php -->
<div class="contact-us-section">
    <h5 class="mb-4 platform-title text-white text-center" data-aos="fade-up" data-aos-duration="1000">Contact Us</h5>

    <!-- Contact Information -->
    <div data-aos="fade-right" data-aos-duration="1000">
        <p class="text-white">
            We would love to hear from you! If you have any questions, suggestions, or feedback, please feel free to reach out to us through the contact details below.
        </p>
        <p class="text-white">
            <strong>Email:</strong><a href="mailto:info@sjdm.store" class="text-white text-decoration-none">info@sjdm.store</a></p>

        <p class="text-white">
            <strong>Phone:</strong>: <a href="tel:+1234567890" class="text-white text-decoration-none">+971557830054</a>
        </p>
        <p class="text-white">
            <strong>Address:</strong> Al-Maktoom St,Diera, Dubai, UAE
        </p>
    </div>

    <!-- Social Media Links -->
    <div class="social-icons mt-3" data-aos="fade-up" data-aos-duration="1000">
        <p>Phone: <a href="tel:+1234567890" class="text-white text-decoration-none">+971557830054</a></p>
        <div class="social-icons">
            <a href="https://www.facebook.com/S.J.Digitals.Marketing" class="text-white me-2"><i class="fab fa-facebook-f"></i></a>
            <a href="https://www.instagram.com/s.j.digital.marketting/" class="text-white"><i class="fab fa-instagram"></i></a>
        </div>
    </div>

    <!-- Optional: Contact Form -->
    <form action="{{ url('/contact') }}" method="POST" class="mt-4" data-aos="fade-left" data-aos-duration="1000">
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
