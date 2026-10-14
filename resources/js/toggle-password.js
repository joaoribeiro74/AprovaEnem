const passwordInput = document.getElementById('password');
const toggleButton = document.getElementById('toggle-password');
const eyeOff = document.getElementById('eye-off');
const eyeOn = document.getElementById('eye-on');

if (passwordInput && toggleButton) {
  toggleButton.addEventListener('click', () => {
    const show = passwordInput.type === 'password';

    passwordInput.type = show ? 'text' : 'password';
    eyeOff.classList.toggle('hidden', show);
    eyeOn.classList.toggle('hidden', !show);

    toggleButton.setAttribute('aria-pressed', String(show));
    toggleButton.setAttribute('aria-label', show ? 'Ocultar senha' : 'Mostrar senha');
  });
}