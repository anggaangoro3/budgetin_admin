function searchUser() {
    const phone = document.getElementById('searchPhone').value;

    // Kirim permintaan AJAX untuk mencari data
    fetch('edit_user.php', {
        method: 'POST',
        headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
        body: 'search_phone=' + encodeURIComponent(phone)
    })
        .then(response => response.json())
        .then(data => {
            if (data.status === 'success') {
                // Isi form dengan data pengguna
                document.getElementById('userId').value = data.data.user_id;
                document.getElementById('name').value = data.data.name;
                document.getElementById('phone').value = data.data.phone;
                document.getElementById('email').value = data.data.email;
                document.getElementById('balance').value = data.data.balance;
                // Tampilkan popup
                document.getElementById('editPopup').style.display = 'block';
            } else {
                alert(data.message);
            }
        });
}

function submitEdit() {
    const form = document.getElementById('editForm');
    const formData = new FormData(form);

    // Kirim permintaan AJAX untuk update data
    fetch('edit_user.php', {
        method: 'POST',
        body: new URLSearchParams(formData)
    })
        .then(response => response.json())
        .then(data => {
            if (data.status === 'success') {
                alert(data.message);
                document.getElementById('editPopup').style.display = 'none';
                location.reload(); // Refresh halaman
            } else {
                alert(data.message);
            }
        });
}

function closePopup() {
    document.getElementById('editPopup').style.display = 'none';
}
