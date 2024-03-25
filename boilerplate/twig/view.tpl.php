{% extends '@OHMediaBackend/base.html.twig' %}

{% block meta_title %}{{ <?php echo $singular['snake_case']; ?> }} | <?php echo $plural['title']; ?>{% endblock %}

{% block breadcrumbs %}
{{ bootstrap_breadcrumbs(
  bootstrap_breadcrumb(bootstrap_icon('<?php echo $icon; ?>') ~ ' <?php echo $plural['title']; ?>', '<?php echo $singular['snake_case']; ?>_index'),
  bootstrap_breadcrumb(<?php echo $singular['snake_case']; ?>),
) }}
{% endblock %}

{% block actions %}
<div class="btn-group btn-group-sm">
  {% if is_granted(attributes.edit, <?php echo $singular['snake_case']; ?>) %}
    <a class="btn btn-primary" href="{{ path('<?php echo $singular['snake_case']; ?>_edit', {id: <?php echo $singular['snake_case']; ?>.id}) }}">
      {{ bootstrap_icon('pen-fill') }}
      Edit
    </a>
  {% endif %}
  {% if is_granted(attributes.delete, <?php echo $singular['snake_case']; ?>) %}
    <a class="btn btn-danger" href="{{ path('<?php echo $singular['snake_case']; ?>_delete', {id: <?php echo $singular['snake_case']; ?>.id}) }}" data-confirm="Are you sure you want to delete this <?php echo $singular['readable']; ?>? Clicking OK will take you to a verification step to delete this entry.">
      {{ bootstrap_icon('trash-fill') }}
      Delete
    </a>
  {% endif %}
</div>
{% endblock %}

{% block main %}
<div class="card">
  <div class="card-body">
    <h1 class="card-title h3"><?php echo $singular['title']; ?></h1>

    {{ dump(<?php echo $singular['snake_case']; ?>) }}
  </div>
</div>
{% endblock %}
