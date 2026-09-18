{% extends '@OHMediaBackend/form.html.twig' %}

{% set form_title = 'Edit Menu' %}

{% block breadcrumbs %}
  {{ bootstrap_breadcrumbs(
    bootstrap_breadcrumb(bootstrap_icon('<?php echo $icon; ?>') ~ ' <?php echo $singular['title_case']; ?>s', '<?php echo $singular['snake_case']; ?>_index'),
    bootstrap_breadcrumb(menu, '<?php echo $singular['snake_case']; ?>_view', {id: menu.id}),
    bootstrap_breadcrumb('Edit'),
  ) }}
{% endblock %}
