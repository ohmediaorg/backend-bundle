export default function (imagesUrl) {
  tinymce.PluginManager.add('ohimagebrowser', (editor, url) => {
    async function openDialog() {
      let imageId = null;

      const dialogConfig = {
        title: 'Image Browser',
        size: 'medium',
        buttons: [
          { type: 'cancel', text: 'Close' },
          {
            type: 'submit',
            text: 'Insert',
            buttonType: 'primary',
            name: 'insert_button',
            enabled: false,
          },
        ],
        body: {
          type: 'panel',
          items: [
            {
              type: 'alertbanner',
              text: 'Loading images...',
              level: 'info',
              icon: 'info',
            },
          ],
        },
        onSubmit: (api) => {
          if (imageId) {
            editor.insertContent(`{{ image(${imageId}) }}`);
          }

          api.close();
        },
      };

      const dialog = editor.windowManager.open(dialogConfig);

      const containerId = 'tinymce_imagebrowser_tbody';
      let container = null;

      async function populateImages(url) {
        if (container) {
          container.innerHTML = '';
        }

        dialog.setEnabled('insert_button', false);

        dialogConfig.body = {
          type: 'panel',
          items: [
            {
              type: 'alertbanner',
              text: 'Loading images...',
              level: 'info',
              icon: 'info',
            },
          ],
        };

        dialog.redial(dialogConfig);

        try {
          const response = await fetch(url);
          const items = await response.json();

          dialogConfig.body = {
            type: 'panel',
            items: [
              {
                type: 'htmlpanel',
                html: `
                  <table class="tox-dialog__table" style="vertical-align:middle">
                    <tbody id="${containerId}"></tbody>
                  </table>`,
              },
            ],
          };

          dialog.redial(dialogConfig);

          imageId = null;

          container = document.getElementById(containerId);

          items.forEach(function (item) {
            const row = document.createElement('tr');
            row.style.cursor = 'pointer';

            const col1 = document.createElement('td');
            col1.style.width = '55px';
            col1.style.fontSize = '1.5rem';
            col1.style.textAlign = 'center';

            if ('directory' === item.type) {
              col1.innerHTML = '<i class="bt bi-folder-fill"></i>';

              row.onclick = populateImages.bind(null, item.url);
            } else if ('image' === item.type) {
              col1.innerHTML = item.image;

              row.onclick = () => {
                imageId = item.id;

                dialog.setEnabled('insert_button', true);

                container.querySelectorAll('tr').forEach((tr) => {
                  tr.style.fontWeight = '';
                });

                row.style.fontWeight = 'bold';
              };
            }

            row.append(col1);

            const col2 = document.createElement('td');
            col2.innerHTML = item.text;

            row.append(col2);

            container.append(row);
          });
        } catch (e) {
          console.log(e);
          dialogConfig.body = {
            type: 'panel',
            items: [
              {
                type: 'alertbanner',
                text: 'There was an issue loading the images.',
                level: 'warn',
                icon: 'warning',
              },
            ],
          };

          dialog.redial(dialogConfig);
        }
      }

      populateImages(imagesUrl);
    }

    editor.ui.registry.addButton('ohimagebrowser', {
      name: 'Image Browser',
      icon: 'image',
      tooltip: 'Image Browser',
      onAction: openDialog,
    });

    editor.ui.registry.addMenuItem('ohimagebrowser', {
      text: 'Image Browser',
      icon: 'image',
      onAction: openDialog,
    });

    return {
      getMetadata: () => ({
        name: 'Image Browser',
        url: 'mailto:support@ohmedia.ca',
      }),
    };
  });
}
