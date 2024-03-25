import { Modal } from 'bootstrap';

const confirmModalInnerHTML = `
<div class="modal-dialog">
  <div class="modal-content">
    <div class="modal-header">
      <h5 id="confirm-modal-title" class="modal-title">Confirmation</h5>
    </div>
    <div class="modal-body"></div>
    <div class="modal-footer">
      <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
      <button type="button" class="btn btn-primary">OK</button>
    </div>
  </div>
</div>
`;

const confirmModalEl = document.createElement('div');
confirmModalEl.classList.add('modal');
confirmModalEl.classList.add('fade');
confirmModalEl.tabIndex = -1;
confirmModalEl.ariaLabelledBy = '#confirm-modal-title';
confirmModalEl.ariaHidden = true;
confirmModalEl.innerHTML = confirmModalInnerHTML;
const confirmModalBody = confirmModalEl.querySelector('.modal-body');
const confirmModalOK = confirmModalEl.querySelector('.btn-primary');

document.body.appendChild(confirmModalEl);

const confirmModal = new Modal(confirmModalEl, {
  keyboard: false,
  backdrop: 'static',
});

async function customConfirm(message) {
  if (!message) {
    message = 'Are you sure?';
  }

  confirmModalBody.innerHTML = message;
  confirmModal.show();

  let confirmed = false;

  confirmModalOK.addEventListener('click', () => {
    confirmed = true;
    confirmModal.hide();
  });

  return new Promise((resolve) => {
    confirmModalEl.addEventListener('hidden.bs.modal', () => {
      resolve(confirmed);
    });
  });
}

const confirmLinks = document.querySelectorAll('a[data-confirm]');

confirmLinks.forEach(function (confirmLink) {
  confirmLink.addEventListener('click', async function (e) {
    e.preventDefault();

    const confirmed = await customConfirm(confirmLink.dataset.confirm);

    if (confirmed) {
      window.location.href = confirmLink.href;
    }

    return false;
  });
});

const confirmForms = document.querySelectorAll('form[data-confirm]');

confirmForms.forEach(function (confirmForm) {
  confirmForm.addEventListener('submit', async function (e) {
    e.preventDefault();

    const confirmed = await customConfirm(confirmForm.dataset.confirm);

    if (confirmed) {
      confirmForm.submit();
    }

    return false;
  });
});
