// watch status management/toggle_status.js
function toggleStatus(id, currentStatus) {
    const formData = new FormData();
    formData.append('id', id);
    formData.append('current_status', currentStatus);

    fetch('watchlistcontroller.php', {
        method: 'POST',
        body: formData,
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
            alert('Update failed.');
        }
    })
    .catch(error => {
        console.error('Error updating watch status:', error);
        alert('Unable to update watch status. Please try again.');
    });
}
