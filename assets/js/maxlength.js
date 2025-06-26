function init(input) {
  const label = document.querySelector(`label[for="${input.id}"]`);

  if (!label) {
    return;
  }

  if (input.dataset.maxlengthInit) {
    return;
  }

  input.dataset.maxlengthInit = 1;

  label.style.display = 'block';

  const badge = document.createElement('span');

  label.appendChild(badge);

  function updateBadge() {
    const maxlength = input.maxLength * 1;

    const currentLength = input.value.length;

    const percent = (currentLength / maxlength) * 100;

    badge.textContent = `${currentLength} / ${maxlength}`;

    if (100 <= percent) {
      badge.className = 'badge text-bg-danger float-end';
    } else if (90 <= percent) {
      badge.className = 'badge text-bg-warning float-end';
    } else {
      badge.className = 'badge text-bg-success float-end';
    }
  }

  input.addEventListener('input', updateBadge);
  input.addEventListener('change', updateBadge);

  updateBadge();
}

export default function (container) {
  const inputs = container.querySelectorAll(
    'input[maxlength],textarea[maxlength]'
  );

  inputs.forEach(init);
}
