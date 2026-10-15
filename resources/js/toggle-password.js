document.querySelectorAll('[data-password-toggle]').forEach((button) => {
  const input = document.getElementById(button.dataset.passwordToggle);
  const iconHidden = button.querySelector('[data-icon-hidden]');
  const iconVisible = button.querySelector('[data-icon-visible]');

  if (!input) return;

  button.addEventListener('click', () => {
    const show = input.type === 'password';

    input.type = show ? 'text' : 'password';
    iconHidden.classList.toggle('hidden', show);
    iconVisible.classList.toggle('hidden', !show);

    button.setAttribute('aria-pressed', String(show));
    button.setAttribute('aria-label', show ? 'Ocultar senha' : 'Mostrar senha');
  });
});