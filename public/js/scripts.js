document.addEventListener('DOMContentLoaded', function () {
    // Highlight.js initialiseren voor code snippets
    document.querySelectorAll('pre code').forEach((block) => {
        hljs.highlightBlock(block);
        addLineNumbers(block);
    });

    // Voeg lijnnummers toe aan code blokken
    function addLineNumbers(block) {
        // Skip if it's a small code preview
        if (block.closest('.post-preview')) return;

        const lines = block.innerHTML.split('\n');
        let numbered = '';

        for (let i = 0; i < lines.length; i++) {
            if (i === lines.length - 1 && lines[i].trim() === '') continue; // Skip empty last line
            numbered += `<span class="line-number">${i + 1}</span>${lines[i]}\n`;
        }

        block.innerHTML = numbered;
        block.parentNode.classList.add('line-numbers');
    }

    // Taal badges toevoegen aan code blokken
    document.querySelectorAll('.post-content').forEach(content => {
        const codeBlock = content.querySelector('code');
        if (codeBlock && !content.querySelector('.language-badge')) {
            const language = codeBlock.className.replace('language-', '').replace('hljs', '').trim();
            if (language) {
                const badge = document.createElement('div');
                badge.className = 'language-badge';
                badge.textContent = language;
                content.appendChild(badge);
            }
        }
    });

    // Kopieer code functionaliteit
    document.querySelectorAll('.copy-code-btn').forEach(btn => {
        btn.addEventListener('click', function (e) {
            e.preventDefault();
            const postId = this.getAttribute('data-post-id');
            const codeBlock = document.querySelector(`.post[data-post-id="${postId}"] .post-content code`) ||
                document.querySelector('.post-content code');

            if (codeBlock) {
                // Verwijder lijnnummers voor het kopiëren
                const textToCopy = codeBlock.innerText.replace(/^\d+/gm, '').trim();

                navigator.clipboard.writeText(textToCopy).then(() => {
                    const toast = document.getElementById('copyToast');
                    if (toast) {
                        toast.classList.add('show');
                        setTimeout(() => toast.classList.remove('show'), 2000);
                    }
                }).catch(err => {
                    console.error('Kon code niet kopiëren: ', err);
                });
            }
        });
    });

    // Likes afhandelen met AJAX
    const likeForms = document.querySelectorAll('.like-form');
    likeForms.forEach(form => {
        form.addEventListener('submit', function (e) {
            e.preventDefault();

            const formData = new FormData(form);
            const button = form.querySelector('.btn-like');
            const likesCount = form.closest('.post-action') ?
                form.closest('.post-action').querySelector('.likes-count') :
                form.closest('.post-actions').querySelector('.likes-count');
            const postId = formData.get('post_id');

            fetch('/posts/like', {
                method: 'POST',
                body: formData,
                headers: {
                    'X-Requested-With': 'XMLHttpRequest'
                }
            })
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        button.classList.toggle('liked');
                        // Zorg ervoor dat het aantal likes als een getal wordt behandeld
                        const likesNumber = parseInt(data.likes, 10);

                        // Update het aantal likes
                        likesCount.textContent = likesNumber;

                        // Update alle andere like buttons voor dezelfde post op de pagina
                        updateAllLikeButtonsForPost(postId, data.liked, likesNumber);

                        // Sla de like status op in localStorage als backup
                        updateLocalStorageLikeStatus(postId, data.liked);
                    }
                })
                .catch(error => {
                    console.error('Error:', error);
                });
        });
    });

    // Functie om alle like buttons voor dezelfde post bij te werken
    function updateAllLikeButtonsForPost(postId, liked, likesCount) {
        document.querySelectorAll('.like-form input[name="post_id"][value="' + postId + '"]').forEach(input => {
            const likeForm = input.closest('.like-form');
            const likeButton = likeForm.querySelector('.btn-like');
            const likesCountElement = likeForm.closest('.post-action') ?
                likeForm.closest('.post-action').querySelector('.likes-count') :
                likeForm.closest('.post-actions').querySelector('.likes-count');

            if (liked) {
                likeButton.classList.add('liked');
            } else {
                likeButton.classList.remove('liked');
            }

            // Als het aantal likes is meegegeven, update ook de teller
            if (likesCount !== undefined) {
                // Zorg ervoor dat likesCount een getal is
                const likesNumber = parseInt(likesCount, 10);
                likesCountElement.textContent = likesNumber;
            }
        });
    }

    // Functie om de like status in localStorage op te slaan
    function updateLocalStorageLikeStatus(postId, liked) {
        try {
            let likedPosts = JSON.parse(localStorage.getItem('likedPosts')) || {};
            if (liked) {
                likedPosts[postId] = true;
            } else {
                delete likedPosts[postId];
            }
            localStorage.setItem('likedPosts', JSON.stringify(likedPosts));
        } catch (e) {
            console.error('Error updating localStorage:', e);
        }
    }

    // Commentaar formulieren afhandelen met AJAX
    const commentForms = document.querySelectorAll('.comment-form');
    commentForms.forEach(form => {
        form.addEventListener('submit', function (e) {
            // Alleen bij AJAX verzoeken, voorkom normale submit
            if (form.classList.contains('ajax-enabled')) {
                e.preventDefault();

                const formData = new FormData(form);
                const input = form.querySelector('input[name="content"]');
                const commentsContainer = form.closest('.post-comments') || form.closest('.comments-section');

                if (input.value.trim() === '') {
                    return;
                }

                fetch('/comments/create', {
                    method: 'POST',
                    body: formData,
                    headers: {
                        'X-Requested-With': 'XMLHttpRequest'
                    }
                })
                    .then(response => response.json())
                    .then(data => {
                        if (data.success) {
                            // Nieuw commentaar toevoegen aan de DOM
                            const newComment = document.createElement('div');
                            newComment.className = 'comment';

                            // HTML voor het comment bouwen
                            const userLink = document.createElement('a');
                            userLink.href = '/profile?id=' + data.user_id; // Verwijst naar de gebruiker
                            userLink.className = 'comment-user';
                            userLink.textContent = data.username;

                            const commentContent = document.createElement('span');
                            commentContent.className = 'comment-content';
                            commentContent.textContent = data.content;

                            const commentTime = document.createElement('div');
                            commentTime.className = 'comment-time';
                            commentTime.textContent = data.created_at;

                            // Elementen toevoegen aan het comment
                            newComment.appendChild(userLink);
                            newComment.appendChild(document.createTextNode(' '));
                            newComment.appendChild(commentContent);
                            newComment.appendChild(commentTime);

                            // Comment invoegen voor het formulier
                            const commentsSection = commentsContainer.querySelector('.comments-section') || commentsContainer;
                            const noCommentsMessage = commentsSection.querySelector('.no-comments');

                            if (noCommentsMessage) {
                                noCommentsMessage.remove(); // Verwijder "Nog geen reacties" als dat er staat
                            }

                            commentsSection.insertBefore(newComment, form);

                            // Update commentaarteller
                            const commentCount = document.querySelector('.post-action-count');
                            if (commentCount) {
                                const currentCount = parseInt(commentCount.textContent, 10);
                                commentCount.textContent = currentCount + 1;
                            }

                            // Formulier resetten
                            input.value = '';
                        }
                    })
                    .catch(error => {
                        console.error('Error:', error);
                    });
            }
        });
    });

    // Dark mode toggle (indien aanwezig)
    const darkModeToggle = document.querySelector('.dark-mode-toggle');
    if (darkModeToggle) {
        darkModeToggle.addEventListener('click', function () {
            document.body.classList.toggle('light-mode');
            const isDarkMode = !document.body.classList.contains('light-mode');
            localStorage.setItem('darkMode', isDarkMode);

            // Update icon
            const icon = this.querySelector('i');
            if (icon) {
                if (isDarkMode) {
                    icon.className = 'fa-solid fa-sun';
                } else {
                    icon.className = 'fa-solid fa-moon';
                }
            }
        });

        // Check localStorage voor gebruikersvoorkeur
        const prefersDarkMode = localStorage.getItem('darkMode') !== 'false';
        if (!prefersDarkMode) {
            document.body.classList.add('light-mode');
            const icon = darkModeToggle.querySelector('i');
            if (icon) {
                icon.className = 'fa-solid fa-moon';
            }
        }
    }

    // Mobiele menu toggle
    const menuToggle = document.querySelector('.menu-toggle');
    const sidebar = document.querySelector('.sidebar');

    if (menuToggle && sidebar) {
        menuToggle.addEventListener('click', function () {
            sidebar.classList.toggle('show');
            document.body.classList.toggle('sidebar-open');
        });
    }

    // Sluit sidebar wanneer er buiten wordt geklikt (alleen op mobiel)
    document.addEventListener('click', function (event) {
        if (sidebar && sidebar.classList.contains('show')) {
            if (!sidebar.contains(event.target) && !menuToggle.contains(event.target)) {
                sidebar.classList.remove('show');
                document.body.classList.remove('sidebar-open');
            }
        }
    });

    // Extra: Synchronisatie van likes op basis van localStorage als backup
    try {
        const likedPosts = JSON.parse(localStorage.getItem('likedPosts')) || {};
        Object.keys(likedPosts).forEach(postId => {
            updateAllLikeButtonsForPost(postId, true);
        });
    } catch (e) {
        console.error('Error reading from localStorage:', e);
    }
});