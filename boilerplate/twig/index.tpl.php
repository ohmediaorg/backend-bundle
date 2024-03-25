{% extends '@OHMediaBackend/base.html.twig' %}

{% block meta_title %}<?php echo $plural['title']; ?>{% endblock %}

{% block breadcrumbs %}
{{ bootstrap_breadcrumbs(
  bootstrap_breadcrumb(bootstrap_icon('<?php echo $icon; ?>') ~ ' <?php echo $plural['title']; ?>', '<?php echo $singular['snake_case']; ?>_index'),
) }}
{% endblock %}

{% block actions %}
{% if is_granted(attributes.create, new_<?php echo $singular['snake_case']; ?>) %}
<a href="{{ path('<?php echo $singular['snake_case']; ?>_create') }}" class="btn btn-sm btn-primary">
  <i class="bi bi-plus"></i> Add <?php echo $singular['title']; ?>
</a>
{% endif %}
{% endblock %}

{% block main %}
<div class="card">
  <div class="card-body">
    <h1 class="card-title h3"><?php echo $plural['title']; ?></h1>

    <table class="table table-striped">
      <thead>
        <tr>
          <th><?php echo $singular['title']; ?></th>
          <th></th>
        </tr>
      </thead>
      <tbody>
        {% for <?php echo $singular['snake_case']; ?> in pagination.results %}
        <tr>
          <td>{{ <?php echo $singular['snake_case']; ?> }}</td>
          <td>
<?php if ($has_view_route) { ?>
            {% if is_granted(attributes.view, <?php echo $singular['snake_case']; ?>) %}
            <a class="btn btn-sm btn-primary btn-action" href="{{ path('<?php echo $singular['snake_case']; ?>_view', {id: <?php echo $singular['snake_case']; ?>.id}) }}" title="View">
              {{ bootstrap_icon('eye-fill') }}
              <span class="visually-hidden">View</span>
            </a>
            {% endif %}
<?php } ?>
            {% if is_granted(attributes.edit, <?php echo $singular['snake_case']; ?>) %}
            <a class="btn btn-sm btn-primary btn-action" href="{{ path('<?php echo $singular['snake_case']; ?>_edit', {id: <?php echo $singular['snake_case']; ?>.id}) }}" title="Edit">
              {{ bootstrap_icon('pen-fill') }}
              <span class="visually-hidden">Edit</span>
            </a>
            {% endif %}
            {% if is_granted(attributes.delete, <?php echo $singular['snake_case']; ?>) %}
            <a class="btn btn-sm btn-danger btn-action" href="{{ path('<?php echo $singular['snake_case']; ?>_delete', {id: <?php echo $singular['snake_case']; ?>.id}) }}" title="Delete" data-confirm="Are you sure you want to delete this <?php echo $singular['readable']; ?>? Clicking OK will take you to a verification step to delete this entry.">
              {{ bootstrap_icon('trash-fill') }}
              <span class="visually-hidden">Delete</span>
            </a>
            {% endif %}
          </td>
        </tr>
        {% else %}
        <tr><td colspan="100%">No <?php echo $plural['readable']; ?> found.</td></tr>
        {% endfor %}
      </tbody>
    </table>

    {{ bootstrap_pagination(pagination) }}

    <small>{{ bootstrap_pagination_info(pagination) }}</small>
  </div>
</div>
{% endblock %}
