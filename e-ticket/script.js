document.addEventListener("DOMContentLoaded", () => {
    const loginForm = document.getElementById('loginForm');
    loginForm.addEventListener('submit', (e) => {
        const username = document.getElementById('username').value;
        const password = document.getElementById('password').value;
        
        // Simple front-end validation
        if (username.trim() === '' || password.trim() === '') {
            e.preventDefault();
            alert("Please fill in all fields.");
        }
    });
});

document.addEventListener("DOMContentLoaded", () => {
    const signupForm = document.getElementById('signupForm');
    signupForm?.addEventListener('submit', (e) => {
        const password = document.getElementById('password').value;
        const confirmPassword = document.getElementById('confirm_password').value;
        if (password !== confirmPassword) {
            e.preventDefault();
            alert("Passwords do not match.");
        }
    });
});

