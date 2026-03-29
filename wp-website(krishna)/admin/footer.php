            </div>
        </main>
    </div>
    <script>
        document.addEventListener('DOMContentLoaded', () => {
            const sidebar = document.getElementById('sidebar');
            const toggle = document.getElementById('sidebar-toggle');
            const overlay = document.getElementById('sidebar-overlay');

            if (toggle && sidebar && overlay) {
                toggle.addEventListener('click', () => {
                    sidebar.classList.toggle('active');
                    overlay.classList.toggle('active');
                });

                overlay.addEventListener('click', () => {
                    sidebar.classList.remove('active');
                    overlay.classList.remove('active');
                });
                
                // Close sidebar on link click (for mobile)
                const navLinks = sidebar.querySelectorAll('.nav-item');
                navLinks.forEach(link => {
                    link.addEventListener('click', () => {
                        if (window.innerWidth <= 1024) {
                            sidebar.classList.remove('active');
                            overlay.classList.remove('active');
                        }
                    });
                });
            }
        });
    </script>
</body>
</html>
