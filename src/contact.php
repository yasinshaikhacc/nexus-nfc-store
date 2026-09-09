<?php
require_once '../config/db.php';
require_once '../includes/header.php';

$sent = false;
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Mock sending email
    $sent = true;
}
?>

<div class="container my-5 pt-3">
    <div class="row justify-content-center">
        <div class="col-lg-8 text-center mb-5">
            <h1 class="display-4 fw-bold text-white mb-3">Get in Touch</h1>
            <p class="text-muted lead">
                Have questions or need support? We're here to help you power your digital life.
            </p>
        </div>
    </div>

    <div class="row g-5">
        <div class="col-lg-5">
            <div class="card bg-dark text-white h-100">
                <div class="card-body p-4">
                    <h4 class="mb-4">Contact Information</h4>

                    <div class="d-flex align-items-start mb-4">
                        <div class="p-3 bg-primary rounded-circle bg-opacity-10 text-primary me-3">
                            <i class="fas fa-map-marker-alt fa-lg"></i>
                        </div>
                        <div>
                            <h6 class="fw-bold mb-1">Our Location</h6>
                            <p class="text-muted mb-0">123 Tech Avenue, Silicon Valley, CA, USA</p>
                        </div>
                    </div>

                    <div class="d-flex align-items-start mb-4">
                        <div class="p-3 bg-success rounded-circle bg-opacity-10 text-success me-3">
                            <i class="fas fa-phone-alt fa-lg"></i>
                        </div>
                        <div>
                            <h6 class="fw-bold mb-1">Phone Number</h6>
                            <p class="text-muted mb-0">+1 (555) 123-4567</p>
                        </div>
                    </div>

                    <div class="d-flex align-items-start mb-4">
                        <div class="p-3 bg-info rounded-circle bg-opacity-10 text-info me-3">
                            <i class="fas fa-envelope fa-lg"></i>
                        </div>
                        <div>
                            <h6 class="fw-bold mb-1">Email Address</h6>
                            <p class="text-muted mb-0">support@voltix.com</p>
                        </div>
                    </div>

                    <hr class="border-secondary my-4">

                    <h5 class="mb-3">Follow Us</h5>
                    <div class="d-flex gap-3">
                        <a href="#" class="btn btn-outline-light btn-floating"><i class="fab fa-facebook-f"></i></a>
                        <a href="#" class="btn btn-outline-light btn-floating"><i class="fab fa-twitter"></i></a>
                        <a href="#" class="btn btn-outline-light btn-floating"><i class="fab fa-instagram"></i></a>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-lg-7">
            <div class="card bg-dark text-white">
                <div class="card-body p-5">
                    <?php if ($sent): ?>
                        <div class="text-center py-5">
                            <i class="fas fa-check-circle fa-4x text-success mb-3"></i>
                            <h3 class="fw-bold">Message Sent!</h3>
                            <p class="text-muted">Thank you for contacting us. We will get back to you shortly.</p>
                            <a href="index.php" class="btn btn-primary mt-3">Back to Home</a>
                        </div>
                    <?php else: ?>
                        <h4 class="mb-4">Send us a Message</h4>
                        <form method="POST">
                            <div class="row g-3">
                                <div class="col-md-6">
                                    <div class="form-outline">
                                        <input type="text" id="name" name="name" class="form-control text-white" required />
                                        <label class="form-label text-white-50" for="name">Your Name</label>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="form-outline">
                                        <input type="email" id="email" name="email" class="form-control text-white"
                                            required />
                                        <label class="form-label text-white-50" for="email">Your Email</label>
                                    </div>
                                </div>
                                <div class="col-12">
                                    <div class="form-outline">
                                        <input type="text" id="subject" name="subject" class="form-control text-white"
                                            required />
                                        <label class="form-label text-white-50" for="subject">Subject</label>
                                    </div>
                                </div>
                                <div class="col-12">
                                    <div class="form-outline">
                                        <textarea class="form-control text-white" id="message" name="message" rows="5"
                                            required></textarea>
                                        <label class="form-label text-white-50" for="message">Message</label>
                                    </div>
                                </div>
                                <div class="col-12 mt-4">
                                    <button type="submit" class="btn btn-voltix-primary w-100 btn-lg">Send Message <i
                                            class="fas fa-paper-plane ms-2"></i></button>
                                </div>
                            </div>
                        </form>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
    document.querySelectorAll('.form-outline').forEach((formOutline) => {
        new mdb.Input(formOutline).init();
    });
</script>

<?php require_once '../includes/footer.php'; ?>