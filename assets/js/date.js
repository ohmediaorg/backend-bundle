function enhance(input) {
  const group = document.createElement('div');
  group.className = 'input-group';

  const isSmall =
    input.classList.contains('form-control-sm') ||
    input.parentNode.classList.contains('input-group-sm');

  if (isSmall) {
    group.classList.add('input-group-sm');
  }

  input.parentNode.insertBefore(group, input);

  group.appendChild(input);

  const today = document.createElement('button');
  today.type = 'button';
  today.className = 'btn btn-secondary';
  today.title = 'Now';
  today.ariaLabel = 'Set the input value to the current date';
  today.innerHTML = '<i class="bi bi-clock"></i>';

  group.appendChild(today);

  today.addEventListener('click', function (e) {
    e.preventDefault();

    const date = new Date();

    const dateParts = [
      date.getFullYear(),
      ('0' + (date.getMonth() + 1)).slice(-2),
      ('0' + date.getDate()).slice(-2),
    ];

    let value = dateParts.join('-');

    if ('datetime-local' === input.type) {
      const timeParts = [
        ('0' + date.getHours()).slice(-2),
        ('0' + date.getMinutes()).slice(-2),
      ];

      value += 'T' + timeParts.join(':');
    }

    input.value = value;
  });

  const clear = document.createElement('button');
  clear.type = 'button';
  clear.className = 'btn btn-danger';
  clear.title = 'Clear';
  clear.ariaLabel = 'Clear date input value';
  clear.innerHTML = '<i class="bi bi-x-circle"></i>';

  group.appendChild(clear);

  clear.addEventListener('click', function (e) {
    e.preventDefault();

    input.value = null;
  });
}

document
  .querySelectorAll('input[type="date"]:not([data-bypass])')
  .forEach(enhance);

document
  .querySelectorAll('input[type="datetime-local"]:not([data-bypass])')
  .forEach(enhance);
