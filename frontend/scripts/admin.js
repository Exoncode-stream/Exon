document.getElementById('add-video-form').addEventListener('submit', async function (e) {
    e.preventDefault();

    const messageDiv = document.getElementById('message');
    messageDiv.textContent = 'Sending...';
    messageDiv.className = '';

    const formData = new FormData(this);
    const data = Object.fromEntries(formData.entries());

    try {
        const response = await fetch('http://localhost:8000/add-video.php', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json'
            },
            body: JSON.stringify(data)
        });

        const result = await response.json();

        if (response.ok) {
            messageDiv.textContent = 'Success: Video added!';
            messageDiv.className = 'success';
            this.reset();
        } else {
            messageDiv.textContent = 'Error: ' + (result.error || 'Failed to add video');
            messageDiv.className = 'error';
        }
    } catch (error) {
        console.error('Error:', error);
        messageDiv.textContent = 'Network Error: ' + error.message;
        messageDiv.className = 'error';
    }
});
