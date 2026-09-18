{% extends '@backend/menu_item/menu_item_form.html.twig' %}

{% set form_title = 'Create Menu Item' %}

{% block breadcrumbs %}
  {{ bootstrap_breadcrumbs(
    bootstrap_breadcrumb(bootstrap_icon('fork-knife') ~ ' Menus', 'menu_index'),
    bootstrap_breadcrumb(menu_item.section.menu, 'menu_view', {id: menu_item.section.menu.id}),
    bootstrap_breadcrumb(menu_item.section, 'menu_section_view', {id: menu_item.section.id}),
    bootstrap_breadcrumb('Create'),
  ) }}
{% endblock %}
