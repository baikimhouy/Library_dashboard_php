    </main>

    <footer class="bg-gradient-to-r from-romantic-deepblue to-romantic-lightblue text-white text-center p-4 mt-auto">
        <p>Library Management System &copy; <?php echo date('Y'); ?></p>
        
    </footer>

</body>
</html>
    </div>
  </main>

  <!-- JavaScript -->
  <script>
    document.addEventListener('DOMContentLoaded', () => {
      const menuBtn = document.getElementById('menu-btn');
      const sidebar = document.getElementById('sidebar');
      const overlay = document.getElementById('overlay');

      menuBtn.addEventListener('click', () => {
        sidebar.classList.toggle('-translate-x-full');
        overlay.classList.toggle('hidden');
        document.body.classList.toggle('overflow-hidden');
      });

      overlay.addEventListener('click', () => {
        sidebar.classList.add('-translate-x-full');
        overlay.classList.add('hidden');
        document.body.classList.remove('overflow-hidden');
      });

      document.addEventListener('click', (e) => {
        if (window.innerWidth < 768 && 
            !sidebar.contains(e.target) && 
            e.target !== menuBtn) {
          sidebar.classList.add('-translate-x-full');
          overlay.classList.add('hidden');
          document.body.classList.remove('overflow-hidden');
        }
      });

      window.addEventListener('resize', () => {
        if (window.innerWidth >= 768) {
          sidebar.classList.remove('-translate-x-full');
          overlay.classList.add('hidden');
          document.body.classList.remove('overflow-hidden');
        }
      });
    });
  </script>
</body>
</html>