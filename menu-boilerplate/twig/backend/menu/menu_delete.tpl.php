{% extends '@OHMediaBackend/form.html.twig' %}

{% set form_title = 'Delete ' ~ menu %}

{% block breadcrumbs %}
  {{ bootstrap_breadcrumbs(
    bootstrap_breadcrumb(bootstrap_icon('fork-knife') ~ ' Menus', '<?php echo $singular['snake_case']; ?>_index'),
    bootstrap_breadcrumb(menu, '<?php echo $singular['snake_case']; ?>_view', {id: menu.id}),
    bootstrap_breadcrumb('Delete'),
  ) }}
{% endblock %}
