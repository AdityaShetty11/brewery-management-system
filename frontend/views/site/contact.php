<?php

/** @var yii\web\View $this */

$this->title = 'Contact Us — Hofer BrauHaus';
?>

<div class="py-5 text-center">
    <h1 class="display-5 fw-bold">📬 Contact Us</h1>
    <p class="lead text-muted col-md-6 mx-auto">
        Have a question, wholesale enquiry, or just want to say hello? We'd love to hear from you.
    </p>
</div>

<hr class="my-4">

<div class="row g-5 my-2">
    <div class="col-md-6">
        <h4 class="fw-bold mb-4">Get in Touch</h4>
        <ul class="list-unstyled text-muted">
            <li class="mb-3">
                <strong>📍 Address</strong><br>
                12 Barrel Lane, Hopsville, HV1 2BR
            </li>
            <li class="mb-3">
                <strong>📞 Phone</strong><br>
                +1 (555) 012-3456
            </li>
            <li class="mb-3">
                <strong>📧 Email</strong><br>
                <a href="mailto:hello@hopsandbarrel.local">hello@hopsandbarrel.local</a>
            </li>
            <li class="mb-3">
                <strong>🕐 Opening Hours</strong><br>
                Monday – Friday: 9am – 5pm<br>
                Saturday: 10am – 3pm
            </li>
        </ul>
    </div>

    <div class="col-md-6">
        <h4 class="fw-bold mb-4">Send a Message</h4>
        <form>
            <div class="mb-3">
                <label class="form-label">Your Name</label>
                <input type="text" class="form-control" placeholder="Jane Smith">
            </div>
            <div class="mb-3">
                <label class="form-label">Email Address</label>
                <input type="email" class="form-control" placeholder="jane@example.com">
            </div>
            <div class="mb-3">
                <label class="form-label">Message</label>
                <textarea class="form-control" rows="4" placeholder="How can we help?"></textarea>
            </div>
            <button type="submit" class="btn btn-dark">Send Message</button>
        </form>
    </div>
</div>
