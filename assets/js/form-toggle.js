function toggleForm(id) {
    const form = document.getElementById(id);
    const btn = document.getElementById('btn-' + id);
    
    if (form.classList.contains('hidden')) {
        form.classList.remove('hidden');
        if(btn) btn.classList.add('hidden');
    } else {
        form.classList.add('hidden');
        if(btn) btn.classList.remove('hidden');
    }
}