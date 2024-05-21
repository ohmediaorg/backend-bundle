function getBackRow(onclick) {
  const row = document.createElement('tr');

  const col1 = getColumnOne();
  col1.innerHTML = '<i class="bt bi-arrow-up-left-square-fill"></i>';

  row.append(col1);

  const col2 = document.createElement('td');
  col2.innerHTML = 'Back';

  row.append(col2);

  const col3 = document.createElement('td');
  col3.innerHTML = '&nbsp;';

  row.append(col3);

  row.onclick = onclick;
  row.style.cursor = 'pointer';

  return row;
}

function getFolderRow(item, onclick) {
  const row = document.createElement('tr');

  const col1 = getColumnOne();
  col1.innerHTML = '<i class="bt bi-folder-fill"></i>';

  row.append(col1);

  const col2 = document.createElement('td');
  col2.innerHTML = item.name;

  row.append(col2);

  const col3 = document.createElement('td');
  col3.innerHTML = '&nbsp;';

  row.append(col3);

  row.onclick = onclick;
  row.style.cursor = 'pointer';

  return row;
}

function getImageRow(item, onclickImage, onclickLink) {
  const row = document.createElement('tr');

  const col1 = getColumnOne();
  col1.innerHTML = item.image;

  row.append(col1);

  const col2 = document.createElement('td');
  col2.innerHTML = item.name + ' (ID:' + item.id + ')';

  row.append(col2);

  const col3 = document.createElement('td');
  col3.style.textAlign = 'right';

  col3.append(getButtonImage(onclickImage));
  col3.append(getButtonLink(onclickLink));

  row.append(col3);

  return row;
}

function getFileRow(item, onclickLink) {
  const row = document.createElement('tr');

  const col1 = getColumnOne();
  col1.innerHTML = '<i class="bt bi-file-earmark-fill"></i>';

  row.append(col1);

  const col2 = document.createElement('td');
  col2.innerHTML = item.name + ' (ID:' + item.id + ')';

  row.append(col2);

  const col3 = document.createElement('td');
  col3.style.textAlign = 'right';

  col3.append(getButtonLink(onclickLink));

  row.append(col3);

  return row;
}

function getColumnOne() {
  const column = document.createElement('td');
  column.style.width = '55px';
  column.style.fontSize = '1.5rem';
  column.style.textAlign = 'center';

  return column;
}

function getButtonLink(onclick) {
  const button = getButton();
  button.title = 'Insert Link';
  button.innerHTML = '<i class="bi bi-link-45deg"></i>';
  button.onclick = onclick;

  return button;
}

function getButtonImage(onclick) {
  const button = getButton();
  button.title = 'Insert Image';
  button.innerHTML = '<i class="bi bi-image"></i>';
  button.onclick = onclick;

  return button;
}

function getButton() {
  const button = document.createElement('button');
  button.className = 'tox-button';
  button.style.marginLeft = '8px';
  button.style.marginBottom = '8px';
  button.style.padding = '4px 8px';

  return button;
}

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

      function onclickFile(item) {
        editor.insertContent(
          `<a href="{{ file_href(${item.id}) }}" title="${item.name}" target="_blank">${item.name}</a>`
        );

        dialog.close();
      }

      async function populateFiles(url) {
        localStorage.setItem('tinymce_filebrowser_url', url);

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
          const data = await response.json();

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

          if (data.back_path) {
            const back = getBackRow(populateFiles.bind(null, data.back_path));

            container.append(back);
          }

          data.items.forEach(function (item) {
            let row = null;

            if ('folder' === item.type) {
              row = getFolderRow(item, populateFiles.bind(null, item.url));
            } else if ('image' === item.type) {
              const onclickImage = () => {
                editor.insertContent(`{{ image(${item.id}) }}`);

                dialog.close();
              };

              row = getImageRow(
                item,
                onclickImage,
                onclickFile.bind(null, item)
              );
            } else if ('file' === item.type) {
              row = getFileRow(item, onclickFile.bind(null, item));
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

      const savedUrl = localStorage.getItem('tinymce_filebrowser_url');

      populateFiles(savedUrl ?? filesUrl);
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
