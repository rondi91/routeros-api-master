// Mengatur collapse sidebar dan content
const sidebar = document.getElementById('sidebar');
const content = document.getElementById('content');
const collapseBtn = document.getElementById('collapseBtn');

collapseBtn.onclick = function () {
    sidebar.classList.toggle('collapsed');
    content.classList.toggle('collapsed');
    collapseBtn.classList.toggle('collapsed');
}
