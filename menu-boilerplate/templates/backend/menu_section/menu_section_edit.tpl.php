{% extends '@OHMediaBackend/form.html.twig' %}

{% set form_title = 'Edit Menu Section' %}

{% block breadcrumbs %}
  {{ bootstrap_breadcrumbs(
    bootstrap_breadcrumb(bootstrap_icon('fork-knife') ~ ' Menus', 'menu_index'),
    bootstrap_breadcrumb(menu_section.menu, 'menu_view', {id: menu_section.menu.id}),
    bootstrap_breadcrumb(menu_section, 'menu_section_view', {id: menu_section.id}),
    bootstrap_breadcrumb('Edit'),
  ) }}
{% endblock %}
