{% extends '@OHMediaBackend/form.html.twig' %}

{% set form_title = 'Edit Menu Section' %}

{% block breadcrumbs %}
  {{ bootstrap_breadcrumbs(
    bootstrap_breadcrumb(bootstrap_icon('fork-knife') ~ ' Menus', '<?php echo $singular['snake_case']; ?>_index'),
    bootstrap_breadcrumb(<?php echo $singular['snake_case']; ?>_section.menu, '<?php echo $singular['snake_case']; ?>_view', {id: <?php echo $singular['snake_case']; ?>_section.menu.id}),
    bootstrap_breadcrumb(<?php echo $singular['snake_case']; ?>_section, '<?php echo $singular['snake_case']; ?>_section_view', {id: <?php echo $singular['snake_case']; ?>_section.id}),
    bootstrap_breadcrumb('Edit'),
  ) }}
{% endblock %}
