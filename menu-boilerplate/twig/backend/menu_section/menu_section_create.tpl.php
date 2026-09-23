{% extends '@OHMediaBackend/form.html.twig' %}

{% set form_title = 'Create Menu Section' %}

{% block breadcrumbs %}
  {{ bootstrap_breadcrumbs(
    bootstrap_breadcrumb(bootstrap_icon('<?php echo $icon; ?>') ~ ' <?php echo $singular['title']; ?>s', '<?php echo $singular['snake_case']; ?>_index'),
    bootstrap_breadcrumb(<?php echo $singular['snake_case']; ?>_section.menu, '<?php echo $singular['snake_case']; ?>_view', {id: <?php echo $singular['snake_case']; ?>_section.menu.id}),
    bootstrap_breadcrumb('Create'),
  ) }}
{% endblock %}
