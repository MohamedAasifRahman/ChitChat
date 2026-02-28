document.addEventListener('DOMContentLoaded', function(){
  const search = document.getElementById('userSearch');
  const results = document.getElementById('searchResults');
  if (search && results) {
    let t;
    search.addEventListener('input', () => {
      clearTimeout(t);
      const q = search.value.trim();
      if (q.length < 2){ results.style.display='none'; return; }
      t = setTimeout(async () => {
        const res = await fetch('api/search.php?q=' + encodeURIComponent(q));
        const data = await res.json();
        if (!Array.isArray(data) || data.length === 0){ results.style.display='none'; return; }
        results.innerHTML = data.map(u => `<a href="user.php?u=${u.id}">${u.full_name}</a>`).join('');
        results.style.display='block';
      }, 200);
    });
  }
  const file = document.getElementById('postImage');
  const preview = document.getElementById('preview');
  if (file && preview) {
    file.addEventListener('change', () => {
      const [f] = file.files;
      if (!f) { preview.style.display='none'; return; }
      const url = URL.createObjectURL(f);
      preview.src = url; preview.style.display = 'block';
    });
  }
});