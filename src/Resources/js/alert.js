import { Modal } from 'bootstrap';

const alertModalInnerHTML = `
<div class="modal-dialog">
  <div class="modal-content">
    <div class="modal-header">
      <h5 id="alert-modal-title" class="modal-title">Notice</h5>
    </div>
    <div class="modal-body"></div>
    <div class="modal-footer">
      <button type="button" class="btn btn-primary" data-bs-dismiss="modal">OK</button>
    </div>
  </div>
</div>
`;

const alertModalEl = document.createElement('div');
alertModalEl.classList.add('modal');
alertModalEl.classList.add('fade');
alertModalEl.tabIndex = -1;
alertModalEl.ariaLabelledBy = '#alert-modal-title';
alertModalEl.ariaHidden = true;
alertModalEl.innerHTML = alertModalInnerHTML;
const alertModalBody = alertModalEl.querySelector('.modal-body');

document.body.appendChild(alertModalEl);

const alertModal = new Modal(alertModalEl, {
  keyboard: false,
  backdrop: 'static',
});

window.alert = function (message = '') {
  alertModalBody.innerHTML = message;
  alertModal.show();
};
