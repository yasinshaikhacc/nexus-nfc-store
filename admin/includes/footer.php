</div>

<!-- Scripts -->
<script src="https://cdnjs.cloudflare.com/ajax/libs/mdb-ui-kit/7.1.0/mdb.umd.min.js"></script>
<script>
    // Theme Toggle Logic
    const themeBtn = document.getElementById('themeToggle');
    const themeBtnMobile = document.getElementById('themeToggleMobile');
    const body = document.body;
    const icon = themeBtn ? themeBtn.querySelector('i') : null;
    const iconMobile = themeBtnMobile ? themeBtnMobile.querySelector('i') : null;

    function applyTheme(isLight) {
        if (isLight) {
            body.classList.add('light-mode');
            if (icon) { icon.classList.remove('fa-moon'); icon.classList.add('fa-sun'); }
            if (iconMobile) { iconMobile.classList.remove('fa-moon'); iconMobile.classList.add('fa-sun'); }
        } else {
            body.classList.remove('light-mode');
            if (icon) { icon.classList.remove('fa-sun'); icon.classList.add('fa-moon'); }
            if (iconMobile) { iconMobile.classList.remove('fa-sun'); iconMobile.classList.add('fa-moon'); }
        }
    }

    // Init
    const storedTheme = localStorage.getItem('theme');
    if (storedTheme === 'light') {
        applyTheme(true);
    }

    // Event Listeners
    if (themeBtn) {
        themeBtn.addEventListener('click', () => {
            const isLight = !body.classList.contains('light-mode');
            applyTheme(isLight);
            localStorage.setItem('theme', isLight ? 'light' : 'dark');
        });
    }
    if (themeBtnMobile) {
        themeBtnMobile.addEventListener('click', () => {
            const isLight = !body.classList.contains('light-mode');
            applyTheme(isLight);
            localStorage.setItem('theme', isLight ? 'light' : 'dark');
        });
    }

    // Sidebar auto-close on mobile
    document.querySelectorAll('.admin-sidebar .nav-link').forEach(l => {
        l.addEventListener('click', () => {
            if (window.innerWidth < 992) {
                document.querySelector('.admin-sidebar').classList.remove('show');
            }
        });
    });
</script>
</body>

</html>