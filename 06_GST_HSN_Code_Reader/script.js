document.addEventListener('DOMContentLoaded', () => {
    const dataTable = document.getElementById('data-table');
    const uploadStatus = document.getElementById('upload-status');

    // Load table data from the server
    async function loadTableData() {
        const response = await fetch('fetch_data.php');
        const data = await response.json();
        dataTable.innerHTML = data.map(row => `
            <tr>
                <td>${row.hsn_code}</td>
                <td>${row.description}</td>
                <td>${row.tax_rate}</td>
                <td>
                    <button class="btn btn-warning btn-sm edit-btn" data-id="${row.id}">Edit</button>
                    <button class="btn btn-danger btn-sm delete-btn" data-id="${row.id}">Delete</button>
                </td>
            </tr>
        `).join('');
    }

    // File upload form handler
    document.getElementById('upload-form').addEventListener('submit', async (e) => {
        e.preventDefault();
        const fileInput = document.getElementById('file');
        if (fileInput.files.length === 0) return;

        const file = fileInput.files[0];
        const formData = new FormData();
        formData.append('file', file);

        uploadStatus.textContent = "Uploading...";
        const response = await fetch('upload_file.php', { method: 'POST', body: formData });
        const result = await response.text();

        uploadStatus.textContent = result;
        loadTableData();
    });

    // Handle Delete Action
    dataTable.addEventListener('click', async (e) => {
        if (e.target.classList.contains('delete-btn')) {
            const id = e.target.getAttribute('data-id');
            if (confirm('Are you sure you want to delete this row?')) {
                const response = await fetch('delete_data.php', {
                    method: 'POST',
                    headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
                    body: `id=${id}`
                });
                alert(await response.text());
                loadTableData();
            }
        }
    });

    // Initial data load
    loadTableData();
});