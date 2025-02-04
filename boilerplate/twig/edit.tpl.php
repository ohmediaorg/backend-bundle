{% extends '@OHMediaBackend/form.html.twig' %}

{% set form_title = 'Edit <?php echo $singular['title']; ?>' %}

{% block breadcrumbs %}
  {{ bootstrap_breadcrumbs(
    bootstrap_breadcrumb(bootstrap_icon('<?php echo $icon; ?>') ~ ' <?php echo $plural['title']; ?>', '<?php echo $singular['snake_case']; ?>_index'),
  <?php if ($has_view_route) { ?>
    bootstrap_breadcrumb(<?php echo $singular['snake_case']; ?>, '<?php echo $singular['snake_case']; ?>_view', {id: <?php echo $singular['snake_case']; ?>.id}),
  <?php } ?>
    bootstrap_breadcrumb('Edit'),
  ) }}
{% endblock %}
