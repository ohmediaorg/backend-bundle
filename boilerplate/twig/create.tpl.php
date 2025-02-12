{% extends '@OHMediaBackend/form.html.twig' %}

{% set form_title = 'Create <?php echo $singular['title']; ?>' %}

{% block breadcrumbs %}
  {{ bootstrap_breadcrumbs(
    bootstrap_breadcrumb(bootstrap_icon('<?php echo $icon; ?>') ~ ' <?php echo $plural['title']; ?>', '<?php echo $singular['snake_case']; ?>_index'),
    bootstrap_breadcrumb('Create'),
  ) }}
{% endblock %}
