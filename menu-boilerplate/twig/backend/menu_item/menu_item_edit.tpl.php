{% extends '@backend/<?php echo $singular['snake_case']; ?>_item/<?php echo $singular['snake_case']; ?>_item_form.html.twig' %}

{% set form_title = 'Edit Menu Item' %}

{% block breadcrumbs %}
  {{ bootstrap_breadcrumbs(
    bootstrap_breadcrumb(bootstrap_icon('<?php echo $icon; ?>') ~ ' <?php echo $singular['title_case']; ?>s', '<?php echo $singular['snake_case']; ?>_index'),
    bootstrap_breadcrumb(<?php echo $singular['snake_case']; ?>_item.section.menu, '<?php echo $singular['snake_case']; ?>_view', {id: <?php echo $singular['snake_case']; ?>_item.section.menu.id}),
    bootstrap_breadcrumb(<?php echo $singular['snake_case']; ?>_item.section, '<?php echo $singular['snake_case']; ?>_section_view', {id: <?php echo $singular['snake_case']; ?>_item.section.id}),
    bootstrap_breadcrumb('Edit'),
  ) }}
{% endblock %}
