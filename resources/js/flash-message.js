document.addEventListener('click', (event) => {
  const botao = event.target.closest('[data-flash-close]');
  if (!botao) return;

  const flash = botao.closest('[data-flash]');
  if (!flash) return;

  flash.style.opacity = '0';
  setTimeout(() => flash.remove(), 500);
});