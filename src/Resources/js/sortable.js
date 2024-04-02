import Sortable from 'sortablejs';

document.addEventListener('DOMContentLoaded', function () {
  document.querySelectorAll('[data-sortable]').forEach(function (container) {
    const csrfName = container.dataset.sortableCsrfName;
    const csrfToken = container.dataset.sortableCsrfToken;
    const url = container.dataset.sortableUrl;

    const sortable = new Sortable(container, {
      handle: '[data-handle]',
      onEnd: (e) => {
        const formData = new FormData();

        formData.set(csrfName, csrfToken);

        sortable.toArray().forEach((id) => {
          formData.append('order[]', id);
        });

        fetch(url, {
          method: 'POST',
          body: formData,
        })
          .then((r) => r.json())
          .then((result) => {
            alert('Order updated');
          })
          .catch((error) => {
            alert(error);
          });
      },
    });
  });
});
