import { Toast } from 'bootstrap';

const container = document.getElementById('toasts');

export default function (message, type) {
  const toast = document.createElement('div');
  toast.className = `toast text-bg-${type}`;
  toast.role = 'alert';
  toast.ariaLive = 'assertive';
  toast.ariaAtomic = true;

  const flex = document.createElement('div');
  flex.className = 'd-flex';

  toast.appendChild(flex);

  const body = document.createElement('div');
  body.className = 'toast-body';
  body.innerHTML = message;

  flex.appendChild(body);

  const button = document.createElement('button');
  button.type = 'button';
  button.className = 'btn-close me-2 m-auto';
  button.ariaLabel = 'Close';

  flex.appendChild(button);

  container.appendChild(toast);

  const bootstrapToast = new Toast(toast);
  bootstrapToast.show();

  button.addEventListener('click', (e) => {
    e.preventDefault();

    bootstrapToast.hide();
  });

  toast.addEventListener('hidden.bs.toast', () => {
    toast.remove();
  });
}
