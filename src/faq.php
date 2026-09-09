<?php
require_once '../includes/header.php';
?>

<div class="container my-5 pt-3">
    <div class="text-center mb-5">
        <h1 class="display-4 fw-bold text-white mb-3">Frequently Asked Questions</h1>
        <p class="text-muted lead">
            Find answers to common questions about your orders, shipping, and returns.
        </p>
    </div>

    <div class="row justify-content-center">
        <div class="col-lg-8">
            <div class="accordion accordion-flush" id="accordionFlushExample">

                <!-- Item 1 -->
                <div class="accordion-item bg-dark text-white border-bottom border-secondary">
                    <h2 class="accordion-header" id="flush-headingOne">
                        <button class="accordion-button collapsed bg-dark text-white shadow-none" type="button"
                            data-mdb-toggle="collapse" data-mdb-target="#flush-collapseOne" aria-expanded="false"
                            aria-controls="flush-collapseOne">
                            <span class="fw-bold">How long does shipping take?</span>
                        </button>
                    </h2>
                    <div id="flush-collapseOne" class="accordion-collapse collapse" aria-labelledby="flush-headingOne"
                        data-mdb-parent="#accordionFlushExample">
                        <div class="accordion-body text-muted">
                            Standard shipping typically takes 3-5 business days. Express shipping options are available
                            at checkout for 1-2 day delivery.
                        </div>
                    </div>
                </div>

                <!-- Item 2 -->
                <div class="accordion-item bg-dark text-white border-bottom border-secondary">
                    <h2 class="accordion-header" id="flush-headingTwo">
                        <button class="accordion-button collapsed bg-dark text-white shadow-none" type="button"
                            data-mdb-toggle="collapse" data-mdb-target="#flush-collapseTwo" aria-expanded="false"
                            aria-controls="flush-collapseTwo">
                            <span class="fw-bold">What is your return policy?</span>
                        </button>
                    </h2>
                    <div id="flush-collapseTwo" class="accordion-collapse collapse" aria-labelledby="flush-headingTwo"
                        data-mdb-parent="#accordionFlushExample">
                        <div class="accordion-body text-muted">
                            We offer a 30-day return policy for all unused items in their original packaging. Simply
                            contact our support team to initiate a return.
                        </div>
                    </div>
                </div>

                <!-- Item 3 -->
                <div class="accordion-item bg-dark text-white border-bottom border-secondary">
                    <h2 class="accordion-header" id="flush-headingThree">
                        <button class="accordion-button collapsed bg-dark text-white shadow-none" type="button"
                            data-mdb-toggle="collapse" data-mdb-target="#flush-collapseThree" aria-expanded="false"
                            aria-controls="flush-collapseThree">
                            <span class="fw-bold">Do you offer international shipping?</span>
                        </button>
                    </h2>
                    <div id="flush-collapseThree" class="accordion-collapse collapse"
                        aria-labelledby="flush-headingThree" data-mdb-parent="#accordionFlushExample">
                        <div class="accordion-body text-muted">
                            Yes, we ship to most countries worldwide. International shipping rates and times vary
                            depending on the destination.
                        </div>
                    </div>
                </div>

                <!-- Item 4 -->
                <div class="accordion-item bg-dark text-white border-bottom border-secondary">
                    <h2 class="accordion-header" id="flush-headingFour">
                        <button class="accordion-button collapsed bg-dark text-white shadow-none" type="button"
                            data-mdb-toggle="collapse" data-mdb-target="#flush-collapseFour" aria-expanded="false"
                            aria-controls="flush-collapseFour">
                            <span class="fw-bold">How can I track my order?</span>
                        </button>
                    </h2>
                    <div id="flush-collapseFour" class="accordion-collapse collapse" aria-labelledby="flush-headingFour"
                        data-mdb-parent="#accordionFlushExample">
                        <div class="accordion-body text-muted">
                            Once your order is shipped, you will receive a tracking number via email. You can also view
                            your order status in your account dashboard.
                        </div>
                    </div>
                </div>

            </div>

            <div class="text-center mt-5">
                <p class="text-muted">Still have questions?</p>
                <a href="contact.php" class="btn btn-voltix-primary btn-lg">Contact Support</a>
            </div>
        </div>
    </div>
</div>

<?php require_once '../includes/footer.php'; ?>