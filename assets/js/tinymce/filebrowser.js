export default function (filesUrl) {
  tinymce.PluginManager.add('ohfilebrowser', (editor, url) => {
    async function openDialog() {
      let selectedItem = null;

      const dialogConfig = {
        title: 'File Browser',
        size: 'medium',
        buttons: [{ type: 'cancel', text: 'Close' }],
        body: {
          type: 'panel',
          items: [
            {
              type: 'alertbanner',
              text: 'Loading files...',
              level: 'info',
              icon: 'info',
            },
          ],
        },
      };

      const dialog = editor.windowManager.open(dialogConfig);

      const containerId = 'tinymce_filebrowser_tbody';
      let container = null;

      async function populateFiles(url) {
        if (container) {
          container.innerHTML = '';
        }

        dialog.setEnabled('insert_button', false);

        dialogConfig.body = {
          type: 'panel',
          items: [
            {
              type: 'alertbanner',
              text: 'Loading files...',
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

          selectedItem = null;

          container = document.getElementById(containerId);

          items.forEach(function (item) {
            const row = document.createElement('tr');

            const col1 = document.createElement('td');
            col1.style.width = '55px';
            col1.style.fontSize = '1.5rem';
            col1.style.textAlign = 'center';

            const col2 = document.createElement('td');

            row.append(col1);

            row.append(col2);

            if ('directory' === item.type) {
              col1.innerHTML = '<i class="bt bi-folder-fill"></i>';
              col2.innerHTML = item.name;
              col2.colspan = 2;

              row.onclick = populateFiles.bind(null, item.url);
              row.style.cursor = 'pointer';
            } else {
              if ('image' === item.type) {
                col1.innerHTML = item.image;
              } else {
                col1.innerHTML = '<i class="bt bi-file-earmark-fill"></i>';
              }

              col2.innerHTML = item.name + ' (ID:' + item.id + ')';

              const col3 = document.createElement('td');

              const buttonLink = document.createElement('button');
              buttonLink.className = 'tox-button';
              buttonLink.title = 'Insert Link';
              buttonLink.style.padding = '4px 8px';
              buttonLink.innerHTML = '<i class="bt bi-link-45deg"></i>';
              buttonLink.onclick = () => {
                editor.insertContent(
                  `<a href="{{ file_href(${item.id}) }}" title="${item.name}" target="_blank">${item.name}</a>`
                );

                dialog.close();
              };

              col3.append(buttonLink);

              if ('image' === item.type) {
                const buttonImage = document.createElement('button');
                buttonImage.className = 'tox-button';
                buttonLink.title = 'Insert Image';
                buttonImage.style.marginLeft = '8px';
                buttonImage.style.padding = '4px 8px';
                buttonImage.innerHTML = '<i class="bt bi-image"></i>';
                buttonImage.onclick = () => {
                  editor.insertContent(`{{ image(${item.id}) }}`);

                  dialog.close();
                };

                col3.append(buttonImage);
              }

              row.append(col3);
            }

            container.append(row);
          });
        } catch (e) {
          console.log(e);
          dialogConfig.body = {
            type: 'panel',
            items: [
              {
                type: 'alertbanner',
                text: 'There was an issue loading the files.',
                level: 'warn',
                icon: 'warning',
              },
            ],
          };

          dialog.redial(dialogConfig);
        }
      }

      populateFiles(filesUrl);
    }

    editor.ui.registry.addButton('ohfilebrowser', {
      name: 'File Browser',
      icon: 'image',
      tooltip: 'File Browser',
      onAction: openDialog,
    });

    editor.ui.registry.addMenuItem('ohfilebrowser', {
      text: 'File Browser',
      icon: 'image',
      onAction: openDialog,
    });

    return {
      getMetadata: () => ({
        name: 'File Browser',
        url: 'mailto:support@ohmedia.ca',
      }),
    };
  });
}
