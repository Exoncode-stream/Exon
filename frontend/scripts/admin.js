document.addEventListener('DOMContentLoaded', async () => {
    const token = localStorage.getItem('token');
    if (!token) {
        window.location.href = 'login.html';
        return;
    }

    try {
        const response = await fetch('http://localhost:8000/verify-token.php', {
            method: 'GET',
            headers: {
                'Authorization': `Bearer ${token}`
            }
        });

        if (!response.ok) {
            throw new Error('Invalid token');
        }

        const data = await response.json();
        if (!data.valid) {
            throw new Error('Invalid token');
        }
    } catch (error) {
        console.error('Session expired or invalid:', error);
        localStorage.removeItem('token');
        window.location.href = 'login.html';
    }
});

function handleFormSubmit(formId, endpoint, messageDivId) {
    document.getElementById(formId).addEventListener('submit', async function (e) {
        e.preventDefault();

        const messageDiv = document.getElementById(messageDivId);
        messageDiv.textContent = 'Sending...';
        messageDiv.className = '';

        const formData = new FormData(this);
        const data = Object.fromEntries(formData.entries());

        try {
            const token = localStorage.getItem('token');
            const response = await fetch(`http://localhost:8000/${endpoint}`, {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'Authorization': `Bearer ${token}`
                },
                body: JSON.stringify(data)
            });

            const result = await response.json();

            if (response.ok) {
                messageDiv.textContent = 'Success: ' + result.message;
                messageDiv.className = 'success';
                this.reset();
            } else {
                messageDiv.textContent = 'Error: ' + (result.error || 'Request failed');
                messageDiv.className = 'error';
            }
        } catch (error) {
            console.error('Error:', error);
            messageDiv.textContent = 'Network Error: ' + error.message;
            messageDiv.className = 'error';
        }
    });
}

handleFormSubmit('add-video-form', 'add-video.php', 'message-video');
handleFormSubmit('add-article-form', 'add-article.php', 'message-article');
