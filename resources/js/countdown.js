document.querySelectorAll('[data-countdown]').forEach((el) => {
  const { template, done } = el.dataset;
  const flash = el.closest('[data-flash]');
  const end = Date.now() + Number(el.dataset.countdown) * 1000;

  const finish = () => {
    const successTemplate = document.getElementById('flash-success-template');

    if (!successTemplate || !flash) {
      el.textContent = done;
      return;
    }

    const success = successTemplate.content.firstElementChild.cloneNode(true);
    success.querySelector('[data-flash-text]').textContent = done;
    flash.replaceWith(success);
  };

  const update = () => {
    const remaining = Math.max(0, Math.ceil((end - Date.now()) / 1000));

    if (remaining > 0) {
      el.textContent = template.replace(':seconds', remaining);
      setTimeout(update, 250);
    } else {
      finish();
    }
  };

  update();
});