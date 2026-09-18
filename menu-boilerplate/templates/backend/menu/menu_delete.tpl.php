{% extends '@OHMediaBackend/form.html.twig' %}

{% set form_title = 'Delete ' ~ menu %}

{% block breadcrumbs %}
  {{ bootstrap_breadcrumbs(
    bootstrap_breadcrumb(bootstrap_icon('fork-knife') ~ ' Menus', 'menu_index'),
    bootstrap_breadcrumb(menu, 'menu_view', {id: menu.id}),
    bootstrap_breadcrumb('Delete'),
  ) }}
{% endblock %}
