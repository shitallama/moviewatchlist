/* Watchlist interactions */
function applyFilters() {
    var q  = document.getElementById('search').value.toLowerCase().trim();
    var sf = document.getElementById('statusFilter').value.toLowerCase();
    document.querySelectorAll('#tbody tr').forEach(function(row) {
        var title  = row.querySelector('.title-cell').textContent.toLowerCase();
        var state  = (row.dataset.state || '').toLowerCase();
        var tMatch = !q  || title.includes(q);
        var sMatch = !sf || state === sf;
        row.style.display = tMatch && sMatch ? '' : 'none';
    });
}

function clearFilters() {
    document.getElementById('search').value = '';
    document.getElementById('statusFilter').value = '';
    applyFilters();
}

function toggleWatched(btn, movieId, currentStatus) {
    var normalizedStatus = Number(currentStatus) || 0;
    fetch('watchlistcontroller.php', {
        method: 'POST',
        headers: {'Content-Type': 'application/x-www-form-urlencoded'},
        body: 'id=' + movieId + '&current_status=' + normalizedStatus
    })
    .then(function(r){ return r.json(); })
    .then(function(data) {
        if (data.success) {
            var isNowOn = normalizedStatus ? 0 : 1;
            var row = btn.closest('tr');
            btn.classList.toggle('on', isNowOn);
            btn.querySelector('.watch-label').textContent = isNowOn ? 'Watched' : 'Unwatch';
            btn.setAttribute('onclick',
                'toggleWatched(this,' + movieId + ',' + (isNowOn ? 1 : 0) + ')');

            if (row) {
                var pill = row.querySelector('.pill');
                var bar = row.querySelector('.bar span');
                var progressText = row.querySelector('.progress small');
                var nextState = isNowOn ? 'completed' : 'plan';
                var nextProgress = isNowOn ? 100 : 0;

                row.dataset.state = nextState;
                if (pill) {
                    pill.classList.remove('plan', 'watching', 'completed');
                    pill.classList.add(nextState);
                    pill.textContent = nextState.charAt(0).toUpperCase() + nextState.slice(1);
                }
                if (bar) {
                    bar.style.width = nextProgress + '%';
                }
                if (progressText) {
                    progressText.textContent = nextProgress + '%';
                }
            }
        } else {
            alert(data.error || 'Could not update status. Please try again.');
        }
    })
    .catch(function(){ alert('Could not update status. Please try again.'); });
}

function openEditModal(statusId, movieId, title, state, progress) {
    document.getElementById('editStatusId').value   = statusId;
    document.getElementById('editMovieId').value    = movieId;
    document.getElementById('editMovieTitle').value = title;
    document.getElementById('editWatchState').value = state;
    document.getElementById('editProgress').value   = progress;
    document.getElementById('editProgressLabel').textContent = progress + '%';
    document.getElementById('editOverlay').classList.add('show');
}

function closeEditModal() {
    document.getElementById('editOverlay').classList.remove('show');
}

function openAddModal() {
    document.getElementById('addOverlay').classList.add('show');
}

function closeAddModal() {
    document.getElementById('addOverlay').classList.remove('show');
}

document.addEventListener('DOMContentLoaded', function() {
    var editOverlay = document.getElementById('editOverlay');
    if (editOverlay) {
        editOverlay.addEventListener('click', function(e){
            if (e.target === this) {
                closeEditModal();
            }
        });
    }

    var addOverlay = document.getElementById('addOverlay');
    if (addOverlay) {
        addOverlay.addEventListener('click', function(e){
            if (e.target === this) {
                closeAddModal();
            }
        });
    }
});
