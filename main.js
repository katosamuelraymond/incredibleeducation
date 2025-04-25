const toggleBtn = document.getElementById('toggleSidebar');
const openBtn = document.getElementById('openSidebar');
const sidebar = document.getElementById('sidebarMenu');
const mainContent = document.getElementById('mainContent');

toggleBtn.addEventListener('click', () => {
  // Toggle for both large and small screens
  sidebar.classList.toggle('collapsed');
  sidebar.classList.toggle('show'); // Show/hide for mobile
  mainContent.classList.toggle('expanded');
});

openBtn?.addEventListener('click', () => {
  // Explicitly open the sidebar
  sidebar.classList.remove('collapsed');
  sidebar.classList.add('show');
  mainContent.classList.remove('expanded');
});

function simpleCountdown() {
    document.querySelectorAll('.countdown').forEach(function(el) {
        const date = new Date(el.getAttribute('data-date'));
        const now = new Date();
        const diff = date - now;

        if (diff > 0) {
            const totalSeconds = Math.floor(diff / 1000);
            const days = Math.floor(totalSeconds / 86400);
            const hours = Math.floor((totalSeconds % 86400) / 3600);
            const minutes = Math.floor((totalSeconds % 3600) / 60);
            const seconds = totalSeconds % 60;

            el.textContent = `${days}d ${hours}h ${minutes}m ${seconds}s`;
        } else {
            el.textContent = "Started";
        }
    });
}

// Start countdown and update every second
simpleCountdown();
setInterval(simpleCountdown, 1000);

setTimeout(function() {
  var alert = document.getElementById('messageAlert');
  alert.classList.remove('show');
  alert.classList.add('fade');
}, 3000);const toggleBtn = document.getElementById('toggleSidebar');
const openBtn = document.getElementById('openSidebar');
const sidebar = document.getElementById('sidebarMenu');
const mainContent = document.getElementById('mainContent');

toggleBtn.addEventListener('click', () => {
  // Toggle for both large and small screens
  sidebar.classList.toggle('collapsed');
  sidebar.classList.toggle('show'); // Show/hide for mobile
  mainContent.classList.toggle('expanded');
});

openBtn?.addEventListener('click', () => {
  // Explicitly open the sidebar
  sidebar.classList.remove('collapsed');
  sidebar.classList.add('show');
  mainContent.classList.remove('expanded');
});

function simpleCountdown() {
    document.querySelectorAll('.countdown').forEach(function(el) {
        const date = new Date(el.getAttribute('data-date'));
        const now = new Date();
        const diff = date - now;

        if (diff > 0) {
            const totalSeconds = Math.floor(diff / 1000);
            const days = Math.floor(totalSeconds / 86400);
            const hours = Math.floor((totalSeconds % 86400) / 3600);
            const minutes = Math.floor((totalSeconds % 3600) / 60);
            const seconds = totalSeconds % 60;

            el.textContent = `${days}d ${hours}h ${minutes}m ${seconds}s`;
        } else {
            el.textContent = "Started";
        }
    });
}

// Start countdown and update every second
simpleCountdown();
setInterval(simpleCountdown, 1000);

setTimeout(function() {
  var alert = document.getElementById('messageAlert');
  alert.classList.remove('show');
  alert.classList.add('fade');
}, 3000);