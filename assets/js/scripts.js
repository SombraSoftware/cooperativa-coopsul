document.addEventListener('DOMContentLoaded', function() {
    const newsContainer = document.getElementById('noticias-coopsul');
    if (newsContainer) {
        fetch('./api/noticias.php?limite=6', { headers: { Accept: 'application/json' } })
            .then(function(response) {
                if (!response.ok) throw new Error('Falha ao carregar notícias');
                return response.json();
            })
            .then(function(data) {
                newsContainer.textContent = '';
                if (!data.noticias.length) {
                    const empty = document.createElement('p');
                    empty.className = 'news-status';
                    empty.textContent = 'Em breve, novas notícias da Coopsul.';
                    newsContainer.appendChild(empty);
                    return;
                }
                data.noticias.forEach(function(news) {
                    const article = document.createElement('article');
                    article.className = 'news-card';
                    if (news.image_url) {
                        const image = document.createElement('img');
                        image.src = news.image_url;
                        image.alt = '';
                        image.loading = 'lazy';
                        article.appendChild(image);
                    }
                    const body = document.createElement('div');
                    body.className = 'news-body';
                    const title = document.createElement('h3');
                    title.textContent = news.title;
                    const summary = document.createElement('p');
                    summary.textContent = news.summary;
                    const details = document.createElement('details');
                    const toggle = document.createElement('summary');
                    toggle.textContent = 'Leia mais';
                    const content = document.createElement('p');
                    content.className = 'news-content';
                    content.textContent = news.content;
                    details.append(toggle, content);
                    const date = document.createElement('small');
                    const published = news.published_at ? new Date(news.published_at.replace(' ', 'T') + 'Z') : new Date();
                    date.textContent = 'Por ' + news.author + ' · ' + new Intl.DateTimeFormat('pt-BR').format(published);
                    body.append(title, summary, details, date);
                    article.appendChild(body);
                    newsContainer.appendChild(article);
                });
            })
            .catch(function() {
                newsContainer.innerHTML = '<p class="news-status">Não foi possível carregar as notícias agora.</p>';
            });
    }

});
