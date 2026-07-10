document.getElementById('login-form').addEventListener('submit', async function (e) {
    e.preventDefault();

    const messageDiv = document.getElementById('message');
    messageDiv.textContent = 'Connexion en cours...';
    messageDiv.className = '';

    const formData = new FormData(this);
    const data = Object.fromEntries(formData.entries());

    try {
        const response = await fetch('http://localhost:8000/login.php', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json'
            },
            body: JSON.stringify(data)
        });

        const result = await response.json();

        if (response.ok) {
            messageDiv.textContent = 'Succès : Connecté !';
            messageDiv.className = 'success';

            localStorage.setItem('token', result.token);

            setTimeout(() => {
                window.location.href = 'admin.html';
            }, 1000);
        } else {
            messageDiv.textContent = 'Erreur : ' + (result.error || 'Échec de la connexion');
            messageDiv.className = 'error';
        }
    } catch (error) {
        console.error('Error:', error);
        messageDiv.textContent = 'Erreur réseau : ' + error.message;
        messageDiv.className = 'error';
    }
});
