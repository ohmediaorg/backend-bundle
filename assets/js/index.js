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

document.querySelectorAll('a[target=_blank]').forEach((a) => {
  a.innerHTML += '&nbsp;<i class="bi bi-box-arrow-up-right"></i>';
});

const dropdowns = document.querySelectorAll('#side-nav .nav-item.dropdown');

dropdowns.forEach((dropdown) => {
  dropdown.classList.add('dropend');
});

document.querySelectorAll('table.table').forEach((table) => {
  if (!table.parentNode.classList.contains('table-responsive')) {
    const wrapper = document.createElement('div');
    wrapper.className = 'table-responsive';

    table.parentNode.insertBefore(wrapper, table);

    wrapper.appendChild(table);
  }
});
