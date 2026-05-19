</article>
  </main>

</div>

<script>
  const menuToggle = document.getElementById('menu-toggle');
  const sidebar = document.getElementById('sidebar');
  const overlay = document.getElementById('overlay');

  function toggleMenu() {
    if (window.innerWidth < 768) {
      sidebar.classList.toggle('active');
      if (overlay) {
        overlay.classList.toggle('hidden');
      }
    }
  }

  if (menuToggle) {
    menuToggle.addEventListener('click', toggleMenu);
  }

  if (overlay) {
    overlay.addEventListener('click', toggleMenu);
  }

  window.addEventListener('resize', function() {
    if (window.innerWidth >= 768) {
      sidebar.classList.remove('active');
      if (overlay) {
        overlay.classList.add('hidden');
      }
    }
  });
</script>

</body>
</html>