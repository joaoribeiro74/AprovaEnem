const formatPhone = (raw) => {
  const digits = raw.replace(/\D/g, '').slice(0, 11);

  if (digits.length === 0) return '';
  if (digits.length <= 2) return `(${digits}`;
  if (digits.length <= 7) return `(${digits.slice(0, 2)}) ${digits.slice(2)}`;

  return `(${digits.slice(0, 2)}) ${digits.slice(2, 7)}-${digits.slice(7)}`;
};

document.querySelectorAll('[data-mask="phone"]').forEach((input) => {
  input.value = formatPhone(input.value);

  input.addEventListener('input', () => {
    input.value = formatPhone(input.value);
  });
});