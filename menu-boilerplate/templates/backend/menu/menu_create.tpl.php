{% extends '@OHMediaBackend/form.html.twig' %}

{% set form_title = 'Create Menu' %}

{% block breadcrumbs %}
  {{ bootstrap_breadcrumbs(
    bootstrap_breadcrumb(bootstrap_icon('fork-knife') ~ ' Menus', 'menu_index'),
    bootstrap_breadcrumb('Create'),
  ) }}
{% endblock %}
