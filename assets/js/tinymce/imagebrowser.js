export default function (imagesUrl) {
  tinymce.PluginManager.add('ohimagebrowser', (editor, url) => {
    async function openDialog() {
      let imageId = null;

      const dialogConfig = {
        title: 'Image Browser',
        size: 'medium',
        buttons: [
          { type: 'cancel', text: 'Close' },
          { type: 'submit', text: 'Insert', buttonType: 'primary' },
        ],
        onSubmit: (api) => {
          if (imageId) {
            editor.insertContent(`{{ image(${imageId}) }}`);
          }

          api.close();
        },
      };

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

      const dialog = editor.windowManager.open(dialogConfig);

      try {
        const response = await fetch(imagesUrl);
        const images = await response.json();

        dialogConfig.body = {
          type: 'panel',
          items: [
            {
              // TODO: look at using htmlpanel instead so image thumbnails
              // can be rendered
              type: 'tree',
              onLeafAction: (id) => {
                imageId = id;
              },
              items: images,
            },
          ],
        };

        dialog.redial(dialogConfig);

        imageId = null;
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
