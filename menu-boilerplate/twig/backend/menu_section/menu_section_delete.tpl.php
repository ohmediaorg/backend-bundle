{% extends '@OHMediaBackend/form.html.twig' %}

{% set form_title = 'Delete ' ~ <?php echo $singular['snake_case']; ?>_section %}

{% block breadcrumbs %}
  {{ bootstrap_breadcrumbs(
    bootstrap_breadcrumb(bootstrap_icon('<?php echo $icon; ?>') ~ ' Menus', '<?php echo $singular['snake_case']; ?>_index'),
    bootstrap_breadcrumb(<?php echo $singular['snake_case']; ?>_section.menu, '<?php echo $singular['snake_case']; ?>_view', {id: <?php echo $singular['snake_case']; ?>_section.menu.id}),
    bootstrap_breadcrumb(<?php echo $singular['snake_case']; ?>_section, '<?php echo $singular['snake_case']; ?>_section_view', {id: <?php echo $singular['snake_case']; ?>_section.id}),
    bootstrap_breadcrumb('Delete'),
  ) }}
{% endblock %}
