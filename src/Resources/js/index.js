import './alert.js';
import './confirm.js';

document.addEventListener('DOMContentLoaded', function () {
  function preventDoubleSubmit(form) {
    let submitted = false;

    form.addEventListener('submit', (e) => {
      if (submitted) {
        e.preventDefault();

        return false;
      }

      submitted = true;
    });
  }

  document.querySelectorAll('form').forEach((form) => {
    preventDoubleSubmit(form);
  });

  document.querySelectorAll('a[target=_blank]').forEach((a) => {
    a.innerHTML += '&nbsp;<i class="bi bi-box-arrow-up-right"></i>';
  });

  // TODO: see if there's a nice way to set this through the bootstrap-bundle
  const dropdowns = document.querySelectorAll('#side-nav .nav-item.dropdown');

  dropdowns.forEach((dropdown) => {
    dropdown.classList.add('dropend');
  });

  // TODO: strip out honeypot from antispam bundle?
  // or make the class in the honeypot bundle fieldset-nostyle
  document.querySelectorAll('.form-row-topyenoh').forEach((fieldset) => {
    fieldset.classList.add('fieldset-nostyle');
  });

  document.querySelectorAll('table.table').forEach((table) => {
    if (!table.parentNode.classList.contains('table-responsive')) {
      const wrapper = document.createElement('div');
      wrapper.className = 'table-responsive';

      table.parentNode.insertBefore(wrapper, table);

      wrapper.appendChild(table);
    }
  });
});
