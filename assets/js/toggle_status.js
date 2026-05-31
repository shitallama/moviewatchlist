// watch status management/toggle_status.js
function toggleStatus(id, currentStatus) {
    const body = new URLSearchParams();
    body.append('id', id);
    body.append('current_status', currentStatus ? '1' : '0');

    fetch('watchlistcontroller.php', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/x-www-form-urlencoded; charset=UTF-8',
            'X-Requested-With': 'XMLHttpRequest'
        },
        body: body.toString()
    })
    .then(response => {
        if (!response.ok) {
            throw new Error('Network response was not OK');
        }
        return response.json();
    })
    .then(data => {
        if (data.success) {
            location.reload();
        } else {
            alert(data.error || 'Update failed.');
        }
    })
    .catch(error => {
        console.error('Error updating watch status:', error);
        alert('Unable to update watch status. Please try again.');
    });
}
