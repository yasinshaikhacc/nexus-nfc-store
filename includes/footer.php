</main>

<!-- Footer -->
<footer class="text-center text-lg-start bg-black">
    <!-- Section: Links  -->
    <section class="p-4 border-bottom border-light border-opacity-10">
        <div class="container text-center text-md-start mt-5">
            <!-- Grid row -->
            <div class="row mt-3">
                <!-- Grid column -->
                <div class="col-md-3 col-lg-4 col-xl-3 mx-auto mb-4">
                    <!-- Content -->
                    <h6 class="text-uppercase fw-bold mb-4 text-gradient">
                        <i class="fas fa-bolt me-3"></i>VOLTIX
                    </h6>
                    <p>
                        Powering your digital life with the latest tech. Premium electronics, unbeatable prices, and
                        lightning-fast delivery.
                    </p>
                </div>
                <!-- Grid column -->

                <!-- Grid column -->
                <div class="col-md-2 col-lg-2 col-xl-2 mx-auto mb-4">
                    <!-- Links -->
                    <h6 class="text-uppercase fw-bold mb-4">
                        Products
                    </h6>
                    <p>
                        <a href="products.php?category=Mobiles" class="text-reset">Mobiles</a>
                    </p>
                    <p>
                        <a href="products.php?category=Laptops" class="text-reset">Laptops</a>
                    </p>
                    <p>
                        <a href="products.php?category=Audio" class="text-reset">Audio</a>
                    </p>
                    <p>
                        <a href="products.php?category=Gaming" class="text-reset">Gaming</a>
                    </p>
                </div>
                <!-- Grid column -->

                <!-- Grid column -->
                <div class="col-md-3 col-lg-2 col-xl-2 mx-auto mb-4">
                    <!-- Links -->
                    <h6 class="text-uppercase fw-bold mb-4">
                        Useful links
                    </h6>
                    <p>
                        <a href="profile.php" class="text-reset">Your Account</a>
                    </p>
                    <p>
                        <a href="orders.php" class="text-reset">Orders</a>
                    </p>
                    <p>
                        <a href="contact.php" class="text-reset">Help</a>
                    </p>
                </div>
                <!-- Grid column -->

                <!-- Grid column -->
                <div class="col-md-4 col-lg-3 col-xl-3 mx-auto mb-md-0 mb-4">
                    <!-- Links -->
                    <h6 class="text-uppercase fw-bold mb-4">Contact</h6>
                    <p><i class="fas fa-home me-3"></i> New York, NY 10012, US</p>
                    <p>
                        <i class="fas fa-envelope me-3"></i>
                        info@voltix.com
                    </p>
                    <p><i class="fas fa-phone me-3"></i> + 01 234 567 88</p>
                </div>
                <!-- Grid column -->
            </div>
            <!-- Grid row -->
        </div>
    </section>
    <!-- Section: Links  -->

    <!-- Copyright -->
    <div class="text-center p-4" style="background-color: rgba(0, 0, 0, 0.2);">
        © 2026 Copyright:
        <a class="text-reset fw-bold" href="#">Voltix.com</a>
    </div>
    <!-- Copyright -->
</footer>
<!-- Footer -->

<!-- MDB -->
<script type="text/javascript" src="https://cdnjs.cloudflare.com/ajax/libs/mdb-ui-kit/7.1.0/mdb.umd.min.js"></script>
<script>
    // Auto-dismiss alerts after 3 seconds
    setTimeout(() => {
        document.querySelectorAll('.alert').forEach(alert => {
            // Using MDB's built-in alert close method
            const bsAlert = mdb.Alert.getOrCreateInstance(alert);
            if (bsAlert) bsAlert.close();
        });
    }, 3000);
</script>
</body>

</html>