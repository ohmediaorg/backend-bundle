import '@popperjs/core';
import * as Bootstrap from 'bootstrap';
window.Bootstrap = Bootstrap;

import './alert.js';
import './confirm.js';
import './sortable.js';

import bootstrapToast from './toast.js';
window.bootstrapToast = bootstrapToast;

import Sortable from 'sortablejs';
window.Sortable = Sortable;

import '../../../wysiwyg-bundle/assets/js/index.js';

import NiceSelect from 'nice-select2';

document.querySelectorAll('select.nice-select2').forEach((select) => {
  for (let i = 0; i < select.selectedOptions.length; i++) {
    // ensures the starting/default value is shown as selected in the nice-select2 UI
    select.selectedOptions.item(i).setAttribute('selected', '');
  }

  new NiceSelect(select, {
    searchable: true,
    placeholder: select.placeholder,
  });
});

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

document.querySelectorAll('[data-bs-toggle="tooltip"]').forEach((el) => {
  new Bootstrap.Tooltip(el);
});

document.querySelectorAll('[data-bs-toggle="popover"]').forEach((el) => {
  new Bootstrap.Popover(el);
});

document
  .querySelectorAll('a[target=_blank]:not([data-bypass])')
  .forEach((a) => {
    a.innerHTML += '&nbsp;<i class="bi bi-box-arrow-up-right"></i>';
  });

document.querySelectorAll('table.table').forEach((table) => {
  if (!table.parentNode.classList.contains('table-responsive')) {
    const wrapper = document.createElement('div');
    wrapper.className = 'table-responsive';

    table.parentNode.insertBefore(wrapper, table);

    wrapper.appendChild(table);
  }
});
